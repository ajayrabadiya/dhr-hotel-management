<?php
/**
 * Fifth Package Design (Bird Packages Experiences) Template
 * Shortcodes: [dhr_package_experiences_design] or [dhr_category_list]
 * Displays categories from Admin → Category List in the same card design.
 */

if (!defined('ABSPATH')) {
    exit;
}

$plugin_url = plugin_dir_url(dirname(__FILE__, 2));
$categories = class_exists('DHR_Hotel_Database') ? DHR_Hotel_Database::get_active_categories() : array();
$default_image = $plugin_url . 'assets/images/family.png';
$accordion_svg = '<path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />';
?>

<!-- Five Package Design - Category List data -->
<div class="bird-packages__experinces">
    <div class="bird-packages__two">
        <div class="bird-packages__two__content">
            <?php if (empty($categories)) : ?>
                <p class="bird-packages__empty"><?php esc_html_e('No package experiences available. Add categories in the admin panel.', 'dhr-hotel-management'); ?></p>
            <?php else : ?>
                <?php foreach ($categories as $index => $cat) :
                    $image_url = !empty($cat->image_url) ? $cat->image_url : $default_image;
                    $is_image_first = ($index % 2) === 1;
                ?>
                <div class="bird-packages-grid__card">
                    <?php if ($is_image_first) : ?>
                        <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($image_url); ?>')"></div>
                    <?php endif; ?>
                    <div class="bird-packages-grid__card__info">
                        <span class="bird-packages__content__label"><?php echo esc_html(strtoupper($cat->title)); ?></span>
                        <div class="bird-packages__content__title__tag">
                            <h3 class="bird-packages__content__title"><?php echo esc_html($cat->title); ?></h3>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <?php echo $accordion_svg; ?>
                            </svg>
                        </div>
                        <div class="bird-packages__content__description-wrapper">
                            <div class="bird-packages__content__description"><?php echo wp_kses_post(wpautop($cat->description ? $cat->description : '')); ?></div>
                        </div>
                        <a href="#" class="bys-package-button button--theme-2"><?php esc_html_e('View Package', 'dhr-hotel-management'); ?></a>
                    </div>
                    <?php if (!$is_image_first) : ?>
                        <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($image_url); ?>')"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accordionTriggers = document.querySelectorAll('.bird-packages__content__title__tag');

            function isMobileView() {
                return window.innerWidth < 1024;
            }

            function closeAllAccordions() {
                accordionTriggers.forEach(trigger => {
                    const card = trigger.closest('.bird-packages-grid__card__info');
                    const wrapper = card.querySelector('.bird-packages__content__description-wrapper');

                    wrapper.style.height = '0px';
                    trigger.classList.remove('active');
                });
            }

            function toggleAccordion(trigger, wrapper, description) {
                if (!isMobileView()) return;

                const isActive = trigger.classList.contains('active');

                if (isActive) {
                    wrapper.style.height = '0px';
                    trigger.classList.remove('active');
                } else {
                    closeAllAccordions();

                    const fullHeight = description.scrollHeight;
                    wrapper.style.height = fullHeight + 'px';
                    trigger.classList.add('active');
                }
            }

            function handleResize() {
                accordionTriggers.forEach(trigger => {
                    const card = trigger.closest('.bird-packages-grid__card__info');
                    const wrapper = card.querySelector('.bird-packages__content__description-wrapper');
                    const description = card.querySelector('.bird-packages__content__description');

                    if (isMobileView()) {
                        if (!trigger.classList.contains('active')) {
                            wrapper.style.height = '0px';
                        } else {
                            const fullHeight = description.scrollHeight;
                            wrapper.style.height = fullHeight + 'px';
                        }
                    } else {
                        wrapper.style.height = 'auto';
                        trigger.classList.remove('active');
                    }
                });
            }

            accordionTriggers.forEach(trigger => {
                const card = trigger.closest('.bird-packages-grid__card__info');
                const wrapper = card.querySelector('.bird-packages__content__description-wrapper');
                const description = card.querySelector('.bird-packages__content__description');

                trigger.addEventListener('click', function() {
                    toggleAccordion(trigger, wrapper, description);
                });
            });

            window.addEventListener('resize', handleResize);

            handleResize();
        });
    </script>
</div>
