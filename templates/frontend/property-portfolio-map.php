<?php
/**
 * Property Portfolio Map Template (Map 6)
 */

if (!defined('ABSPATH')) {
    exit;
}

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

$panel_title = isset($settings['panel_title']) ? $settings['panel_title'] : 'Ownership Property Portfolio';
?>

<div class="all-maps property-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div id="property-map" class="property-map" data-hotels="<?php echo esc_attr(wp_json_encode($hotels_js)); ?>"></div>
    <div class="property-panel">
        <div class="property-panel__icon property-panel__toggle" style="cursor: pointer;">
            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" style="transition: transform 0.3s ease;">
                <path
                    d="M4.95703 5.48437L5.22656 5.22656L9.91406 0.539062L9.375 -2.35632e-08L4.95703 4.41797L0.539063 -4.09794e-07L2.16166e-07 0.539062L4.6875 5.22656L4.95703 5.48437Z"
                    fill="#FAFAFA" />
            </svg>
        </div>
        <div class="property-panel__content">
            <h4><?php echo esc_html($panel_title); ?></h4>
            <ul>
                <?php if (!empty($hotels)): ?>
                    <?php foreach ($hotels as $index => $hotel): ?>
                        <li class="property-item" data-hotel-id="<?php echo esc_attr($hotel->id); ?>"
                            data-index="<?php echo esc_attr($index + 1); ?>">
                            <?php echo esc_html($hotel->name); ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div> 
    </div>
</div>

<script>
    var dhrPropertyPortfolioMapHotels = <?php echo json_encode($hotels_js); ?>;
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
            PulseOverlay = function (position, map, isActive) {
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
                if (this.isActive) {
                    div.classList.add('dhr-marker-pulse-active');
                } else {
                    div.classList.add('dhr-marker-pulse-hover');
                }

                // Create SVG structure matching the EXACT marker design
                // Both active and hover use 57x57 to match normal marker size
                var size = 57;
                var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', size);
                svg.setAttribute('height', size);
                svg.setAttribute('viewBox', '0 0 57 57');
                svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                svg.style.width = '100%';
                svg.style.height = '100%';
                svg.style.display = 'block';

                if (this.isActive) {
                    // Active marker structure - EXACT match
                    // Outer circle (pulsing)
                    var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    outerCircle.setAttribute('cx', '28.314');
                    outerCircle.setAttribute('cy', '28.314');
                    outerCircle.setAttribute('r', '28.314');
                    outerCircle.setAttribute('fill', '#44B9F8');
                    outerCircle.setAttribute('opacity', '0.1');
                    outerCircle.classList.add('pulse-outer-circle');
                    svg.appendChild(outerCircle);

                    // Middle circle (pulsing)
                    var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    middleCircle.setAttribute('cx', '27.8784');
                    middleCircle.setAttribute('cy', '28.7496');
                    middleCircle.setAttribute('r', '20.9088');
                    middleCircle.setAttribute('fill', '#44B9F8');
                    middleCircle.setAttribute('opacity', '0.3');
                    middleCircle.classList.add('pulse-middle-circle');
                    svg.appendChild(middleCircle);
                } else {
                    // Hover marker structure - smaller pulse circles but same 57x57 container
                    // Outer circle (pulsing) - centered at marker center (27.8784, 28.7498)
                    var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    outerCircle.setAttribute('cx', '27.8784');
                    outerCircle.setAttribute('cy', '28.7498');
                    outerCircle.setAttribute('r', '13.068');
                    outerCircle.setAttribute('fill', '#44B9F8');
                    outerCircle.setAttribute('opacity', '0.1');
                    outerCircle.classList.add('pulse-outer-circle');
                    svg.appendChild(outerCircle);

                    // Middle circle (pulsing)
                    var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    middleCircle.setAttribute('cx', '27.8784');
                    middleCircle.setAttribute('cy', '28.7498');
                    middleCircle.setAttribute('r', '6.0984');
                    middleCircle.setAttribute('fill', '#44B9F8');
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
                    // Both active and hover use 57x57 to match normal marker size
                    var size = 57;

                    // Use same anchor point for both active and hover to match marker anchor
                    // Both markers now use anchor: (27.8784, 28.7498)
                    var anchorOffsetX = 27.8784;
                    var anchorOffsetY = 28.7498;
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

        function initPropertyPortfolioMap() {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                console.error('Google Maps API not loaded');
                return;
            }

            // Define PulseOverlay class now that Google Maps is loaded
            definePulseOverlay();

            // Check if the map element exists
            var mapElement = document.getElementById('property-map');
            if (!mapElement) {
                // Map element doesn't exist, this script is not needed
                return;
            }

            var hotels = [];
            try {
                var dataHotels = mapElement.getAttribute('data-hotels');
                if (dataHotels) {
                    var parsed = JSON.parse(dataHotels);
                    if (Array.isArray(parsed) && parsed.length > 0) hotels = parsed;
                }
                if (hotels.length === 0 && typeof dhrPropertyPortfolioMapHotels !== 'undefined' && dhrPropertyPortfolioMapHotels.length) hotels = dhrPropertyPortfolioMapHotels;
                if (hotels.length === 0 && dhrHotelsData && dhrHotelsData.hotels && dhrHotelsData.hotels.length) hotels = dhrHotelsData.hotels;
            } catch (e) {
                if (typeof dhrPropertyPortfolioMapHotels !== 'undefined' && dhrPropertyPortfolioMapHotels.length) hotels = dhrPropertyPortfolioMapHotels;
                else if (dhrHotelsData && dhrHotelsData.hotels) hotels = dhrHotelsData.hotels;
            }
            if (!hotels) {
                hotels = [];
            }
            if (hotels.length === 0) {
                console.warn('No hotels data available; showing South Africa map');
            }

            // Filter to hotels with valid latitude/longitude so one bad entry does not break the map
            function isValidCoord(val) {
                var n = parseFloat(val);
                return isFinite(n) && n >= -90 && n <= 90;
            }
            function isValidLng(val) {
                var n = parseFloat(val);
                return isFinite(n) && n >= -180 && n <= 180;
            }
            var validHotels = hotels.filter(function (hotel) {
                return isValidCoord(hotel.latitude) && isValidLng(hotel.longitude);
            });
            if (validHotels.length === 0) {
                console.warn('No hotels with valid coordinates; showing default center');
            }

            var bounds = new google.maps.LatLngBounds();
            var deviceType = getDeviceType();
            // South Africa default center and zoom (map set to South Africa)
            var southAfricaCenter = { lat: -26.2, lng: 28.5 };
            var southAfricaZoom = 5;

            if (validHotels.length > 0) {
                validHotels.forEach(function (hotel) {
                    var lat = parseFloat(hotel.latitude);
                    var lng = parseFloat(hotel.longitude);
                    bounds.extend(new google.maps.LatLng(lat, lng));
                });
            }

            // Initialize map
            map = new google.maps.Map(document.getElementById('property-map'), {
                zoom: southAfricaZoom,
                center: southAfricaCenter,
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
                        stylers: [{ color: '#A0B6CB' }]
                    },
                    {
                        featureType: 'road',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#A0B6CB' }]
                    },
                ]
            });

            // After map loads, fit to markers with 2% zoom-in and shift to left-bottom (same zoom as other maps, content starts left and bottom)
            google.maps.event.addListenerOnce(map, 'idle', function () {
                if (validHotels.length > 0 && !bounds.isEmpty()) {
                    var padding = deviceType === 'mobile' ? 40 : (deviceType === 'tablet' ? 60 : 80);
                    var ne = bounds.getNorthEast();
                    var sw = bounds.getSouthWest();
                    var center = bounds.getCenter();
                    var latSpan = ne.lat() - sw.lat();
                    var lngSpan = ne.lng() - sw.lng();
                    var expandFactor = 0.98;
                    var expandedBounds = new google.maps.LatLngBounds(
                        new google.maps.LatLng(center.lat() - (latSpan * expandFactor) / 2, center.lng() - (lngSpan * expandFactor) / 2),
                        new google.maps.LatLng(center.lat() + (latSpan * expandFactor) / 2, center.lng() + (lngSpan * expandFactor) / 2)
                    );
                    map.fitBounds(expandedBounds, padding);
                    setTimeout(function () {
                        var mapDiv = document.getElementById('property-map');
                        if (mapDiv) {
                            var w = mapDiv.offsetWidth;
                            var h = mapDiv.offsetHeight;
                            // Pan so content sits toward left and bottom (opposite of right-bottom: pan right + up)
                            map.panBy(Math.round(w * 0.12), -Math.round(h * 0.12));
                        }
                    }, 400);
                }
            });

            // Create markers for each valid hotel
            validHotels.forEach(function (hotel, index) {
                createMarker(hotel, index);
            });

            // Add click handlers to property items
            var propertyItems = document.querySelectorAll('.property-item');
            propertyItems.forEach(function (item) {
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

                        // Open info window for clicked property
                        markerData.infoWindow.open(map, markerData.marker);

                        // Highlight property item in sidebar
                        propertyItems.forEach(function (pi) {
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

            // Store the number and hotel ID on the marker for later use
            marker.markerNumber = number;
            marker.hotelId = hotel.id;

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

                // Highlight property item in sidebar
                var propertyItems = document.querySelectorAll('.property-item');
                propertyItems.forEach(function (item) {
                    item.classList.remove('active');
                });
                var propertyItem = document.querySelector('.property-item[data-hotel-id="' + hotel.id + '"]');
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
                anchor: new google.maps.Point(27.8784, 28.7498)
            };
        }

        function createActiveMarkerIcon() {
            // Create SVG for active map marker (more visible) - no number shown when active
            var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="#062943"/>' +
                '<circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="#062943"/>' +
                '<circle cx="27.8784" cy="28.7498" r="6.0984" fill="#0B5991"/>' +
                '</svg>';

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
            var pulseOverlay = new PulseOverlay(position, map, isActive);

            // Store overlay using hotelId as unique identifier
            var markerId = marker.hotelId ? marker.hotelId.toString() : marker.getPosition().toString();
            pulseOverlays[markerId] = pulseOverlay;

            // Ensure pulse continues by forcing a redraw after a short delay
            setTimeout(function () {
                if (pulseOverlay && pulseOverlay.div) {
                    pulseOverlay.draw();
                }
            }, 100);
        }

        function stopPulse(marker) {
            var markerId = marker.hotelId ? marker.hotelId.toString() : marker.getPosition().toString();
            if (pulseOverlays[markerId]) {
                pulseOverlays[markerId].setMap(null);
                delete pulseOverlays[markerId];
            }
        }

        function setAllMarkersToNormal() {
            // First, stop all pulse overlays
            for (var markerId in pulseOverlays) {
                if (pulseOverlays.hasOwnProperty(markerId)) {
                    if (pulseOverlays[markerId]) {
                        pulseOverlays[markerId].setMap(null);
                    }
                    delete pulseOverlays[markerId];
                }
            }
            // Clear the pulseOverlays object
            pulseOverlays = {};
            
            // Then set all markers to normal icon
            markers.forEach(function (markerData) {
                // Set icon back to normal with the marker's number
                var normalIcon = createNormalMarkerIcon(markerData.marker.markerNumber);
                markerData.marker.setIcon(normalIcon);
            });
            activeMarker = null;
            hoveredMarker = null;
        }

        function setMarkerToActive(marker) {
            // Stop pulse first
            stopPulse(marker);

            // Set icon to active (no number shown when active)
            var activeIcon = createActiveMarkerIcon();
            marker.setIcon(activeIcon);

            // Start pulse for active marker
            activeMarker = marker;
            hoveredMarker = null;
            startPulse(marker, true);
        }

        function escapeHtml(text) {
            var entityMap = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return (text || '').replace(/[&<>"']/g, function (m) { return entityMap[m]; });
        }



        // Initialize map when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
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

<script>
    jQuery(document).ready(function($) {
        // Accordion toggle functionality for property panel
        var $toggle = $('.property-panel__toggle');
        var $content = $('.property-panel__content');
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