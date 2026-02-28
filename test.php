<?php

/**
 * Subscription Payment Plan Controller
 *
 * Handles subscription upgrades and plan changes. Refactored from legacy controller
 * for security, clarity, and maintainability. See inline REFACTOR comments for
 * what changed and why—useful when onboarding new developers.
 *
 * Legacy: when debug_skip_cost_calc is true in the request payload, cost calculation
 * is skipped and totalCost is treated as 0 (e.g. for testing or legacy clients).
 */

declare(strict_types=1);

class SubscriptionController
{
    /** @var float Flat tax rate applied to each plan (e.g. 5.00 = $5 per plan). */
    private const TAX_PER_PLAN = 5.00;

    /** @var float Threshold above which orders require manual review (e.g. $100). */
    private const MANUAL_REVIEW_THRESHOLD = 100.00;

    /** @var int Grace period days added after subscription end for billing review. */
    private const GRACE_PERIOD_DAYS = 7;

    /**
     * Allowed keys when updating user profile from request (whitelist).
     * REFACTOR: Replaces unrestricted mass assignment; prevents clients from
     * setting arbitrary user fields (e.g. role, email_verified) via userData.
     */
    private const ALLOWED_USER_UPDATE_KEYS = ['display_name', 'billing_address', 'phone'];

    /**
     * Processes a subscription upgrade request: validates input, computes cost,
     * updates user/profile, and returns a structured JSON response.
     *
     * REFACTOR: Method now has a single responsibility and explicit return type.
     * New hires should explain: why we validate first, then compute, then persist.
     *
     * @param Request $request HTTP request with JSON body (planIds, userData).
     * @return void Outputs JSON and sets headers; consider returning a Response DTO in future.
     */
    public function upgrade(Request $request): void
    {
        $this->ensureJsonResponse();

        try {
            // REFACTOR: Decode once and validate structure before any side effects.
            $payload = $this->decodeAndValidatePayload($request);

            $planIds = $payload['planIds'];
            $userData = $payload['userData'];
            $debugSkip = !empty($payload['debug_skip_cost_calc']);

            // REFACTOR: Centralized auth check; fail fast with 401 if no session user.
            $user = $this->getAuthenticatedUser();

            // REFACTOR: Load plans through a dedicated method so we can validate
            // planIds (type, count, existence) and avoid modifying ORM objects in place.
            $plans = $this->loadPlansForIds($planIds);
            $this->validatePlansExistAndMatch($planIds, $plans);
            $this->validatePlanShape($plans);

            // Legacy: when debug_skip_cost_calc is set, totalCost = 0 and status = auto_approve.
            $totalCost = $debugSkip ? 0.0 : $this->calculateTotalCostWithTax($plans);

            // REFACTOR: Grace period uses a clone of the end date so we never
            // mutate the user's stored end date by reference (legacy bug).
            $user = $this->applyGracePeriod($user);

            // REFACTOR: Only whitelisted user fields are updated (see ALLOWED_USER_UPDATE_KEYS).
            $this->updateUserProfileFromPayload($user, $userData);
            $user->save();

            $status = $debugSkip ? 'auto_approve' : $this->resolveUpgradeStatus($totalCost);

            $this->sendSuccessResponse(
                status: $status,
                totalCost: $totalCost,
                userId: (int) $user->id
            );
        } catch (InvalidRequestException $e) {
            $this->sendErrorResponse(400, $e->getMessage());
        } catch (UnauthorizedException $e) {
            $this->sendErrorResponse(401, 'Authentication required');
        } catch (Exception $e) {
            $this->sendErrorResponse(500, 'Internal Server Error');
        }
    }

    /**
     * REFACTOR: Ensures JSON content-type is set once for all responses.
     * Prevents mixed output (e.g. HTML before JSON) and duplicate headers.
     */
    private function ensureJsonResponse(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Decodes request body and validates required keys and types.
     *
     * REFACTOR: Replaces blind json_decode + ?? []; invalid/malformed payload
     * now yields 400 with a clear message instead of silent wrong behavior.
     *
     * @return array{planIds: array<int>, userData: array<string, mixed>}
     * @throws InvalidRequestException When body is invalid or required keys missing.
     */
    private function decodeAndValidatePayload(Request $request): array
    {
        $raw = $request->getBody();
        $payload = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidRequestException('Invalid JSON in request body');
        }

        if (!is_array($payload)) {
            throw new InvalidRequestException('Request body must be a JSON object');
        }

        $planIds = $payload['planIds'] ?? null;
        if (!is_array($planIds)) {
            throw new InvalidRequestException('planIds must be an array');
        }

        // REFACTOR: Accept numeric IDs (JSON may send numbers as int or string); cast to int and drop invalid.
        $planIds = array_values(array_unique(array_filter(array_map('intval', $planIds), fn($id) => $id > 0)));
        if ($planIds === []) {
            throw new InvalidRequestException('At least one valid plan ID is required');
        }

        $userData = $payload['userData'] ?? [];
        if (!is_array($userData)) {
            throw new InvalidRequestException('userData must be an array');
        }

        $debugSkipCostCalc = $payload['debug_skip_cost_calc'] ?? false;
        return [
            'planIds' => $planIds,
            'userData' => $userData,
            'debug_skip_cost_calc' => (bool) $debugSkipCostCalc,
        ];
    }

    /**
     * Returns the current user from session; throws if not authenticated.
     *
     * REFACTOR: Session access is encapsulated. New hires should explain why we
     * don't use $_SESSION directly in upgrade() (testability, single place for auth).
     *
     * @throws UnauthorizedException When session user is missing.
     */
    private function getAuthenticatedUser(): User
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null || $userId === '') {
            throw new UnauthorizedException('Not authenticated');
        }

        $user = User::findById($userId);
        if ($user === null) {
            throw new UnauthorizedException('User not found');
        }

        return $user;
    }

    /**
     * Loads plan entities by IDs and returns them without mutating ORM objects.
     *
     * REFACTOR: We no longer use array_sanitize_recursive_deep (undefined in many setups)
     * or foreach-by-reference to add tax onto plan->price. Tax is applied only when
     * calculating total, so Plan entities stay unchanged and cacheable.
     */
    private function loadPlansForIds(array $planIds): array
    {
        $plans = Plan::findMany($planIds);
        if (!is_array($plans)) {
            return [];
        }
        return array_values($plans);
    }

    /**
     * Ensures the number of loaded plans matches the requested plan IDs.
     * Fails with 400 when one or more plan IDs do not exist or are not returned.
     */
    private function validatePlansExistAndMatch(array $planIds, array $plans): void
    {
        if (count($plans) !== count($planIds)) {
            throw new InvalidRequestException('One or more plan IDs do not exist or are invalid');
        }
        $loadedIds = array_map(fn($p) => isset($p->id) ? (int) $p->id : 0, $plans);
        $requestedSet = array_fill_keys($planIds, true);
        foreach ($loadedIds as $id) {
            if (!isset($requestedSet[$id])) {
                throw new InvalidRequestException('One or more plan IDs do not exist or are invalid');
            }
        }
    }

    /**
     * Validates that each plan has a valid price (numeric and >= 0).
     * Plans without a price or with invalid price are treated as invalid data; throws 400.
     */
    private function validatePlanShape(array $plans): void
    {
        foreach ($plans as $plan) {
            if (!isset($plan->price) || $plan->price === '' || $plan->price === null) {
                throw new InvalidRequestException('One or more plans have missing or invalid price');
            }
            if (!is_numeric($plan->price) || (float) $plan->price < 0) {
                throw new InvalidRequestException('One or more plans have invalid price');
            }
        }
    }

    /**
     * Computes total cost: sum of (plan price + TAX_PER_PLAN) for each plan.
     *
     * REFACTOR: Tax is applied here instead of mutating plan->price. Single place
     * for pricing logic; new hires should explain why we don't change Plan model.
     */
    private function calculateTotalCostWithTax(array $plans): float
    {
        $total = 0.0;
        foreach ($plans as $plan) {
            $price = isset($plan->price) ? (float) $plan->price : 0.0;
            $total += $price + self::TAX_PER_PLAN;
        }
        return round($total, 2);
    }

    /**
     * Applies grace period to user's subscription end date without mutating the original.
     *
     * REFACTOR: DateTime::modify() changes the instance in place. We clone the end
     * date first so getSubscriptionEndDate() remains consistent for other code.
     */
    private function applyGracePeriod(User $user): User
    {
        $endDate = $user->getSubscriptionEndDate();
        if ($endDate === null) {
            return $user;
        }
        $graceUntil = (clone $endDate)->modify('+' . self::GRACE_PERIOD_DAYS . ' days');
        $user->grace_period_until = $graceUntil;
        return $user;
    }

    /**
     * Updates only whitelisted user fields from the request userData.
     *
     * REFACTOR: Replaces "foreach ($userData as $key => $value) $user->$key = $value"
     * which allowed mass assignment (e.g. is_admin, password). New hires should
     * explain the security risk of unrestricted mass assignment.
     */
    private function updateUserProfileFromPayload(User $user, array $userData): void
    {
        foreach (self::ALLOWED_USER_UPDATE_KEYS as $key) {
            if (!array_key_exists($key, $userData)) {
                continue;
            }
            $value = $userData[$key];
            if (is_scalar($value) || $value === null) {
                $user->$key = $value;
            }
        }
    }

    /**
     * Determines upgrade status from total cost (business rule).
     *
     * REFACTOR: Replaces cryptic single-letter 's' and magic numbers. Status
     * values are explicit: fail when no cost, manual_flag above threshold.
     */
    private function resolveUpgradeStatus(float $totalCost): string
    {
        if ($totalCost <= 0) {
            return 'fail';
        }
        return $totalCost > self::MANUAL_REVIEW_THRESHOLD ? 'manual_flag' : 'auto_approve';
    }

    /**
     * Sends a successful JSON response with consistent keys.
     *
     * REFACTOR: Response shape is explicit; 's' -> status, 'c' -> totalCost,
     * 'u' -> userId for readability and API docs. Backward compatibility: if
     * the frontend expects s/c/u, keep them as documented here.
     * JSON encoding errors are guarded; on failure we send 500 and exit.
     */
    private function sendSuccessResponse(string $status, float $totalCost, int $userId): void
    {
        $payload = [
            'status' => $status,
            'totalCost' => $totalCost,
            'userId' => $userId,
            // Legacy keys for backward compatibility; remove when client is updated.
            's' => $status,
            'c' => $totalCost,
            'u' => $userId,
        ];
        $this->sendJson($payload);
    }

    /**
     * Sends a JSON error response with given HTTP status code.
     *
     * REFACTOR: Single place for error format; we set response code and body
     * together so we never send 200 with an error payload.
     * JSON encoding errors are guarded; on failure we send 500 and exit.
     */
    private function sendErrorResponse(int $httpCode, string $message): void
    {
        http_response_code($httpCode);
        $this->sendJson(['error' => $message]);
    }

    /**
     * Encodes payload as JSON and outputs it. Avoids JSON_THROW_ON_ERROR so we
     * can handle encode failures without uncaught exceptions; on failure sends 500.
     */
    private function sendJson(array $payload): void
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false && json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo '{"error":"Internal Server Error"}';
            return;
        }
        echo $json;
    }
}

/**
 * REFACTOR: Custom exceptions allow the controller to catch specific cases (e.g.
 * 400 vs 401) and keep try/catch in upgrade() readable. New hires should
 * explain why we use different status codes for client vs server errors.
 */
class InvalidRequestException extends Exception
{
}

class UnauthorizedException extends Exception
{
}
