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

// API Settings
$api_url = get_option('dhr_hotel_api_url', 'https://ota.windsurfercrs.com/HotelDescriptiveInfo');
$api_username = get_option('dhr_hotel_api_username', '4SHAWDREAM1225');
$api_password_encrypted = get_option('dhr_hotel_api_password', '');
$api_password = !empty($api_password_encrypted) ? base64_decode($api_password_encrypted) : '';
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php _e('DHR Hotel Management Settings', 'dhr-hotel-management'); ?></h1>
    
    <?php if ($message === 'saved'): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully!', 'dhr-hotel-management'); ?></p>
        </div>
    <?php elseif ($message === 'sync_success'): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php 
                $hotel_code = isset($_GET['hotel_code']) ? sanitize_text_field($_GET['hotel_code']) : '';
                printf(__('Hotel data synced successfully for hotel code: %s', 'dhr-hotel-management'), esc_html($hotel_code)); 
                ?>
            </p>
        </div>
    <?php elseif ($message === 'sync_error'): ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php 
                $error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : 'Unknown error';
                printf(__('Sync failed: %s', 'dhr-hotel-management'), esc_html($error)); 
                ?>
            </p>
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
                <th colspan="2"><h2><?php _e('Hotel Details API Settings', 'dhr-hotel-management'); ?></h2></th>
            </tr>
            <tr>
                <th><label for="api_url"><?php _e('API URL', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url" id="api_url" name="api_url" 
                           class="regular-text" value="<?php echo esc_attr($api_url); ?>" 
                           placeholder="https://ota.windsurfercrs.com/HotelDescriptiveInfo">
                    <p class="description">
                        <?php _e('The base URL for the SOAP API endpoint (without trailing slash).', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="api_username"><?php _e('API Username', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="api_username" name="api_username" 
                           class="regular-text" value="<?php echo esc_attr($api_username); ?>" 
                           placeholder="4SHAWDREAM1225">
                    <p class="description">
                        <?php _e('Username for authenticating with the SOAP API.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="api_password"><?php _e('API Password', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="password" id="api_password" name="api_password" 
                               class="regular-text" value="" 
                               placeholder="<?php echo !empty($api_password) ? __('Enter new password to update', 'dhr-hotel-management') : __('Enter API password', 'dhr-hotel-management'); ?>"
                               style="flex: 1;">
                        <button type="button" class="button" id="toggle_api_password" style="white-space: nowrap;">
                            <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span>
                            <?php _e('Show', 'dhr-hotel-management'); ?>
                        </button>
                    </div>
                    <?php if (!empty($api_password)): ?>
                        <p class="description" style="color: #46b450; margin-top: 5px;">
                            <span class="dashicons dashicons-yes-alt" style="vertical-align: middle; font-size: 16px;"></span>
                            <?php _e('Password is currently set. Enter a new password to update it.', 'dhr-hotel-management'); ?>
                        </p>
                    <?php else: ?>
                        <p class="description">
                            <?php _e('Password for authenticating with the SOAP API. Leave blank to keep current password.', 'dhr-hotel-management'); ?>
                        </p>
                    <?php endif; ?>
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
    
    <!-- Sync Hotel Data Section -->
    <div class="dhr-sync-section" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
        <h2><?php _e('Sync Hotel Data from API', 'dhr-hotel-management'); ?></h2>
        <p class="description">
            <?php _e('Fetch and sync hotel details, rooms, and services from the SOAP API. Enter a hotel code (e.g., DRE013) to sync data.', 'dhr-hotel-management'); ?>
        </p>
        
        <form id="dhr-sync-hotel-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin: 20px 0;">
            <?php wp_nonce_field('dhr_sync_hotel_nonce'); ?>
            <input type="hidden" name="action" value="dhr_sync_hotel_data">
            
            <table class="form-table">
                <tr>
                    <th><label for="sync_hotel_code"><?php _e('Hotel Code', 'dhr-hotel-management'); ?></label></th>
                    <td>
                        <input type="text" 
                               id="sync_hotel_code" 
                               name="hotel_code" 
                               class="regular-text" 
                               placeholder="DRE013" 
                               required>
                        <button type="submit" 
                                class="button button-secondary" 
                                id="dhr-sync-btn"
                                style="margin-left: 10px;">
                            <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                            <?php _e('Sync Now', 'dhr-hotel-management'); ?>
                        </button>
                        <span id="dhr-sync-spinner" class="spinner" style="float: none; margin-left: 10px; display: none;"></span>
                        <p class="description">
                            <?php _e('Enter the hotel code from the SOAP API (e.g., DRE013 for 10 2nd Avenue Boutique Hotel).', 'dhr-hotel-management'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </form>
        
        <!-- Synced Hotels List -->
        <?php 
        $synced_hotels = DHR_Hotel_Admin::get_synced_hotels();
        if (!empty($synced_hotels)): 
        ?>
            <h3><?php _e('Synced Hotels', 'dhr-hotel-management'); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Hotel Code', 'dhr-hotel-management'); ?></th>
                        <th><?php _e('Hotel Name', 'dhr-hotel-management'); ?></th>
                        <th><?php _e('Last Synced', 'dhr-hotel-management'); ?></th>
                        <th><?php _e('Actions', 'dhr-hotel-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($synced_hotels as $hotel): ?>
                        <tr>
                            <td><strong><?php echo esc_html($hotel->hotel_code); ?></strong></td>
                            <td><?php echo esc_html($hotel->hotel_name); ?></td>
                            <td>
                                <?php 
                                if ($hotel->last_synced_at) {
                                    $date = new DateTime($hotel->last_synced_at);
                                    echo esc_html($date->format('Y-m-d H:i:s'));
                                } else {
                                    _e('Never', 'dhr-hotel-management');
                                }
                                ?>
                            </td>
                            <td>
                                <button type="button" 
                                        class="button button-small dhr-resync-btn" 
                                        data-hotel-code="<?php echo esc_attr($hotel->hotel_code); ?>"
                                        data-hotel-name="<?php echo esc_attr($hotel->hotel_name); ?>">
                                    <span class="dashicons dashicons-update" style="vertical-align: middle; font-size: 16px;"></span>
                                    <?php _e('Re-sync', 'dhr-hotel-management'); ?>
                                </button>
                                <a href="<?php echo esc_url(rest_url('dhr-hotel/v1/hotel-details/' . $hotel->hotel_code)); ?>" 
                                   target="_blank" 
                                   class="button button-small">
                                    <span class="dashicons dashicons-external" style="vertical-align: middle; font-size: 16px;"></span>
                                    <?php _e('View API', 'dhr-hotel-management'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('No hotels have been synced yet. Use the form above to sync hotel data.', 'dhr-hotel-management'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Toggle API password visibility
    $('#toggle_api_password').on('click', function() {
        var $input = $('#api_password');
        var $button = $(this);
        var $icon = $button.find('.dashicons');
        
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
            $button.html('<span class="dashicons dashicons-hidden" style="vertical-align: middle;"></span> <?php _e('Hide', 'dhr-hotel-management'); ?>');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
            $button.html('<span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span> <?php _e('Show', 'dhr-hotel-management'); ?>');
        }
    });
    
    // AJAX sync functionality
    var syncInProgress = false;
    
    // Handle form submission with AJAX
    $('#dhr-sync-hotel-form').on('submit', function(e) {
        if (syncInProgress) {
            e.preventDefault();
            return false;
        }
        
        var hotelCode = $('#sync_hotel_code').val().trim();
        if (!hotelCode) {
            alert('<?php _e('Please enter a hotel code', 'dhr-hotel-management'); ?>');
            e.preventDefault();
            return false;
        }
        
        // Show spinner
        $('#dhr-sync-spinner').show();
        $('#dhr-sync-btn').prop('disabled', true);
        syncInProgress = true;
        
        // AJAX request
        $.ajax({
            url: (typeof dhrHotelAdmin !== 'undefined' ? dhrHotelAdmin.ajaxurl : ajaxurl),
            type: 'POST',
            data: {
                action: 'dhr_sync_hotel_ajax',
                nonce: (typeof dhrHotelAdmin !== 'undefined' ? dhrHotelAdmin.nonce : '<?php echo wp_create_nonce('dhr_sync_hotel_ajax_nonce'); ?>'),
                hotel_code: hotelCode
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('Hotel data synced successfully!', 'dhr-hotel-management'); ?>\n\n' + 
                          'Hotel: ' + response.data.hotel_name + '\n' +
                          'Rooms: ' + response.data.rooms_count + '\n' +
                          'Last Synced: ' + response.data.last_synced);
                    location.reload();
                } else {
                    alert('<?php _e('Sync failed:', 'dhr-hotel-management'); ?> ' + response.data.message);
                }
            },
            error: function() {
                alert('<?php _e('An error occurred during sync. Please try again.', 'dhr-hotel-management'); ?>');
            },
            complete: function() {
                $('#dhr-sync-spinner').hide();
                $('#dhr-sync-btn').prop('disabled', false);
                syncInProgress = false;
            }
        });
        
        e.preventDefault();
        return false;
    });
    
    // Handle re-sync buttons
    $('.dhr-resync-btn').on('click', function() {
        if (syncInProgress) {
            return false;
        }
        
        var $btn = $(this);
        var hotelCode = $btn.data('hotel-code');
        var hotelName = $btn.data('hotel-name');
        
        if (!confirm('<?php _e('Are you sure you want to re-sync data for', 'dhr-hotel-management'); ?> ' + hotelName + '?')) {
            return false;
        }
        
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner is-active" style="float: none; margin: 0;"></span> <?php _e('Syncing...', 'dhr-hotel-management'); ?>');
        syncInProgress = true;
        
        $.ajax({
            url: (typeof dhrHotelAdmin !== 'undefined' ? dhrHotelAdmin.ajaxurl : ajaxurl),
            type: 'POST',
            data: {
                action: 'dhr_sync_hotel_ajax',
                nonce: (typeof dhrHotelAdmin !== 'undefined' ? dhrHotelAdmin.nonce : '<?php echo wp_create_nonce('dhr_sync_hotel_ajax_nonce'); ?>'),
                hotel_code: hotelCode
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('Hotel data re-synced successfully!', 'dhr-hotel-management'); ?>');
                    location.reload();
                } else {
                    alert('<?php _e('Re-sync failed:', 'dhr-hotel-management'); ?> ' + response.data.message);
                    $btn.html('<span class="dashicons dashicons-update" style="vertical-align: middle; font-size: 16px;"></span> <?php _e('Re-sync', 'dhr-hotel-management'); ?>');
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('<?php _e('An error occurred during re-sync. Please try again.', 'dhr-hotel-management'); ?>');
                $btn.html('<span class="dashicons dashicons-update" style="vertical-align: middle; font-size: 16px;"></span> <?php _e('Re-sync', 'dhr-hotel-management'); ?>');
                $btn.prop('disabled', false);
            },
            complete: function() {
                syncInProgress = false;
            }
        });
    });
});
</script>


