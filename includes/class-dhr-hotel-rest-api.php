<?php
/**
 * REST API endpoints for DHR Hotel Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class DHR_Hotel_REST_API {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('dhr-hotel/v1', '/hotel-details/(?P<hotel_code>[a-zA-Z0-9]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_hotel_details'),
            'permission_callback' => '__return_true',
            'args' => array(
                'hotel_code' => array(
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_string($param) && !empty($param);
                    }
                ),
                'sync' => array(
                    'required' => false,
                    'default' => false,
                    'validate_callback' => function($param) {
                        return in_array($param, array('true', 'false', true, false, '1', '0', 1, 0), true);
                    }
                )
            )
        ));
        
        register_rest_route('dhr-hotel/v1', '/hotel-details', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_all_hotel_details'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * Get hotel details by hotel code
     */
    public function get_hotel_details($request) {
        $hotel_code = sanitize_text_field($request['hotel_code']);
        $sync = filter_var($request->get_param('sync'), FILTER_VALIDATE_BOOLEAN);
        
        // If sync is requested, fetch fresh data from API
        if ($sync) {
            $api = new DHR_Hotel_API();
            $result = $api->fetch_and_save_hotel_data($hotel_code);
            
            if (!$result['success']) {
                return new WP_Error(
                    'api_error',
                    $result['error'],
                    array('status' => 500)
                );
            }
        }
        
        // Get hotel details from database
        $hotel_details = DHR_Hotel_Database::get_hotel_details($hotel_code);
        
        if (!$hotel_details) {
            return new WP_Error(
                'not_found',
                'Hotel not found. Use ?sync=true to fetch from API.',
                array('status' => 404)
            );
        }
        
        // Get rooms
        $rooms = DHR_Hotel_Database::get_hotel_rooms($hotel_code);
        
        // Get services
        $services = DHR_Hotel_Database::get_hotel_services($hotel_code);
        
        // Format response with room-wise array
        $response = array(
            'hotel_code' => $hotel_details->hotel_code,
            'hotel_name' => $hotel_details->hotel_name,
            'chain_code' => $hotel_details->chain_code,
            'chain_name' => $hotel_details->chain_name,
            'currency_code' => $hotel_details->currency_code,
            'language_code' => $hotel_details->language_code,
            'time_zone' => $hotel_details->time_zone,
            'when_built' => $hotel_details->when_built,
            'hotel_status' => $hotel_details->hotel_status,
            'hotel_status_code' => $hotel_details->hotel_status_code,
            'location' => array(
                'latitude' => $hotel_details->latitude,
                'longitude' => $hotel_details->longitude
            ),
            'description' => $hotel_details->description,
            'renovation_text' => $hotel_details->renovation_text,
            'policies' => array(
                'check_in_time' => $hotel_details->check_in_time,
                'check_out_time' => $hotel_details->check_out_time,
                'cancellation_policy' => $hotel_details->cancellation_policy,
                'guarantee_policy' => $hotel_details->guarantee_policy,
                'pets_allowed' => $hotel_details->pets_allowed,
                'commission_percent' => $hotel_details->commission_percent
            ),
            'rooms' => array(),
            'services' => array(),
            'last_synced_at' => $hotel_details->last_synced_at
        );
        
        // Format rooms array
        foreach ($rooms as $room) {
            $response['rooms'][] = array(
                'id' => $room->id,
                'room_type_name' => $room->room_type_name,
                'room_type_code' => $room->room_type_code,
                'occupancy' => array(
                    'max_occupancy' => $room->max_occupancy,
                    'max_adult_occupancy' => $room->max_adult_occupancy,
                    'max_child_occupancy' => $room->max_child_occupancy,
                    'standard_occupancy' => $room->standard_occupancy
                ),
                'beds' => array(
                    'standard_num_beds' => $room->standard_num_beds
                ),
                'room_size' => $room->room_size,
                'description' => $room->description,
                'amenities' => is_array($room->amenities) ? $room->amenities : array(),
                'images' => is_array($room->images) ? $room->images : array()
            );
        }
        
        // Format services array
        foreach ($services as $service) {
            $response['services'][] = array(
                'id' => $service->id,
                'service_code' => $service->service_code,
                'service_name' => $service->service_name,
                'exists_code' => $service->exists_code,
                'proximity_code' => $service->proximity_code,
                'description' => $service->description
            );
        }
        
        return rest_ensure_response($response);
    }
    
    /**
     * Get all hotel details
     */
    public function get_all_hotel_details($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dhr_hotel_details';
        
        $hotels = $wpdb->get_results("SELECT hotel_code FROM $table_name ORDER BY hotel_name ASC");
        
        $response = array();
        
        foreach ($hotels as $hotel) {
            $hotel_details = DHR_Hotel_Database::get_hotel_details($hotel->hotel_code);
            $rooms = DHR_Hotel_Database::get_hotel_rooms($hotel->hotel_code);
            
            $hotel_data = array(
                'hotel_code' => $hotel_details->hotel_code,
                'hotel_name' => $hotel_details->hotel_name,
                'chain_code' => $hotel_details->chain_code,
                'chain_name' => $hotel_details->chain_name,
                'location' => array(
                    'latitude' => $hotel_details->latitude,
                    'longitude' => $hotel_details->longitude
                ),
                'rooms_count' => count($rooms),
                'rooms' => array()
            );
            
            // Add rooms summary
            foreach ($rooms as $room) {
                $hotel_data['rooms'][] = array(
                    'room_type_name' => $room->room_type_name,
                    'room_type_code' => $room->room_type_code,
                    'max_occupancy' => $room->max_occupancy
                );
            }
            
            $response[] = $hotel_data;
        }
        
        return rest_ensure_response($response);
    }
}

