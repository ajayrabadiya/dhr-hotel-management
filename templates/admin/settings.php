<?php
/**
 * Settings template
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$api_key = get_option('dhr_hotel_google_maps_api_key', '');

// SHR WS Shop API (REST) settings
$shr_access_token = get_option('dhr_shr_access_token', '');
$shr_client_id = get_option('dhr_shr_client_id', '');
$shr_client_secret_encrypted = get_option('dhr_shr_client_secret', '');
$shr_client_secret = !empty($shr_client_secret_encrypted) ? base64_decode($shr_client_secret_encrypted) : '';
$shr_scope = get_option('dhr_shr_scope', 'wsapi.hoteldetails.read');
$shr_token_url = get_option('dhr_shr_token_url', 'https://id.shrglobal.com/connect/token');
$shr_shop_base_url = get_option('dhr_shr_shop_base_url', 'https://api.shrglobal.com/shop');
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php _e('DHR Hotel Management Settings', 'dhr-hotel-management'); ?></h1>
    
    <?php if ($message === 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully!', 'dhr-hotel-management'); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="dhr-hotel-form">
        <?php wp_nonce_field('dhr_settings_nonce'); ?>
        <input type="hidden" name="action" value="dhr_save_settings">
        
        <table class="form-table">
            <tr>
                <th><label for="google_maps_api_key"><?php _e('Google Maps API Key', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="google_maps_api_key" name="google_maps_api_key" 
                           class="regular-text" value="<?php echo esc_attr($api_key); ?>" 
                           placeholder="Enter your Google Maps API Key">
                    <p class="description">
                        <?php _e('Get your API key from', 'dhr-hotel-management'); ?> 
                        <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>.
                        <?php _e('Enable "Maps JavaScript API" for your project.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th colspan="2"><h2><?php _e('SHR WS Shop API (REST) Settings', 'dhr-hotel-management'); ?></h2></th>
            </tr>
            <tr>
                <th><label for="shr_manual_access_token"><?php _e('Access Token', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text"
                           id="shr_manual_access_token"
                           name="shr_manual_access_token"
                           class="large-text"
                           value="<?php echo esc_attr($shr_access_token); ?>"
                           placeholder="<?php esc_attr_e('Enter your access token directly', 'dhr-hotel-management'); ?>">
                    <p class="description">
                        <?php _e('Enter your SHR access token directly. If provided, this will be used instead of generating a token from client credentials.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <h3 style="margin-top: 20px;"><?php _e('Optional: Client Credentials (for auto token generation)', 'dhr-hotel-management'); ?></h3>
                    <p class="description" style="font-weight: normal;">
                        <?php _e('Only configure these if you want the plugin to automatically generate tokens. If you have a token, use the Access Token field above instead.', 'dhr-hotel-management'); ?>
                    </p>
                </th>
            </tr>
            <tr>
                <th><label for="shr_client_id"><?php _e('Client ID', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text"
                           id="shr_client_id"
                           name="shr_client_id"
                           class="regular-text"
                           value="<?php echo esc_attr($shr_client_id); ?>"
                           placeholder="WSAPI_...">
                    <p class="description">
                        <?php _e('Client ID for automatic token generation (optional).', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="shr_client_secret"><?php _e('Client Secret', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="password"
                           id="shr_client_secret"
                           name="shr_client_secret"
                           class="regular-text"
                           value=""
                           placeholder="<?php echo !empty($shr_client_secret) ? esc_attr__('Enter new secret to update', 'dhr-hotel-management') : esc_attr__('Enter client secret', 'dhr-hotel-management'); ?>">
                    <?php if (!empty($shr_client_secret)) : ?>
                        <p class="description" style="color:#46b450;margin-top:5px;">
                            <span class="dashicons dashicons-yes-alt" style="vertical-align:middle;font-size:16px;"></span>
                            <?php _e('Secret is currently stored. Enter a new value to replace it.', 'dhr-hotel-management'); ?>
                        </p>
                    <?php else : ?>
                        <p class="description">
                            <?php _e('Client secret for automatic token generation (optional).', 'dhr-hotel-management'); ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="shr_scope"><?php _e('Scope', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text"
                           id="shr_scope"
                           name="shr_scope"
                           class="regular-text"
                           value="<?php echo esc_attr($shr_scope); ?>"
                           placeholder="wsapi.hoteldetails.read">
                    <p class="description">
                        <?php _e('OAuth2 scope used when requesting the access token.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="shr_token_url"><?php _e('Token URL', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url"
                           id="shr_token_url"
                           name="shr_token_url"
                           class="regular-text"
                           value="<?php echo esc_attr($shr_token_url); ?>"
                           placeholder="https://id.shrglobal.com/connect/token">
                    <p class="description">
                        <?php _e('Endpoint used to request OAuth2 access tokens.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="shr_shop_base_url"><?php _e('Shop API Base URL', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url"
                           id="shr_shop_base_url"
                           name="shr_shop_base_url"
                           class="regular-text"
                           value="<?php echo esc_attr($shr_shop_base_url); ?>"
                           placeholder="https://api.shrglobal.com/shop">
                    <p class="description">
                        <?php _e('Base URL for the SHR Shop API (used for /hotelDetails/ calls).', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php _e('Save Settings', 'dhr-hotel-management'); ?>">
        </p>
    </form>
</div>


