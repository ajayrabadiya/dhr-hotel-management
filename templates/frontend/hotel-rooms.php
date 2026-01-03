<?php
/**
 * Hotel Rooms Display Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$hotel_code = $hotel_data['hotel_code'];
$hotel_name = $hotel_data['hotel_name'];
$rooms = $hotel_data['rooms'];
$columns = $hotel_data['columns'];
$show_images = $hotel_data['show_images'];
$show_amenities = $hotel_data['show_amenities'];
$show_description = $hotel_data['show_description'];
?>

<div class="dhr-hotel-rooms-container">
    <div class="dhr-hotel-rooms-header">
        <h2 class="dhr-hotel-rooms-title"><?php echo esc_html($hotel_name); ?></h2>
        <p class="dhr-hotel-rooms-subtitle"><?php printf(__('Available Rooms (%d)', 'dhr-hotel-management'), count($rooms)); ?></p>
    </div>
    
    <div class="dhr-hotel-rooms-grid dhr-rooms-columns-<?php echo esc_attr($columns); ?>">
        <?php foreach ($rooms as $room): ?>
            <div class="dhr-hotel-room-card">
                <?php if ($show_images && !empty($room->images) && is_array($room->images)): ?>
                    <div class="dhr-room-images">
                        <?php if (count($room->images) > 1): ?>
                            <div class="dhr-room-image-slider">
                                <?php foreach ($room->images as $index => $image_url): ?>
                                    <div class="dhr-room-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($room->room_type_name); ?>" loading="lazy">
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($room->images) > 1): ?>
                                    <button class="dhr-room-slider-prev" aria-label="<?php _e('Previous image', 'dhr-hotel-management'); ?>">‹</button>
                                    <button class="dhr-room-slider-next" aria-label="<?php _e('Next image', 'dhr-hotel-management'); ?>">›</button>
                                    <div class="dhr-room-slider-dots">
                                        <?php foreach ($room->images as $index => $image_url): ?>
                                            <span class="dhr-room-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="dhr-room-single-image">
                                <img src="<?php echo esc_url($room->images[0]); ?>" alt="<?php echo esc_attr($room->room_type_name); ?>" loading="lazy">
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="dhr-room-content">
                    <h3 class="dhr-room-title"><?php echo esc_html($room->room_type_name); ?></h3>
                    
                    <div class="dhr-room-meta">
                        <?php if ($room->max_occupancy): ?>
                            <div class="dhr-room-meta-item">
                                <span class="dhr-room-meta-label"><?php _e('Max Occupancy:', 'dhr-hotel-management'); ?></span>
                                <span class="dhr-room-meta-value"><?php echo esc_html($room->max_occupancy); ?> <?php _e('guests', 'dhr-hotel-management'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($room->standard_num_beds): ?>
                            <div class="dhr-room-meta-item">
                                <span class="dhr-room-meta-label"><?php _e('Beds:', 'dhr-hotel-management'); ?></span>
                                <span class="dhr-room-meta-value"><?php echo esc_html($room->standard_num_beds); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($room->room_size): ?>
                            <div class="dhr-room-meta-item">
                                <span class="dhr-room-meta-label"><?php _e('Size:', 'dhr-hotel-management'); ?></span>
                                <span class="dhr-room-meta-value"><?php echo esc_html($room->room_size); ?> m²</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($show_description && !empty($room->description)): ?>
                        <div class="dhr-room-description">
                            <?php echo wp_kses_post($room->description); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($show_amenities && !empty($room->amenities) && is_array($room->amenities)): ?>
                        <div class="dhr-room-amenities">
                            <h4 class="dhr-room-amenities-title"><?php _e('Amenities', 'dhr-hotel-management'); ?></h4>
                            <ul class="dhr-room-amenities-list">
                                <?php foreach ($room->amenities as $amenity): ?>
                                    <li class="dhr-room-amenity-item">
                                        <span class="dhr-room-amenity-name"><?php echo esc_html($amenity['name']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Room image slider functionality
    $('.dhr-room-image-slider').each(function() {
        var $slider = $(this);
        var $slides = $slider.find('.dhr-room-slide');
        var $dots = $slider.find('.dhr-room-dot');
        var currentSlide = 0;
        var totalSlides = $slides.length;
        
        if (totalSlides <= 1) return;
        
        var $prevBtn = $slider.find('.dhr-room-slider-prev');
        var $nextBtn = $slider.find('.dhr-room-slider-next');
        
        function showSlide(index) {
            $slides.removeClass('active');
            $dots.removeClass('active');
            $slides.eq(index).addClass('active');
            $dots.eq(index).addClass('active');
        }
        
        $nextBtn.on('click', function() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        });
        
        $prevBtn.on('click', function() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        });
        
        $dots.on('click', function() {
            currentSlide = $(this).data('slide');
            showSlide(currentSlide);
        });
        
        // Auto-play (optional)
        // setInterval(function() {
        //     currentSlide = (currentSlide + 1) % totalSlides;
        //     showSlide(currentSlide);
        // }, 5000);
    });
});
</script>
