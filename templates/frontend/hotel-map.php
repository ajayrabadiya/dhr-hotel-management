<?php
/**
 * Frontend hotel map template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="dhr-hotel-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-hotel-info-panel">
        <div class="dhr-hotel-info-content">
            <h2 class="dhr-location-heading">LOCATED IN THE WESTERN CAPE</h2>
            <h3 class="dhr-main-heading">Find Us</h3>
            <p class="dhr-description">
                <?php _e('Discover our hotel locations across the Western Cape. Click on any marker to view hotel details and make a reservation.', 'dhr-hotel-management'); ?>
            </p>
            
            <?php if (!empty($hotels)): ?>
                <div class="dhr-hotels-list">
                    <?php foreach ($hotels as $hotel): ?>
                        <div class="dhr-hotel-item" data-hotel-id="<?php echo esc_attr($hotel->id); ?>">
                            <h4><?php echo esc_html($hotel->name); ?></h4>
                            <p><?php echo esc_html($hotel->city . ', ' . $hotel->province); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($hotels) && isset($hotels[0])): ?>
                <?php $first_hotel = $hotels[0]; ?>
                <div class="dhr-reservation-info">
                    <div class="dhr-phone-section">
                        <span class="dhr-phone-icon">📞</span>
                        <div>
                            <p class="dhr-reservation-label">RESERVATION BY PHONE</p>
                            <p class="dhr-phone-number"><?php echo esc_html($first_hotel->phone); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dhr-hotel-map-panel">
        <div id="dhr-hotel-map" class="dhr-hotel-map"></div>
    </div>
</div>

<div id="dhr-hotel-info-window-template" style="display: none;">
    <div class="dhr-info-window">
        <div class="dhr-info-window-image">
            <img src="{image_url}" alt="{name}" onerror="this.src='<?php echo DHR_HOTEL_PLUGIN_URL; ?>assets/images/default-hotel.jpg';">
        </div>
        <div class="dhr-info-window-content">
            <h3 class="dhr-info-window-title">{name}</h3>
            <p class="dhr-info-window-location">{city} | {province}</p>
            <div class="dhr-info-window-actions">
                <a href="{google_maps_url}" target="_blank" class="dhr-btn-info">
                    <span class="dhr-icon">ℹ</span>
                </a>
                <a href="tel:{phone}" class="dhr-btn-book">
                    Book Now
                </a>
            </div>
        </div>
    </div>
</div>


