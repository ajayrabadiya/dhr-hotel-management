<?php
/**
 * Property Portfolio Map Template (Map 6)
 */

if (!defined('ABSPATH')) {
    exit;
}

$panel_title = isset($settings['panel_title']) ? $settings['panel_title'] : 'Ownership Property Portfolio';
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

<div class="dhr-property-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-property-map-wrapper">
        <div id="dhr-property-map" class="dhr-property-map"></div>
    </div>
    <div class="dhr-property-panel">
        <div class="dhr-property-panel__icon">
            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.95703 5.48437L5.22656 5.22656L9.91406 0.539062L9.375 -2.35632e-08L4.95703 4.41797L0.539063 -4.09794e-07L2.16166e-07 0.539062L4.6875 5.22656L4.95703 5.48437Z" fill="#FAFAFA"/>
            </svg>
        </div>
        <h4><?php echo esc_html($panel_title); ?></h4>
        <ul>
            <?php if (!empty($hotels)): ?>
                <?php foreach ($hotels as $index => $hotel): ?>
                    <li class="dhr-property-item" data-hotel-id="<?php echo esc_attr($hotel->id); ?>" data-index="<?php echo esc_attr($index + 1); ?>">
                        <?php echo esc_html($hotel->name); ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
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
            // Active markers use 57x57 size
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
                var size = 57; // Both active and normal markers use 57x57
                // Match the anchor point of the marker exactly
                // Both markers: anchor (12.5, 12.5), size 57x57
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
                if (this.isActive && !this.div.classList.contains('dhr-marker-pulse-active')) {
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

    function initPropertyPortfolioMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        // Define PulseOverlay class now that Google Maps is loaded
        definePulseOverlay();

        // Check if the map element exists
        var mapElement = document.getElementById('dhr-property-map');
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
        map = new google.maps.Map(document.getElementById('dhr-property-map'), {
            zoom: 10,
            center: { lat: centerLat, lng: centerLng },
            styles: [
                {
                    featureType: 'all',
                    elementType: 'geometry',
                    stylers: [{ color: '#f2f2f2' }]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{ color: '#A0B6CB' }]
                },
                {
                    featureType: 'road',
                    elementType: 'labels.text.fill',
                    stylers: [{ color: '#A0B6CB' }]
                },
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

        // Add click handlers to property items
        var propertyItems = document.querySelectorAll('.dhr-property-item');
        propertyItems.forEach(function(item) {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function() {
                var hotelId = parseInt(this.getAttribute('data-hotel-id'));
                var markerData = markers.find(function (m) {
                    return m.hotelId == hotelId;
                });

                if (markerData) {
                    // Set all markers to normal
                    setAllMarkersToNormal();

                    // Set this marker to active
                    setMarkerToActive(markerData.marker);
                    activeMarker = markerData.marker;

                    // Close all info windows
                    infoWindows.forEach(function (iw) {
                        iw.close();
                    });

                    // Open info window for clicked property
                    markerData.infoWindow.open(map, markerData.marker);
                    
                    // Center map with mobile offset if needed
                    centerMapOnMarker(markerData.marker, markerData.infoWindow);

                    // Highlight property item in sidebar
                    propertyItems.forEach(function(pi) {
                        pi.classList.remove('active');
                    });
                    item.classList.add('active');
                }
            });
        });
    }

    function createMarker(hotel, index) {
        var position = {
            lat: parseFloat(hotel.latitude),
            lng: parseFloat(hotel.longitude)
        };

        // Create number for marker (01, 02, 03, etc.)
        var number = (index + 1).toString().padStart(2, '0');

        // Create normal marker icon with number
        var normalIcon = createNormalMarkerIcon(number);

        // Create marker
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: hotel.name,
            icon: normalIcon,
            animation: index === 0 ? google.maps.Animation.DROP : null
        });

        // Store the number on the marker for later use
        marker.markerNumber = number;

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

            // Highlight property item in sidebar
            var propertyItems = document.querySelectorAll('.dhr-property-item');
            propertyItems.forEach(function(item) {
                item.classList.remove('active');
            });
            var propertyItem = document.querySelector('.dhr-property-item[data-hotel-id="' + hotel.id + '"]');
            if (propertyItem) {
                propertyItem.classList.add('active');
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

                // Highlight first property item
                var firstPropertyItem = document.querySelector('.dhr-property-item[data-hotel-id="' + hotel.id + '"]');
                if (firstPropertyItem) {
                    firstPropertyItem.classList.add('active');
                }
            }, 500);
        }
    }

    function getInfoWindowContent(hotel) {
    var content =
        '<div class="dhr-info-window">' +
            '<img src="' + (hotel.image_url || (dhrHotelsData.pluginUrl + "assets/images/default-hotel.jpg")) + '" alt="' + escapeHtml(hotel.name) + '" style="width:100%;height:150px;object-fit:cover;border-radius:4px;margin-bottom:10px;">' +
            '<h4 class="dhr-info-window-title">' + escapeHtml(hotel.name) + '</h4>' +
            '<p class="dhr-info-window-content mb-0">' + escapeHtml(hotel.city) + ", " + escapeHtml(hotel.province) + '</p>' +

            '<div class="dhr-info-window-action">' +
                '<a href="' + (hotel.google_maps_url || "#") + '" class="dhr-btn-info" target="_blank">' +
                    '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                        '<path d="M10.4544 1.95996C5.77085 1.95996 1.96021 5.77061 1.96021 10.4542C1.96021 15.1377 5.77085 18.9484 10.4544 18.9484C15.138 18.9484 18.9486 15.1377 18.9486 10.4542C18.9486 5.77061 15.138 1.95996 10.4544 1.95996ZM10.4544 3.26676C14.431 3.26676 17.6418 6.47761 17.6418 10.4542C17.6418 14.4307 14.431 17.6416 10.4544 17.6416C6.47785 17.6416 3.26701 14.4307 3.26701 10.4542C3.26701 6.47761 6.47785 3.26676 10.4544 3.26676ZM9.80101 6.53376V7.84056H11.1078V6.53376H9.80101ZM9.80101 9.14736V14.3746H11.1078V9.14736H9.80101Z" fill="#0B5991"/>' +
                    '</svg>' +
                '</a>' +

                '<a href="' + (hotel.website_url || "#") + '" class="dhr-btn-book"  target="_blank">View Packages</a>' +

            '</div>' +
        '</div>';

    return content;
}


    function createNormalMarkerIcon(number) {
        // Create SVG for normal map marker with number - outer 2 circles with lighter shades
        var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<circle opacity="0.5" cx="28.314" cy="28.314" r="28.314" fill="#B8E3FF"/>' +
            '<circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="#7BC9FF"/>' +
            '<circle cx="27.8784" cy="28.7498" r="20" fill="#062943"/>' +
            '<text x="27.8784" y="33.5" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="#ffffff" text-anchor="middle">' + number + '</text>' +
            '</svg>';

        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(57, 57),
            anchor: new google.maps.Point(12.5, 12.5)
        };
    }

    function createActiveMarkerIcon(number) {
        // Create SVG for active map marker (more visible) with number - outer 2 circles with lighter shades
        var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="#B8E3FF"/>' +
            '<circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="#7BC9FF"/>' +
            '<circle cx="27.8784" cy="28.7498" r="20" fill="#062943"/>' +
            '<text x="27.8784" y="33.5" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="#ffffff" text-anchor="middle">' + number + '</text>' +
            '</svg>';

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
        markers.forEach(function (markerData) {
            // Stop pulse for all markers
            stopPulse(markerData.marker);
            // Set icon back to normal with the marker's number
            var normalIcon = createNormalMarkerIcon(markerData.marker.markerNumber);
            markerData.marker.setIcon(normalIcon);
        });
        activeMarker = null;
    }

    function setMarkerToActive(marker) {
        // Stop pulse first
        stopPulse(marker);
        
        // Set icon to active with the marker's number
        var activeIcon = createActiveMarkerIcon(marker.markerNumber);
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
                var mapDiv = document.getElementById('dhr-property-map');
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
                initPropertyPortfolioMap();
            } else {
                // Wait for Google Maps API
                window.addEventListener('load', function () {
                    setTimeout(initPropertyPortfolioMap, 1000);
                });
            }
        });
    } else {
        // DOM already loaded
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initPropertyPortfolioMap();
        } else {
            // Wait for Google Maps API
            window.addEventListener('load', function () {
                setTimeout(initPropertyPortfolioMap, 1000);
            });
        }
    }

})();
</script>