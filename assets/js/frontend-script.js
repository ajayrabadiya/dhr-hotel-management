/**
 * Frontend JavaScript for DHR Hotel Management
 */

(function($) {
    'use strict';
    
    var map;
    var markers = [];
    var infoWindows = [];
    
    function initMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API not loaded');
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
        
        hotels.forEach(function(hotel) {
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
        map = new google.maps.Map(document.getElementById('dhr-hotel-map'), {
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
                    stylers: [{ color: '#c9c9c9' }]
                },
                {
                    featureType: 'road',
                    elementType: 'labels.text.fill',
                    stylers: [{ color: '#999999' }]
                }
            ]
        });
        
        // Fit bounds to show all hotels
        if (hotels.length > 1) {
            map.fitBounds(bounds);
        }
        
        // Create markers for each hotel
        hotels.forEach(function(hotel, index) {
            createMarker(hotel, index);
        });
        
        // Add click handlers to hotel items
        $('.dhr-hotel-item').on('click', function() {
            var hotelId = $(this).data('hotel-id');
            var marker = markers.find(function(m) {
                return m.hotelId == hotelId;
            });
            
            if (marker) {
                // Close all info windows
                infoWindows.forEach(function(iw) {
                    iw.close();
                });
                
                // Open info window for clicked hotel
                marker.infoWindow.open(map, marker.marker);
                map.setCenter(marker.marker.getPosition());
                map.setZoom(15);
            }
        });
    }
    
    function createMarker(hotel, index) {
        var position = {
            lat: parseFloat(hotel.latitude),
            lng: parseFloat(hotel.longitude)
        };
        
        // Create marker
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: hotel.name,
            animation: index === 0 ? google.maps.Animation.DROP : null
        });
        
        // Create info window content
        var infoWindowContent = getInfoWindowContent(hotel);
        
        // Create info window
        var infoWindow = new google.maps.InfoWindow({
            content: infoWindowContent
        });
        
        // Add click listener to marker
        marker.addListener('click', function() {
            // Close all other info windows
            infoWindows.forEach(function(iw) {
                iw.close();
            });
            
            // Open this info window
            infoWindow.open(map, marker);
            
            // Highlight hotel item in sidebar
            $('.dhr-hotel-item').removeClass('active');
            $('.dhr-hotel-item[data-hotel-id="' + hotel.id + '"]').addClass('active');
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
            setTimeout(function() {
                infoWindow.open(map, marker);
            }, 500);
        }
    }
    
    function getInfoWindowContent(hotel) {
        var template = $('#dhr-hotel-info-window-template').html();
        
        var content = template
            .replace(/{name}/g, escapeHtml(hotel.name))
            .replace(/{city}/g, escapeHtml(hotel.city))
            .replace(/{province}/g, escapeHtml(hotel.province))
            .replace(/{image_url}/g, hotel.image_url || (dhrHotelsData.pluginUrl + 'assets/images/default-hotel.jpg'))
            .replace(/{google_maps_url}/g, hotel.google_maps_url || 'https://www.google.com/maps?q=' + hotel.latitude + ',' + hotel.longitude)
            .replace(/{phone}/g, escapeHtml(hotel.phone || ''));
        
        return content;
    }
    
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (text || '').replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initialize map when DOM is ready
    $(document).ready(function() {
        // Wait for Google Maps API to load
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initMap();
        } else {
            // Wait for Google Maps API
            window.addEventListener('load', function() {
                setTimeout(initMap, 1000);
            });
        }
    });
    
})(jQuery);


