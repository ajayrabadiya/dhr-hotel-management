<?php
/**
 * Uninstall script for DHR Hotel Management
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Drop the database table
$table_name = $wpdb->prefix . 'dhr_hotels';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Delete plugin options if any
delete_option('dhr_hotel_settings');


