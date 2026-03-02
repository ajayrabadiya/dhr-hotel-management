<?php
/**
 * Partner Portfolio Map Template (Map 3)
 */

if (!defined('ABSPATH')) {
    exit;
}

$hotels_js = array();
$cityblue_ids_map = array_flip(isset($cityblue_hotel_ids) ? $cityblue_hotel_ids : array());
if (!empty($hotels)) {
    foreach ($hotels as $h) {
        $hid = (int) $h->id;
        $hotels_js[] = array(
            'id' => $hid, 'name' => isset($h->name) ? wp_unslash((string) $h->name) : '', 'description' => isset($h->description) ? wp_unslash((string) $h->description) : '',
            'address' => isset($h->address) ? $h->address : '', 'city' => isset($h->city) ? $h->city : '', 'province' => isset($h->province) ? $h->province : '',
            'country' => isset($h->country) ? $h->country : '', 'latitude' => isset($h->latitude) ? floatval($h->latitude) : 0, 'longitude' => isset($h->longitude) ? floatval($h->longitude) : 0,
            'phone' => isset($h->phone) ? $h->phone : '', 'email' => isset($h->email) ? $h->email : '', 'website' => isset($h->website) ? $h->website : '',
            'image_url' => isset($h->image_url) ? $h->image_url : '', 'google_maps_url' => isset($h->google_maps_url) ? $h->google_maps_url : '', 'status' => isset($h->status) ? $h->status : 'active',
            'is_cityblue' => isset($cityblue_ids_map[$hid]),
            'hotel_code' => isset($h->hotel_code) ? $h->hotel_code : ''
        );
    }
}
// Map-wise default hotel: use map settings first, then fall back to global Book Your Stay code.
$default_hotel_code = '';
if (isset($settings['default_hotel_code']) && $settings['default_hotel_code'] !== '') {
    $default_hotel_code = trim((string) $settings['default_hotel_code']);
} else {
    $default_hotel_code = trim((string) get_option('bys_hotel_code', ''));
}

$overview_label = isset($settings['overview_label']) ? $settings['overview_label'] : 'DISCOVER AFRICA';
$main_heading = isset($settings['main_heading']) ? $settings['main_heading'] : 'Our Partner Portfolio';
$description = isset($settings['description']) ? $settings['description'] : 'Together with CityBlue Hotels, we\'re crafting a unified hospitality experience that celebrates the rich cultures, stunning landscapes, and warm hospitality that Africa is known for.';
$legend_cityblue = isset($settings['legend_cityblue']) ? $settings['legend_cityblue'] : 'CityBlue Hotels';
$legend_dream = isset($settings['legend_dream']) ? $settings['legend_dream'] : 'Dream Hotels & Resorts';
$book_now_text = isset($settings['book_now_text']) ? $settings['book_now_text'] : 'Get A Quote';
?>

<div class="all-maps partner-portfolio-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div id="partner-portfolio-map" class="partner-portfolio-map" data-hotels="<?php echo esc_attr(wp_json_encode($hotels_js)); ?>" data-default-hotel-code="<?php echo esc_attr($default_hotel_code); ?>"></div>
    <div class="partner-info-content">
        <p class="map-label"><?php echo esc_html($overview_label); ?></p>
        <h2 class="map-title"><?php echo esc_html($main_heading); ?></h2>
        <p class="map-description"><?php echo esc_html($description); ?></p>
        <div class="partner-portfolio-legend">
            <ul>
                <li><?php echo esc_html($legend_cityblue); ?></li>
                <li><?php echo esc_html($legend_dream); ?></li>
            </ul>
        </div>
    </div>
</div>

<div id="partner-portfolio-info-window-template" style="display: none;">
    <div class="info-window">
        <div class="info-window-image">
            <img src="{image_url}" alt="{name}"
                onerror="this.onerror=null; this.src='{pluginUrl}assets/images/default-hotel.jpg';">
        </div>
        <div class="info-window-content">
            <h3 class="info-window-title">{name}</h3>
            <p class="info-window-location">{city} | {province}</p>
            <div class="info-window-actions">
                <a href="{google_maps_url}" target="_blank" class="btn-info">
                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.4544 1.95996C5.77085 1.95996 1.96021 5.77061 1.96021 10.4542C1.96021 15.1377 5.77085 18.9484 10.4544 18.9484C15.138 18.9484 18.9486 15.1377 18.9486 10.4542C18.9486 5.77061 15.138 1.95996 10.4544 1.95996ZM10.4544 3.26676C14.431 3.26676 17.6418 6.47761 17.6418 10.4542C17.6418 14.4307 14.431 17.6416 10.4544 17.6416C6.47785 17.6416 3.26701 14.4307 3.26701 10.4542C3.26701 6.47761 6.47785 3.26676 10.4544 3.26676ZM9.80101 6.53376V7.84056H11.1078V6.53376H9.80101ZM9.80101 9.14736V14.3746H11.1078V9.14736H9.80101Z"
                            fill="#0B5991" />
                    </svg>
                </a>
                <a href="tel:{phone}" class="btn-book">
                    {book_now_text}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
var dhrPartnerPortfolioMapSettings = {
    book_now_text: '<?php echo esc_js($book_now_text); ?>',
    default_hotel_code: '<?php echo esc_js($default_hotel_code); ?>'
};
var dhrPartnerPortfolioMapHotels = <?php echo json_encode($hotels_js); ?>;
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
        var fitMapBounds;

        // Color schemes for different marker types
        var colorSchemes = {
            cityblue: {
                lightest: '#B3D9F2',
                medium: '#66B3E0',
                dark: '#0B5991'
            },
            dream: {
                lightest: '#D4EDFF',
                medium: '#99D6FF',
                dark: '#4DB8FF'
            }
        };

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
            PulseOverlay = function (position, map, isActive, isCityBlue) {
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
                if (this.isActive) {
                    div.classList.add('dhr-marker-pulse-active');
                } else {
                    div.classList.add('dhr-marker-pulse-hover');
                }

                var colors = this.isCityBlue ? colorSchemes.cityblue : colorSchemes.dream;

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

                if (this.isActive) {
                    // Active marker structure - EXACT match
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
                } else {
                    // Normal marker structure - EXACT match
                    // Outer circle (pulsing)
                    var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    outerCircle.setAttribute('cx', '13.068');
                    outerCircle.setAttribute('cy', '13.068');
                    outerCircle.setAttribute('r', '13.068');
                    outerCircle.setAttribute('fill', colors.lightest);
                    outerCircle.setAttribute('opacity', '0.1');
                    outerCircle.classList.add('pulse-outer-circle');
                    svg.appendChild(outerCircle);

                    // Middle circle (pulsing)
                    var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    middleCircle.setAttribute('cx', '13.068');
                    middleCircle.setAttribute('cy', '13.0681');
                    middleCircle.setAttribute('r', '6.0984');
                    middleCircle.setAttribute('fill', colors.medium);
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

        function initPartnerPortfolioMap() {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                console.error('Google Maps API not loaded');
                return;
            }

            // Define PulseOverlay class now that Google Maps is loaded
            definePulseOverlay();

            // Check if the map element exists
            var mapElement = document.getElementById('partner-portfolio-map');
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
                if (hotels.length === 0 && typeof dhrPartnerPortfolioMapHotels !== 'undefined' && dhrPartnerPortfolioMapHotels.length) hotels = dhrPartnerPortfolioMapHotels;
                if (hotels.length === 0 && dhrHotelsData && dhrHotelsData.hotels && dhrHotelsData.hotels.length) hotels = dhrHotelsData.hotels;
            } catch (e) {
                if (typeof dhrPartnerPortfolioMapHotels !== 'undefined' && dhrPartnerPortfolioMapHotels.length) hotels = dhrPartnerPortfolioMapHotels;
                else if (dhrHotelsData && dhrHotelsData.hotels) hotels = dhrHotelsData.hotels;
            }
            if (!hotels || hotels.length === 0) {
                console.warn('No hotels data available');
                return;
            }

            var validHotels = hotels.filter(function (hotel) {
                var lat = parseFloat(hotel.latitude);
                var lng = parseFloat(hotel.longitude);
                return isFinite(lat) && lat >= -90 && lat <= 90 && isFinite(lng) && lng >= -180 && lng <= 180;
            });

            var southAfricaCenter = { lat: -36.0, lng: 24.0 };
            var southAfricaZoom = 4.5;
            var bounds = new google.maps.LatLngBounds();
            var deviceType = getDeviceType();

            if (validHotels.length > 0) {
                validHotels.forEach(function (hotel) {
                    var lat = parseFloat(hotel.latitude);
                    var lng = parseFloat(hotel.longitude);
                    bounds.extend(new google.maps.LatLng(lat, lng));
                });
            }

            // Start with South Africa by default; fit to markers after load
            map = new google.maps.Map(document.getElementById('partner-portfolio-map'), {
                zoom: southAfricaZoom,
                center: southAfricaCenter,
                minZoom: 2,
                maxZoom: 10,
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

            function activateDefaultHotelMarker() {
                var defaultCode = ((typeof dhrPartnerPortfolioMapSettings !== 'undefined' && dhrPartnerPortfolioMapSettings.default_hotel_code) || mapElement.getAttribute('data-default-hotel-code') || '').trim();
                if (!defaultCode) return;
                defaultCode = defaultCode.toUpperCase();
                for (var i = 0; i < validHotels.length; i++) {
                    var hCode = (String(validHotels[i].hotel_code || '')).trim().toUpperCase();
                    if (hCode && hCode === defaultCode) {
                        var m = markers[i];
                        if (m) {
                            (function (markerData) {
                                setTimeout(function () {
                                    google.maps.event.trigger(markerData.marker, 'click');
                                }, 50);
                            })(m);
                        }
                        break;
                    }
                }
            }

            // After map loads, fit to markers with 10% zoom out and shift to right-bottom
            google.maps.event.addListenerOnce(map, 'idle', function () {
                if (validHotels.length > 0 && !bounds.isEmpty()) {
                    var padding = deviceType === 'mobile' ? 40 : (deviceType === 'tablet' ? 60 : 80);
                    // Zoom out 10%: expand bounds by 10% then fit (avoids setZoom timing issues)
                    var ne = bounds.getNorthEast();
                    var sw = bounds.getSouthWest();
                    var center = bounds.getCenter();
                    var latSpan = ne.lat() - sw.lat();
                    var lngSpan = ne.lng() - sw.lng();
                    var expandFactor = 1.07;
                    var expandedBounds = new google.maps.LatLngBounds(
                        new google.maps.LatLng(center.lat() - (latSpan * expandFactor) / 2, center.lng() - (lngSpan * expandFactor) / 2),
                        new google.maps.LatLng(center.lat() + (latSpan * expandFactor) / 2, center.lng() + (lngSpan * expandFactor) / 2)
                    );
                    map.fitBounds(expandedBounds, padding);
                    // Pan so content sits toward right-bottom (after fitBounds animation)
                    setTimeout(function () {
                        var mapDiv = document.getElementById('partner-portfolio-map');
                        if (mapDiv) {
                            var w = mapDiv.offsetWidth;
                            var h = mapDiv.offsetHeight;
                            map.panBy(-Math.round(w * 0.22), -Math.round(h * 0.22));
                        }
                        activateDefaultHotelMarker();
                        setTimeout(activateDefaultHotelMarker, 500);
                    }, 400);
                } else {
                    activateDefaultHotelMarker();
                    setTimeout(activateDefaultHotelMarker, 500);
                }
            });

            // Create markers for each valid hotel
            validHotels.forEach(function (hotel, index) {
                createMarker(hotel, index);
            });
        }

        function createMarker(hotel, index) {
            var position = {
                lat: parseFloat(hotel.latitude),
                lng: parseFloat(hotel.longitude)
            };

            var isCityBlue = !!hotel.is_cityblue;

            // Create normal marker icon
            var normalIcon = createNormalMarkerIcon(isCityBlue);

            // Create marker
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: hotel.name,
                icon: normalIcon
            });

            marker.isCityBlue = isCityBlue;

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
                hotelId: hotel.id
            });

            infoWindows.push(infoWindow);
        }

        function getInfoWindowContent(hotel) {
            var templateElement = document.getElementById('partner-portfolio-info-window-template');
            var template = templateElement.innerHTML;
            var bookNowText = (typeof dhrPartnerPortfolioMapSettings !== 'undefined' && dhrPartnerPortfolioMapSettings.book_now_text) ? dhrPartnerPortfolioMapSettings.book_now_text : 'Get A Quote';
            var pluginUrl = (typeof dhrHotelsData !== 'undefined' && dhrHotelsData.pluginUrl) ? dhrHotelsData.pluginUrl : '';

            var content = template
                .replace(/{name}/g, escapeHtml(hotel.name))
                .replace(/{city}/g, escapeHtml(hotel.city))
                .replace(/{province}/g, escapeHtml(hotel.province))
                .replace(/{image_url}/g, hotel.image_url || (pluginUrl + 'assets/images/default-hotel.jpg'))
                .replace(/{pluginUrl}/g, pluginUrl)
                .replace(/{google_maps_url}/g, hotel.google_maps_url || 'https://www.google.com/maps?q=' + hotel.latitude + ',' + hotel.longitude)
                .replace(/{phone}/g, escapeHtml(hotel.phone || ''))
                .replace(/{book_now_text}/g, escapeHtml(bookNowText));

            return content;
        }

        function createNormalMarkerIcon(isCityBlue) {
            var colors = isCityBlue ? colorSchemes.cityblue : colorSchemes.dream;
            
            var outerOpacity = isCityBlue ? '0.5' : '1';
            
            var svg = '<svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<circle opacity="' + outerOpacity + '" cx="13.068" cy="13.068" r="13.068" fill="' + colors.lightest + '"/>' +
                '<circle opacity="0.3" cx="13.068" cy="13.0681" r="6.0984" fill="' + colors.medium + '"/>' +
                '<circle cx="13.068" cy="13.0681" r="6.0984" fill="' + colors.dark + '"/>' +
                '</svg>';

            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(27, 27),
                anchor: new google.maps.Point(13.068, 13.0681)
            };
        }

        function createActiveMarkerIcon(isCityBlue) {
            var colors = isCityBlue ? colorSchemes.cityblue : colorSchemes.dream;
            
            var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="' + colors.lightest + '"/>' +
                '<circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="' + colors.medium + '"/>' +
                '<circle cx="27.8784" cy="28.7498" r="6.0984" fill="' + colors.dark + '"/>' +
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
            var pulseOverlay = new PulseOverlay(position, map, isActive, marker.isCityBlue);

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
                var normalIcon = createNormalMarkerIcon(markerData.marker.isCityBlue);
                markerData.marker.setIcon(normalIcon);
            });
            activeMarker = null;
            hoveredMarker = null;
        }

        function setMarkerToActive(marker) {
            // Stop pulse first
            stopPulse(marker);

            var activeIcon = createActiveMarkerIcon(marker.isCityBlue);
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
                    var mapDiv = document.getElementById('partner-portfolio-map');
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
                // Only fit bounds if no marker is active (to prevent zoom out on marker click)
                if (map && fitMapBounds && !activeMarker) {
                    fitMapBounds();
                }
                
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