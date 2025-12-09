<?php
/**
 * Head Office Map Template (Map 2)
 */

if (!defined('ABSPATH')) {
    exit;
}

$title = isset($settings['title']) ? $settings['title'] : 'Head Office';
$address = isset($settings['address']) ? $settings['address'] : '310 Main Road, Bryanston 2021, Gauteng, South Africa';
$phone1 = isset($settings['phone1']) ? $settings['phone1'] : '+27 (0) 11 267 8300';
$phone2 = isset($settings['phone2']) ? $settings['phone2'] : '+27 861 010 347';
$po_box = isset($settings['po_box']) ? $settings['po_box'] : '86027, Sandton 2146, Gauteng, South Africa';
$email = isset($settings['email']) ? $settings['email'] : 'info@dreamresorts.co.za';
$trade_phone = isset($settings['trade_phone']) ? $settings['trade_phone'] : '+27 (0) 11 267 8300';
$trade_email = isset($settings['trade_email']) ? $settings['trade_email'] : 'trade@dreamresorts.co.za';
$complaints_phone = isset($settings['complaints_phone']) ? $settings['complaints_phone'] : '+27 (0) 11 267 8300';
$complaints_email = isset($settings['complaints_email']) ? $settings['complaints_email'] : 'complaints@dreamresorts.co.za';
$latitude = isset($settings['latitude']) ? $settings['latitude'] : '';
$longitude = isset($settings['longitude']) ? $settings['longitude'] : '';
$google_maps_url = isset($settings['google_maps_url']) ? $settings['google_maps_url'] : '';
$twitter_url = isset($settings['twitter_url']) ? $settings['twitter_url'] : '#';
$instagram_url = isset($settings['instagram_url']) ? $settings['instagram_url'] : '#';
$facebook_url = isset($settings['facebook_url']) ? $settings['facebook_url'] : '#';
$linkedin_url = isset($settings['linkedin_url']) ? $settings['linkedin_url'] : '#';
$youtube_url = isset($settings['youtube_url']) ? $settings['youtube_url'] : '#';
?>

<style>
.dhr-marker-pulse {
    position: absolute;
    pointer-events: none;
    transform-origin: center center;
    z-index: 0;
    overflow: visible;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
}

.dhr-marker-pulse svg {
    display: block;
    overflow: visible;
    border: none !important;
    outline: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.dhr-marker-pulse .pulse-outer-circle {
    transform-origin: center;
    animation: pulse-outer 2s ease-in-out infinite;
}

.dhr-marker-pulse .pulse-middle-circle {
    transform-origin: center;
    animation: pulse-middle 2s ease-in-out infinite;
}

.dhr-marker-pulse.dhr-marker-pulse-active .pulse-outer-circle {
    animation: pulse-outer-active 2s ease-in-out infinite;
}

.dhr-marker-pulse.dhr-marker-pulse-active .pulse-middle-circle {
    animation: pulse-middle-active 2s ease-in-out infinite;
}

/* --- FIXED + SMOOTHED ANIMATIONS BELOW --- */
@keyframes pulse-outer {
    0%   { transform: scale(1);   opacity: 0.15; }
    50%  { transform: scale(1.7); opacity: 0.35; }
    100% { transform: scale(1);   opacity: 0.15; }
}

@keyframes pulse-middle {
    0%   { transform: scale(1);    opacity: 0.35; }
    50%  { transform: scale(1.45); opacity: 0.55; }
    100% { transform: scale(1);    opacity: 0.35; }
}

@keyframes pulse-outer-active {
    0%   { transform: scale(1);   opacity: 0.20; }
    50%  { transform: scale(1.55); opacity: 0.40; }
    100% { transform: scale(1);   opacity: 0.20; }
}

@keyframes pulse-middle-active {
    0%   { transform: scale(1);    opacity: 0.45; }
    50%  { transform: scale(1.35); opacity: 0.75; }
    100% { transform: scale(1);    opacity: 0.45; }
}
</style>

<div class="dhr-head-office-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-head-office-map-area">
        <div id="dhr-head-office-map" class="dhr-head-office-map"></div>
    </div>
    <!-- <div class="dhr-map-container">
        <div class="dhr-head-office-area">
            <div class="dhr-map-row justify-content-between">
                <div class="dhr-head-office-left">
                    <h2 class="dhr-map-title dhr-text-primary mb-0"><?php echo esc_html($title); ?></h2>
                </div>
                <div class="dhr-head-office-right">
                    <?php if (!empty($google_maps_url)): ?>
                        <a href="<?php echo esc_url($google_maps_url); ?>" target="_blank" rel="noopener noreferrer" class="dhr-google-maps-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2C6.13 2 3 5.13 3 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S8.62 6.5 10 6.5 12.5 7.62 12.5 9 11.38 11.5 10 11.5z" fill="currentColor"/>
                            </svg>
                            View On Google Maps
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dhr-map-row justify-content-between">
                <div class="dhr-head-office-left">
                    <div class="dhr-head-office-block">
                        <ul>
                            <li>
                                Address: <?php echo esc_html($address); ?>
                            </li>
                            <li>
                                Phone Number: <?php echo esc_html($phone1); ?>
                            </li>
                            <li>
                                Phone Number: <?php echo esc_html($phone2); ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="dhr-head-office-right">
                    <div class="dhr-head-office-block">
                        <ul>
                            <li>
                                PO Box: <?php echo esc_html($po_box); ?>
                            </li>
                            <li>
                                Email: <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            </li>
                            <li>
                                <div class="dhr-head-office-social-list">
                                    <ul>
                                        <li>
                                            <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="dhr-social-icon">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer" class="dhr-social-icon">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="dhr-social-icon">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" class="dhr-social-icon">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer" class="dhr-social-icon">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="dhr-map-row justify-content-between">
                <div class="dhr-head-office-left">
                    <div class="dhr-head-office-block">
                        <h4 class="dhr-head-office-block-subtitle dhr-text-primary">Trade Enquiries</h4>
                        <ul>
                            <li>
                                Phone Number: <?php echo esc_html($trade_phone); ?>
                            </li>
                            <li>
                                Email: <a href="mailto:<?php echo esc_attr($trade_email); ?>"><?php echo esc_html($trade_email); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="dhr-head-office-right">
                    <div class="dhr-head-office-block">
                        <h4 class="dhr-head-office-block-subtitle dhr-text-primary">Complaints</h4>
                        <ul>
                            <li>
                                Phone Number: <?php echo esc_html($complaints_phone); ?>
                            </li>
                            <li>
                                Email: <a href="mailto:<?php echo esc_attr($complaints_email); ?>"><?php echo esc_html($complaints_email); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>

<script>
(function() {
    'use strict';

    var map;
    var markers = [];
    var infoWindows = [];
    var pulseOverlays = {}; // Store pulse overlay elements for each marker
    var activeMarker = null; // Track currently active marker
    var PulseOverlay; // Will be defined after Google Maps loads

    // Detect if device is mobile
    function isMobileDevice() {
        return window.innerWidth <= 991;
    }

    // Function to define PulseOverlay class (called after Google Maps loads)
    function definePulseOverlay() {
        // Custom Overlay for Pulse Effect
        PulseOverlay = function(position, map, isActive) {
            this.position = position;
            this.map = map;
            this.isActive = isActive;
            this.div = null;
            this.setMap(map);
        };

        PulseOverlay.prototype = new google.maps.OverlayView();

        PulseOverlay.prototype.onAdd = function () {
            var div = document.createElement('div');
            div.className = 'dhr-marker-pulse';
            div.classList.add('dhr-marker-pulse-active');
            
            // Create SVG structure matching the EXACT marker design
            var size = 57;
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', size);
            svg.setAttribute('height', size);
            svg.setAttribute('viewBox', '0 0 57 57');
            svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
            svg.style.width = '100%';
            svg.style.height = '100%';
            svg.style.display = 'block';
            
            // Active marker structure - EXACT match with lighter shades
            // Outer circle (pulsing)
            var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            outerCircle.setAttribute('cx', '28.314');
            outerCircle.setAttribute('cy', '28.314');
            outerCircle.setAttribute('r', '28.314');
            outerCircle.setAttribute('fill', '#B8E3FF');
            outerCircle.setAttribute('opacity', '0.1');
            outerCircle.classList.add('pulse-outer-circle');
            svg.appendChild(outerCircle);
            
            // Middle circle (pulsing)
            var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            middleCircle.setAttribute('cx', '27.8784');
            middleCircle.setAttribute('cy', '28.7496');
            middleCircle.setAttribute('r', '20.9088');
            middleCircle.setAttribute('fill', '#7BC9FF');
            middleCircle.setAttribute('opacity', '0.3');
            middleCircle.classList.add('pulse-middle-circle');
            svg.appendChild(middleCircle);
            
            div.appendChild(svg);
            this.div = div;

            var panes = this.getPanes();
            panes.overlayLayer.appendChild(div);
            
            // Force initial draw
            this.draw();
        };

        PulseOverlay.prototype.draw = function () {
            var overlayProjection = this.getProjection();
            if (!overlayProjection) {
                return;
            }

            var position = overlayProjection.fromLatLngToDivPixel(this.position);

            if (this.div) {
                var size = 57;
                // Match the anchor point of the marker exactly
                // Active marker: anchor (12.5, 12.5), size 57x57
                var anchorOffset = 12.5;
                this.div.style.left = (position.x - anchorOffset) + 'px';
                this.div.style.top = (position.y - anchorOffset) + 'px';
                this.div.style.width = size + 'px';
                this.div.style.height = size + 'px';
                this.div.style.margin = '0';
                this.div.style.padding = '0';
                this.div.style.border = 'none';
                this.div.style.outline = 'none';
                
                // Ensure the pulse animation continues
                if (!this.div.classList.contains('dhr-marker-pulse-active')) {
                    this.div.classList.add('dhr-marker-pulse-active');
                }
            }
        };

        PulseOverlay.prototype.onRemove = function () {
            if (this.div && this.div.parentNode) {
                this.div.parentNode.removeChild(this.div);
                this.div = null;
            }
        };
    }

    function initHeadOfficeMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        // Define PulseOverlay class now that Google Maps is loaded
        definePulseOverlay();

        // Check if the map element exists
        var mapElement = document.getElementById('dhr-head-office-map');
        if (!mapElement) {
            // Map element doesn't exist, this script is not needed
            return;
        }

        if (!dhrHotelsData || !dhrHotelsData.hotels || dhrHotelsData.hotels.length === 0) {
            console.warn('No hotels data available');
            return;
        }

        var hotels = dhrHotelsData.hotels;

        // Calculate center of all hotels
        var bounds = new google.maps.LatLngBounds();
        var centerLat = 0;
        var centerLng = 0;

        hotels.forEach(function (hotel) {
            centerLat += parseFloat(hotel.latitude);
            centerLng += parseFloat(hotel.longitude);
            bounds.extend(new google.maps.LatLng(
                parseFloat(hotel.latitude),
                parseFloat(hotel.longitude)
            ));
        });

        centerLat = centerLat / hotels.length;
        centerLng = centerLng / hotels.length;

        // Initialize map
        map = new google.maps.Map(document.getElementById('dhr-head-office-map'), {
            zoom: 10,
            center: { lat: centerLat, lng: centerLng },
            styles: [
                {
                    featureType: 'all',
                    elementType: 'geometry',
                    stylers: [{ color: '#f5f5f5' }]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{ color: '#e0e0e0' }]
                },
                // {
                //     featureType: 'road',
                //     elementType: 'labels.text.fill',
                //     stylers: [{ color: '#c9c9c9' }]
                // }
            ]
        });

        // Fit bounds to show all hotels
        if (hotels.length > 1) {
            map.fitBounds(bounds);
        }

        // Create markers for each hotel
        hotels.forEach(function (hotel, index) {
            createMarker(hotel, index);
        });
    }

    function createMarker(hotel, index) {
        var position = {
            lat: parseFloat(hotel.latitude),
            lng: parseFloat(hotel.longitude)
        };

        // Create normal marker icon
        var normalIcon = createNormalMarkerIcon();

        // Create marker
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: hotel.name,
            icon: normalIcon,
            animation: index === 0 ? google.maps.Animation.DROP : null
        });

        // Create info window content
        var infoWindowContent = getInfoWindowContent(hotel);

        // Create info window
        var infoWindow = new google.maps.InfoWindow({
            content: infoWindowContent
        });

        // Add click listener to marker
        marker.addListener('click', function () {
            // Set all markers to normal
            setAllMarkersToNormal();

            // Set this marker to active
            setMarkerToActive(marker);
            activeMarker = marker;

            // Close all other info windows
            infoWindows.forEach(function (iw) {
                iw.close();
            });

            // Open this info window
            infoWindow.open(map, marker);

            // Center map with mobile offset if needed
            centerMapOnMarker(marker, infoWindow);
        });


        // Store marker and info window
        markers.push({
            marker: marker,
            infoWindow: infoWindow,
            hotelId: hotel.id
        });

        infoWindows.push(infoWindow);

        // Open first hotel's info window by default
        if (index === 0) {
            setTimeout(function () {
                // Set all markers to normal first
                setAllMarkersToNormal();
                // Set first marker to active
                setMarkerToActive(marker);
                activeMarker = marker;
                infoWindow.open(map, marker);
                
                // Center map with mobile offset if needed
                centerMapOnMarker(marker, infoWindow);
            }, 500);
        }
    }

    function getInfoWindowContent(hotel) {
        var content = '<div class="dhr-info-window">' +
            '<h4 class="dhr-info-window-title">' + escapeHtml(hotel.name) + '</h4>' +
            '<p class="dhr-info-window-content">' + escapeHtml(hotel.address || '') + '</p>' +
            '<p class="dhr-info-window-content mb-0">' + escapeHtml(hotel.city) + ', ' + escapeHtml(hotel.province) + '</p>' +
            '</div>';

        return content;
    }
    
    function createNormalMarkerIcon() {
        // Create SVG for normal map marker - outer 2 circles with lighter shades
        var svg = '<svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.6" cx="13.068" cy="13.068" r="13.068" fill="#B8E3FF"/><circle opacity="0.3" cx="13.068" cy="13.0681" r="6.0984" fill="#7BC9FF"/><circle cx="13.068" cy="13.0681" r="6.0984" fill="#062943"/></svg>';

        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(27, 27),
            anchor: new google.maps.Point(12.5, 12.5)
        };
    }

    function createActiveMarkerIcon() {
        // Create SVG for active map marker (more visible) - outer 2 circles with lighter shades
        var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="#B8E3FF"/><circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="#7BC9FF"/><circle cx="27.8784" cy="28.7498" r="6.0984" fill="#062943"/></svg>';

        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(57, 57),
            anchor: new google.maps.Point(12.5, 12.5)
        };
    }

    function startPulse(marker) {
        // Stop any existing pulse for this marker
        stopPulse(marker);

        var position = marker.getPosition();
        var pulseOverlay = new PulseOverlay(position, map, true);
        
        // Store overlay
        var markerId = marker.getPosition().toString();
        pulseOverlays[markerId] = pulseOverlay;
        
        // Ensure pulse continues by forcing a redraw after a short delay
        setTimeout(function() {
            if (pulseOverlay && pulseOverlay.div) {
                pulseOverlay.draw();
            }
        }, 100);
    }

    function stopPulse(marker) {
        var markerId = marker.getPosition().toString();
        if (pulseOverlays[markerId]) {
            pulseOverlays[markerId].setMap(null);
            delete pulseOverlays[markerId];
        }
    }

    function setAllMarkersToNormal() {
        var normalIcon = createNormalMarkerIcon();
        markers.forEach(function (markerData) {
            // Stop pulse for all markers
            stopPulse(markerData.marker);
            markerData.marker.setIcon(normalIcon);
        });
        activeMarker = null;
    }

    function setMarkerToActive(marker) {
        // Stop pulse first
        stopPulse(marker);
        
        var activeIcon = createActiveMarkerIcon();
        marker.setIcon(activeIcon);
        
        // Start pulse for active marker
        activeMarker = marker;
        startPulse(marker);
    }

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (text || '').replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    // Center map on marker with offset for mobile devices
    function centerMapOnMarker(marker, infoWindow) {
        var position = marker.getPosition();
        
        if (isMobileDevice()) {
            // On mobile, center the map with an offset to account for info window
            // The info window appears above the marker, so we need to pan the map down
            // to center the info window in the visible area
            
            // Set zoom first
            map.setZoom(15);
            
            // Wait a moment for map to settle, then adjust position
            setTimeout(function() {
                var mapDiv = document.getElementById('dhr-head-office-map');
                if (!mapDiv) {
                    map.setCenter(position);
                    return;
                }
                
                var mapHeight = mapDiv.offsetHeight;
                
                // Calculate the pixel position of the marker
                var projection = map.getProjection();
                if (!projection) {
                    map.setCenter(position);
                    return;
                }
                
                var markerPixel = projection.fromLatLngToContainerPixel(position);
                
                // We want the marker to be at about 35% from top of map
                // This will center the info window (which appears above marker) in the viewport
                var desiredMarkerY = mapHeight * 0.35;
                var offsetY = markerPixel.y - desiredMarkerY;
                
                // Convert pixel offset to lat/lng offset
                // At zoom 15, approximate conversion: 1 pixel ≈ 0.00001 degrees latitude
                var currentZoom = map.getZoom();
                var degreesPerPixel = 360 / (256 * Math.pow(2, currentZoom));
                var latOffset = offsetY * degreesPerPixel;
                
                // Pan to adjusted position
                var adjustedPosition = new google.maps.LatLng(
                    position.lat() - latOffset,
                    position.lng()
                );
                
                map.panTo(adjustedPosition);
            }, 100);
        } else {
            // On desktop, just center normally
            map.setCenter(position);
            map.setZoom(10);
        }
    }

    // Handle window resize for mobile devices
    var resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // If a marker is active and we're on mobile, recenter it
            if (isMobileDevice() && activeMarker && map) {
                var markerData = markers.find(function(m) {
                    return m.marker === activeMarker;
                });
                if (markerData && markerData.infoWindow) {
                    // Check if info window is open
                    if (markerData.infoWindow.getMap()) {
                        centerMapOnMarker(activeMarker, markerData.infoWindow);
                    }
                }
            }
        }, 250);
    });

    // Initialize map when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for Google Maps API to load
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                initHeadOfficeMap();
            } else {
                // Wait for Google Maps API
                window.addEventListener('load', function () {
                    setTimeout(initHeadOfficeMap, 1000);
                });
            }
        });
    } else {
        // DOM already loaded
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initHeadOfficeMap();
        } else {
            // Wait for Google Maps API
            window.addEventListener('load', function () {
                setTimeout(initHeadOfficeMap, 1000);
            });
        }
    }

})();
</script>