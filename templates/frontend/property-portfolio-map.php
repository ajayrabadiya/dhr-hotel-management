<?php
/**
 * Property Portfolio Map Template (Map 6)
 */

if (!defined('ABSPATH')) {
    exit;
}

$panel_title = isset($settings['panel_title']) ? $settings['panel_title'] : 'Ownership Property Portfolio';
$show_numbers = isset($settings['show_numbers']) ? $settings['show_numbers'] : true;
?>

<div class="dhr-property-portfolio-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <div class="dhr-property-portfolio-map-wrapper">
        <div id="dhr-property-portfolio-map" class="dhr-property-portfolio-map"></div>
    </div>
    <div class="dhr-property-portfolio-panel">
        <h3 class="dhr-property-panel-title"><?php echo esc_html($panel_title); ?></h3>
        <ul class="dhr-property-list">
            <?php if (!empty($hotels)): ?>
                <?php foreach ($hotels as $index => $hotel): ?>
                    <li class="dhr-property-item" data-hotel-id="<?php echo esc_attr($hotel->id); ?>" data-index="<?php echo esc_attr($index + 1); ?>">
                        <span class="dhr-property-bullet"></span>
                        <?php echo esc_html($hotel->name); ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
(function() {
    function initPropertyPortfolioMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            setTimeout(initPropertyPortfolioMap, 100);
            return;
        }
        
        var mapElement = document.getElementById('dhr-property-portfolio-map');
        if (!mapElement) {
            return;
        }
        
        var hotels = (typeof dhrHotelsData !== 'undefined' && dhrHotelsData.hotels) ? dhrHotelsData.hotels : [];
        var markers = [];
        var infoWindows = [];
        
        if (hotels.length > 0) {
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
            
            var map = new google.maps.Map(mapElement, {
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
            
            if (hotels.length > 1) {
                map.fitBounds(bounds);
            }
            
            hotels.forEach(function(hotel, index) {
                var number = (index + 1).toString().padStart(2, '0');
                
                // Create numbered marker
                var marker = new google.maps.Marker({
                    position: { lat: parseFloat(hotel.latitude), lng: parseFloat(hotel.longitude) },
                    map: map,
                    title: hotel.name,
                    label: {
                        text: number,
                        color: '#fff',
                        fontSize: '12px',
                        fontWeight: 'bold'
                    },
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 25,
                        fillColor: '#0066CC',
                        fillOpacity: 1,
                        strokeColor: '#fff',
                        strokeWeight: 2
                    }
                });
                
                // Create info window
                var infoContent = '<div class="dhr-property-info-window">' +
                    '<img src="' + (hotel.image_url || dhrHotelsData.pluginUrl + 'assets/images/default-hotel.jpg') + '" alt="' + hotel.name + '" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">' +
                    '<h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: bold;">' + hotel.name + '</h4>' +
                    '<p style="margin: 0 0 10px 0; font-size: 12px; color: #666;">' + hotel.city + ', ' + hotel.province + '</p>' +
                    '<a href="' + (hotel.google_maps_url || '#') + '" target="_blank" style="display: inline-block; padding: 8px 16px; background: #0066CC; color: #fff; text-decoration: none; border-radius: 4px; font-size: 12px;">View Portfolio</a>' +
                    '</div>';
                
                var infoWindow = new google.maps.InfoWindow({
                    content: infoContent
                });
                
                marker.addListener('click', function() {
                    infoWindows.forEach(function(iw) { iw.close(); });
                    infoWindow.open(map, marker);
                });
                
                markers.push({ marker: marker, hotel: hotel, index: index });
                infoWindows.push(infoWindow);
            });
            
            // Handle property list item clicks
            var propertyItems = document.querySelectorAll('.dhr-property-item');
            propertyItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    var hotelId = parseInt(this.getAttribute('data-hotel-id'));
                    var markerData = markers.find(function(m) {
                        return m.hotel.id == hotelId;
                    });
                    
                    if (markerData) {
                        infoWindows.forEach(function(iw) { iw.close(); });
                        var infoWindow = infoWindows[markerData.index];
                        infoWindow.open(map, markerData.marker);
                        map.setCenter(markerData.marker.getPosition());
                        map.setZoom(15);
                    }
                });
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPropertyPortfolioMap);
    } else {
        initPropertyPortfolioMap();
    }
})();
</script>

