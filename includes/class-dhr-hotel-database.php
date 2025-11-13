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

