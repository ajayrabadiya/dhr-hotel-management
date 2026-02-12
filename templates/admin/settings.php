<?php
/**
 * Settings template
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$api_key = get_option('dhr_hotel_google_maps_api_key', '');
$location_heading = get_option('dhr_hotel_location_heading', 'LOCATED IN THE WESTERN CAPE');
$main_heading = get_option('dhr_hotel_main_heading', 'Find Us');
$description_text = get_option('dhr_hotel_description_text', 'Discover our hotel locations across the Western Cape. Click on any marker to view hotel details and make a reservation.');
$reservation_label = get_option('dhr_hotel_reservation_label', 'RESERVATION BY PHONE');
$reservation_phone = get_option('dhr_hotel_reservation_phone', '+27 (0)21 876 8900');
$view_on_google_maps_link = get_option('dhr_hotel_view_on_google_maps_link', '');

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

            <tr>
                <th colspan="2"><h2><?php _e('Map Display Settings', 'dhr-hotel-management'); ?></h2></th>
            </tr>
            <tr>
                <th><label for="location_heading"><?php _e('Location Heading', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="location_heading" name="location_heading" 
                           class="regular-text" value="<?php echo esc_attr($location_heading); ?>" 
                           placeholder="LOCATED IN THE WESTERN CAPE">
                    <p class="description">
                        <?php _e('The small heading text displayed above the main heading (e.g., "LOCATED IN THE WESTERN CAPE").', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="main_heading"><?php _e('Main Heading', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="main_heading" name="main_heading" 
                           class="regular-text" value="<?php echo esc_attr($main_heading); ?>" 
                           placeholder="Find Us">
                    <p class="description">
                        <?php _e('The main heading text displayed on the map section (e.g., "Find Us").', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="description_text"><?php _e('Description Text', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <textarea id="description_text" name="description_text" 
                              class="large-text" rows="3" 
                              placeholder="Discover our hotel locations across the Western Cape. Click on any marker to view hotel details and make a reservation."><?php echo esc_textarea($description_text); ?></textarea>
                    <p class="description">
                        <?php _e('The descriptive text displayed below the main heading.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="reservation_label"><?php _e('Reservation Label', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="reservation_label" name="reservation_label" 
                           class="regular-text" value="<?php echo esc_attr($reservation_label); ?>" 
                           placeholder="RESERVATION BY PHONE">
                    <p class="description">
                        <?php _e('The label text displayed above the phone number (e.g., "RESERVATION BY PHONE").', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="reservation_phone"><?php _e('Reservation Phone Number', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="reservation_phone" name="reservation_phone" 
                           class="regular-text" value="<?php echo esc_attr($reservation_phone); ?>" 
                           placeholder="+27 (0)21 876 8900">
                    <p class="description">
                        <?php _e('The phone number displayed for reservations. If left empty, the first hotel\'s phone number will be used.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="view_on_google_maps_link"><?php _e('View On Google Maps Link', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url" id="view_on_google_maps_link" name="view_on_google_maps_link" 
                           class="regular-text" value="<?php echo esc_url($view_on_google_maps_link); ?>" 
                           placeholder="https://www.google.com/maps?q=Western+Cape">
                    <p class="description">
                        <?php _e('The URL for the "View On Google Maps" button. This can be a general location link or a specific Google Maps URL.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('All Map Shortcodes', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <?php 
                    $all_maps = DHR_Hotel_Database::get_all_map_configs();
                    if (!empty($all_maps)): 
                    ?>
                        <div class="dhr-all-shortcodes">
                            <?php foreach ($all_maps as $map): ?>
                                <div class="dhr-shortcode-item" style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                                        <?php echo esc_html($map->map_name); ?>:
                                    </label>
                                    <div class="dhr-shortcode-wrapper">
                                        <input type="text" 
                                               class="regular-text dhr-shortcode-input" 
                                               value="[<?php echo esc_attr($map->shortcode); ?>]" 
                                               readonly>
                                        <button type="button" 
                                                class="button dhr-copy-btn" 
                                                data-shortcode="[<?php echo esc_attr($map->shortcode); ?>]">
                                            <span class="dhr-copy-text"><?php _e('Copy', 'dhr-hotel-management'); ?></span>
                                            <span class="dhr-copied-text" style="display: none;"><?php _e('Copied!', 'dhr-hotel-management'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="description">
                            <?php _e('Use these shortcodes to display different map types on any page or post. Visit', 'dhr-hotel-management'); ?>
                            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-map-management'); ?>"><?php _e('Map Management', 'dhr-hotel-management'); ?></a>
                            <?php _e('to configure each map\'s settings.', 'dhr-hotel-management'); ?>
                        </p>
                    <?php else: ?>
                        <p><?php _e('No maps configured yet.', 'dhr-hotel-management'); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php _e('Save Settings', 'dhr-hotel-management'); ?>">
        </p>
    </form>
</div>


