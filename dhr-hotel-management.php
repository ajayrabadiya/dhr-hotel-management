<?php
/**
 * Plugin Name: DHR Hotel Management
 * Plugin URI: https://example.com/dhr-hotel-management
 * Description: A comprehensive hotel management plugin with Google Maps integration for displaying hotel locations.
 * Version: 1.0.0
 * Author: DHR
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dhr-hotel-management
 * Network: true
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DHR_HOTEL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DHR_HOTEL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DHR_HOTEL_PLUGIN_VERSION', '1.0.0');

// Include required files
require_once DHR_HOTEL_PLUGIN_PATH . 'includes/class-dhr-hotel-database.php';
require_once DHR_HOTEL_PLUGIN_PATH . 'includes/class-dhr-hotel-admin.php';
require_once DHR_HOTEL_PLUGIN_PATH . 'includes/class-dhr-hotel-frontend.php';
require_once DHR_HOTEL_PLUGIN_PATH . 'includes/class-dhr-hotel-api.php';
require_once DHR_HOTEL_PLUGIN_PATH . 'includes/class-dhr-hotel-rest-api.php';
require_once DHR_HOTEL_PLUGIN_PATH . 'includes/display-all-shortcodes.php';

/**
 * Main plugin class
 */
class DHR_Hotel_Management {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Hook for new site creation in multisite
        add_action('wpmu_new_blog', array($this, 'new_site_activation'), 10, 6);
        
        // Load plugin textdomain - must be on 'init' or later
        add_action('init', array($this, 'load_textdomain'));

        // Ensure hotels table has all columns (e.g. hotel_code) for existing installs
        add_action('admin_init', array($this, 'maybe_upgrade_hotels_table'));
        
        // Initialize admin - after init to ensure translations are loaded
        add_action('init', array($this, 'init_admin'));
        
        // Initialize frontend - after init to ensure translations are loaded
        add_action('init', array($this, 'init_frontend'));
        
        // Initialize REST API - can be earlier
        new DHR_Hotel_REST_API();
    }
    
    /**
     * Run hotels table schema upgrade (add missing columns like hotel_code) for existing installs.
     */
    public function maybe_upgrade_hotels_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotels';
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name) {
            DHR_Hotel_Database::maybe_upgrade_dhr_hotels_table($table_name);
        }
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'dhr-hotel-management',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Initialize admin functionality
     */
    public function init_admin() {
        if (is_admin()) {
            new DHR_Hotel_Admin();
        }
    }
    
    /**
     * Initialize frontend functionality
     */
    public function init_frontend() {
        // Initialize frontend for both admin and frontend (shortcodes work in both)
        new DHR_Hotel_Frontend();
    }
    
    /**
     * Activate plugin for single site or network
     */
    public function activate($network_wide = false) {
        if (is_multisite() && $network_wide) {
            // Network activation - create tables for all sites
            $this->network_activate();
        } else {
            // Single site activation
            DHR_Hotel_Database::create_tables();
        }
    }
    
    /**
     * Network activation - create tables for all existing sites
     */
    public function network_activate() {
        if (!is_multisite()) {
            return;
        }
        
        global $wpdb;
        
        // Get all site IDs
        $site_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
        
        foreach ($site_ids as $site_id) {
            switch_to_blog($site_id);
            DHR_Hotel_Database::create_tables();
            restore_current_blog();
        }
    }
    
    /**
     * Handle new site creation in multisite
     */
    public function new_site_activation($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        if (is_plugin_active_for_network(plugin_basename(__FILE__))) {
            switch_to_blog($blog_id);
            DHR_Hotel_Database::create_tables();
            restore_current_blog();
        }
    }
    
    /**
     * Deactivate plugin
     */
    public function deactivate($network_wide) {
        // Cleanup if needed
        // Note: Tables are not dropped on deactivation, only on uninstall
    }
}

// Initialize the plugin
DHR_Hotel_Management::get_instance();

