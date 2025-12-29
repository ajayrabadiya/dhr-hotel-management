<?php
/**
 * Database handler for DHR Hotel Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class DHR_Hotel_Database {
    
    /**
     * Create database table on plugin activation
     */
    public static function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'dhr_hotels';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            address varchar(500) NOT NULL,
            city varchar(100) NOT NULL,
            province varchar(100) NOT NULL,
            country varchar(100) DEFAULT 'South Africa',
            latitude decimal(10, 8) NOT NULL,
            longitude decimal(11, 8) NOT NULL,
            phone varchar(50),
            email varchar(255),
            website varchar(255),
            image_url varchar(500),
            google_maps_url varchar(500),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Create map configurations table
        $map_config_table = $wpdb->prefix . 'dhr_map_configs';
        $sql_map = "CREATE TABLE IF NOT EXISTS $map_config_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            map_type varchar(50) NOT NULL,
            map_name varchar(255) NOT NULL,
            shortcode varchar(100) NOT NULL,
            settings longtext,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY shortcode (shortcode)
        ) $charset_collate;";
        
        dbDelta($sql_map);
        
        // Insert default map configurations if they don't exist
        self::create_default_map_configs();
    }
    
    /**
     * Create default map configurations
     */
    public static function create_default_map_configs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_map_configs';
        
        $default_maps = array(
            array(
                'map_type' => 'standard',
                'map_name' => 'Standard Map',
                'shortcode' => 'dhr_hotel_map',
                'settings' => json_encode(array(
                    'location_heading' => 'LOCATED IN THE WESTERN CAPE',
                    'main_heading' => 'Find Us',
                    'description_text' => 'Discover our hotel locations across the Western Cape. Click on any marker to view hotel details and make a reservation.',
                    'reservation_label' => 'RESERVATION BY PHONE',
                    'reservation_phone' => '+27 (0)13 243 9401/2',
                    'view_on_google_maps_link' => '',
                    'view_on_google_maps_text' => 'View On Google Maps',
                    'book_now_text' => 'Book Now'
                ))
            ),
            array(
                'map_type' => 'head_office',
                'map_name' => 'Head Office Map',
                'shortcode' => 'dhr_head_office_map',
                'settings' => json_encode(array(
                    'title' => 'Head Office',
                    'address' => '330 Main Road, Bryanston 2021, Gauteng, South Africa',
                    'latitude' => '-26.0519',
                    'longitude' => '28.0231',
                    'google_maps_url' => ''
                ))
            ),
            array(
                'map_type' => 'partner_portfolio',
                'map_name' => 'Partner Portfolio Map',
                'shortcode' => 'dhr_partner_portfolio_map',
                'settings' => json_encode(array(
                    'overview_label' => 'DISCOVER AFRICA',
                    'main_heading' => 'Our Partner Portfolio',
                    'description' => 'Together with CityBlue Hotels, we\'re crafting a unified hospitality experience that celebrates the rich cultures, stunning landscapes, and warm hospitality that Africa is known for. Whether you\'re seeking adventure, relaxation, or a blend of both, our properties are designed to connect you to the heart of Africa.',
                    'legend_cityblue' => 'CityBlue Hotels',
                    'legend_dream' => 'Dream Hotels & Resorts'
                ))
            ),
            array(
                'map_type' => 'dining_venue',
                'map_name' => 'Dining Venue Map',
                'shortcode' => 'dhr_dining_venue_map',
                'settings' => json_encode(array(
                    'overview_label' => 'OVERVIEW',
                    'main_heading' => 'Find A Dining Venue',
                    'description' => 'Whether you\'re savoring fresh seafood with a view of Table Mountain or indulging in gourmet delights by the Indian Ocean, our dining experiences promise to delight every palate. Join us for an unforgettable gastronomic adventure across our exquisite destinations.',
                    'reservation_label' => 'RESERVATION BY PHONE',
                    'reservation_phone' => '+27 (0)13 243 9401/2',
                    'dropdown_placeholder' => 'Select a Hotel'
                ))
            ),
            array(
                'map_type' => 'wedding_venue',
                'map_name' => 'Wedding Venue Map',
                'shortcode' => 'dhr_wedding_venue_map',
                'settings' => json_encode(array(
                    'header_label' => 'WEDDINGS',
                    'main_heading' => 'Find A Wedding Venue For Your Dream Celebration',
                    'description' => 'Embraced by the tranquil beauty of lakes, sunlit beaches, wild African bushveld, and majestic mountain views, our venues offer stunning settings that will transform your special moments into unforgettable memories.',
                    'reservation_label' => 'RESERVATION BY PHONE',
                    'reservation_phone' => '+27 (0)13 243 9401/2',
                    'dropdown_placeholder' => 'Select a Hotel'
                ))
            ),
            array(
                'map_type' => 'property_portfolio',
                'map_name' => 'Ownership Property Portfolio Map',
                'shortcode' => 'dhr_property_portfolio_map',
                'settings' => json_encode(array(
                    'panel_title' => 'Ownership Property Portfolio',
                    'show_numbers' => true
                ))
            ),
            array(
                'map_type' => 'lodges_camps',
                'map_name' => 'Lodges & Camps Map',
                'shortcode' => 'dhr_lodges_camps_map',
                'settings' => json_encode(array(
                    'panel_title' => 'Lodges & Camps',
                    'legend_lodges' => 'Lodges & Camps',
                    'legend_weddings' => 'Weddings & Conferences',
                    'show_list' => true
                ))
            )
        );
        
        foreach ($default_maps as $map) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE shortcode = %s",
                $map['shortcode']
            ));
            
            if ($exists == 0) {
                $wpdb->insert($table_name, $map, array('%s', '%s', '%s', '%s', '%s'));
            }
        }
    }
    
    /**
     * Get all map configurations
     */
    public static function get_all_map_configs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_map_configs';
        
        // Check if table exists
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ));
        
        if (!$table_exists) {
            // Table doesn't exist, create it
            self::create_tables();
        }
        
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");
    }
    
    /**
     * Get map configuration by shortcode
     */
    public static function get_map_config($shortcode) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_map_configs';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE shortcode = %s",
            $shortcode
        ));
    }
    
    /**
     * Update map configuration
     */
    public static function update_map_config($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_map_configs';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'map_name' => sanitize_text_field($data['map_name']),
                'settings' => json_encode($data['settings']),
                'status' => sanitize_text_field($data['status'])
            ),
            array('id' => intval($id)),
            array('%s', '%s', '%s'),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Get all hotels
     */
    public static function get_all_hotels($status = 'all') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';
        
        if ($status === 'all') {
            return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        } else {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC",
                $status
            ));
        }
    }
    
    /**
     * Get hotel by ID
     */
    public static function get_hotel($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Insert new hotel
     */
    public static function insert_hotel($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => sanitize_text_field($data['name']),
                'description' => sanitize_textarea_field($data['description']),
                'address' => sanitize_text_field($data['address']),
                'city' => sanitize_text_field($data['city']),
                'province' => sanitize_text_field($data['province']),
                'country' => sanitize_text_field($data['country']),
                'latitude' => floatval($data['latitude']),
                'longitude' => floatval($data['longitude']),
                'phone' => sanitize_text_field($data['phone']),
                'email' => sanitize_email($data['email']),
                'website' => esc_url_raw($data['website']),
                'image_url' => esc_url_raw($data['image_url']),
                'google_maps_url' => esc_url_raw($data['google_maps_url']),
                'status' => sanitize_text_field($data['status'])
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        return $result !== false ? $wpdb->insert_id : false;
    }
    
    /**
     * Update hotel
     */
    public static function update_hotel($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => sanitize_text_field($data['name']),
                'description' => sanitize_textarea_field($data['description']),
                'address' => sanitize_text_field($data['address']),
                'city' => sanitize_text_field($data['city']),
                'province' => sanitize_text_field($data['province']),
                'country' => sanitize_text_field($data['country']),
                'latitude' => floatval($data['latitude']),
                'longitude' => floatval($data['longitude']),
                'phone' => sanitize_text_field($data['phone']),
                'email' => sanitize_email($data['email']),
                'website' => esc_url_raw($data['website']),
                'image_url' => esc_url_raw($data['image_url']),
                'google_maps_url' => esc_url_raw($data['google_maps_url']),
                'status' => sanitize_text_field($data['status'])
            ),
            array('id' => intval($id)),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Delete hotel
     */
    public static function delete_hotel($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';
        
        return $wpdb->delete(
            $table_name,
            array('id' => intval($id)),
            array('%d')
        );
    }
}

