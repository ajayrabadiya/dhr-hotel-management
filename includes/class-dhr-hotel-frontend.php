<?php
/**
 * Frontend functionality for DHR Hotel Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class DHR_Hotel_Frontend {
    
    public function __construct() {
        add_shortcode('dhr_hotel_map', array($this, 'display_hotel_map'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'dhr-hotel-frontend-style',
            DHR_HOTEL_PLUGIN_URL . 'assets/css/frontend-style.css',
            array(),
            DHR_HOTEL_PLUGIN_VERSION
        );
        
        // Google Maps API - Get API key from settings
        $api_key = get_option('dhr_hotel_google_maps_api_key', '');
        if (!empty($api_key)) {
            wp_enqueue_script(
                'google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($api_key) . '&libraries=places',
                array(),
                null,
                true
            );
        } else {
            // Show admin notice if API key is not set
            if (current_user_can('manage_options')) {
                add_action('wp_footer', function() {
                    echo '<div class="notice notice-error"><p>Google Maps API key is not configured. Please set it in <a href="' . admin_url('admin.php?page=dhr-hotel-settings') . '">DHR Hotel Management Settings</a>.</p></div>';
                });
            }
        }
        
        wp_enqueue_script(
            'dhr-hotel-frontend-script',
            DHR_HOTEL_PLUGIN_URL . 'assets/js/frontend-script.js',
            array('jquery', 'google-maps-api'),
            DHR_HOTEL_PLUGIN_VERSION,
            true
        );
        
        // Localize script with hotels data
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        wp_localize_script('dhr-hotel-frontend-script', 'dhrHotelsData', array(
            'hotels' => $hotels,
            'pluginUrl' => DHR_HOTEL_PLUGIN_URL
        ));
    }
    
    /**
     * Display hotel map shortcode
     */
    public function display_hotel_map($atts) {
        $atts = shortcode_atts(array(
            'province' => '',
            'city' => '',
            'height' => '600px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        
        // Filter by province or city if specified
        if (!empty($atts['province'])) {
            $hotels = array_filter($hotels, function($hotel) use ($atts) {
                return strtolower($hotel->province) === strtolower($atts['province']);
            });
        }
        
        if (!empty($atts['city'])) {
            $hotels = array_filter($hotels, function($hotel) use ($atts) {
                return strtolower($hotel->city) === strtolower($atts['city']);
            });
        }
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/hotel-map.php';
        return ob_get_clean();
    }
}

