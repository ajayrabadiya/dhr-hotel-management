<?php
/**
 * API handler for DHR Hotel Management - SOAP API integration
 */

if (!defined('ABSPATH')) {
    exit;
}

class DHR_Hotel_API {
    
    /**
     * Get API URL from settings
     */
    private function get_api_url() {
        $url = get_option('dhr_hotel_api_url', '');
        if (empty($url)) {
            // Default URL if not set
            $url = 'https://ota.windsurfercrs.com/HotelDescriptiveInfo';
        }
        return rtrim($url, '/') . '/';
    }
    
    /**
     * Get API username from settings
     */
    private function get_username() {
        $username = get_option('dhr_hotel_api_username', '');
        if (empty($username)) {
            // Default username if not set
            $username = '4SHAWDREAM1225';
        }
        return $username;
    }
    
    /**
     * Get API password from settings (decrypted)
     */
    private function get_password() {
        $encrypted = get_option('dhr_hotel_api_password', '');
        if (empty($encrypted)) {
            // Fallback to default if not set (for backward compatibility)
            // Note: This should be set in settings for production
            return 'aYvtZl$T#y#L';
        }
        // Decode base64 encoded password
        $password = base64_decode($encrypted);
        return $password !== false ? $password : '';
    }
    
    /**
     * Get hotel descriptive info from SOAP API
     */
    public function get_hotel_descriptive_info($hotel_code) {
        $soap_request = $this->build_soap_request($hotel_code);
        
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => ''
            ),
            'body' => $soap_request,
            'timeout' => 30
        );
        
        $response = wp_remote_request($this->get_api_url(), $args);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code !== 200) {
            return array(
                'success' => false,
                'error' => 'API returned status code: ' . $status_code
            );
        }
        
        return array(
            'success' => true,
            'xml' => $body
        );
    }
    
    /**
     * Build SOAP request XML
     */
    private function build_soap_request($hotel_code) {
        $echo_token = md5(uniqid(rand(), true));
        
        $xml = '<?xml version="1.0"?>';
        $xml .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
        $xml .= '<soap:Header>';
        $xml .= '<HTNGHeader xmlns="http://htng.org/2009">';
        $xml .= '<From>';
        $xml .= '<Credential>';
        $xml .= '<userName>' . esc_html($this->get_username()) . '</userName>';
        $xml .= '<password>' . esc_html($this->get_password()) . '</password>';
        $xml .= '</Credential>';
        $xml .= '</From>';
        $xml .= '</HTNGHeader>';
        $xml .= '</soap:Header>';
        $xml .= '<soap:Body>';
        $xml .= '<OTA_HotelDescriptiveInfoRQ EchoToken="' . esc_attr($echo_token) . '" Target="Production" Version="1.002" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05/OTA_HotelDescriptiveInfoRQ.xsd">';
        $xml .= '<HotelDescriptiveInfos>';
        $xml .= '<HotelDescriptiveInfo HotelCode="' . esc_attr($hotel_code) . '"/>';
        $xml .= '</HotelDescriptiveInfos>';
        $xml .= '</OTA_HotelDescriptiveInfoRQ>';
        $xml .= '</soap:Body>';
        $xml .= '</soap:Envelope>';
        
        return $xml;
    }
    
    /**
     * Parse SOAP XML response and extract hotel data
     */
    public function parse_hotel_xml($xml_string) {
        // Suppress XML errors
        libxml_use_internal_errors(true);
        
        $xml = simplexml_load_string($xml_string);
        
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            return array(
                'success' => false,
                'error' => 'Failed to parse XML: ' . print_r($errors, true)
            );
        }
        
        // Register namespaces
        $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
        
        // Get the body content
        $body = $xml->xpath('//soap:Body/ota:OTA_HotelDescriptiveInfoRS');
        
        if (empty($body)) {
            return array(
                'success' => false,
                'error' => 'No hotel data found in response'
            );
        }
        
        $hotel_data = $body[0];
        $hotel_data->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
        
        // Extract hotel information
        $hotel_contents = $hotel_data->xpath('.//ota:HotelDescriptiveContent');
        
        if (empty($hotel_contents)) {
            return array(
                'success' => false,
                'error' => 'No hotel descriptive content found'
            );
        }
        
        $content = $hotel_contents[0];
        $content->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
        
        // Get hotel code and name from parent
        $hotel_code = (string)$hotel_data->HotelDescriptiveContents['HotelCode'];
        $hotel_name = (string)$hotel_data->HotelDescriptiveContents['HotelName'];
        $chain_code = (string)$hotel_data->HotelDescriptiveContents['ChainCode'];
        
        // Extract hotel info
        $hotel_info = $content->xpath('.//ota:HotelInfo');
        $hotel_info_data = !empty($hotel_info) ? $hotel_info[0] : null;
        
        // Extract position
        $position = $content->xpath('.//ota:Position');
        $latitude = !empty($position) ? (string)$position[0]['Latitude'] : null;
        $longitude = !empty($position) ? (string)$position[0]['Longitude'] : null;
        
        // Extract descriptions
        $descriptions = $content->xpath('.//ota:Descriptions');
        $description_text = '';
        $renovation_text = '';
        
        if (!empty($descriptions)) {
            $desc_elements = $descriptions[0]->xpath('.//ota:DescriptiveText');
            foreach ($desc_elements as $desc) {
                $text = (string)$desc;
                if (strpos($text, 'Renovation') !== false) {
                    $renovation_text = $text;
                } else {
                    $description_text = $text;
                }
            }
        }
        
        // Extract policies
        $policies = $content->xpath('.//ota:Policies/ota:Policy');
        $check_in_time = '';
        $check_out_time = '';
        $cancellation_policy = '';
        $guarantee_policy = '';
        $pets_allowed = '';
        $commission_percent = null;
        
        if (!empty($policies)) {
            $policy = $policies[0];
            $policy->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
            
            // Check-in/out times
            $policy_info = $policy->xpath('.//ota:PolicyInfo');
            if (!empty($policy_info)) {
                $check_in_time = (string)$policy_info[0]['CheckInTime'];
                $check_out_time = (string)$policy_info[0]['CheckOutTime'];
            }
            
            // Cancellation policy
            $cancel_policy = $policy->xpath('.//ota:CancelPolicy/ota:CancelPenalty/ota:PenaltyDescription/ota:Text');
            if (!empty($cancel_policy)) {
                $cancellation_policy = (string)$cancel_policy[0];
            }
            
            // Guarantee policy
            $guarantee = $policy->xpath('.//ota:GuaranteePayment/ota:Description/ota:Text');
            if (!empty($guarantee)) {
                $guarantee_policy = (string)$guarantee[0];
            }
            
            // Pets policy
            $pets = $policy->xpath('.//ota:PetsPolicies');
            if (!empty($pets)) {
                $pets_allowed = (string)$pets[0]['PetsAllowedCode'];
            }
            
            // Commission
            $commission = $policy->xpath('.//ota:CommissionPolicy');
            if (!empty($commission)) {
                $commission_percent = (string)$commission[0]['Percent'];
            }
        }
        
        // Extract rooms
        $rooms = array();
        $guest_rooms = $content->xpath('.//ota:GuestRooms/ota:GuestRoom');
        
        foreach ($guest_rooms as $guest_room) {
            $guest_room->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
            
            $room_type = $guest_room->xpath('.//ota:TypeRoom');
            $room_type_code = !empty($room_type) ? (string)$room_type[0]['RoomTypeCode'] : '';
            $room_type_name = !empty($room_type) ? (string)$room_type[0]['Name'] : (string)$guest_room['RoomTypeName'];
            
            // Extract amenities
            $amenities = array();
            $amenity_elements = $guest_room->xpath('.//ota:Amenity');
            foreach ($amenity_elements as $amenity) {
                $amenities[] = array(
                    'code' => (string)$amenity['RoomAmenityCode'],
                    'name' => (string)$amenity['CodeDetail'],
                    'exists_code' => (string)$amenity['ExistsCode'],
                    'description' => (string)$amenity->DescriptiveText
                );
            }
            
            // Extract images
            $images = array();
            $image_items = $guest_room->xpath('.//ota:ImageItem/ota:ImageFormat/ota:URL');
            foreach ($image_items as $image_url) {
                $images[] = (string)$image_url;
            }
            
            // Extract description
            $room_desc = $guest_room->xpath('.//ota:DescriptiveText');
            $room_description = !empty($room_desc) ? html_entity_decode((string)$room_desc[0]) : '';
            
            $rooms[] = array(
                'room_type_name' => $room_type_name,
                'room_type_code' => $room_type_code,
                'max_occupancy' => (int)$guest_room['MaxOccupancy'],
                'max_adult_occupancy' => (int)$guest_room['MaxAdultOccupancy'],
                'max_child_occupancy' => (int)$guest_room['MaxChildOccupancy'],
                'standard_num_beds' => !empty($room_type) ? (int)$room_type[0]['StandardNumBeds'] : null,
                'standard_occupancy' => !empty($room_type) ? (int)$room_type[0]['StandardOccupancy'] : null,
                'room_size' => !empty($room_type) ? (float)$room_type[0]['Size'] : null,
                'description' => $room_description,
                'amenities' => $amenities,
                'images' => $images
            );
        }
        
        // Extract services
        $services = array();
        $service_elements = $content->xpath('.//ota:Services/ota:Service');
        
        foreach ($service_elements as $service) {
            $services[] = array(
                'service_code' => (string)$service['Code'],
                'service_name' => (string)$service['CodeDetail'],
                'exists_code' => (string)$service['ExistsCode'],
                'proximity_code' => (string)$service['ProximityCode'],
                'description' => (string)$service->DescriptiveText
            );
        }
        
        return array(
            'success' => true,
            'hotel_code' => $hotel_code,
            'hotel_name' => $hotel_name,
            'chain_code' => $chain_code,
            'chain_name' => (string)$content['ChainName'],
            'currency_code' => (string)$content['CurrencyCode'],
            'language_code' => (string)$content['LanguageCode'],
            'time_zone' => (string)$content['TimeZone'],
            'when_built' => $hotel_info_data ? (string)$hotel_info_data['WhenBuilt'] : '',
            'hotel_status' => $hotel_info_data ? (string)$hotel_info_data['HotelStatus'] : '',
            'hotel_status_code' => $hotel_info_data ? (string)$hotel_info_data['HotelStatusCode'] : '',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'description' => $description_text,
            'renovation_text' => $renovation_text,
            'check_in_time' => $check_in_time,
            'check_out_time' => $check_out_time,
            'cancellation_policy' => $cancellation_policy,
            'guarantee_policy' => $guarantee_policy,
            'pets_allowed' => $pets_allowed,
            'commission_percent' => $commission_percent,
            'rooms' => $rooms,
            'services' => $services,
            'raw_xml' => $xml_string
        );
    }
    
    /**
     * Fetch and save hotel data from API
     */
    public function fetch_and_save_hotel_data($hotel_code) {
        // Get data from API
        $api_response = $this->get_hotel_descriptive_info($hotel_code);
        
        if (!$api_response['success']) {
            return array(
                'success' => false,
                'error' => $api_response['error']
            );
        }
        
        // Parse XML
        $parsed_data = $this->parse_hotel_xml($api_response['xml']);
        
        if (!$parsed_data['success']) {
            return $parsed_data;
        }
        
        // Save hotel details
        $hotel_details_data = array(
            'hotel_code' => $parsed_data['hotel_code'],
            'hotel_name' => $parsed_data['hotel_name'],
            'chain_code' => $parsed_data['chain_code'],
            'chain_name' => $parsed_data['chain_name'],
            'currency_code' => $parsed_data['currency_code'],
            'language_code' => $parsed_data['language_code'],
            'time_zone' => $parsed_data['time_zone'],
            'when_built' => $parsed_data['when_built'],
            'hotel_status' => $parsed_data['hotel_status'],
            'hotel_status_code' => $parsed_data['hotel_status_code'],
            'latitude' => $parsed_data['latitude'],
            'longitude' => $parsed_data['longitude'],
            'description' => $parsed_data['description'],
            'renovation_text' => $parsed_data['renovation_text'],
            'check_in_time' => $parsed_data['check_in_time'],
            'check_out_time' => $parsed_data['check_out_time'],
            'cancellation_policy' => $parsed_data['cancellation_policy'],
            'guarantee_policy' => $parsed_data['guarantee_policy'],
            'pets_allowed' => $parsed_data['pets_allowed'],
            'commission_percent' => $parsed_data['commission_percent'],
            'raw_xml_data' => $parsed_data['raw_xml']
        );
        
        $hotel_details_id = DHR_Hotel_Database::save_hotel_details($hotel_details_data);
        
        if ($hotel_details_id === false) {
            global $wpdb;
            $error_message = 'Failed to save hotel details';
            
            // Get detailed error if available
            if (!empty($wpdb->last_error)) {
                $error_message .= ': ' . $wpdb->last_error;
            }
            
            // Log for debugging
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('DHR Hotel API: Failed to save hotel details for ' . $parsed_data['hotel_code']);
                error_log('DHR Hotel API: Last query - ' . $wpdb->last_query);
                error_log('DHR Hotel API: Data - ' . print_r($hotel_details_data, true));
            }
            
            return array(
                'success' => false,
                'error' => $error_message
            );
        }
        
        // Save rooms
        DHR_Hotel_Database::save_hotel_rooms($parsed_data['hotel_code'], $parsed_data['rooms']);
        
        // Save services
        DHR_Hotel_Database::save_hotel_services($parsed_data['hotel_code'], $parsed_data['services']);
        
        return array(
            'success' => true,
            'hotel_code' => $parsed_data['hotel_code'],
            'hotel_details_id' => $hotel_details_id
        );
    }
}
