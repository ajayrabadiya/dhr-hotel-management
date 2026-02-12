<?php
/**
 * Admin functionality for DHR Hotel Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class DHR_Hotel_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_post_dhr_save_hotel', array($this, 'save_hotel'));
        add_action('admin_post_dhr_delete_hotel', array($this, 'delete_hotel'));
        add_action('admin_post_dhr_save_settings', array($this, 'save_settings'));
        add_action('admin_post_dhr_save_map_config', array($this, 'save_map_config'));
        add_action('admin_post_dhr_create_default_maps', array($this, 'create_default_maps'));

        // SHR WS Shop API (REST) sync actions
        add_action('admin_post_dhr_sync_shr_hotel', array($this, 'sync_shr_hotel'));
        add_action('wp_ajax_dhr_sync_shr_hotel_ajax', array($this, 'sync_shr_hotel_ajax'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('DHR Hotel Management', 'dhr-hotel-management'),
            __('DHR Hotel Management', 'dhr-hotel-management'),
            'manage_options',
            'dhr-hotel-management',
            array($this, 'display_hotels_list'),
            'dashicons-location-alt',
            30
        );
        
        add_submenu_page(
            'dhr-hotel-management',
            __('All Hotels', 'dhr-hotel-management'),
            __('All Hotels', 'dhr-hotel-management'),
            'manage_options',
            'dhr-hotel-management',
            array($this, 'display_hotels_list')
        );
        
        add_submenu_page(
            'dhr-hotel-management',
            __('Settings', 'dhr-hotel-management'),
            __('Settings', 'dhr-hotel-management'),
            'manage_options',
            'dhr-hotel-settings',
            array($this, 'display_settings')
        );
        
        add_submenu_page(
            'dhr-hotel-management',
            __('Map Management', 'dhr-hotel-management'),
            __('Map Management', 'dhr-hotel-management'),
            'manage_options',
            'dhr-hotel-map-management',
            array($this, 'display_map_management')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'dhr-hotel') === false) {
            return;
        }
        
        wp_enqueue_style(
            'dhr-hotel-admin-style',
            DHR_HOTEL_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            DHR_HOTEL_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'dhr-hotel-admin-script',
            DHR_HOTEL_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            DHR_HOTEL_PLUGIN_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('dhr-hotel-admin-script', 'dhrHotelAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'shrSyncNonce' => wp_create_nonce('dhr_sync_shr_hotel_ajax_nonce')
        ));
        
        // Enqueue media uploader
        wp_enqueue_media();
    }
    
    /**
     * Display hotels list
     */
    public function display_hotels_list() {
        $hotels = DHR_Hotel_Database::get_all_hotels();
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;
        
        if ($action === 'edit' && $hotel_id > 0) {
            $this->display_hotel_form($hotel_id);
            return;
        }
        
        include DHR_HOTEL_PLUGIN_PATH . 'templates/admin/hotels-list.php';
    }
    
    /**
     * Display hotel form (edit only; add new hotel has been removed)
     */
    public function display_hotel_form($hotel_id = 0) {
        if ($hotel_id <= 0) {
            wp_safe_redirect(admin_url('admin.php?page=dhr-hotel-management'));
            exit;
        }
        $hotel = DHR_Hotel_Database::get_hotel($hotel_id);
        if (!$hotel) {
            wp_die(__('Hotel not found.', 'dhr-hotel-management'));
        }
        include DHR_HOTEL_PLUGIN_PATH . 'templates/admin/hotel-form.php';
    }
    
    /**
     * Save hotel (add or update)
     */
    public function save_hotel() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        check_admin_referer('dhr_hotel_nonce');
        
        $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : 0;
        if ($hotel_id <= 0) {
            wp_safe_redirect(admin_url('admin.php?page=dhr-hotel-management&message=error'));
            exit;
        }
        
        $data = array(
            'hotel_code' => isset($_POST['hotel_code']) ? $_POST['hotel_code'] : '',
            'name' => isset($_POST['name']) ? $_POST['name'] : '',
            'description' => isset($_POST['description']) ? $_POST['description'] : '',
            'address' => isset($_POST['address']) ? $_POST['address'] : '',
            'city' => isset($_POST['city']) ? $_POST['city'] : '',
            'province' => isset($_POST['province']) ? $_POST['province'] : '',
            'country' => isset($_POST['country']) ? $_POST['country'] : 'South Africa',
            'latitude' => isset($_POST['latitude']) ? $_POST['latitude'] : '',
            'longitude' => isset($_POST['longitude']) ? $_POST['longitude'] : '',
            'phone' => isset($_POST['phone']) ? $_POST['phone'] : '',
            'email' => isset($_POST['email']) ? $_POST['email'] : '',
            'website' => isset($_POST['website']) ? $_POST['website'] : '',
            'image_url' => isset($_POST['image_url']) ? $_POST['image_url'] : '',
            'google_maps_url' => isset($_POST['google_maps_url']) ? $_POST['google_maps_url'] : '',
            'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
        );
        
        $result = DHR_Hotel_Database::update_hotel($hotel_id, $data);
        $message = $result ? 'updated' : 'error';
        wp_redirect(admin_url('admin.php?page=dhr-hotel-management&message=' . $message));
        exit;
    }
    
    /**
     * Delete hotel
     */
    public function delete_hotel() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        check_admin_referer('dhr_delete_hotel_nonce');
        
        $hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;
        
        if ($hotel_id > 0) {
            $result = DHR_Hotel_Database::delete_hotel($hotel_id);
            $message = $result ? 'deleted' : 'error';
        } else {
            $message = 'error';
        }
        
        wp_redirect(admin_url('admin.php?page=dhr-hotel-management&message=' . $message));
        exit;
    }
    
    /**
     * Display settings page
     */
    public function display_settings() {
        $api_key = get_option('dhr_hotel_google_maps_api_key', '');
        include DHR_HOTEL_PLUGIN_PATH . 'templates/admin/settings.php';
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        check_admin_referer('dhr_settings_nonce');
        
        $api_key = isset($_POST['google_maps_api_key']) ? sanitize_text_field($_POST['google_maps_api_key']) : '';
        update_option('dhr_hotel_google_maps_api_key', $api_key);

        // SHR WS Shop API (REST) settings
        // Manual access token (preferred method)
        $shr_manual_token = isset($_POST['shr_manual_access_token']) ? trim($_POST['shr_manual_access_token']) : '';
        if (!empty($shr_manual_token)) {
            update_option('dhr_shr_manual_access_token', $shr_manual_token);
        } else {
            // Clear manual token if empty
            delete_option('dhr_shr_manual_access_token');
        }

        // Client credentials (optional, for auto token generation)
        $shr_client_id = isset($_POST['shr_client_id']) ? sanitize_text_field($_POST['shr_client_id']) : '';
        update_option('dhr_shr_client_id', $shr_client_id);

        $shr_client_secret = isset($_POST['shr_client_secret']) ? $_POST['shr_client_secret'] : '';
        if (!empty($shr_client_secret)) {
            // Store encoded for basic obfuscation
            update_option('dhr_shr_client_secret', base64_encode($shr_client_secret));
        }

        $shr_scope = isset($_POST['shr_scope']) ? sanitize_text_field($_POST['shr_scope']) : 'wsapi.hoteldetails.read';
        update_option('dhr_shr_scope', $shr_scope);

        $shr_token_url = isset($_POST['shr_token_url']) ? esc_url_raw($_POST['shr_token_url']) : 'https://id.shrglobal.com/connect/token';
        update_option('dhr_shr_token_url', rtrim($shr_token_url, '/'));

        $shr_shop_base_url = isset($_POST['shr_shop_base_url']) ? esc_url_raw($_POST['shr_shop_base_url']) : 'https://api.shrglobal.com/shop';
        update_option('dhr_shr_shop_base_url', rtrim($shr_shop_base_url, '/'));

        $shr_channel_id = isset($_POST['shr_channel_id']) ? sanitize_text_field($_POST['shr_channel_id']) : '30';
        update_option('dhr_shr_channel_id', $shr_channel_id);

        wp_redirect(admin_url('admin.php?page=dhr-hotel-settings&message=saved'));
        exit;
    }
    
    /**
     * Display map management page
     */
    public function display_map_management() {
        // Ensure tables exist
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_map_configs';
        
        // Check if table exists, if not create it
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ));
        
        if (!$table_exists) {
            DHR_Hotel_Database::create_tables();
        }
        
        // Get map configs
        $map_configs = DHR_Hotel_Database::get_all_map_configs();
        
        // If no maps exist, try to create them
        if (empty($map_configs)) {
            DHR_Hotel_Database::create_default_map_configs();
            $map_configs = DHR_Hotel_Database::get_all_map_configs();
        }
        
        include DHR_HOTEL_PLUGIN_PATH . 'templates/admin/map-management.php';
    }
    
    /**
     * Save map configuration
     */
    public function save_map_config() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        check_admin_referer('dhr_map_config_nonce');
        
        $map_id = isset($_POST['map_id']) ? intval($_POST['map_id']) : 0;
        $settings = array();
        
        // Get all POST data and build settings array
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $setting_key = str_replace('setting_', '', $key);
                
                // Handle different field types
                if (strpos($setting_key, 'description') !== false || strpos($setting_key, 'text') !== false) {
                    // Textarea fields
                    $settings[$setting_key] = sanitize_textarea_field($value);
                } elseif (strpos($setting_key, 'url') !== false || strpos($setting_key, 'link') !== false) {
                    // URL fields
                    $settings[$setting_key] = esc_url_raw($value);
                } elseif ($setting_key === 'show_numbers' || $setting_key === 'show_list') {
                    // Boolean/checkbox fields
                    $settings[$setting_key] = isset($_POST[$key]) && ($value == '1' || $value == true) ? true : false;
                } else {
                    // Regular text fields
                    $settings[$setting_key] = sanitize_text_field($value);
                }
            }
        }
        
        $data = array(
            'map_name' => isset($_POST['map_name']) ? sanitize_text_field($_POST['map_name']) : '',
            'settings' => $settings,
            'status' => isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'active'
        );
        
        $result = DHR_Hotel_Database::update_map_config($map_id, $data);
        $message = $result ? 'updated' : 'error';
        
        wp_redirect(admin_url('admin.php?page=dhr-hotel-map-management&message=' . $message));
        exit;
    }
    
    /**
     * Create default maps
     */
    public function create_default_maps() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        check_admin_referer('dhr_create_default_maps_nonce');
        
        // Ensure table exists
        DHR_Hotel_Database::create_tables();
        
        // Create default maps
        DHR_Hotel_Database::create_default_map_configs();
        
        wp_redirect(admin_url('admin.php?page=dhr-hotel-map-management&message=maps_created'));
        exit;
    }
    
    /**
     * Sync a hotel from SHR WS Shop API (non-AJAX, from list form)
     */
    public function sync_shr_hotel() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        check_admin_referer('dhr_sync_shr_hotel_nonce');

        $hotel_code = isset($_POST['hotel_code']) ? sanitize_text_field($_POST['hotel_code']) : '';

        if (empty($hotel_code)) {
            wp_redirect(admin_url('admin.php?page=dhr-hotel-management&message=error'));
            exit;
        }

        $api    = new DHR_Hotel_API();
        $result = $api->fetch_shr_and_save_hotel($hotel_code);

        if ($result['success']) {
            wp_redirect(admin_url('admin.php?page=dhr-hotel-management&message=added'));
        } else {
            $error_param = urlencode($result['error']);
            wp_redirect(admin_url('admin.php?page=dhr-hotel-management&message=error&error=' . $error_param));
        }
        exit;
    }

    /**
     * Sync a hotel from SHR WS Shop API via AJAX (can be used from forms)
     */
    public function sync_shr_hotel_ajax() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
            return;
        }

        check_ajax_referer('dhr_sync_shr_hotel_ajax_nonce', 'nonce');

        $hotel_code = isset($_POST['hotel_code']) ? sanitize_text_field($_POST['hotel_code']) : '';

        if (empty($hotel_code)) {
            wp_send_json_error(array('message' => __('Hotel code is required.', 'dhr-hotel-management')));
            return;
        }

        $api    = new DHR_Hotel_API();
        $result = $api->fetch_shr_and_save_hotel($hotel_code);

        if ($result['success']) {
            wp_send_json_success(array(
                'message'     => __('Hotel synced successfully from SHR.', 'dhr-hotel-management'),
                'hotel_id'    => $result['hotel_id'],
                'hotel_code'  => $result['hotel_code'],
                'hotel_name'  => $result['hotel_name'],
            ));
        } else {
            wp_send_json_error(array('message' => $result['error']));
        }
    }
}

