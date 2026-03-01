<?php
/**
 * Package add/edit form
 */
if (!defined('ABSPATH')) {
    exit;
}
$is_edit = $package !== null;
$page_title = $is_edit ? __('Edit Package', 'dhr-hotel-management') : __('Add New Package', 'dhr-hotel-management');
$package_code = $is_edit ? $package->package_code : '';
$hotel_code   = $is_edit ? $package->hotel_code : '';
$category_id  = $is_edit ? (int) $package->category_id : 0;
$is_active    = $is_edit ? (int) $package->is_active : 1;
$hotels = DHR_Hotel_Database::get_all_hotels('active');
$categories = DHR_Hotel_Database::get_active_categories();
if (empty($categories)) {
    $categories = DHR_Hotel_Database::get_all_categories();
}
?>
<div class="wrap dhr-hotel-admin">
    <h1><?php echo esc_html($page_title); ?></h1>
    <p><a href="<?php echo admin_url('admin.php?page=dhr-hotel-packages'); ?>">&larr; <?php _e('Back to Package List', 'dhr-hotel-management'); ?></a></p>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('dhr_package_nonce'); ?>
        <input type="hidden" name="action" value="dhr_save_package">
        <?php if ($is_edit): ?>
            <input type="hidden" name="package_id" value="<?php echo esc_attr($package->id); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th><label for="package_code"><?php _e('Package Code', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="package_code" name="package_code" class="regular-text" value="<?php echo esc_attr($package_code); ?>" required>
                </td>
            </tr>
            <tr>
                <th><label for="hotel_code"><?php _e('Hotel Code', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <select id="hotel_code" name="hotel_code" class="regular-text" required>
                        <option value=""><?php _e('— Select Hotel —', 'dhr-hotel-management'); ?></option>
                        <?php foreach ($hotels as $h): ?>
                            <option value="<?php echo esc_attr($h->hotel_code); ?>" <?php selected($hotel_code, $h->hotel_code); ?>>
                                <?php echo esc_html($h->name . ' (' . $h->hotel_code . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($hotels)): ?>
                        <p class="description"><?php _e('No active hotels found. Add hotels first from All Hotels.', 'dhr-hotel-management'); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="category_id"><?php _e('Category', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <select id="category_id" name="category_id" class="regular-text" required>
                        <option value=""><?php _e('— Select Category —', 'dhr-hotel-management'); ?></option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo esc_attr($c->id); ?>" <?php selected($category_id, $c->id); ?>>
                                <?php echo esc_html(wp_unslash((string) ($c->title ?? ''))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($categories)): ?>
                        <p class="description"><?php _e('No categories found. Add categories first from Category List.', 'dhr-hotel-management'); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="is_active"><?php _e('Status', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <label><input type="checkbox" id="is_active" name="is_active" value="1" <?php checked($is_active, 1); ?>>
                        <?php _e('Active (only active packages within the valid period are considered available)', 'dhr-hotel-management'); ?></label>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php echo $is_edit ? __('Update Package', 'dhr-hotel-management') : __('Add Package', 'dhr-hotel-management'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-packages'); ?>" class="button"><?php _e('Cancel', 'dhr-hotel-management'); ?></a>
        </p>
    </form>
</div>
