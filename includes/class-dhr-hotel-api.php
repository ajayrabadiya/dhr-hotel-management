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

    /**
     * ==============================
     * SHR WS Shop API (REST) helpers
     * ==============================
     */

    /**
     * Get SHR client ID from settings
     */
    private function get_shr_client_id() {
        return get_option('dhr_shr_client_id', '');
    }

    /**
     * Get SHR client secret from settings (decrypted)
     */
    private function get_shr_client_secret() {
        $encrypted = get_option('dhr_shr_client_secret', '');
        return !empty($encrypted) ? base64_decode($encrypted) : '';
    }

    /**
     * Get SHR scope
     */
    private function get_shr_scope() {
        $scope = get_option('dhr_shr_scope', 'wsapi.hoteldetails.read');
        return trim($scope) !== '' ? $scope : 'wsapi.hoteldetails.read';
    }

    /**
     * Get SHR token URL
     */
    private function get_shr_token_url() {
        $url = get_option('dhr_shr_token_url', 'https://id.shrglobal.com/connect/token');
        return rtrim($url, '/');
    }

    /**
     * Get SHR Shop API base URL
     */
    private function get_shr_shop_base_url() {
        $url = get_option('dhr_shr_shop_base_url', 'https://api.shrglobal.com/shop');
        return rtrim($url, '/');
    }

    /**
     * Get cached SHR access token or request a new one
     */
    public function get_shr_access_token() {
        // Check for manually configured token first
        $manual_token = get_option('dhr_shr_manual_access_token', '');
        if (!empty($manual_token)) {
            return array(
                'success'      => true,
                'access_token' => $manual_token,
                'from_cache'   => false,
                'manual'       => true,
            );
        }

        // Check cached token
        $cached_token = get_option('dhr_shr_access_token', '');
        $expires_at   = intval(get_option('dhr_shr_access_token_expires_at', 0));

        // Reuse token if still valid (with 60s buffer)
        if (!empty($cached_token) && $expires_at > (time() + 60)) {
            return array(
                'success'      => true,
                'access_token' => $cached_token,
                'from_cache'   => true,
            );
        }

        // Request new token (only if client credentials are configured)
        $client_id     = $this->get_shr_client_id();
        $client_secret = $this->get_shr_client_secret();
        $scope         = $this->get_shr_scope();
        $token_url     = $this->get_shr_token_url();

        // If no client credentials, return error
        if (empty($client_id) || empty($client_secret)) {
            return array(
                'success' => false,
                'error'   => __('SHR access token is not configured. Please either set a manual access token or configure client ID and secret.', 'dhr-hotel-management'),
            );
        }

        $response = wp_remote_post(
            $token_url,
            array(
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ),
                'body'    => array(
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type'    => 'client_credentials',
                    'scope'         => $scope,
                ),
                'timeout' => 30,
            )
        );

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error'   => $response->get_error_message(),
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body        = wp_remote_retrieve_body($response);
        $data        = json_decode($body, true);

        if ($status_code !== 200 || !is_array($data) || empty($data['access_token'])) {
            return array(
                'success' => false,
                'error'   => sprintf(
                    /* translators: 1: HTTP status code */
                    __('SHR token request failed (status %d).', 'dhr-hotel-management'),
                    $status_code
                ),
                'details' => $body,
            );
        }

        $access_token = $data['access_token'];
        $expires_in   = isset($data['expires_in']) ? intval($data['expires_in']) : 3600;

        // Cache token and expiry
        update_option('dhr_shr_access_token', $access_token);
        update_option('dhr_shr_access_token_expires_at', time() + $expires_in);

        return array(
            'success'      => true,
            'access_token' => $access_token,
            'from_cache'   => false,
        );
    }

    /**
     * Call SHR /hotelDetails/{hotelCode} and return raw decoded data
     */
    private function call_shr_hotel_details($hotel_code) {
        $token_result = $this->get_shr_access_token();
        if (!$token_result['success']) {
            return $token_result;
        }

        $access_token = $token_result['access_token'];
        $base_url     = $this->get_shr_shop_base_url();

        // Build URL - ensure no double slashes
        $base_url = rtrim($base_url, '/');
        $url = $base_url . '/hotelDetails/' . rawurlencode($hotel_code);

        // Get configurable parameters from settings
        $hotel_id = get_option('dhr_shr_hotel_id', '');
        $language_id = get_option('dhr_shr_language_id', '4416');
        $channel_id = get_option('dhr_shr_channel_id', '6232');

        // Build query parameters - use minimal required params first
        $params = array(
            'requiredDetails' => 'all',
        );

        // Add optional parameters if configured
        if (!empty($hotel_id)) {
            $params['hotelID'] = $hotel_id;
        }
        if (!empty($language_id)) {
            $params['languageId'] = $language_id;
        }
        if (!empty($channel_id)) {
            $params['channelId'] = $channel_id;
        }

        // Allow filtering of parameters
        $params = apply_filters(
            'dhr_shr_hotel_details_query_args',
            $params,
            $hotel_code
        );

        // Build URL with query parameters
        if (!empty($params)) {
            $url = add_query_arg($params, $url);
        }

        $response = wp_remote_get(
            $url,
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ),
                'timeout' => 30,
            )
        );

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error'   => $response->get_error_message(),
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body        = wp_remote_retrieve_body($response);
        $data        = json_decode($body, true);

        if ($status_code !== 200) {
            // Try to extract error message from response
            $error_message = sprintf(
                /* translators: 1: HTTP status code */
                __('SHR hotelDetails request failed (status %d).', 'dhr-hotel-management'),
                $status_code
            );

            // Try to get more details from error response
            if (!empty($body)) {
                $error_data = json_decode($body, true);
                if (is_array($error_data)) {
                    if (isset($error_data['error'])) {
                        $error_message .= ' ' . $error_data['error'];
                    } elseif (isset($error_data['message'])) {
                        $error_message .= ' ' . $error_data['message'];
                    } elseif (isset($error_data['error_description'])) {
                        $error_message .= ' ' . $error_data['error_description'];
                    }
                } else {
                    // If not JSON, include first 200 chars of body
                    $error_message .= ' Response: ' . substr(strip_tags($body), 0, 200);
                }
            }

            // Log full error for debugging
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('DHR SHR API Error - URL: ' . $url);
                error_log('DHR SHR API Error - Status: ' . $status_code);
                error_log('DHR SHR API Error - Response: ' . $body);
            }

            return array(
                'success' => false,
                'error'   => $error_message,
                'details' => $body,
                'url'     => $url,
            );
        }

        if (!is_array($data)) {
            return array(
                'success' => false,
                'error'   => __('Invalid response format from SHR API.', 'dhr-hotel-management'),
                'details' => $body,
            );
        }

        return array(
            'success' => true,
            'data'    => $data,
        );
    }

    /**
     * Normalise SHR hotel details data into the fields we need
     */
    private function normalise_shr_hotel_data($hotel_code, $raw_data) {
        // SHR responses wrap data inside "hotelDetailInfo"
        $info = isset($raw_data['hotelDetailInfo']) && is_array($raw_data['hotelDetailInfo'])
            ? $raw_data['hotelDetailInfo']
            : $raw_data;

        // Hotel name
        $name = isset($info['hotelName']) ? $info['hotelName'] : $hotel_code;

        // Description - prefer longDescription, fallback to sellingPoints
        $description = '';
        if (!empty($info['longDescription'])) {
            // Strip HTML tags for plain text, but keep line breaks
            $description = wp_strip_all_tags($info['longDescription']);
        } elseif (!empty($info['sellingPoints'])) {
            $description = $info['sellingPoints'];
        } elseif (!empty($info['generalPolicy'])) {
            $description = wp_strip_all_tags($info['generalPolicy']);
        }

        // Address from contactInfo.address
        $address = '';
        $city = '';
        $province = '';
        $country = 'South Africa';
        $postal_code = '';

        if (!empty($info['contactInfo']['address'])) {
            $addr = $info['contactInfo']['address'];
            
            // Build address from addressLine array
            if (!empty($addr['addressLine']) && is_array($addr['addressLine'])) {
                $address = implode(', ', array_filter($addr['addressLine']));
            }
            
            $city = isset($addr['cityName']) ? $addr['cityName'] : '';
            $province = isset($addr['stateProv']['state']) ? $addr['stateProv']['state'] : '';
            $postal_code = isset($addr['postalCode']) ? $addr['postalCode'] : '';
            
            // Country code mapping (ZA = South Africa)
            if (!empty($addr['countryName']['code'])) {
                $country_code = $addr['countryName']['code'];
                // Map common codes to full names
                $country_map = array(
                    'ZA' => 'South Africa',
                    'US' => 'United States',
                    'GB' => 'United Kingdom',
                );
                $country = isset($country_map[$country_code]) ? $country_map[$country_code] : $country_code;
            }
        }

        // Coordinates (directly on hotelDetailInfo root)
        $latitude = isset($info['latitude']) ? floatval($info['latitude']) : 0;
        $longitude = isset($info['longitude']) ? floatval($info['longitude']) : 0;

        // Phone from contactInfo.phones array (find primary voice phone)
        $phone = '';
        if (!empty($info['contactInfo']['phones']) && is_array($info['contactInfo']['phones'])) {
            foreach ($info['contactInfo']['phones'] as $phone_obj) {
                if (isset($phone_obj['phoneTechType']) && $phone_obj['phoneTechType'] === 'voice' 
                    && isset($phone_obj['primary']) && $phone_obj['primary'] === true) {
                    // Build full phone number
                    $country_code = isset($phone_obj['countryAccessCode']) ? $phone_obj['countryAccessCode'] : '';
                    $area_code = isset($phone_obj['areaCityCode']) ? $phone_obj['areaCityCode'] : '';
                    $number = isset($phone_obj['phoneNumber']) ? $phone_obj['phoneNumber'] : '';
                    
                    if ($country_code && $area_code && $number) {
                        $phone = '+' . $country_code . '-' . $area_code . '-' . $number;
                    } elseif ($number) {
                        $phone = $number;
                    }
                    break;
                }
            }
            // Fallback: use resPhone if available
            if (empty($phone) && !empty($info['resPhone'])) {
                $phone = $info['resPhone'];
            }
        }

        // Email from contactInfo.emails array
        $email = '';
        if (!empty($info['contactInfo']['emails']) && is_array($info['contactInfo']['emails'])) {
            // Get first email value (may contain multiple emails separated by semicolon)
            $email_str = isset($info['contactInfo']['emails'][0]['value']) 
                ? $info['contactInfo']['emails'][0]['value'] 
                : '';
            // Take first email if multiple
            if (strpos($email_str, ';') !== false) {
                $emails = explode(';', $email_str);
                $email = trim($emails[0]);
            } else {
                $email = trim($email_str);
            }
        }
        // Fallback: use resEmail
        if (empty($email) && !empty($info['resEmail'])) {
            $email_str = $info['resEmail'];
            if (strpos($email_str, ';') !== false) {
                $emails = explode(';', $email_str);
                $email = trim($emails[0]);
            } else {
                $email = trim($email_str);
            }
        }

        // Website from contactInfo.urLs array (prefer Property type)
        $website = '';
        if (!empty($info['contactInfo']['urLs']) && is_array($info['contactInfo']['urLs'])) {
            foreach ($info['contactInfo']['urLs'] as $url_obj) {
                if (isset($url_obj['type']) && $url_obj['type'] === 'Property') {
                    $url_value = isset($url_obj['value']) ? $url_obj['value'] : '';
                    if (!empty($url_value)) {
                        // Add https:// if missing
                        $website = (strpos($url_value, 'http') === 0) ? $url_value : 'https://' . $url_value;
                    }
                    break;
                }
            }
            // Fallback: use Reservation URL or urlHotel
            if (empty($website)) {
                foreach ($info['contactInfo']['urLs'] as $url_obj) {
                    if (isset($url_obj['type']) && $url_obj['type'] === 'Reservation') {
                        $url_value = isset($url_obj['value']) ? $url_obj['value'] : '';
                        if (!empty($url_value)) {
                            $website = (strpos($url_value, 'http') === 0) ? $url_value : 'https://' . $url_value;
                        }
                        break;
                    }
                }
            }
        }
        // Fallback: use urlHotel
        if (empty($website) && !empty($info['urlHotel'])) {
            $url_value = $info['urlHotel'];
            $website = (strpos($url_value, 'http') === 0) ? $url_value : 'https://' . $url_value;
        }

        // Image URL - get first propertyImage_Stardard from images array
        $image_url = '';
        if (!empty($info['images']) && is_array($info['images'])) {
            foreach ($info['images'] as $img) {
                if (isset($img['mediaType']) && $img['mediaType'] === 'propertyImage_Stardard') {
                    $image_url = isset($img['fileName']) ? $img['fileName'] : '';
                    if (!empty($image_url)) {
                        break;
                    }
                }
            }
        }

        // Google Maps URL - build from coordinates
        $google_maps_url = '';
        if ($latitude != 0 && $longitude != 0) {
            $google_maps_url = 'https://www.google.com/maps?q=' . $latitude . ',' . $longitude;
        }

        return array(
            'hotel_code'      => $hotel_code,
            'name'            => $name,
            'description'     => $description,
            'address'         => $address,
            'city'            => $city,
            'province'        => $province,
            'country'         => $country,
            'postal_code'     => $postal_code,
            'latitude'        => $latitude,
            'longitude'       => $longitude,
            'phone'           => $phone,
            'email'           => $email,
            'website'         => $website,
            'image_url'       => $image_url,
            'google_maps_url' => $google_maps_url,
            'raw'             => $raw_data,
        );
    }

    /**
     * Fetch hotel details from SHR and create/update local hotel + details
     */
    public function fetch_shr_and_save_hotel($hotel_code) {
        $hotel_code = trim($hotel_code);
        if ($hotel_code === '') {
            return array(
                'success' => false,
                'error'   => __('Hotel code is required.', 'dhr-hotel-management'),
            );
        }

        $api_result = $this->call_shr_hotel_details($hotel_code);
        if (!$api_result['success']) {
            return $api_result;
        }

        $normalised = $this->normalise_shr_hotel_data($hotel_code, $api_result['data']);

        // Insert or update hotel record
        $existing = DHR_Hotel_Database::get_hotel_by_code($hotel_code);

        $hotel_data = array(
            'hotel_code'      => $normalised['hotel_code'],
            'name'            => $normalised['name'],
            'description'     => $normalised['description'],
            'address'         => $normalised['address'],
            'city'            => $normalised['city'],
            'province'        => $normalised['province'],
            'country'         => $normalised['country'],
            'latitude'        => $normalised['latitude'],
            'longitude'       => $normalised['longitude'],
            'phone'           => $normalised['phone'],
            'email'           => $normalised['email'],
            'website'         => $normalised['website'],
            'image_url'       => $normalised['image_url'],
            'google_maps_url' => $normalised['google_maps_url'],
            'status'          => 'active',
        );

        if ($existing) {
            $hotel_id = $existing->id;
            $updated  = DHR_Hotel_Database::update_hotel($hotel_id, $hotel_data);
            if (!$updated) {
                return array(
                    'success' => false,
                    'error'   => __('Failed to update existing hotel record.', 'dhr-hotel-management'),
                );
            }
        } else {
            $hotel_id = DHR_Hotel_Database::insert_hotel($hotel_data);
            if ($hotel_id === false) {
                return array(
                    'success' => false,
                    'error'   => __('Failed to insert new hotel record.', 'dhr-hotel-management'),
                );
            }
        }

        // Store detailed SHR data in the hotel details table
        $info = isset($normalised['raw']['hotelDetailInfo']) 
            ? $normalised['raw']['hotelDetailInfo'] 
            : (isset($normalised['raw']) ? $normalised['raw'] : array());

        // Extract check-in/out times from policies
        $check_in_time = '';
        $check_out_time = '';
        if (!empty($info['policies']['policyInfo'])) {
            $policy_info = $info['policies']['policyInfo'];
            $check_in_time = isset($policy_info['checkInTime']) ? $policy_info['checkInTime'] : '';
            $check_out_time = isset($policy_info['checkOutTime']) ? $policy_info['checkOutTime'] : '';
        }

        // Extract cancellation policy
        $cancellation_policy = '';
        if (!empty($info['policies']['cancelPolicy']['cancelPenalty']) 
            && is_array($info['policies']['cancelPolicy']['cancelPenalty'])) {
            $penalties = array();
            foreach ($info['policies']['cancelPolicy']['cancelPenalty'] as $penalty) {
                if (!empty($penalty['penaltyDescription'])) {
                    $penalties[] = $penalty['penaltyDescription'];
                }
            }
            $cancellation_policy = implode("\n\n", $penalties);
        }

        // Extract guarantee policy
        $guarantee_policy = '';
        if (!empty($info['policies']['guaranteePaymentPolicy']['guaranteePayment']) 
            && is_array($info['policies']['guaranteePaymentPolicy']['guaranteePayment'])) {
            $guarantees = array();
            foreach ($info['policies']['guaranteePaymentPolicy']['guaranteePayment'] as $guarantee) {
                if (!empty($guarantee['description'])) {
                    $guarantees[] = $guarantee['description'];
                }
            }
            $guarantee_policy = implode("\n\n", $guarantees);
        }

        // Extract pets policy
        $pets_allowed = '';
        if (!empty($info['policies']['petsPolicy'])) {
            $pets_policy = $info['policies']['petsPolicy'];
            $pets_allowed = isset($pets_policy['petsAllowed']) && $pets_policy['petsAllowed'] === true ? 'Yes' : 'No';
            if (!empty($pets_policy['description'])) {
                $pets_allowed .= ' - ' . $pets_policy['description'];
            }
        }

        // Extract commission percent
        $commission_percent = null;
        if (!empty($info['policies']['commissionPolicy']['percent'])) {
            $commission_percent = floatval($info['policies']['commissionPolicy']['percent']);
        }

        // Chain info
        $chain_code = isset($info['chainCode']) ? $info['chainCode'] : '';
        $chain_name = isset($info['chainCode']) ? $info['chainCode'] : ''; // Can be extended if chain name is available

        // Currency
        $currency_code = '';
        if (!empty($info['currencies']) && is_array($info['currencies'])) {
            foreach ($info['currencies'] as $curr) {
                if (isset($curr['default']) && $curr['default'] === true) {
                    $currency_code = isset($curr['code']) ? $curr['code'] : '';
                    break;
                }
            }
        }

        // Language
        $language_code = '';
        if (!empty($info['languageCodes']) && is_array($info['languageCodes'])) {
            $language_code = $info['languageCodes'][0];
        }

        // Time zone
        $time_zone = '';
        if (!empty($info['timeZone'])) {
            $time_zone = $info['timeZone'];
        }

        $details = array(
            'hotel_code'          => $normalised['hotel_code'],
            'hotel_name'          => $normalised['name'],
            'chain_code'          => $chain_code,
            'chain_name'          => $chain_name,
            'currency_code'       => $currency_code,
            'language_code'       => $language_code,
            'time_zone'           => $time_zone,
            'description'         => $normalised['description'],
            'latitude'            => $normalised['latitude'],
            'longitude'           => $normalised['longitude'],
            'check_in_time'       => $check_in_time,
            'check_out_time'      => $check_out_time,
            'cancellation_policy' => $cancellation_policy,
            'guarantee_policy'    => $guarantee_policy,
            'pets_allowed'        => $pets_allowed,
            'commission_percent'  => $commission_percent,
            'raw_xml_data'        => wp_json_encode($normalised['raw']),
        );

        DHR_Hotel_Database::save_hotel_details($details);

        return array(
            'success'    => true,
            'hotel_id'   => $hotel_id,
            'hotel_code' => $normalised['hotel_code'],
            'hotel_name' => $normalised['name'],
        );
    }
}
