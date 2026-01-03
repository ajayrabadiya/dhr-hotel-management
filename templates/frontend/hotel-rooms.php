<?php
/**
 * Hotel Rooms Display Template
 * Matches the bys-rooms design structure
 */

if (!defined('ABSPATH')) {
    exit;
}

$hotel_code = $hotel_data['hotel_code'];
$hotel_name = $hotel_data['hotel_name'];
$rooms = $hotel_data['rooms'];
$columns = $hotel_data['columns'];
$show_images = $hotel_data['show_images'];
$show_amenities = $hotel_data['show_amenities'];
$show_description = $hotel_data['show_description'];

/**
 * Get amenity icon SVG
 */

function get_amenity_icon($amenity_name)
{
    // Map amenity names to icon font classes
    $icon_map = array(
        'Shower' => 'fa-dhr-shower',
        'Bidet' => 'fa-dhr-dish-washer',
        'Bath Tub' => 'fa-dhr-bathtub',
        'Bathrobes' => 'fa-dhr-bathroom',
        'DSTV' => 'fa-dhr-dstv',
        'Internet available' => 'fa-dhr-internet-available',
        'Safe' => 'fa-dhr-safe',
        'Air Conditioning' => 'fa-dhr-air-conditioning',
        'Minibar' => 'fa-dhr-minibar',
        'Balcony' => 'fa-dhr-balcony',
    );

    $icon_class = isset($icon_map[$amenity_name])
        ? $icon_map[$amenity_name]
        : 'fa-dhr-air-conditioning';

    return '<i aria-hidden="true" class="' . esc_attr($icon_class) . '"></i>';
}

/**
 * Format room description
 */
function format_room_description($room)
{
    $parts = array();

    // Bed type
    if (!empty($room->standard_num_beds)) {
        $bed_text = $room->standard_num_beds > 1 ? 'beds' : 'bed';
        $parts[] = $room->standard_num_beds . ' ' . $bed_text;
    }

    // Room type
    if (!empty($room->room_type_name)) {
        $parts[] = $room->room_type_name;
    }

    // Occupancy
    if (!empty($room->max_occupancy)) {
        $adults = $room->max_occupancy;
        $children = max(0, $adults - 2); // Estimate children
        if ($children > 0) {
            $parts[] = $adults . ' adults, ' . $children . ' child';
        } else {
            $parts[] = $adults . ' adults';
        }
    }

    return implode(' • ', $parts);
}
?>

<link rel='stylesheet' id='custom-icons-animation-css-css'
    href='https://dhr.4shaw-development.co/le-franschhoek-hotel-spa/wp-content/uploads/elementor/custom-icons/facilitiesandactivityicons/css/animation.css?ver=1743154111'
    media='all' />
<link rel='stylesheet' id='custom-icons-facilitiesandactivityicons-codes-css-css'
    href='https://dhr.4shaw-development.co/le-franschhoek-hotel-spa/wp-content/uploads/elementor/custom-icons/facilitiesandactivityicons/css/facilitiesandactivityicons-codes.css?ver=1743154111'
    media='all' />
<link rel='stylesheet' id='custom-icons-facilitiesandactivityicons-embedded-css-css'
    href='https://dhr.4shaw-development.co/le-franschhoek-hotel-spa/wp-content/uploads/elementor/custom-icons/facilitiesandactivityicons/css/facilitiesandactivityicons-embedded.css?ver=1743154111'
    media='all' />
<link rel='stylesheet' id='custom-icons-facilitiesandactivityicons-ie7-codes-css-css'
    href='https://dhr.4shaw-development.co/le-franschhoek-hotel-spa/wp-content/uploads/elementor/custom-icons/facilitiesandactivityicons/css/facilitiesandactivityicons-ie7-codes.css?ver=1743154111'
    media='all' />
<link rel='stylesheet' id='custom-icons-facilitiesandactivityicons-ie7-css-css'
    href='https://dhr.4shaw-development.co/le-franschhoek-hotel-spa/wp-content/uploads/elementor/custom-icons/facilitiesandactivityicons/css/facilitiesandactivityicons-ie7.css?ver=1743154111'
    media='all' />
<link rel='stylesheet' id='custom-icons-facilitiesandactivityicons-css-css'
    href='https://dhr.4shaw-development.co/le-franschhoek-hotel-spa/wp-content/uploads/elementor/custom-icons/facilitiesandactivityicons/css/facilitiesandactivityicons.css?ver=1743154111'
    media='all' />

<div class="bys-hotel-rooms">
    <div class="bys-rooms-grid" data-columns="<?php echo esc_attr($columns); ?>">
        <?php foreach ($rooms as $room):
            $room_images = !empty($room->images) && is_array($room->images) ? $room->images : array();
            $room_amenities = !empty($room->amenities) && is_array($room->amenities) ? $room->amenities : array();
            $first_image = !empty($room_images) ? $room_images[0] : 'https://dummyimage.com/1024x682/ccc/000';
            $has_images = $show_images && !empty($room_images);
            $room_price = 0; // Can be extended to get from pricing API
            ?>
            <div class="bys-room-card">
                <span class="bys-room-price"></span>
    
                <?php if ($has_images): ?>
                    <div class="bys-room-image">
                        <img src="<?php echo esc_url($first_image); ?>" alt="<?php echo esc_attr($room->room_type_name); ?>"
                            loading="lazy">
                        <div class="bys-room-price-badge">
                            <span class="bys-price-label">FROM</span>
                            <span class="bys-price-amount">R<?php echo esc_html($room_price); ?></span>
                            <span class="bys-price-period">/ NIGHT</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bys-room-image bys-room-image-placeholder">
                        <img src="<?php echo esc_url($first_image); ?>" alt="<?php echo esc_attr($room->room_type_name); ?>"
                            loading="lazy">
                        <div class="bys-room-price-badge">
                            <span class="bys-price-label">FROM</span>
                            <span class="bys-price-amount">R<?php echo esc_html($room_price); ?></span>
                            <span class="bys-price-period">/ NIGHT</span>
                        </div>
                    </div>
                <?php endif; ?>
    
                <div class="bys-room-content">
                    <h3 class="bys-room-title"><?php echo esc_html($room->room_type_name); ?></h3>
    
                    <?php if ($room->max_occupancy): ?>
                        <div class="bys-room-specs">
                            <span class="bys-room-specs-line"><?php echo esc_html($room->max_occupancy); ?>
                                <?php echo $room->max_occupancy == 1 ? __('Guest', 'dhr-hotel-management') : __('Guests', 'dhr-hotel-management'); ?></span>
                        </div>
                    <?php endif; ?>
    
                    <?php if ($show_amenities && !empty($room_amenities)): ?>
                        <ul class="bys-room-amenities">
                            <?php foreach ($room_amenities as $amenity):
                                $amenity_name = isset($amenity['name']) ? $amenity['name'] : (is_string($amenity) ? $amenity : '');
                                if (empty($amenity_name))
                                    continue;
                                ?>
                                <li class="bys-room-amenity-item">
                                    <span class="bys-amenity-icon">
                                        <?php echo get_amenity_icon($amenity_name); ?>
                                    </span>
                                    <span class="bys-amenity-text"><?php echo esc_html($amenity_name); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
    
                    <?php if ($show_description): ?>
                        <div class="bys-room-description">
                            <?php echo esc_html(format_room_description($room)); ?>
                        </div>
                    <?php endif; ?>
    
                    <div class="bys-room-actions">
                        <a href="#" class="bys-book-now-link" data-room-code="<?php echo esc_attr($room->room_type_code); ?>"
                            data-hotel-code="<?php echo esc_attr($hotel_code); ?>" data-property-id=""
                            data-checkin="<?php echo esc_attr(date('Y-m-d', strtotime('+1 day'))); ?>"
                            data-checkout="<?php echo esc_attr(date('Y-m-d', strtotime('+3 days'))); ?>"
                            data-adults="<?php echo esc_attr($room->max_occupancy ?: 2); ?>" data-children="0" data-rooms="1">
                            <?php _e('Book Now', 'dhr-hotel-management'); ?>
                        </a>
                        <!-- <a href="#" class="bys-book-now-button" data-room-code="<?php //echo esc_attr($room->room_type_code); ?>"
                            data-hotel-code="<?php //echo esc_attr($hotel_code); ?>" data-property-id=""
                            data-checkin="<?php //echo esc_attr(date('Y-m-d', strtotime('+1 day'))); ?>"
                            data-checkout="<?php //echo esc_attr(date('Y-m-d', strtotime('+3 days'))); ?>"
                            data-adults="<?php //echo esc_attr($room->max_occupancy ?: 2); ?>" data-children="0" data-rooms="1">
                            <?php //_e('Book Now', 'dhr-hotel-management'); ?>
                        </a> -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- First Package Design -->
<div class="bys-packages">
    <div class="bys-packages-grid">
        <div class="bys-packages-card">
            <div></div>
        </div> 
    </div>
</div>