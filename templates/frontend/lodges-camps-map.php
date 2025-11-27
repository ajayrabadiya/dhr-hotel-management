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

<div class="dhr-lodges-camps-map-container" style="height: <?php echo esc_attr($atts['height']); ?>;">
    <?php if ($show_list && !empty($hotels)): ?>
    <div class="dhr-lodges-camps-panel">
        <ul class="dhr-lodges-list">
            <?php foreach ($hotels as $index => $hotel): ?>
                <li class="dhr-lodge-item" data-hotel-id="<?php echo esc_attr($hotel->id); ?>">
                    <?php echo esc_html($index + 1); ?>. <?php echo esc_html($hotel->name); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="dhr-lodges-legend">
            <div class="dhr-legend-item">
                <span class="dhr-legend-dot dhr-legend-lodges"></span>
                <span class="dhr-legend-text"><?php echo esc_html($legend_lodges); ?></span>
            </div>
            <div class="dhr-legend-item">
                <span class="dhr-legend-dot dhr-legend-weddings"></span>
                <span class="dhr-legend-text"><?php echo esc_html($legend_weddings); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="dhr-lodges-camps-map-wrapper">
        <div id="dhr-lodges-camps-map" class="dhr-lodges-camps-map"></div>
    </div>
</div>

<script>
(function() {
    function initLodgesCampsMap() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            setTimeout(initLodgesCampsMap, 100);
            return;
        }
        
        var mapElement = document.getElementById('dhr-lodges-camps-map');
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
                // Alternate between lodges (blue) and weddings (orange)
                var isLodge = index % 2 === 0;
                var markerColor = isLodge ? '#0066CC' : '#FF6B35';
                
                var marker = new google.maps.Marker({
                    position: { lat: parseFloat(hotel.latitude), lng: parseFloat(hotel.longitude) },
                    map: map,
                    title: hotel.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 12,
                        fillColor: markerColor,
                        fillOpacity: 1,
                        strokeColor: '#fff',
                        strokeWeight: 2
                    }
                });
                
                var infoContent = '<div class="dhr-lodge-info-window">' +
                    '<h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: bold;">' + hotel.name + '</h4>' +
                    '<p style="margin: 0; font-size: 12px; color: #666;">' + hotel.city + ', ' + hotel.province + '</p>' +
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
            
            // Handle list item clicks
            var lodgeItems = document.querySelectorAll('.dhr-lodge-item');
            lodgeItems.forEach(function(item) {
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
        document.addEventListener('DOMContentLoaded', initLodgesCampsMap);
    } else {
        initLodgesCampsMap();
    }
})();
</script>

