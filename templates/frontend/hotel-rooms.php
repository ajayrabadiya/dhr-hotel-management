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
                        <!-- <a href="#" class="bys-book-now-button" data-room-code="<?php //echo esc_attr($room->room_type_code); 
                                                                                        ?>"
                            data-hotel-code="<?php //echo esc_attr($hotel_code); 
                                                ?>" data-property-id=""
                            data-checkin="<?php //echo esc_attr(date('Y-m-d', strtotime('+1 day'))); 
                                            ?>"
                            data-checkout="<?php //echo esc_attr(date('Y-m-d', strtotime('+3 days'))); 
                                            ?>"
                            data-adults="<?php //echo esc_attr($room->max_occupancy ?: 2); 
                                            ?>" data-children="0" data-rooms="1">
                            <?php //_e('Book Now', 'dhr-hotel-management'); 
                            ?>
                        </a> -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php $plugin_url = plugin_dir_url(dirname(__FILE__, 2)); ?>

<!-- Package Experience Design -->
<div class="bys-packages">

    <!-- First Package Design -->
    <div class="bys-packages-grid first-packages__design">
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Easter in the Winelands <span>(3 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Le Franschhoek Hotel & Spae</h6>
                            <p>Franschhoek, ZA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bys-package-content">
                <h4 class="package-content__title">INCLUDED</h4>
                <div>
                    <p class="package-content__description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>
                    <ul class="bys-package-features">
                        <li>Stay 4 nights over Easter Weekend 2024 and get 30% off the 4th night</li>
                        <li>An unforgettable 4-night stay in the beautiful winelands including breakfast</li>
                        <li>Kids under 12 years stay free, including breakfast</li>
                        <li>Easter Hunt included on Easter Sunday for the kids</li>
                    </ul>
                </div>
                <div class="package-content__divider"></div>
                <a href="#" class="bys-package-button">View Package</a>
            </div>
        </div>
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Easter in the Winelands <span>(3 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Le Franschhoek Hotel & Spae</h6>
                            <p>Franschhoek, ZA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bys-package-content">
                <h4 class="package-content__title">INCLUDED</h4>
                <div>
                    <p class="package-content__description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>
                    <ul class="bys-package-features">
                        <li>Stay 4 nights over Easter Weekend 2024 and get 30% off the 4th night</li>
                        <li>An unforgettable 4-night stay in the beautiful winelands including breakfast</li>
                        <li>Kids under 12 years stay free, including breakfast</li>
                        <li>Easter Hunt included on Easter Sunday for the kids</li>
                    </ul>
                </div>
                <div class="package-content__divider"></div>
                <a href="#" class="bys-package-button">View Package</a>
            </div>
        </div>
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Easter in the Winelands <span>(3 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Le Franschhoek Hotel & Spae</h6>
                            <p>Franschhoek, ZA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bys-package-content">
                <h4 class="package-content__title">INCLUDED</h4>
                <div>
                    <p class="package-content__description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>
                    <ul class="bys-package-features">
                        <li>Stay 4 nights over Easter Weekend 2024 and get 30% off the 4th night</li>
                        <li>An unforgettable 4-night stay in the beautiful winelands including breakfast</li>
                        <li>Kids under 12 years stay free, including breakfast</li>
                        <li>Easter Hunt included on Easter Sunday for the kids</li>
                    </ul>
                </div>
                <div class="package-content__divider"></div>
                <a href="#" class="bys-package-button">View Package</a>
            </div>
        </div>
    </div>
    <br><br>

    <!-- Second Package Design -->
    <div class="bys-packages-grid second-packages__design">
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Easter in the Winelands <span>(3 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Le Franschhoek Hotel & Spae</h6>
                            <p>Franschhoek, ZA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bys-package-content">
                <h4 class="package-content__title">INCLUDED</h4>
                <div>
                    <p class="package-content__description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>
                    <ul class="bys-package-features">
                        <li>Stay 4 nights over Easter Weekend 2024 and get 30% off the 4th night</li>
                        <li>An unforgettable 4-night stay in the beautiful winelands including breakfast</li>
                        <li>Kids under 12 years stay free, including breakfast</li>
                        <li>Easter Hunt included on Easter Sunday for the kids</li>
                    </ul>
                </div>
                <div class="package-content__divider"></div>
                <a href="#" class="bys-package-button button--theme-2">View Package</a>
            </div>
        </div>
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Easter in the Winelands <span>(3 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Le Franschhoek Hotel & Spae</h6>
                            <p>Franschhoek, ZA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bys-package-content">
                <h4 class="package-content__title">INCLUDED</h4>
                <div>
                    <p class="package-content__description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>
                    <ul class="bys-package-features">
                        <li>Stay 4 nights over Easter Weekend 2024 and get 30% off the 4th night</li>
                        <li>An unforgettable 4-night stay in the beautiful winelands including breakfast</li>
                        <li>Kids under 12 years stay free, including breakfast</li>
                        <li>Easter Hunt included on Easter Sunday for the kids</li>
                    </ul>
                </div>
                <div class="package-content__divider"></div>
                <a href="#" class="bys-package-button button--theme-2">View Package</a>
            </div>
        </div>
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Easter in the Winelands <span>(3 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Le Franschhoek Hotel & Spae</h6>
                            <p>Franschhoek, ZA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bys-package-content">
                <h4 class="package-content__title">INCLUDED</h4>
                <div>
                    <p class="package-content__description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>
                    <ul class="bys-package-features">
                        <li>Stay 4 nights over Easter Weekend 2024 and get 30% off the 4th night</li>
                        <li>An unforgettable 4-night stay in the beautiful winelands including breakfast</li>
                        <li>Kids under 12 years stay free, including breakfast</li>
                        <li>Easter Hunt included on Easter Sunday for the kids</li>
                    </ul>
                </div>
                <div class="package-content__divider"></div>
                <a href="#" class="bys-package-button button--theme-2">View Package</a>
            </div>
        </div>
    </div>
    <br><br>

    <!-- Third Package Design -->
    <div class="bys-packages-grid kids-packages__design">
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Kids stay & eat free</h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Jozini Tiger Lodge & Spa</h6>
                            <p>Jozini - KwaZulu-Natal</p>
                        </div>
                    </div>
                    <div class="package-overlay__btn-grp">
                        <a href="#" class="bys-package-button button--theme-3">View Package</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Reconnect Package <span>(2 Night Stay)</span></h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Jozini Tiger Lodge & Spa</h6>
                            <p>Jozini - KwaZulu-Natal</p>
                        </div>
                    </div>
                    <div class="package-overlay__btn-grp">
                        <a href="#" class="bys-package-button button--theme-3">View Package</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bys-packages-card">
            <div class="bys-packages-card__top">
                <div class="bys-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/1.png'); ?>')"></div>
                <div class="card__top-content">
                    <img class="top-left__icon" src="<?php echo esc_url($plugin_url . 'assets/images/icons/experience-icon.svg'); ?>" alt="">
                </div>
                <div class="package-overlay">
                    <span class="package-overlay__label">Package Experience</span>
                    <h3 class="package-overlay__title">Kids stay & eat free</h3>
                    <div class="package-overlay__divider">
                        <span></span>
                    </div>
                    <div class="package-overlay__location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                            <path d="M6 0.75C3.1084 0.75 0.75 3.1084 0.75 6C0.75 8.63672 2.71289 10.8135 5.25 11.1797V18H6.75V11.1797C9.28711 10.8135 11.25 8.63672 11.25 6C11.25 3.1084 8.8916 0.75 6 0.75ZM6 2.25C8.08008 2.25 9.75 3.91992 9.75 6C9.75 8.08008 8.08008 9.75 6 9.75C3.91992 9.75 2.25 8.08008 2.25 6C2.25 3.91992 3.91992 2.25 6 2.25ZM6 3C4.35059 3 3 4.35059 3 6H4.5C4.5 5.16211 5.16211 4.5 6 4.5V3Z"></path>
                        </svg>
                        <div>
                            <h6>Jozini Tiger Lodge & Spa</h6>
                            <p>Jozini - KwaZulu-Natal</p>
                        </div>
                    </div>
                    <div class="package-overlay__btn-grp">
                        <a href="#" class="bys-package-button button--theme-3">View Package</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>

    <!-- Forth Package Design -->
    <div class="early-bird-packages-grid">
        <div class="bird-packages-card">
            <div class="bird-packages-card__frature-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/2.png'); ?>')"></div>

            <div class="bird-packages-card__premium-img" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/package/primium-img.png'); ?>')"></div>
            <div class="card__bottom-content">
                <div class="card__top-badge">
                    <p class="package-overlay__tag">
                        Year End PACKAGE
                    </p>
                </div>
                <div class="package-overlay__content">
                    <div class="package-overlay__content__inner">
                        <h3 class="package-overlay__main-title">Hello Spring Package</h3>
                        <p class="package-overlay__description">Celebrate your wedding with the start of summer.</p>
                        <span class="package-overlay__valid">Valid: 1 Sep - 15 Oct 2024</span>
                    </div>
                    <a href="#" class="package-overlay__link">
                        View Package
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="path-1-inside-1_5416_79089" fill="white">
                                <path d="M5.67578 1.87305L5.04688 2.50195L9.54492 7L5.04688 11.498L5.67578 12.127L10.4883 7.31445L10.7891 7L10.4883 6.68555L5.67578 1.87305Z" />
                            </mask>
                            <path d="M5.67578 1.87305L5.04688 2.50195L9.54492 7L5.04688 11.498L5.67578 12.127L10.4883 7.31445L10.7891 7L10.4883 6.68555L5.67578 1.87305Z" fill="#EFF8FD" />
                            <path d="M5.67578 1.87305L6.38289 1.16594L5.67578 0.458833L4.96867 1.16594L5.67578 1.87305ZM5.04688 2.50195L4.33977 1.79485L3.63266 2.50195L4.33977 3.20906L5.04688 2.50195ZM9.54492 7L10.252 7.70711L10.9591 7L10.252 6.29289L9.54492 7ZM5.04688 11.498L4.33977 10.7909L3.63266 11.498L4.33977 12.2052L5.04688 11.498ZM5.67578 12.127L4.96867 12.8341L5.67578 13.5412L6.38289 12.8341L5.67578 12.127ZM10.4883 7.31445L11.1954 8.02156L11.2032 8.0137L11.2109 8.00568L10.4883 7.31445ZM10.7891 7L11.5117 7.69122L12.1729 7L11.5117 6.30878L10.7891 7ZM10.4883 6.68555L11.2109 5.99432L11.2032 5.9863L11.1954 5.97844L10.4883 6.68555ZM4.96867 1.16594L4.33977 1.79485L5.75398 3.20906L6.38289 2.58015L4.96867 1.16594ZM4.33977 3.20906L8.83782 7.70711L10.252 6.29289L5.75398 1.79485L4.33977 3.20906ZM8.83782 6.29289L4.33977 10.7909L5.75398 12.2052L10.252 7.70711L8.83782 6.29289ZM4.33977 12.2052L4.96867 12.8341L6.38289 11.4198L5.75398 10.7909L4.33977 12.2052ZM6.38289 12.8341L11.1954 8.02156L9.78117 6.60735L4.96867 11.4198L6.38289 12.8341ZM11.2109 8.00568L11.5117 7.69122L10.0664 6.30878L9.76564 6.62323L11.2109 8.00568ZM11.5117 6.30878L11.2109 5.99432L9.76564 7.37677L10.0664 7.69122L11.5117 6.30878ZM11.1954 5.97844L6.38289 1.16594L4.96867 2.58015L9.78117 7.39265L11.1954 5.97844Z" fill="#D3AA74" mask="url(#path-1-inside-1_5416_79089)" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>