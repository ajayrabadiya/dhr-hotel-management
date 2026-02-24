<?php
/**
 * Where To Find Us Map Template (Map 9)
 * Single-hotel map with location details, image card, and contact info.
 */

if (!defined('ABSPATH')) {
    exit;
}

$hotel = null;
if (!empty($hotels) && is_array($hotels)) {
    $hotel = reset($hotels);
}

if (!$hotel) {
    return;
}

$heading = isset($settings['main_heading']) ? $settings['main_heading'] : 'Where To Find Us';
$address_text = isset($settings['address_text']) ? $settings['address_text'] : '';
$phone_label = isset($settings['phone_label']) ? $settings['phone_label'] : '';
$phone_number = isset($settings['phone_number']) ? $settings['phone_number'] : '';
$email_address = isset($settings['email_address']) ? $settings['email_address'] : '';
$gps_coordinates = isset($settings['gps_coordinates']) ? $settings['gps_coordinates'] : '';
$enquire_text = isset($settings['enquire_text']) ? $settings['enquire_text'] : 'Enquire now';
$enquire_url = isset($settings['enquire_url']) ? $settings['enquire_url'] : '';
$logo_url = isset($settings['logo_url']) ? $settings['logo_url'] : '';
$bg_color = isset($settings['bg_color']) ? $settings['bg_color'] : '#8FA7BF';

if (empty($address_text) && !empty($hotel->address)) {
    $address_text = $hotel->address;
    if (!empty($hotel->city)) $address_text .= ', ' . $hotel->city;
    if (!empty($hotel->province)) $address_text .= ', ' . $hotel->province;
}
if (empty($phone_number) && !empty($hotel->phone)) {
    $phone_number = $hotel->phone;
}
if (empty($email_address) && !empty($hotel->email)) {
    $email_address = $hotel->email;
}

$lat = isset($hotel->latitude) ? floatval($hotel->latitude) : 0;
$lng = isset($hotel->longitude) ? floatval($hotel->longitude) : 0;
$hotel_name = isset($hotel->name) ? $hotel->name : '';
$hotel_image = isset($hotel->image_url) ? $hotel->image_url : '';
$google_maps_url = isset($hotel->google_maps_url) ? $hotel->google_maps_url : '';
if (empty($google_maps_url) && $lat && $lng) {
    $google_maps_url = 'https://www.google.com/maps?q=' . $lat . ',' . $lng;
}
?>

<div class="dhr-wtfu-wrapper" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-wtfu-map-side">
        <div id="dhr-wtfu-gmap" class="dhr-wtfu-gmap"
             data-lat="<?php echo esc_attr($lat); ?>"
             data-lng="<?php echo esc_attr($lng); ?>"
             data-name="<?php echo esc_attr($hotel_name); ?>"></div>
    </div>

    <div class="dhr-wtfu-info-side" style="background-color: <?php echo esc_attr($bg_color); ?>;">
        <div class="dhr-wtfu-hotel-card">
            <?php if (!empty($hotel_image)): ?>
                <div class="dhr-wtfu-hotel-image">
                    <img src="<?php echo esc_url($hotel_image); ?>" alt="<?php echo esc_attr($hotel_name); ?>"
                         onerror="this.onerror=null; this.src='<?php echo esc_url(DHR_HOTEL_PLUGIN_URL); ?>assets/images/default-hotel.jpg';">
                </div>
            <?php endif; ?>
            <?php if (!empty($logo_url)): ?>
                <div class="dhr-wtfu-hotel-logo">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($hotel_name); ?> Logo">
                </div>
            <?php endif; ?>
            <?php if (!empty($enquire_text)): ?>
                <a class="dhr-wtfu-enquire-btn"
                   href="<?php echo !empty($enquire_url) ? esc_url($enquire_url) : '#'; ?>"
                   <?php echo !empty($enquire_url) ? 'target="_blank"' : ''; ?>>
                    <?php echo esc_html($enquire_text); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="dhr-wtfu-contact-panel">
            <h2 class="dhr-wtfu-heading"><?php echo esc_html($heading); ?></h2>

            <?php if (!empty($address_text)): ?>
                <p class="dhr-wtfu-address"><?php echo esc_html($address_text); ?></p>
            <?php endif; ?>

            <?php if (!empty($phone_number)): ?>
                <p class="dhr-wtfu-phone">
                    <?php if (!empty($phone_label)): ?>
                        <span class="dhr-wtfu-phone-label"><?php echo esc_html($phone_label); ?></span>
                    <?php endif; ?>
                    <a href="tel:<?php echo esc_attr($phone_number); ?>"><?php echo esc_html($phone_number); ?></a>
                </p>
            <?php endif; ?>

            <?php if (!empty($email_address)): ?>
                <p class="dhr-wtfu-email">
                    <a href="mailto:<?php echo esc_attr($email_address); ?>"><?php echo esc_html($email_address); ?></a>
                </p>
            <?php endif; ?>

            <?php if (!empty($gps_coordinates)): ?>
                <p class="dhr-wtfu-gps">GPS: <?php echo esc_html($gps_coordinates); ?></p>
            <?php endif; ?>

            <?php if (!empty($google_maps_url)): ?>
                <a class="dhr-wtfu-gmaps-btn" href="<?php echo esc_url($google_maps_url); ?>" target="_blank">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 0C5.69 0 3 2.69 3 6c0 4.5 6 12 6 12s6-7.5 6-12c0-3.31-2.69-6-6-6zm0 8.5c-1.38 0-2.5-1.12-2.5-2.5S7.62 3.5 9 3.5s2.5 1.12 2.5 2.5S10.38 8.5 9 8.5z" fill="currentColor"/>
                    </svg>
                    Google Maps
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    function initWhereToFindUsMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            return;
        }

        var mapEl = document.getElementById('dhr-wtfu-gmap');
        if (!mapEl) return;

        var lat = parseFloat(mapEl.getAttribute('data-lat')) || 0;
        var lng = parseFloat(mapEl.getAttribute('data-lng')) || 0;
        var name = mapEl.getAttribute('data-name') || '';

        if (lat === 0 && lng === 0) return;

        var position = { lat: lat, lng: lng };

        var map = new google.maps.Map(mapEl, {
            zoom: 14,
            center: position,
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            styles: [
                { featureType: 'all', elementType: 'geometry', stylers: [{ color: '#f5f5f5' }] },
                { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#A0B6CB' }] },
                { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
                { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#9e9e9e' }] }
            ]
        });

        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: name,
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(
                    '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<circle opacity="0.15" cx="18" cy="18" r="18" fill="#4A6F8F"/>' +
                    '<circle opacity="0.3" cx="18" cy="18" r="12" fill="#4A6F8F"/>' +
                    '<circle cx="18" cy="18" r="6" fill="#2C5F8A"/>' +
                    '</svg>'
                ),
                scaledSize: new google.maps.Size(36, 36),
                anchor: new google.maps.Point(18, 18)
            }
        });

        var infoWindow = new google.maps.InfoWindow({ content: '<div style="font-size:14px;font-weight:600;padding:4px 0;">' + name + '</div>' });
        marker.addListener('click', function () { infoWindow.open(map, marker); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                initWhereToFindUsMap();
            } else {
                window.addEventListener('load', function () { setTimeout(initWhereToFindUsMap, 1000); });
            }
        });
    } else {
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initWhereToFindUsMap();
        } else {
            window.addEventListener('load', function () { setTimeout(initWhereToFindUsMap, 1000); });
        }
    }
})();
</script>

<style>
.dhr-wtfu-wrapper {
    display: flex;
    width: 100%;
    position: relative;
    overflow: hidden;
    min-height: 400px;
}

.dhr-wtfu-map-side {
    flex: 0 0 50%;
    position: relative;
}

.dhr-wtfu-gmap {
    width: 100%;
    height: 100%;
    min-height: 400px;
}

.dhr-wtfu-info-side {
    flex: 0 0 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    padding: 40px 50px;
    position: relative;
}

.dhr-wtfu-hotel-card {
    position: absolute;
    left: -80px;
    top: 50%;
    transform: translateY(-50%);
    width: 260px;
    background: #fff;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    overflow: hidden;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.dhr-wtfu-hotel-image {
    width: 100%;
    height: 180px;
    overflow: hidden;
}

.dhr-wtfu-hotel-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.dhr-wtfu-hotel-logo {
    padding: 20px;
    text-align: center;
}

.dhr-wtfu-hotel-logo img {
    max-width: 120px;
    max-height: 70px;
    object-fit: contain;
}

.dhr-wtfu-enquire-btn {
    display: block;
    width: 100%;
    padding: 14px 20px;
    background-color: #5A7D9A;
    color: #fff !important;
    text-align: center;
    text-decoration: none !important;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: background-color 0.3s ease;
}

.dhr-wtfu-enquire-btn:hover {
    background-color: #4A6A85;
    color: #fff !important;
}

.dhr-wtfu-contact-panel {
    margin-left: 200px;
    max-width: 380px;
    color: #1a1a2e;
}

.dhr-wtfu-heading {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a2e;
    margin: 0 0 20px 0;
    line-height: 1.3;
    font-family: inherit;
}

.dhr-wtfu-address {
    font-size: 15px;
    line-height: 1.6;
    color: #333;
    margin: 0 0 16px 0;
}

.dhr-wtfu-phone {
    margin: 0 0 8px 0;
    font-size: 15px;
}

.dhr-wtfu-phone-label {
    display: block;
    font-weight: 600;
    color: #2C5F8A;
    font-size: 14px;
}

.dhr-wtfu-phone a {
    color: #2C5F8A;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
}

.dhr-wtfu-phone a:hover {
    text-decoration: underline;
}

.dhr-wtfu-email {
    margin: 0 0 12px 0;
}

.dhr-wtfu-email a {
    color: #333;
    text-decoration: none;
    font-size: 14px;
}

.dhr-wtfu-email a:hover {
    text-decoration: underline;
}

.dhr-wtfu-gps {
    font-size: 13px;
    color: #555;
    margin: 0 0 20px 0;
}

.dhr-wtfu-gmaps-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border: 2px solid #2C5F8A;
    color: #2C5F8A !important;
    text-decoration: none !important;
    font-size: 14px;
    font-weight: 600;
    border-radius: 2px;
    transition: all 0.3s ease;
    background: #fff;
}

.dhr-wtfu-gmaps-btn:hover {
    background: #2C5F8A;
    color: #fff !important;
}

.dhr-wtfu-gmaps-btn svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* Tablet */
@media (max-width: 1024px) {
    .dhr-wtfu-info-side {
        padding: 30px 30px;
    }

    .dhr-wtfu-hotel-card {
        left: -60px;
        width: 220px;
    }

    .dhr-wtfu-hotel-image {
        height: 150px;
    }

    .dhr-wtfu-contact-panel {
        margin-left: 170px;
    }

    .dhr-wtfu-heading {
        font-size: 24px;
    }
}

/* Mobile */
@media (max-width: 768px) {
    .dhr-wtfu-wrapper {
        flex-direction: column;
        height: auto !important;
    }

    .dhr-wtfu-map-side {
        flex: none;
        height: 300px;
    }

    .dhr-wtfu-gmap {
        min-height: 300px;
    }

    .dhr-wtfu-info-side {
        flex: none;
        flex-direction: column;
        padding: 30px 20px;
        gap: 20px;
    }

    .dhr-wtfu-hotel-card {
        position: relative;
        left: auto;
        top: auto;
        transform: none;
        width: 100%;
        max-width: 300px;
        margin: -60px auto 0;
        z-index: 10;
    }

    .dhr-wtfu-contact-panel {
        margin-left: 0;
        text-align: center;
        max-width: 100%;
    }

    .dhr-wtfu-gmaps-btn {
        justify-content: center;
    }
}
</style>
