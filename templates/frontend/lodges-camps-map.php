<?php
/**
 * Lodges & Camps Map Template (Map 7)
 */

if (!defined('ABSPATH')) {
    exit;
}

$panel_title = isset($settings['panel_title']) ? $settings['panel_title'] : 'Lodges & Camps';
$legend_lodges = isset($settings['legend_lodges']) ? $settings['legend_lodges'] : 'Lodges & Camps';
$legend_weddings = isset($settings['legend_weddings']) ? $settings['legend_weddings'] : 'Weddings & Conferences';
$show_list = isset($settings['show_list']) ? $settings['show_list'] : true;
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

<div class="dhr-lodges-camps-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <?php if ($show_list && !empty($hotels)): ?>
    <div class="dhr-lodges-camps-panel">
        <ul>
            <?php foreach ($hotels as $index => $hotel): ?>
                <li data-hotel-id="<?php echo esc_attr($hotel->id); ?>">
                   <?php echo esc_html($hotel->name); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="dhr-lodges-legend">
            <ul>
                <li>
                   <?php echo esc_html($legend_lodges); ?>
                </li>
                <li>
                   <?php echo esc_html($legend_weddings); ?>
                </li>
            </ul>
            
        </div>
    </div>
    <?php endif; ?>
    <div class="dhr-lodges-camps-map-wrapper">
        <div id="dhr-lodges-camps-map" class="dhr-lodges-camps-map"></div>
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
        PulseOverlay = function(position, map, isActive, fillColor) {
            this.position = position;
            this.map = map;
            this.isActive = isActive;
            this.fillColor = fillColor || '#44B9F8'; // Default to blue (lodge color)
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
            // Get lighter shades based on fill color
            var lightestShade = this.fillColor === '#44B9F8' ? '#B8E3FF' : '#F0D9B8';
            var mediumShade = this.fillColor === '#44B9F8' ? '#7BC9FF' : '#E4C49A';
            
            // Outer circle (pulsing)
            var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            outerCircle.setAttribute('cx', '28.314');
            outerCircle.setAttribute('cy', '28.314');
            outerCircle.setAttribute('r', '28.314');
            outerCircle.setAttribute('fill', lightestShade);
            outerCircle.setAttribute('opacity', '0.1');
            outerCircle.classList.add('pulse-outer-circle');
            svg.appendChild(outerCircle);
            
            // Middle circle (pulsing)
            var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            middleCircle.setAttribute('cx', '27.8784');
            middleCircle.setAttribute('cy', '28.7496');
            middleCircle.setAttribute('r', '20.9088');
            middleCircle.setAttribute('fill', mediumShade);
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

    function initLodgesCampsMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        // Define PulseOverlay class now that Google Maps is loaded
        definePulseOverlay();

        // Check if the map element exists
        var mapElement = document.getElementById('dhr-lodges-camps-map');
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
        map = new google.maps.Map(mapElement, {
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
            
        // Add click handlers to list items
        var lodgeItems = document.querySelectorAll('.dhr-lodges-camps-panel > ul > li[data-hotel-id]');
        lodgeItems.forEach(function(item) {
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

                    // Open info window for clicked item
                    markerData.infoWindow.open(map, markerData.marker);
                    
                    // Center map with mobile offset if needed
                    centerMapOnMarker(markerData.marker, markerData.infoWindow);

                    // Highlight list item
                    lodgeItems.forEach(function(li) {
                        li.classList.remove('active');
                    });
                    item.classList.add('active');
                }
            });
        });
        
        // Handle legend item clicks
        var legendItems = document.querySelectorAll('.dhr-lodges-legend ul li');
        legendItems.forEach(function(item, legendIndex) {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function() {
                // Find markers matching the legend type (first item = lodges, second = weddings)
                var isLodgeType = legendIndex === 0;
                var matchingMarkers = markers.filter(function(m) {
                    var markerIndex = m.index;
                    var markerIsLodge = markerIndex % 2 === 0;
                    return isLodgeType ? markerIsLodge : !markerIsLodge;
                });
                
                if (matchingMarkers.length > 0) {
                    // Set all markers to normal
                    setAllMarkersToNormal();
                    
                    // Set first matching marker to active
                    var firstMarker = matchingMarkers[0];
                    setMarkerToActive(firstMarker.marker);
                    activeMarker = firstMarker.marker;
                    
                    // Close all info windows
                    infoWindows.forEach(function (iw) {
                        iw.close();
                    });
                    
                    // Open first matching marker's info window
                    firstMarker.infoWindow.open(map, firstMarker.marker);
                    
                    // Center map
                    centerMapOnMarker(firstMarker.marker, firstMarker.infoWindow);
                    map.setZoom(12);
                }
            });
        });
    }

    function createMarker(hotel, index) {
        var position = {
            lat: parseFloat(hotel.latitude),
            lng: parseFloat(hotel.longitude)
        };

        // Determine if this is a lodge or wedding marker
        var isLodge = index % 2 === 0;
        
        // Create normal marker icon (blue for lodges, orange for weddings)
        var normalIcon = createNormalMarkerIcon(isLodge);

        // Create marker
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: hotel.name,
            icon: normalIcon,
            animation: index === 0 ? google.maps.Animation.DROP : null
        });

        // Store marker type
        marker.isLodge = isLodge;

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
            hotelId: hotel.id,
            index: index
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
            '<p class="dhr-info-window-content mb-0">' + escapeHtml(hotel.city) + ', ' + escapeHtml(hotel.province) + '</p>' +
            '</div>';

        return content;
    }

    function createNormalMarkerIcon(isLodge) {
        // Create SVG for normal map marker - outer 2 circles with lighter shades
        // Blue (#44B9F8) for lodges, Orange (#D3AA74) for weddings
        var lightestShade = isLodge ? '#B8E3FF' : '#F0D9B8';
        var mediumShade = isLodge ? '#7BC9FF' : '#E4C49A';
        var svg = '<svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<circle opacity="0.6" cx="13.068" cy="13.068" r="13.068" fill="' + lightestShade + '"/>' +
            '<circle opacity="0.3" cx="13.068" cy="13.0681" r="6.0984" fill="' + mediumShade + '"/>' +
            '<circle cx="13.068" cy="13.0681" r="6.0984" fill="#062943"/></svg>';

        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(27, 27),
            anchor: new google.maps.Point(12.5, 12.5)
        };
    }

    function createActiveMarkerIcon(isLodge) {
        // Create SVG for active map marker (more visible) - outer 2 circles with lighter shades
        // Blue (#44B9F8) for lodges, Orange (#D3AA74) for weddings
        var lightestShade = isLodge ? '#B8E3FF' : '#F0D9B8';
        var mediumShade = isLodge ? '#7BC9FF' : '#E4C49A';
        var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="' + lightestShade + '"/>' +
            '<circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="' + mediumShade + '"/>' +
            '<circle cx="27.8784" cy="28.7498" r="6.0984" fill="#062943"/></svg>';

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
        // Get the color based on marker type (blue for lodges, orange for weddings)
        var fillColor = marker.isLodge ? '#44B9F8' : '#D3AA74';
        var pulseOverlay = new PulseOverlay(position, map, true, fillColor);
        
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
            // Set icon back to normal
            var normalIcon = createNormalMarkerIcon(markerData.marker.isLodge);
            markerData.marker.setIcon(normalIcon);
        });
        activeMarker = null;
    }

    function setMarkerToActive(marker) {
        // Stop pulse first
        stopPulse(marker);
        
        var activeIcon = createActiveMarkerIcon(marker.isLodge);
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
                var mapDiv = document.getElementById('dhr-lodges-camps-map');
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
            map.setZoom(15);
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
                initLodgesCampsMap();
            } else {
                // Wait for Google Maps API
                window.addEventListener('load', function () {
                    setTimeout(initLodgesCampsMap, 1000);
                });
            }
        });
    } else {
        // DOM already loaded
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initLodgesCampsMap();
        } else {
            // Wait for Google Maps API
            window.addEventListener('load', function () {
                setTimeout(initLodgesCampsMap, 1000);
            });
        }
    }

})();
</script>

