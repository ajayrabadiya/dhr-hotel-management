<?php
/**
 * Dining Venue Map Template (Map 4)
 */

if (!defined('ABSPATH')) {
    exit;
}

$overview_label = isset($settings['overview_label']) ? $settings['overview_label'] : 'OVERVIEW';
$main_heading = isset($settings['main_heading']) ? $settings['main_heading'] : 'Find A Dining Venue';
$description = isset($settings['description']) ? $settings['description'] : 'Whether you\'re savoring fresh seafood with a view of Table Mountain or indulging in gourmet delights by the Indian Ocean, our dining experiences promise to delight every palate.';
$reservation_label = isset($settings['reservation_label']) ? $settings['reservation_label'] : 'RESERVATION BY PHONE';
$reservation_phone = isset($settings['reservation_phone']) ? $settings['reservation_phone'] : '+27 (0)13 243 9401/2';
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

<div class="dhr-dining-venue-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-dining-venue-area">
        <div class="dhr-map-row align-items-end">
            <div class="dhr-map-left">
                <div class="dhr-dining-venue-block">
                    <!-- Mobile Hotel Selection Dropdown -->
                    <div class="dhr-mobile-hotel-select" id="dhr-dining-mobile-hotel-select">
                        <select id="dhr-dining-hotel-dropdown" class="dhr-hotel-dropdown">
                            <option value="">Select a Hotel</option>
                        </select>
                    </div>
                    <p class="dhr-map-label"><?php echo esc_html($overview_label); ?></p>
                    <h2 class="dhr-map-title"><?php echo esc_html($main_heading); ?></h2>
                    <p class="dhr-map-description"><?php echo esc_html($description); ?></p>
                    <div>
                    <div class="dhr-map-reservation">
                        <div class="dhr-dining-phone-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" stroke="#0B5991" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.8203 3.75C10.166 3.75 9.52149 3.98438 8.98438 4.41406L8.90626 4.45312L8.86719 4.49219L4.96094 8.51562L5.00001 8.55469C3.79395 9.66797 3.42286 11.333 3.94532 12.7734C3.9502 12.7832 3.94044 12.8027 3.94532 12.8125C5.00489 15.8447 7.71485 21.6992 13.0078 26.9922C18.3203 32.3047 24.2529 34.9072 27.1875 36.0547H27.2266C28.7451 36.5625 30.3906 36.2012 31.5625 35.1953L35.5078 31.25C36.543 30.2148 36.543 28.418 35.5078 27.3828L30.4297 22.3047L30.3906 22.2266C29.3555 21.1914 27.5195 21.1914 26.4844 22.2266L23.9844 24.7266C23.0811 24.292 20.9277 23.1787 18.8672 21.2109C16.8213 19.2578 15.7764 17.0117 15.3906 16.1328L17.8906 13.6328C18.9404 12.583 18.96 10.835 17.8516 9.80469L17.8906 9.76562L17.7734 9.64844L12.7734 4.49219L12.7344 4.45312L12.6563 4.41406C12.1191 3.98438 11.4746 3.75 10.8203 3.75Z" fill="none"/>
                            </svg>
                        </div>
                        <div>
                            <p class="dhr-map-label"><?php echo esc_html($reservation_label); ?></p>
                            <p class="dhr-map-description"><?php echo esc_html($reservation_phone); ?></p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="dhr-map-right">
                <div id="dhr-dining-venue-map" class="dhr-dining-venue-map"></div>
            </div>
        </div>
    </div>
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
            var color = '#FE4B67'; // Dining venue color
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
            outerCircle.setAttribute('fill', '#FFB5C5');
            outerCircle.setAttribute('opacity', '0.1');
            outerCircle.classList.add('pulse-outer-circle');
            svg.appendChild(outerCircle);
            
            // Middle circle (pulsing)
            var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            middleCircle.setAttribute('cx', '27.8784');
            middleCircle.setAttribute('cy', '28.7496');
            middleCircle.setAttribute('r', '20.9088');
            middleCircle.setAttribute('fill', '#FF8FA8');
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

    function initDiningVenueMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        // Define PulseOverlay class now that Google Maps is loaded
        definePulseOverlay();

        // Check if the map element exists
        var mapElement = document.getElementById('dhr-dining-venue-map');
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
        map = new google.maps.Map(document.getElementById('dhr-dining-venue-map'), {
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
                    stylers: [{ color: '#b1c2a8' }]
                }
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

        // Populate mobile hotel dropdown
        populateMobileHotelDropdown(hotels);
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

            // Update dropdown selection
            var dropdown = document.getElementById('dhr-dining-hotel-dropdown');
            if (dropdown) {
                dropdown.value = hotel.id;
            }
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

                // Update dropdown selection
                var dropdown = document.getElementById('dhr-dining-hotel-dropdown');
                if (dropdown) {
                    dropdown.value = hotel.id;
                }
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
        // Create SVG for normal map marker - dining venue with 3 lighter shades
        var svg = '<svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.5" cx="13.068" cy="13.068" r="13.068" fill="#FFB5C5"/><circle opacity="0.3" cx="13.068" cy="13.0681" r="6.0984" fill="#FF8FA8"/><circle cx="13.068" cy="13.0681" r="6.0984" fill="#FE4B67"/></svg>';

        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(27, 27),
            anchor: new google.maps.Point(12.5, 12.5)
        };
    }

    function createActiveMarkerIcon() {
        // Create SVG for active map marker (more visible) - dining venue with white border and 3 lighter shades
        var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="#FFB5C5"/><circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="#FF8FA8"/><circle cx="27.8784" cy="28.7498" r="6.0984" fill="#FE4B67"/></svg>';

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

    // Populate mobile hotel dropdown
    function populateMobileHotelDropdown(hotels) {
        var dropdown = document.getElementById('dhr-dining-hotel-dropdown');
        if (!dropdown) {
            return;
        }

        // Clear existing options except the first one
        dropdown.innerHTML = '<option value="">Select a Hotel</option>';

        // Add hotels to dropdown
        hotels.forEach(function (hotel, index) {
            var option = document.createElement('option');
            option.value = hotel.id;
            option.textContent = hotel.name;
            option.setAttribute('data-index', index);
            dropdown.appendChild(option);
        });

        // Add change event listener
        dropdown.addEventListener('change', function() {
            var selectedHotelId = this.value;
            if (!selectedHotelId) {
                return;
            }

            // Find the marker for this hotel
            var markerData = markers.find(function(m) {
                return m.hotelId == selectedHotelId;
            });

            if (markerData) {
                // Trigger marker click
                google.maps.event.trigger(markerData.marker, 'click');
            }
        });
    }

    // Center map on marker with offset for mobile devices
    function centerMapOnMarker(marker, infoWindow) {
        var position = marker.getPosition();
        
        if (isMobileDevice()) {
            // On mobile, center the map with an offset to account for info window
            // Set zoom first
            map.setZoom(15);
            
            setTimeout(function() {
                var mapDiv = document.getElementById('dhr-dining-venue-map');
                if (!mapDiv) {
                    map.setCenter(position);
                    return;
                }
                
                var mapHeight = mapDiv.offsetHeight;
                var projection = map.getProjection();
                if (!projection) {
                    map.setCenter(position);
                    return;
                }
                
                var markerPixel = projection.fromLatLngToContainerPixel(position);
                var desiredMarkerY = mapHeight * 0.35;
                var offsetY = markerPixel.y - desiredMarkerY;
                var currentZoom = map.getZoom();
                var degreesPerPixel = 360 / (256 * Math.pow(2, currentZoom));
                var latOffset = offsetY * degreesPerPixel;
                
                var adjustedPosition = new google.maps.LatLng(
                    position.lat() - latOffset,
                    position.lng()
                );
                
                map.panTo(adjustedPosition);
            }, 100);
        } else {
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
                initDiningVenueMap();
            } else {
                // Wait for Google Maps API
                window.addEventListener('load', function () {
                    setTimeout(initDiningVenueMap, 1000);
                });
            }
        });
    } else {
        // DOM already loaded
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initDiningVenueMap();
        } else {
            // Wait for Google Maps API
            window.addEventListener('load', function () {
                setTimeout(initDiningVenueMap, 1000);
            });
        }
    }

})();
</script>