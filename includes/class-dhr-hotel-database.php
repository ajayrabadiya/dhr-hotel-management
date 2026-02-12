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
            hotel_code varchar(50) DEFAULT NULL,
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
            PRIMARY KEY (id),
            UNIQUE KEY hotel_code (hotel_code)
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
        
        // Create hotel details table
        $hotel_details_table = $wpdb->prefix . 'dhr_hotel_details';
        $sql_details = "CREATE TABLE IF NOT EXISTS $hotel_details_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            hotel_code varchar(50) NOT NULL,
            hotel_name varchar(255) NOT NULL,
            chain_code varchar(50),
            chain_name varchar(255),
            currency_code varchar(10),
            language_code varchar(10),
            time_zone varchar(50),
            when_built varchar(50),
            hotel_status varchar(50),
            hotel_status_code varchar(10),
            latitude decimal(10, 8),
            longitude decimal(11, 8),
            description text,
            renovation_text text,
            check_in_time varchar(20),
            check_out_time varchar(20),
            cancellation_policy text,
            guarantee_policy text,
            pets_allowed varchar(50),
            commission_percent decimal(5, 2),
            raw_xml_data longtext,
            last_synced_at datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY hotel_code (hotel_code)
        ) $charset_collate;";
        
        dbDelta($sql_details);
        
        // Create hotel rooms table
        $hotel_rooms_table = $wpdb->prefix . 'dhr_hotel_rooms';
        $sql_rooms = "CREATE TABLE IF NOT EXISTS $hotel_rooms_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            hotel_code varchar(50) NOT NULL,
            room_type_name varchar(255) NOT NULL,
            room_type_code varchar(50),
            max_occupancy int(11),
            max_adult_occupancy int(11),
            max_child_occupancy int(11),
            standard_num_beds int(11),
            standard_occupancy int(11),
            room_size decimal(10, 2),
            description text,
            amenities longtext,
            images longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY hotel_code (hotel_code)
        ) $charset_collate;";
        
        dbDelta($sql_rooms);
        
        // Create hotel services table
        $hotel_services_table = $wpdb->prefix . 'dhr_hotel_services';
        $sql_services = "CREATE TABLE IF NOT EXISTS $hotel_services_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            hotel_code varchar(50) NOT NULL,
            service_code varchar(50),
            service_name varchar(255),
            exists_code varchar(100),
            proximity_code varchar(10),
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY hotel_code (hotel_code)
        ) $charset_collate;";
        
        dbDelta($sql_services);
        
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
     * Get hotel by SHR/remote hotel code
     *
     * @param string $hotel_code
     * @return object|null
     */
    public static function get_hotel_by_code($hotel_code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE hotel_code = %s",
            $hotel_code
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
                'hotel_code' => isset($data['hotel_code']) ? sanitize_text_field($data['hotel_code']) : '',
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
            array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
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
                'hotel_code' => isset($data['hotel_code']) ? sanitize_text_field($data['hotel_code']) : '',
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
            array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
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
    
    /**
     * Save or update hotel details
     */
    public static function save_hotel_details($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_details';
        
        // Ensure table exists
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ));
        
        if (!$table_exists) {
            self::create_tables();
        }
        
        // Prepare data - convert empty strings to empty strings (not null) for text fields
        // WordPress wpdb handles null for numeric fields, but we'll use 0.0 for floats if null
        $prepared_data = array(
            'hotel_name' => !empty($data['hotel_name']) ? sanitize_text_field($data['hotel_name']) : '',
            'chain_code' => !empty($data['chain_code']) ? sanitize_text_field($data['chain_code']) : '',
            'chain_name' => !empty($data['chain_name']) ? sanitize_text_field($data['chain_name']) : '',
            'currency_code' => !empty($data['currency_code']) ? sanitize_text_field($data['currency_code']) : '',
            'language_code' => !empty($data['language_code']) ? sanitize_text_field($data['language_code']) : '',
            'time_zone' => !empty($data['time_zone']) ? sanitize_text_field($data['time_zone']) : '',
            'when_built' => !empty($data['when_built']) ? sanitize_text_field($data['when_built']) : '',
            'hotel_status' => !empty($data['hotel_status']) ? sanitize_text_field($data['hotel_status']) : '',
            'hotel_status_code' => !empty($data['hotel_status_code']) ? sanitize_text_field($data['hotel_status_code']) : '',
            'latitude' => (!empty($data['latitude']) && $data['latitude'] !== '' && $data['latitude'] !== '0') ? floatval($data['latitude']) : null,
            'longitude' => (!empty($data['longitude']) && $data['longitude'] !== '' && $data['longitude'] !== '0') ? floatval($data['longitude']) : null,
            'description' => !empty($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'renovation_text' => !empty($data['renovation_text']) ? sanitize_textarea_field($data['renovation_text']) : '',
            'check_in_time' => !empty($data['check_in_time']) ? sanitize_text_field($data['check_in_time']) : '',
            'check_out_time' => !empty($data['check_out_time']) ? sanitize_text_field($data['check_out_time']) : '',
            'cancellation_policy' => !empty($data['cancellation_policy']) ? sanitize_textarea_field($data['cancellation_policy']) : '',
            'guarantee_policy' => !empty($data['guarantee_policy']) ? sanitize_textarea_field($data['guarantee_policy']) : '',
            'pets_allowed' => !empty($data['pets_allowed']) ? sanitize_text_field($data['pets_allowed']) : '',
            'commission_percent' => (!empty($data['commission_percent']) && $data['commission_percent'] !== '') ? floatval($data['commission_percent']) : null,
            'raw_xml_data' => !empty($data['raw_xml_data']) ? $data['raw_xml_data'] : '',
            'last_synced_at' => current_time('mysql')
        );
        
        // Format specifiers array - must match the order of fields above
        $format_specifiers = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s');
        
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE hotel_code = %s",
            $data['hotel_code']
        ));
        
        if ($existing) {
            // Update existing record
            $result = $wpdb->update(
                $table_name,
                $prepared_data,
                array('hotel_code' => sanitize_text_field($data['hotel_code'])),
                $format_specifiers,
                array('%s')
            );
            
            if ($result === false) {
                // Log error for debugging
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('DHR Hotel: Update failed - ' . $wpdb->last_error);
                }
                return false;
            }
            
            return $existing->id;
        } else {
            // Insert new record
            $prepared_data['hotel_code'] = sanitize_text_field($data['hotel_code']);
            // Add format for hotel_code at the beginning
            $insert_format_specifiers = array_merge(array('%s'), $format_specifiers);
            
            $result = $wpdb->insert(
                $table_name,
                $prepared_data,
                $insert_format_specifiers
            );
            
            if ($result === false) {
                // Log error for debugging
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('DHR Hotel: Insert failed - ' . $wpdb->last_error);
                    error_log('DHR Hotel: Last query - ' . $wpdb->last_query);
                }
                return false;
            }
            
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Save hotel rooms
     */
    public static function save_hotel_rooms($hotel_code, $rooms) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_rooms';
        
        // Delete existing rooms for this hotel
        $wpdb->delete($table_name, array('hotel_code' => $hotel_code), array('%s'));
        
        // Insert new rooms
        foreach ($rooms as $room) {
            $wpdb->insert(
                $table_name,
                array(
                    'hotel_code' => sanitize_text_field($hotel_code),
                    'room_type_name' => sanitize_text_field($room['room_type_name']),
                    'room_type_code' => sanitize_text_field($room['room_type_code']),
                    'max_occupancy' => isset($room['max_occupancy']) ? intval($room['max_occupancy']) : null,
                    'max_adult_occupancy' => isset($room['max_adult_occupancy']) ? intval($room['max_adult_occupancy']) : null,
                    'max_child_occupancy' => isset($room['max_child_occupancy']) ? intval($room['max_child_occupancy']) : null,
                    'standard_num_beds' => isset($room['standard_num_beds']) ? intval($room['standard_num_beds']) : null,
                    'standard_occupancy' => isset($room['standard_occupancy']) ? intval($room['standard_occupancy']) : null,
                    'room_size' => isset($room['room_size']) ? floatval($room['room_size']) : null,
                    'description' => sanitize_textarea_field($room['description']),
                    'amenities' => json_encode($room['amenities']),
                    'images' => json_encode($room['images'])
                ),
                array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%f', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Save hotel services
     */
    public static function save_hotel_services($hotel_code, $services) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_services';
        
        // Delete existing services for this hotel
        $wpdb->delete($table_name, array('hotel_code' => $hotel_code), array('%s'));
        
        // Insert new services
        foreach ($services as $service) {
            $wpdb->insert(
                $table_name,
                array(
                    'hotel_code' => sanitize_text_field($hotel_code),
                    'service_code' => sanitize_text_field($service['service_code']),
                    'service_name' => sanitize_text_field($service['service_name']),
                    'exists_code' => sanitize_text_field($service['exists_code']),
                    'proximity_code' => sanitize_text_field($service['proximity_code']),
                    'description' => sanitize_textarea_field($service['description'])
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Get hotel details by hotel code
     */
    public static function get_hotel_details($hotel_code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_details';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE hotel_code = %s",
            $hotel_code
        ));
    }
    
    /**
     * Get hotel rooms by hotel code
     */
    public static function get_hotel_rooms($hotel_code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_rooms';
        
        $rooms = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE hotel_code = %s ORDER BY id ASC",
            $hotel_code
        ));
        
        // Decode JSON fields
        foreach ($rooms as $room) {
            $room->amenities = json_decode($room->amenities, true);
            $room->images = json_decode($room->images, true);
        }
        
        return $rooms;
    }
    
    /**
     * Get hotel services by hotel code
     */
    public static function get_hotel_services($hotel_code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_services';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE hotel_code = %s ORDER BY id ASC",
            $hotel_code
        ));
    }
}

