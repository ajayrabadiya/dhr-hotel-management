<?php
/**
 * Settings template
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$api_key = get_option('dhr_hotel_google_maps_api_key', '');
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
                <th><label><?php _e('Shortcode', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <div class="dhr-shortcode-wrapper">
                        <input type="text" id="dhr-shortcode-input" 
                               class="regular-text dhr-shortcode-input" 
                               value="[dhr_hotel_map]" 
                               readonly>
                        <button type="button" id="dhr-copy-shortcode-btn" 
                                class="button dhr-copy-btn" 
                                data-shortcode="[dhr_hotel_map]">
                            <span class="dhr-copy-text"><?php _e('Copy', 'dhr-hotel-management'); ?></span>
                            <span class="dhr-copied-text" style="display: none;"><?php _e('Copied!', 'dhr-hotel-management'); ?></span>
                        </button>
                    </div>
                    <p class="description">
                        <?php _e('Use this shortcode to display the hotel map on any page or post. You can also use attributes:', 'dhr-hotel-management'); ?>
                        <code>[dhr_hotel_map province="Western Cape"]</code>, 
                        <code>[dhr_hotel_map city="Cape Town"]</code>, 
                        <code>[dhr_hotel_map height="800px"]</code>
                    </p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php _e('Save Settings', 'dhr-hotel-management'); ?>">
        </p>
    </form>
</div>


