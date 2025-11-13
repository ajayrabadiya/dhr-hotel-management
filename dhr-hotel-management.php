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
        register_activation_hook(__FILE__, array('DHR_Hotel_Database', 'create_tables'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize admin
        if (is_admin()) {
            new DHR_Hotel_Admin();
        }
        
        // Initialize frontend
        new DHR_Hotel_Frontend();
    }
    
    public function deactivate() {
        // Cleanup if needed
    }
}

// Initialize the plugin
DHR_Hotel_Management::get_instance();

