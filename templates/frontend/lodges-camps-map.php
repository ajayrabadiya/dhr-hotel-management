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

<div class="lodges-camps-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div id="lodges-camps-map" class="lodges-camps-map"></div>
    <?php if ($show_list && !empty($hotels)): ?>
        <div class="lodges-camps-panel">
            <div class="lodges-camps-panel__icon lodges-camps-panel__toggle" style="cursor: pointer;">
                <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"
                    style="transition: transform 0.3s ease;">
                    <path
                        d="M4.95703 5.48437L5.22656 5.22656L9.91406 0.539062L9.375 -2.35632e-08L4.95703 4.41797L0.539063 -4.09794e-07L2.16166e-07 0.539062L4.6875 5.22656L4.95703 5.48437Z"
                        fill="#FAFAFA" />
                </svg>
            </div>
            <div class="lodges-camps-panel__content">
                <ul>
                    <?php foreach ($hotels as $index => $hotel): ?>
                        <li data-hotel-id="<?php echo esc_attr($hotel->id); ?>">
                            <?php echo esc_html($hotel->name); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="lodges-legend">
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
        </div>
    <?php endif; ?>
</div>

<?php
$hotels_js = array();
if (!empty($hotels)) {
    foreach ($hotels as $h) {
        $hotels_js[] = array(
            'id' => (int) $h->id, 'name' => isset($h->name) ? $h->name : '', 'description' => isset($h->description) ? $h->description : '',
            'address' => isset($h->address) ? $h->address : '', 'city' => isset($h->city) ? $h->city : '', 'province' => isset($h->province) ? $h->province : '',
            'country' => isset($h->country) ? $h->country : '', 'latitude' => isset($h->latitude) ? floatval($h->latitude) : 0, 'longitude' => isset($h->longitude) ? floatval($h->longitude) : 0,
            'phone' => isset($h->phone) ? $h->phone : '', 'email' => isset($h->email) ? $h->email : '', 'website' => isset($h->website) ? $h->website : '',
            'image_url' => isset($h->image_url) ? $h->image_url : '', 'google_maps_url' => isset($h->google_maps_url) ? $h->google_maps_url : '', 'status' => isset($h->status) ? $h->status : 'active'
        );
    }
}
?>
<script>
    var dhrThisMapHotels = <?php echo json_encode($hotels_js); ?>;
</script>
<script>
    (function () {
        'use strict';

        var map;
        var markers = [];
        var infoWindows = [];
        var pulseOverlays = {}; // Store pulse overlay elements for each marker
        var activeMarker = null; // Track currently active marker
        var hoveredMarker = null; // Track currently hovered marker
        var PulseOverlay; // Will be defined after Google Maps loads

        // Detect if device is mobile
        function isMobileDevice() {
            return window.innerWidth <= 991;
        }

        // Detect device type for responsive adjustments
        function getDeviceType() {
            var width = window.innerWidth;
            if (width < 768) {
                return 'mobile';
            } else if (width < 991) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }

        // Function to define PulseOverlay class (called after Google Maps loads)
        function definePulseOverlay() {
            // Custom Overlay for Pulse Effect
            PulseOverlay = function (position, map, isActive, fillColor) {
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
                if (this.isActive) {
                    div.classList.add('dhr-marker-pulse-active');
                } else {
                    div.classList.add('dhr-marker-pulse-hover');
                }

                // Create SVG structure matching the EXACT marker design
                var size = this.isActive ? 57 : 27;
                var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', size);
                svg.setAttribute('height', size);
                svg.setAttribute('viewBox', this.isActive ? '0 0 57 57' : '0 0 27 27');
                svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                svg.style.width = '100%';
                svg.style.height = '100%';
                svg.style.display = 'block';

                // Get lighter shades based on fill color
                var lightestShade = this.fillColor === '#44B9F8' ? '#B8E3FF' : '#F0D9B8';
                var mediumShade = this.fillColor === '#44B9F8' ? '#7BC9FF' : '#E4C49A';

                if (this.isActive) {
                    // Active marker structure - EXACT match with lighter shades
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
                } else {
                    // Hover marker structure - smaller pulse circles
                    // Outer circle (pulsing) - centered at marker center (12.5, 12.5)
                    var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    outerCircle.setAttribute('cx', '13.068');
                    outerCircle.setAttribute('cy', '13.068');
                    outerCircle.setAttribute('r', '13.068');
                    outerCircle.setAttribute('fill', lightestShade);
                    outerCircle.setAttribute('opacity', '0.1');
                    outerCircle.classList.add('pulse-outer-circle');
                    svg.appendChild(outerCircle);

                    // Middle circle (pulsing)
                    var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    middleCircle.setAttribute('cx', '13.068');
                    middleCircle.setAttribute('cy', '13.0681');
                    middleCircle.setAttribute('r', '6.0984');
                    middleCircle.setAttribute('fill', mediumShade);
                    middleCircle.setAttribute('opacity', '0.3');
                    middleCircle.classList.add('pulse-middle-circle');
                    svg.appendChild(middleCircle);
                }

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
                    var size = this.isActive ? 57 : 27;

                    // Match the anchor point of the marker exactly
                    var anchorOffsetX = this.isActive ? 27.8784 : 13.068;
                    var anchorOffsetY = this.isActive ? 28.7498 : 13.0681;
                    this.div.style.left = (position.x - anchorOffsetX) + 'px';
                    this.div.style.top = (position.y - anchorOffsetY) + 'px';
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

        function initLodgesCampsMap() {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                console.error('Google Maps API not loaded');
                return;
            }

            // Define PulseOverlay class now that Google Maps is loaded
            definePulseOverlay();

            // Check if the map element exists
            var mapElement = document.getElementById('lodges-camps-map');
            if (!mapElement) {
                // Map element doesn't exist, this script is not needed
                return;
            }

            var hotels = (typeof dhrThisMapHotels !== 'undefined' && dhrThisMapHotels.length) ? dhrThisMapHotels : (dhrHotelsData && dhrHotelsData.hotels) ? dhrHotelsData.hotels : [];
            if (!hotels || hotels.length === 0) {
                console.warn('No hotels data available');
                return;
            }

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

            // Adjust center to position map based on device type
            var ne = bounds.getNorthEast();
            var sw = bounds.getSouthWest();
            var latSpan = ne.lat() - sw.lat();
            var lngSpan = ne.lng() - sw.lng();

            // Apply different adjustments based on device type
            var deviceType = getDeviceType();
            var latMultiplier, lngMultiplier;

            if (deviceType === 'mobile') {
                latMultiplier = 8;
                lngMultiplier = 0.5;
            } else if (deviceType === 'tablet') {
                latMultiplier = 3.5;
                lngMultiplier = 0.5;
            } else {
                latMultiplier = 3;
                lngMultiplier = 0.8;
            }

            var adjustedCenterLat = centerLat + (latSpan * latMultiplier);
            var adjustedCenterLng = centerLng + (lngSpan * lngMultiplier);

            // Set zoom based on device type
            var mapZoom = (deviceType === 'mobile') ? 5.6 : 6.9;

            // Initialize map
            map = new google.maps.Map(mapElement, {
                zoom: mapZoom,
                center: { lat: adjustedCenterLat, lng: adjustedCenterLng },
                minZoom: 3,
                maxZoom: 10,
                styles: [
                    {
                        featureType: 'all',
                        elementType: 'geometry',
                        stylers: [{ color: '#f2f2f2' }]
                    },
                    {
                        featureType: 'water',
                        elementType: 'geometry',
                        stylers: [{ color: '#a0b6cb' }]
                    }
                ]
            });

            // Create markers for each hotel
            hotels.forEach(function (hotel, index) {
                createMarker(hotel, index);
            });

            // Add click handlers to list items
            var lodgeItems = document.querySelectorAll('.lodges-camps-panel__content > ul > li[data-hotel-id]');
            lodgeItems.forEach(function (item) {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function () {
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
                        lodgeItems.forEach(function (li) {
                            li.classList.remove('active');
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

            // Add close listener to info window
            infoWindow.addListener('closeclick', function () {
                // Remove active state from all markers when info window is closed
                setAllMarkersToNormal();
            });

            // Add hover listeners for pulse effect
            marker.addListener('mouseover', function () {
                hoveredMarker = marker;
                // Only start pulse if not already active
                if (activeMarker !== marker) {
                    startPulse(marker, false);
                }
            });

            marker.addListener('mouseout', function () {
                hoveredMarker = null;
                // Only stop pulse if not active
                if (activeMarker !== marker) {
                    stopPulse(marker);
                }
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
        }

        function getInfoWindowContent(hotel) {
            var content =
                '<div class="info-window">' +
                '<div class="info-window-image">' +
                '<img src="' + (hotel.image_url || (dhrHotelsData.pluginUrl + "assets/images/default-hotel.jpg")) + '" alt="' + escapeHtml(hotel.name) + '">' +
                '</div>' +
                '<div class="info-window-content">' +
                '<h3 class="info-window-title">' + escapeHtml(hotel.name) + '</h3>' +
                '<p class="info-window-location">' + escapeHtml(hotel.city) + ", " + escapeHtml(hotel.province) + '</p>' +
                '<div class="info-window-actions">' +
                '<a href="' + (hotel.google_maps_url || "#") + '" class="btn-info" target="_blank">' +
                '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<path d="M10.4544 1.95996C5.77085 1.95996 1.96021 5.77061 1.96021 10.4542C1.96021 15.1377 5.77085 18.9484 10.4544 18.9484C15.138 18.9484 18.9486 15.1377 18.9486 10.4542C18.9486 5.77061 15.138 1.95996 10.4544 1.95996ZM10.4544 3.26676C14.431 3.26676 17.6418 6.47761 17.6418 10.4542C17.6418 14.4307 14.431 17.6416 10.4544 17.6416C6.47785 17.6416 3.26701 14.4307 3.26701 10.4542C3.26701 6.47761 6.47785 3.26676 10.4544 3.26676ZM9.80101 6.53376V7.84056H11.1078V6.53376H9.80101ZM9.80101 9.14736V14.3746H11.1078V9.14736H9.80101Z" fill="#0B5991"/>' +
                '</svg>' +
                '</a>' +
                '<a href="' + (hotel.website_url || "#") + '" class="btn-book"  target="_blank">View Packages</a>' +
                '</div>' +
                '</div>' +
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
                anchor: new google.maps.Point(13.068, 13.0681)
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
                anchor: new google.maps.Point(27.8784, 28.7498)
            };
        }

        function startPulse(marker, isActive) {
            // Stop any existing pulse for this marker
            stopPulse(marker);

            var position = marker.getPosition();
            // Get the color based on marker type (blue for lodges, orange for weddings)
            var fillColor = marker.isLodge ? '#44B9F8' : '#D3AA74';
            var pulseOverlay = new PulseOverlay(position, map, isActive, fillColor);

            // Store overlay
            var markerId = marker.getPosition().toString();
            pulseOverlays[markerId] = pulseOverlay;

            // Ensure pulse continues by forcing a redraw after a short delay
            setTimeout(function () {
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
            hoveredMarker = null;
        }

        function setMarkerToActive(marker) {
            // Stop pulse first
            stopPulse(marker);

            var activeIcon = createActiveMarkerIcon(marker.isLodge);
            marker.setIcon(activeIcon);

            // Start pulse for active marker
            activeMarker = marker;
            hoveredMarker = null;
            startPulse(marker, true);
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
                setTimeout(function () {
                    var mapDiv = document.getElementById('lodges-camps-map');
                    if (!mapDiv) {
                        map.panTo(position);
                        return;
                    }

                    var mapHeight = mapDiv.offsetHeight;
                    var projection = map.getProjection();
                    if (!projection) {
                        map.panTo(position);
                        return;
                    }

                    var markerPixel = projection.fromLatLngToContainerPixel(position);
                    var desiredMarkerY = mapHeight * 0.40;
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
            }
            // Desktop (> 991px): Do not center map on marker click
        }

        // Handle window resize for mobile devices
        var resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                // If a marker is active and we're on mobile, recenter it
                if (isMobileDevice() && activeMarker && map) {
                    var markerData = markers.find(function (m) {
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
            document.addEventListener('DOMContentLoaded', function () {
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

<script>
    jQuery(document).ready(function($) {
        // Accordion toggle functionality for lodges camps panel
        var $toggle = $('.lodges-camps-panel__toggle');
        var $content = $('.lodges-camps-panel__content');
        var $svg = $toggle.find('svg');
        
        // Check screen width and set initial state
        var screenWidth = window.innerWidth || $(window).width();
        if (screenWidth < 991) {
            // Collapse by default on screens < 991px
            $content.hide();
            $svg.css('transform', 'rotate(180deg)');
        } else {
            // Expanded by default on screens >= 991px
            $content.show();
            $svg.css('transform', 'rotate(0deg)');
        }
        
        // Toggle functionality
        $toggle.on('click', function() {
            // Toggle the content with smooth animation
            $content.slideToggle(300, function() {
                // Rotate the icon when content is toggled
                if ($content.is(':visible')) {
                    $svg.css('transform', 'rotate(0deg)');
                } else {
                    $svg.css('transform', 'rotate(180deg)');
                }
            });
        });
    });
</script>