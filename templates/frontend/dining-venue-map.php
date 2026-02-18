<?php
/**
 * Dining Venue Map Template (Map 4)
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

$overview_label = isset($settings['overview_label']) ? $settings['overview_label'] : 'Weddings';
$main_heading = isset($settings['main_heading']) ? $settings['main_heading'] : 'Find A Dining Venue';
$description = isset($settings['description']) ? $settings['description'] : 'Whether you\'re savoring fresh seafood with a view of Table Mountain or indulging in gourmet delights by the Indian Ocean, our dining experiences promise to delight every palate.';
$reservation_label = isset($settings['reservation_label']) ? $settings['reservation_label'] : 'RESERVATION BY PHONE';
$reservation_phone = isset($settings['reservation_phone']) ? $settings['reservation_phone'] : '+27 (0)13 243 9401/2';
$dropdown_placeholder = isset($settings['dropdown_placeholder']) ? $settings['dropdown_placeholder'] : 'Select a Hotel';
$book_now_text = isset($settings['book_now_text']) ? $settings['book_now_text'] : 'Get A Quote';
?>

<div class="all-maps dining-venue-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div id="dining-venue-map" class="dining-venue-map" data-hotels="<?php echo esc_attr(wp_json_encode($hotels_js)); ?>"></div>
    <div class="dining-venue-info-content">
        <div class="mobile-hotel-select" id="dining-mobile-hotel-select">
            <select id="dining-hotel-dropdown" class="hotel-dropdown">
                <option value="">
                    <?php echo esc_html($dropdown_placeholder); ?>
                </option>
            </select>
        </div>
        <p class="map-label">
            <?php echo esc_html($overview_label); ?>
        </p>
        <h2 class="map-title">
            <?php echo esc_html($main_heading); ?>
        </h2>
        <p class="map-description">
            <?php echo esc_html($description); ?>
        </p>
        <div class="hotel-reservation-info">
            <div class="hotel-phone-section">
                <span class="hotel-phone-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.8203 3.75C10.166 3.75 9.52149 3.98438 8.98438 4.41406L8.90626 4.45312L8.86719 4.49219L4.96094 8.51562L5.00001 8.55469C3.79395 9.66797 3.42286 11.333 3.94532 12.7734C3.9502 12.7832 3.94044 12.8027 3.94532 12.8125C5.00489 15.8447 7.71485 21.6992 13.0078 26.9922C18.3203 32.3047 24.2529 34.9072 27.1875 36.0547H27.2266C28.7451 36.5625 30.3906 36.2012 31.5625 35.1953L35.5078 31.25C36.543 30.2148 36.543 28.418 35.5078 27.3828L30.4297 22.3047L30.3906 22.2266C29.3555 21.1914 27.5195 21.1914 26.4844 22.2266L23.9844 24.7266C23.0811 24.292 20.9277 23.1787 18.8672 21.2109C16.8213 19.2578 15.7764 17.0117 15.3906 16.1328L17.8906 13.6328C18.9404 12.583 18.96 10.835 17.8516 9.80469L17.8906 9.76562L17.7734 9.64844L12.7734 4.49219L12.7344 4.45312L12.6563 4.41406C12.1191 3.98438 11.4746 3.75 10.8203 3.75ZM10.8203 6.25C10.9131 6.25 11.0059 6.29395 11.0938 6.36719L16.0938 11.4844L16.2109 11.6016C16.2012 11.5918 16.2842 11.7236 16.1328 11.875L13.0078 15L12.4219 15.5469L12.6953 16.3281C12.6953 16.3281 14.1309 20.1709 17.1484 23.0469L17.4219 23.2812C20.3272 25.9326 23.75 27.3828 23.75 27.3828L24.5313 27.7344L28.2422 24.0234C28.457 23.8086 28.418 23.8086 28.6328 24.0234L33.75 29.1406C33.9648 29.3555 33.9648 29.2773 33.75 29.4922L29.9219 33.3203C29.3457 33.8135 28.7354 33.916 28.0078 33.6719C25.1758 32.5586 19.6729 30.1416 14.7656 25.2344C9.81934 20.2881 7.23634 14.6777 6.28907 11.9531C6.09864 11.4453 6.23536 10.6934 6.67969 10.3125L6.75782 10.2344L10.5469 6.36719C10.6348 6.29395 10.7275 6.25 10.8203 6.25Z"
                            fill="currentColor" />
                    </svg>
                </span>
                <div>
                    <?php if (!empty($reservation_label)): ?>
                        <p class="hotel-reservation-label">
                            <?php echo esc_html($reservation_label); ?>
                        </p>
                    <?php endif; ?>
                    <a class="hotel-phone-number" href="tel:<?php echo esc_html($reservation_phone); ?>"
                        target="_blank">
                        <?php echo esc_html($reservation_phone); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dining-venue-info-window-template" style="display: none;">
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
    var dhrDiningVenueMapSettings = {
        book_now_text: '<?php echo esc_js($book_now_text); ?>'
    };
    var dhrDiningVenueMapHotels = <?php echo json_encode($hotels_js); ?>;
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
                    // Normal marker structure - EXACT match
                    // Outer circle (pulsing)
                    var outerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    outerCircle.setAttribute('cx', '13.068');
                    outerCircle.setAttribute('cy', '13.068');
                    outerCircle.setAttribute('r', '13.068');
                    outerCircle.setAttribute('fill', '#44B9F8');
                    outerCircle.setAttribute('opacity', '0.1');
                    outerCircle.classList.add('pulse-outer-circle');
                    svg.appendChild(outerCircle);

                    // Middle circle (pulsing)
                    var middleCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    middleCircle.setAttribute('cx', '13.068');
                    middleCircle.setAttribute('cy', '13.0681');
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

        function initDiningVenueMap() {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                console.error('Google Maps API not loaded');
                return;
            }

            // Define PulseOverlay class now that Google Maps is loaded
            definePulseOverlay();

            // Check if the map element exists
            var mapElement = document.getElementById('dining-venue-map');
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
                if (hotels.length === 0 && typeof dhrDiningVenueMapHotels !== 'undefined' && dhrDiningVenueMapHotels.length) hotels = dhrDiningVenueMapHotels;
                if (hotels.length === 0 && dhrHotelsData && dhrHotelsData.hotels && dhrHotelsData.hotels.length) hotels = dhrHotelsData.hotels;
            } catch (e) {
                if (typeof dhrDiningVenueMapHotels !== 'undefined' && dhrDiningVenueMapHotels.length) hotels = dhrDiningVenueMapHotels;
                else if (dhrHotelsData && dhrHotelsData.hotels) hotels = dhrHotelsData.hotels;
            }
            if (!hotels || hotels.length === 0) {
                console.warn('No hotels data available');
                return;
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

            map = new google.maps.Map(document.getElementById('dining-venue-map'), {
                zoom: southAfricaZoom,
                center: southAfricaCenter,
                minZoom: 3,
                maxZoom: 10,
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

            // After map loads, fit to markers with zoom-in and shift to right-bottom (same as partner-portfolio map)
            google.maps.event.addListenerOnce(map, 'idle', function () {
                if (validHotels.length > 0 && !bounds.isEmpty()) {
                    var padding = deviceType === 'mobile' ? 40 : (deviceType === 'tablet' ? 60 : 80);
                    // Zoom in: use expand factor < 1 so map is slightly zoomed in
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
                    // Pan so content sits toward right-bottom (after fitBounds animation)
                    setTimeout(function () {
                        var mapDiv = document.getElementById('dining-venue-map');
                        if (mapDiv) {
                            var w = mapDiv.offsetWidth;
                            var h = mapDiv.offsetHeight;
                            map.panBy(-Math.round(w * 0.14), -Math.round(h * 0.00));
                        }
                    }, 400);
                }
            });

            // Create markers for each valid hotel
            validHotels.forEach(function (hotel, index) {
                createMarker(hotel, index);
            });

            // Populate mobile hotel dropdown
            populateMobileHotelDropdown(validHotels);
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
                icon: normalIcon
            });

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

                // Reset dropdown selection
                var dropdown = document.getElementById('dining-hotel-dropdown');
                if (dropdown) {
                    dropdown.value = '';
                }
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

                // Update dropdown selection
                var dropdown = document.getElementById('dining-hotel-dropdown');
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
        }

        function getInfoWindowContent(hotel) {
            var templateElement = document.getElementById('dining-venue-info-window-template');
            var template = templateElement.innerHTML;
            var bookNowText = (typeof dhrDiningVenueMapSettings !== 'undefined' && dhrDiningVenueMapSettings.book_now_text) ? dhrDiningVenueMapSettings.book_now_text : 'Get A Quote';
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

        function createNormalMarkerIcon() {
            // Create SVG for normal map marker
            var svg = '<svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.1" cx="13.068" cy="13.068" r="13.068" fill="#062943"/><circle opacity="0.3" cx="13.068" cy="13.0681" r="6.0984" fill="#062943"/><circle cx="13.068" cy="13.0681" r="6.0984" fill="#0B5991"/></svg>';

            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(27, 27),
                anchor: new google.maps.Point(13.068, 13.0681)
            };
        }

        function createActiveMarkerIcon() {
            // Create SVG for active map marker (more visible)
            var svg = '<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.1" cx="28.314" cy="28.314" r="28.314" fill="#062943"/><circle opacity="0.3" cx="27.8784" cy="28.7496" r="20.9088" fill="#062943"/><circle cx="27.8784" cy="28.7498" r="6.0984" fill="#0B5991"/></svg>';

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
            var normalIcon = createNormalMarkerIcon();
            markers.forEach(function (markerData) {
                // Stop pulse for all markers
                stopPulse(markerData.marker);
                markerData.marker.setIcon(normalIcon);
            });
            activeMarker = null;
            hoveredMarker = null;
        }

        function setMarkerToActive(marker) {
            // Stop pulse first
            stopPulse(marker);

            var activeIcon = createActiveMarkerIcon();
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

        // Populate mobile hotel dropdown
        function populateMobileHotelDropdown(hotels) {
            var dropdown = document.getElementById('dining-hotel-dropdown');
            if (!dropdown) {
                return;
            }

            // Clear existing options except the first one
            var dropdownPlaceholder = '<?php echo esc_js($dropdown_placeholder); ?>';
            dropdown.innerHTML = '<option value="">' + dropdownPlaceholder + '</option>';

            // Add hotels to dropdown
            hotels.forEach(function (hotel, index) {
                var option = document.createElement('option');
                option.value = hotel.id;
                option.textContent = hotel.name;
                option.setAttribute('data-index', index);
                dropdown.appendChild(option);
            });

            // Add change event listener
            dropdown.addEventListener('change', function () {
                var selectedHotelId = this.value;
                if (!selectedHotelId) {
                    return;
                }

                // Find the marker for this hotel
                var markerData = markers.find(function (m) {
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
                setTimeout(function () {
                    var mapDiv = document.getElementById('dining-venue-map');
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
                if (map && fitMapBounds) {
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