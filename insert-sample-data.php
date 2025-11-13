<?php
/**
 * Script to insert sample hotel data
 * 
 * Usage: 
 * 1. EASIEST: Go to WordPress Admin > DHR Hotel Management > Insert Sample Data
 * 2. Access this file directly via browser: yoursite.com/wp-content/plugins/dhr-hotel-management/insert-sample-data.php
 * 3. Or run via WP-CLI: wp eval-file wp-content/plugins/dhr-hotel-management/insert-sample-data.php
 * 
 * Note: Make sure the plugin is activated first!
 */

// Load WordPress
if (!defined('ABSPATH')) {
    // Try to load WordPress
    $wp_load_paths = array(
        '../../../wp-load.php',
        '../../../../wp-load.php',
        dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php'
    );
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists(__DIR__ . '/' . $path)) {
            require_once(__DIR__ . '/' . $path);
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('Error: Could not load WordPress. Please use the admin panel method instead.');
    }
}

// Check if user is admin (for security)
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

global $wpdb;
$table_name = $wpdb->prefix . 'dhr_hotels';

// Sample hotel data
$hotels = array(
    array(
        'name' => 'Le Franschhoek Hotel & Spa',
        'description' => 'Luxurious hotel nestled in the heart of Franschhoek wine valley, offering world-class spa facilities and fine dining experiences.',
        'address' => '16 Akademie Street',
        'city' => 'Franschhoek',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9075,
        'longitude' => 19.1234,
        'phone' => '+27 (0)21 876 8900',
        'email' => 'info@lefranschhoek.co.za',
        'website' => 'https://www.lefranschhoek.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9075,19.1234',
        'status' => 'active'
    ),
    array(
        'name' => 'Cape Town Waterfront Hotel',
        'description' => 'Modern 5-star hotel overlooking the V&A Waterfront with stunning views of Table Mountain and the harbor.',
        'address' => '17 Dock Road, V&A Waterfront',
        'city' => 'Cape Town',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9048,
        'longitude' => 18.4211,
        'phone' => '+27 (0)21 419 2000',
        'email' => 'reservations@ctwaterfront.co.za',
        'website' => 'https://www.ctwaterfront.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9048,18.4211',
        'status' => 'active'
    ),
    array(
        'name' => 'Stellenbosch Vineyard Estate',
        'description' => 'Boutique hotel set among rolling vineyards, offering wine tastings and gourmet cuisine in a tranquil setting.',
        'address' => 'R44, Annandale Road',
        'city' => 'Stellenbosch',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9321,
        'longitude' => 18.8602,
        'phone' => '+27 (0)21 880 0100',
        'email' => 'stay@vineyardestate.co.za',
        'website' => 'https://www.vineyardestate.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9321,18.8602',
        'status' => 'active'
    ),
    array(
        'name' => 'Hermanus Ocean View Resort',
        'description' => 'Beachfront resort with panoramic ocean views, perfect for whale watching during season.',
        'address' => 'Marine Drive, Westcliff',
        'city' => 'Hermanus',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -34.4186,
        'longitude' => 19.2345,
        'phone' => '+27 (0)28 312 3456',
        'email' => 'bookings@hermanusresort.co.za',
        'website' => 'https://www.hermanusresort.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-34.4186,19.2345',
        'status' => 'active'
    ),
    array(
        'name' => 'Knysna Lagoon Lodge',
        'description' => 'Elegant lodge on the Knysna Lagoon offering water activities, fine dining, and access to the Garden Route.',
        'address' => 'Thesen Island, Knysna Quays',
        'city' => 'Knysna',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -34.0351,
        'longitude' => 23.0465,
        'phone' => '+27 (0)44 382 5500',
        'email' => 'info@knysnalodge.co.za',
        'website' => 'https://www.knysnalodge.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-34.0351,23.0465',
        'status' => 'active'
    ),
    array(
        'name' => 'Paarl Mountain Retreat',
        'description' => 'Secluded mountain retreat offering spa treatments, hiking trails, and breathtaking valley views.',
        'address' => 'R301, Paarl Mountain Road',
        'city' => 'Paarl',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.7300,
        'longitude' => 18.9750,
        'phone' => '+27 (0)21 872 4848',
        'email' => 'retreat@paarlmountain.co.za',
        'website' => 'https://www.paarlmountain.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.7300,18.9750',
        'status' => 'active'
    ),
    array(
        'name' => 'Cape Winelands Boutique Hotel',
        'description' => 'Intimate boutique hotel in the heart of wine country, featuring elegant rooms and award-winning restaurant.',
        'address' => 'Main Road, Franschhoek',
        'city' => 'Franschhoek',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9147,
        'longitude' => 19.1244,
        'phone' => '+27 (0)21 876 2145',
        'email' => 'reservations@winelandshotel.co.za',
        'website' => 'https://www.winelandshotel.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9147,19.1244',
        'status' => 'active'
    ),
    array(
        'name' => 'Table Mountain View Hotel',
        'description' => 'Contemporary hotel with direct views of Table Mountain, located in the vibrant city center.',
        'address' => 'Long Street 123',
        'city' => 'Cape Town',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9249,
        'longitude' => 18.4241,
        'phone' => '+27 (0)21 422 8888',
        'email' => 'book@tablemountainview.co.za',
        'website' => 'https://www.tablemountainview.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9249,18.4241',
        'status' => 'active'
    ),
    array(
        'name' => 'Garden Route Safari Lodge',
        'description' => 'Luxury safari lodge combining wildlife experiences with modern comfort, set in a private game reserve.',
        'address' => 'N2 Highway, Wilderness',
        'city' => 'Wilderness',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9816,
        'longitude' => 22.5687,
        'phone' => '+27 (0)44 877 1199',
        'email' => 'safari@gardenroute.co.za',
        'website' => 'https://www.gardenroutesafari.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9816,22.5687',
        'status' => 'active'
    ),
    array(
        'name' => 'Robben Island Heritage Hotel',
        'description' => 'Historic hotel near Robben Island ferry terminal, offering cultural tours and waterfront dining.',
        'address' => 'V&A Waterfront, Breakwater Boulevard',
        'city' => 'Cape Town',
        'province' => 'Western Cape',
        'country' => 'South Africa',
        'latitude' => -33.9068,
        'longitude' => 18.4233,
        'phone' => '+27 (0)21 419 5000',
        'email' => 'heritage@robbenislandhotel.co.za',
        'website' => 'https://www.robbenislandhotel.co.za',
        'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
        'google_maps_url' => 'https://www.google.com/maps?q=-33.9068,18.4233',
        'status' => 'active'
    )
);

// Insert hotels
$inserted = 0;
$errors = array();

foreach ($hotels as $hotel) {
    $result = $wpdb->insert(
        $table_name,
        $hotel,
        array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result === false) {
        $errors[] = 'Failed to insert: ' . $hotel['name'] . ' - ' . $wpdb->last_error;
    } else {
        $inserted++;
    }
}

// Display results
echo '<h1>Sample Hotel Data Insertion</h1>';
echo '<p><strong>Successfully inserted:</strong> ' . $inserted . ' hotels</p>';

if (!empty($errors)) {
    echo '<h2>Errors:</h2>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li>' . esc_html($error) . '</li>';
    }
    echo '</ul>';
}

if ($inserted > 0) {
    echo '<p><a href="' . admin_url('admin.php?page=dhr-hotel-management') . '">View Hotels in Admin</a></p>';
}

