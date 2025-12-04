<?php
/**
 * Partner Portfolio Map Template (Map 3)
 */

if (!defined('ABSPATH')) {
    exit;
}

$overview_label = isset($settings['overview_label']) ? $settings['overview_label'] : 'DISCOVER AFRICA';
$main_heading = isset($settings['main_heading']) ? $settings['main_heading'] : 'Our Partner Portfolio';
$description = isset($settings['description']) ? $settings['description'] : 'Together with CityBlue Hotels, we\'re crafting a unified hospitality experience that celebrates the rich cultures, stunning landscapes, and warm hospitality that Africa is known for.';
$legend_cityblue = isset($settings['legend_cityblue']) ? $settings['legend_cityblue'] : 'CityBlue Hotels';
$legend_dream = isset($settings['legend_dream']) ? $settings['legend_dream'] : 'Dream Hotels & Resorts';
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
    0%   { transform: scale(1);    opacity: 0.55; }
    50%  { transform: scale(1.45); opacity: 0.75; }
    100% { transform: scale(1);    opacity: 0.55; }
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


<div class="dhr-partner-portfolio-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-partner-portfolio-area">
        <div class="dhr-map-row align-items-end">
            <div class="dhr-map-left">
                <div class="dhr-partner-portfolio-block">
                    <p class="dhr-map-label"><?php echo esc_html($overview_label); ?></p>
                    <h2 class="dhr-map-title dhr-text-primary"><?php echo esc_html($main_heading); ?></h2>
                    <p class="dhr-map-description"><?php echo esc_html($description); ?></p>
                    <div>
                        <div class="dhr-partner-portfolio-legend">
                            <ul>
                                <li><?php echo esc_html($legend_cityblue); ?></li>
                                <li><?php echo esc_html($legend_dream); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dhr-map-right">
                <div id="dhr-partner-portfolio-map" class="dhr-partner-portfolio-map"></div>
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

    // Color schemes for different marker types
    var colorSchemes = {
        cityblue: {
            lightest: '#B3D9F2',  // Very light blue
            medium: '#66B3E0',    // Medium blue
            dark: '#0B5991'       // Dark blue (original)
        },
        dream: {
            lightest: '#D4EDFF',  // Very light cyan
            medium: '#99D6FF',    // Medium cyan
            dark: '#4DB8FF'       // Bright cyan
        }
    };

    // Detect if device is mobile
    function isMobileDevice() {
        return window.innerWidth <= 991;
    }

    // Function to define PulseOverlay class (called after Google Maps loads)
    function definePulseOverlay() {
        // Custom Overlay for Pulse Effect
        PulseOverlay = function(position, map, isActive, isCityBlue) {
            this.position = position;
            this.map = map;
            this.isActive = isActive;
            this.isCityBlue = isCityBlue;
            this.div = null;
            this.setMap(map);
        };

        PulseOverlay.prototype = new google.maps.OverlayView();

        PulseOverlay.prototype.onAdd = function () {
            var div = document.createElement('div');
            div.className = 'dhr-marker-pulse';
            div.classList.add('dhr-marker-pulse-active');
            
            // Get color scheme
            var colors = this.isCityBlue ? colorSchemes.cityblue : colorSchemes.dream;
            
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
            
            // Outer circle (pulsing)
            var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            outerCircle.setAttribute('cx', '28.314');
            outerCircle.setAttribute('cy', '28.314');
            outerCircle.setAttribute('r', '28.314');
            outerCircle.setAttribute('fill', colors.lightest);
            outerCircle.setAttribute('opacity', '0.1');
            outerCircle.classList.add('pulse-outer-circle');
            svg.appendChild(outerCircle);
            
            // Middle circle (pulsing)
            var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            middleCircle.setAttribute('cx', '27.8784');
            middleCircle.setAttribute('cy', '28.7496');
            middleCircle.setAttribute('r', '20.9088');
            middleCircle.setAttribute('fill', colors.medium);
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

    function initPartnerPortfolioMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        // Define PulseOverlay class now that Google Maps is loaded
        definePulseOverlay();

        // Check if the map element exists
        var mapElement = document.getElementById('dhr-partner-portfolio-map');
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
        map = new google.maps.Map(document.getElementById('dhr-partner-portfolio-map'), {
            zoom: 10,
            center: { lat: centerLat, lng: centerLng },
            styles: [
                {
                    featureType: 'all',
                    elementType: 'geometry',
                    stylers: [{ color: '#ffffff' }]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{ color: '#E2EFF7' }]
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
    }

    function createMarker(hotel, index) {
        var position = {
            lat: parseFloat(hotel.latitude),
            lng: parseFloat(hotel.longitude)
        };

        // Alternate between CityBlue and Dream markers
        var isCityBlue = index % 2 === 0;

        // Create normal marker icon
        var normalIcon = createNormalMarkerIcon(isCityBlue);

        // Create marker
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: hotel.name,
            icon: normalIcon,
            animation: index === 0 ? google.maps.Animation.DROP : null
        });

        // Store marker type
        marker.isCityBlue = isCityBlue;

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

        function createNormalMarkerIcon(isCityBlue) {
            // Get color scheme
            var colors = isCityBlue ? colorSchemes.cityblue : colorSchemes.dream;
            
            // Different opacity for outer circle based on marker type
            var outerOpacity = isCityBlue ? '0.5' : '1';
            
            // Create SVG for normal map marker - 3 distinct circles with proper shades
            var svg = '<svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<circle opacity="' + outerOpacity + '" cx="13.068" cy="13.068" r="13.068" fill="' + colors.lightest + '"/>' +
                '<circle opacity="0.3" cx="13.068" cy="13.0681" r="6.0984" fill="' + colors.medium + '"/>' +
                '<circle cx="13.068" cy="13.0681" r="6.0984" fill="' + colors.dark + '"/>' +
                '</svg>';

            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(27, 27),
                anchor: new google.maps.Point(12.5, 12.5)
            };
        }

    function createActiveMarkerIcon(isCityBlue) {
        // Get color scheme
        var colors = isCityBlue ? colorSchemes.cityblue : colorSchemes.dream;
        
        // Create SVG for active map marker - 3 distinct circles with proper shades
        var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="' + colors.lightest + '"/>' +
            '<circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="' + colors.medium + '"/>' +
            '<circle cx="27.8784" cy="28.7498" r="6.0984" fill="' + colors.dark + '"/>' +
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
        var pulseOverlay = new PulseOverlay(position, map, true, marker.isCityBlue);
        
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
            var normalIcon = createNormalMarkerIcon(markerData.marker.isCityBlue);
            markerData.marker.setIcon(normalIcon);
        });
        activeMarker = null;
    }

    function setMarkerToActive(marker) {
        // Stop pulse first
        stopPulse(marker);
        
        var activeIcon = createActiveMarkerIcon(marker.isCityBlue);
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
            // Set zoom first
            map.setZoom(10);
            
            setTimeout(function() {
                var mapDiv = document.getElementById('dhr-partner-portfolio-map');
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
                initPartnerPortfolioMap();
            } else {
                // Wait for Google Maps API
                window.addEventListener('load', function () {
                    setTimeout(initPartnerPortfolioMap, 1000);
                });
            }
        });
    } else {
        // DOM already loaded
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initPartnerPortfolioMap();
        } else {
            // Wait for Google Maps API
            window.addEventListener('load', function () {
                setTimeout(initPartnerPortfolioMap, 1000);
            });
        }
    }

})();
</script>