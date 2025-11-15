<?php
/**
 * Uninstall script for DHR Hotel Management
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Check if this is a multisite network uninstall
if (is_multisite()) {
    // Get all site IDs
    $site_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    
    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        
        // Drop the database table for this site
        $table_name = $wpdb->prefix . 'dhr_hotels';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        
        // Delete plugin options for this site
        delete_option('dhr_hotel_google_maps_api_key');
        delete_option('dhr_hotel_location_heading');
        delete_option('dhr_hotel_main_heading');
        delete_option('dhr_hotel_description_text');
        delete_option('dhr_hotel_reservation_label');
        delete_option('dhr_hotel_reservation_phone');
        delete_option('dhr_hotel_settings');
        
        restore_current_blog();
    }
} else {
    // Single site uninstall
    $table_name = $wpdb->prefix . 'dhr_hotels';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Delete plugin options
    delete_option('dhr_hotel_google_maps_api_key');
    delete_option('dhr_hotel_location_heading');
    delete_option('dhr_hotel_main_heading');
    delete_option('dhr_hotel_description_text');
    delete_option('dhr_hotel_reservation_label');
    delete_option('dhr_hotel_reservation_phone');
    delete_option('dhr_hotel_settings');
}


