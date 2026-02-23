<?php
/**
 * Frontend functionality for DHR Hotel Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class DHR_Hotel_Frontend {
    
    public function __construct() {
        // Register all map shortcodes
        add_shortcode('dhr_hotel_map', array($this, 'display_hotel_map'));
        add_shortcode('dhr_head_office_map', array($this, 'display_head_office_map'));
        add_shortcode('dhr_partner_portfolio_map', array($this, 'display_partner_portfolio_map'));
        add_shortcode('dhr_dining_venue_map', array($this, 'display_dining_venue_map'));
        add_shortcode('dhr_wedding_venue_map', array($this, 'display_wedding_venue_map'));
        add_shortcode('dhr_property_portfolio_map', array($this, 'display_property_portfolio_map'));
        add_shortcode('dhr_lodges_camps_map', array($this, 'display_lodges_camps_map'));
        
        // Register hotel rooms shortcodes: [hotel_rooms] = grid, [hotel_rooms_cards] = cards
        add_shortcode('hotel_rooms', array($this, 'display_hotel_rooms'));
        add_shortcode('hotel_rooms_cards', array($this, 'display_hotel_rooms_cards'));
        
        // Register package design shortcodes
        add_shortcode('dhr_package_first_design', array($this, 'display_package_first_design'));
        add_shortcode('dhr_package_second_design', array($this, 'display_package_second_design'));
        add_shortcode('dhr_package_kids_design', array($this, 'display_package_kids_design'));
        add_shortcode('dhr_package_early_bird_design', array($this, 'display_package_early_bird_design'));
add_shortcode('dhr_package_experiences_design', array($this, 'display_package_experiences_design'));
        add_shortcode('dhr_category_list', array($this, 'display_package_experiences_design'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        // Always enqueue styles (lightweight)
        wp_enqueue_style(
            'dhr-hotel-frontend-style',
            DHR_HOTEL_PLUGIN_URL . 'assets/css/frontend-style.css',
            array(),
            DHR_HOTEL_PLUGIN_VERSION
        );
        
        // Always enqueue scripts - they will only initialize if map elements exist
        // This ensures maps work even when shortcodes are used in templates
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
                    echo '<div class="notice notice-error" style="position: fixed; top: 32px; left: 160px; right: 20px; z-index: 9999; padding: 10px;"><p><strong>DHR Hotel Management:</strong> Google Maps API key is not configured. Please set it in <a href="' . admin_url('admin.php?page=dhr-hotel-settings') . '">Settings</a>.</p></div>';
                });
            }
        }
        
        wp_enqueue_script(
            'dhr-hotel-frontend-script',
            DHR_HOTEL_PLUGIN_URL . 'assets/js/frontend-script.js',
            array('jquery'),
            DHR_HOTEL_PLUGIN_VERSION,
            true
        );
        
        // Localize script with hotels data
        // CRITICAL: Convert database objects to arrays for JSON encoding
        // WordPress cannot JSON encode database objects directly, which causes infinite loading
        $hotels_array = array();
        
        try {
            $hotels = DHR_Hotel_Database::get_all_hotels('active');

            // echo "<pre>";
            // print_r($hotels);
            // echo "</pre>";
            // die();
            
            if (!empty($hotels) && is_array($hotels)) {
                foreach ($hotels as $hotel) {
                    // Convert object to array for JSON encoding
                    if (is_object($hotel)) {
                        $hotels_array[] = array(
                            'id' => isset($hotel->id) ? intval($hotel->id) : 0,
                            'name' => isset($hotel->name) ? sanitize_text_field($hotel->name) : '',
                            'description' => isset($hotel->description) ? sanitize_text_field($hotel->description) : '',
                            'address' => isset($hotel->address) ? sanitize_text_field($hotel->address) : '',
                            'city' => isset($hotel->city) ? sanitize_text_field($hotel->city) : '',
                            'province' => isset($hotel->province) ? sanitize_text_field($hotel->province) : '',
                            'country' => isset($hotel->country) ? sanitize_text_field($hotel->country) : '',
                            'latitude' => isset($hotel->latitude) ? floatval($hotel->latitude) : 0,
                            'longitude' => isset($hotel->longitude) ? floatval($hotel->longitude) : 0,
                            'phone' => isset($hotel->phone) ? sanitize_text_field($hotel->phone) : '',
                            'email' => isset($hotel->email) ? sanitize_email($hotel->email) : '',
                            'website' => isset($hotel->website) ? esc_url_raw($hotel->website) : '',
                            'image_url' => isset($hotel->image_url) ? esc_url_raw($hotel->image_url) : '',
                            'google_maps_url' => isset($hotel->google_maps_url) ? esc_url_raw($hotel->google_maps_url) : '',
                            'status' => isset($hotel->status) ? sanitize_text_field($hotel->status) : 'active'
                        );
                    } elseif (is_array($hotel)) {
                        // Already an array, just sanitize
                        $hotels_array[] = array(
                            'id' => isset($hotel['id']) ? intval($hotel['id']) : 0,
                            'name' => isset($hotel['name']) ? sanitize_text_field($hotel['name']) : '',
                            'description' => isset($hotel['description']) ? sanitize_text_field($hotel['description']) : '',
                            'address' => isset($hotel['address']) ? sanitize_text_field($hotel['address']) : '',
                            'city' => isset($hotel['city']) ? sanitize_text_field($hotel['city']) : '',
                            'province' => isset($hotel['province']) ? sanitize_text_field($hotel['province']) : '',
                            'country' => isset($hotel['country']) ? sanitize_text_field($hotel['country']) : '',
                            'latitude' => isset($hotel['latitude']) ? floatval($hotel['latitude']) : 0,
                            'longitude' => isset($hotel['longitude']) ? floatval($hotel['longitude']) : 0,
                            'phone' => isset($hotel['phone']) ? sanitize_text_field($hotel['phone']) : '',
                            'email' => isset($hotel['email']) ? sanitize_email($hotel['email']) : '',
                            'website' => isset($hotel['website']) ? esc_url_raw($hotel['website']) : '',
                            'image_url' => isset($hotel['image_url']) ? esc_url_raw($hotel['image_url']) : '',
                            'google_maps_url' => isset($hotel['google_maps_url']) ? esc_url_raw($hotel['google_maps_url']) : '',
                            'status' => isset($hotel['status']) ? sanitize_text_field($hotel['status']) : 'active'
                        );
                    }
                }
            }
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('DHR Hotel Management Error: ' . $e->getMessage());
            }
            $hotels_array = array();
        }
        
        // Only localize if script is registered
        if (wp_script_is('dhr-hotel-frontend-script', 'registered')) {
            wp_localize_script('dhr-hotel-frontend-script', 'dhrHotelsData', array(
                'hotels' => $hotels_array,
                'pluginUrl' => DHR_HOTEL_PLUGIN_URL
            ));
        }
    }
    
    /**
     * Decode map config settings safely (string or array, always return array).
     *
     * @param object|null $map_config Map config row from database.
     * @return array
     */
    public static function get_map_settings($map_config) {
        $settings = array();
        if ($map_config && !empty($map_config->settings)) {
            $settings = is_string($map_config->settings) ? json_decode($map_config->settings, true) : (array) $map_config->settings;
            $settings = is_array($settings) ? $settings : array();
        }
        return $settings;
    }

    /**
     * Filter hotels to only those selected for this map.
     * When selected_hotel_ids is empty (not set, null, '', or []) = show ALL active hotels on the map.
     * When selected_hotel_ids has IDs = show only those hotels.
     * Normalizes selected_hotel_ids from array, object, comma-separated string, or single value.
     */
    public static function filter_hotels_by_map_selection($hotels, $settings) {
        if (!isset($settings['selected_hotel_ids']) || $settings['selected_hotel_ids'] === null || empty($settings['selected_hotel_ids'])) {
            return [];
        }
        $raw = $settings['selected_hotel_ids'];

        $ids = array();
        if (is_array($raw)) {
            $ids = array_values(array_map('intval', $raw));
        } elseif (is_object($raw)) {
            $ids = array_values(array_map('intval', (array) $raw));
        } elseif (is_string($raw)) {
            $trimmed = trim($raw);
            if ($trimmed === '' || $trimmed === '[]' || $trimmed === '{}') {
                return $hotels;
            }
            if (strpos($raw, ',') !== false) {
                $ids = array_values(array_map('intval', array_map('trim', explode(',', $raw))));
            } else {
                $ids = array(intval($raw));
            }
        } else {
            $ids = array(intval($raw));
        }
        $ids = array_values(array_filter($ids));

        // No IDs or all IDs filtered out = show all hotels (same as "no selection")
        if (empty($ids)) {
            return $hotels;
        }
        // Normalize to integers for reliable comparison (DB may return string ids)
        $ids = array_map('intval', $ids);
        $by_id = array();
        foreach ($hotels as $h) {
            $id = (int) $h->id;
            if (in_array($id, $ids, true)) {
                $by_id[$id] = $h;
            }
        }
        // Preserve order of selection (checkbox order from admin)
        $ordered = array();
        foreach ($ids as $id) {
            if (isset($by_id[$id])) {
                $ordered[] = $by_id[$id];
            }
        }
        return $ordered;
    }

    /**
     * Display hotel map shortcode (Map 1 - Standard)
     */
    public function display_hotel_map($atts) {
        $atts = shortcode_atts(array(
            'province' => '',
            'city' => '',
            'height' => '531px'
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
        
        // Get map config and decode settings
        $map_config = DHR_Hotel_Database::get_map_config('dhr_hotel_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/hotel-map.php';
        return ob_get_clean();
    }
    
    /**
     * Display head office map shortcode (Map 2)
     */
    public function display_head_office_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '596px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        $map_config = DHR_Hotel_Database::get_map_config('dhr_head_office_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/head-office-map.php';
        return ob_get_clean();
    }
    
    /**
     * Display partner portfolio map shortcode (Map 3)
     */
    public function display_partner_portfolio_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '1002px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        $map_config = DHR_Hotel_Database::get_map_config('dhr_partner_portfolio_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/partner-portfolio-map.php';
        return ob_get_clean();
    }
    
    /**
     * Display dining venue map shortcode (Map 4)
     */
    public function display_dining_venue_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '620px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        $map_config = DHR_Hotel_Database::get_map_config('dhr_dining_venue_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/dining-venue-map.php';
        return ob_get_clean();
    }
    
    /**
     * Display wedding venue map shortcode (Map 5)
     */
    public function display_wedding_venue_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '600px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        $map_config = DHR_Hotel_Database::get_map_config('dhr_wedding_venue_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/wedding-venue-map.php';
        return ob_get_clean();
    }
    
    /**
     * Display property portfolio map shortcode (Map 6)
     */
    public function display_property_portfolio_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '600px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        $map_config = DHR_Hotel_Database::get_map_config('dhr_property_portfolio_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/property-portfolio-map.php';
        return ob_get_clean();
    }
    
    /**
     * Display lodges & camps map shortcode (Map 7)
     */
    public function display_lodges_camps_map($atts) {
        $atts = shortcode_atts(array(
            'height' => '600px'
        ), $atts);
        
        $hotels = DHR_Hotel_Database::get_all_hotels('active');
        $map_config = DHR_Hotel_Database::get_map_config('dhr_lodges_camps_map');
        $settings = self::get_map_settings($map_config);
        $hotels = self::filter_hotels_by_map_selection($hotels, $settings);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/lodges-camps-map.php';
        return ob_get_clean();
    }
    
    /**
     * [hotel_rooms] – grid layout (specs, amenities, description). No code change, only shortcode.
     */
    public function display_hotel_rooms($atts) {
        return $this->render_hotel_rooms($atts, 'grid');
    }

    /**
     * [hotel_rooms_cards] – card overlay layout. No code change, only shortcode.
     */
    public function display_hotel_rooms_cards($atts) {
        return $this->render_hotel_rooms($atts, 'cards');
    }

    /**
     * Shared renderer: fetches rooms and passes layout so template shows one design only.
     */
    private function render_hotel_rooms($atts, $layout) {
        $atts = shortcode_atts(array(
            'columns' => '2',
            'show_images' => 'true',
            'show_amenities' => 'true',
            'show_description' => 'true'
        ), $atts);

        $hotel_code = get_option('bys_hotel_code', '');
        $hotel_code = is_string($hotel_code) ? trim($hotel_code) : '';

        if (empty($hotel_code)) {
            $settings_url = admin_url('admin.php?page=book-your-stay');
            $sc = $layout === 'cards' ? '[hotel_rooms_cards]' : '[hotel_rooms]';
            $message = sprintf(
                __('Hotel code is required. Set it in %s. Use shortcode: %s', 'dhr-hotel-management'),
                '<a href="' . esc_url($settings_url) . '">' . __('Book Your Stay Settings', 'dhr-hotel-management') . '</a>',
                $sc
            );
            return '<p class="dhr-hotel-rooms-error">' . $message . '</p>';
        }

        $api = new DHR_Hotel_API();
        $result = $api->get_shr_hotel_rooms($hotel_code);

        if (!$result['success']) {
            return '<p class="dhr-hotel-rooms-error">' . esc_html($result['error']) . '</p>';
        }

        $rooms = isset($result['rooms']) ? $result['rooms'] : array();
        $hotel_name = isset($result['hotel_name']) ? $result['hotel_name'] : $hotel_code;

        if (empty($rooms)) {
            return '<p class="dhr-hotel-rooms-error">' . sprintf(__('No rooms found for hotel %s.', 'dhr-hotel-management'), esc_html($hotel_name)) . '</p>';
        }

        $hotel_data = array(
            'layout' => $layout,
            'hotel_code' => $hotel_code,
            'hotel_name' => $hotel_name,
            'rooms' => $rooms,
            'columns' => intval($atts['columns']),
            'show_images' => filter_var($atts['show_images'], FILTER_VALIDATE_BOOLEAN),
            'show_amenities' => filter_var($atts['show_amenities'], FILTER_VALIDATE_BOOLEAN),
            'show_description' => filter_var($atts['show_description'], FILTER_VALIDATE_BOOLEAN)
        );

        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/hotel-rooms.php';
        return ob_get_clean();
    }
    
    /**
     * Display first package design shortcode
     */
    public function display_package_first_design($atts) {
        $atts = shortcode_atts(array(), $atts);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/package-first-design.php';
        return ob_get_clean();
    }
    
    /**
     * Display second package design shortcode
     */
    public function display_package_second_design($atts) {
        $atts = shortcode_atts(array(), $atts);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/package-second-design.php';
        return ob_get_clean();
    }
    
    /**
     * Display kids package design shortcode
     */
    public function display_package_kids_design($atts) {
        $atts = shortcode_atts(array(), $atts);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/package-kids-design.php';
        return ob_get_clean();
    }
    
    /**
     * Display early bird package design shortcode
     */
    public function display_package_early_bird_design($atts) {
        $atts = shortcode_atts(array(), $atts);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/package-early-bird-design.php';
        return ob_get_clean();
    }
    
    /**
     * Display experiences package design shortcode
     */
    public function display_package_experiences_design($atts) {
        $atts = shortcode_atts(array(), $atts);
        
        ob_start();
        include DHR_HOTEL_PLUGIN_PATH . 'templates/frontend/package-experiences-design.php';
        return ob_get_clean();
    }
}

