<?php
/**
 * Package Settings – category-wise shortcode generator
 */

if (!defined('ABSPATH')) {
    exit;
}

$categories = isset($categories) ? $categories : array();
$designs = array(
    'first_design'   => __('First Design (grid)', 'dhr-hotel-management'),
    'second_design'  => __('Second Design (swiper)', 'dhr-hotel-management'),
    'kids_design'    => __('Kids Design', 'dhr-hotel-management'),
    'early_bird_design' => __('Early Bird Design', 'dhr-hotel-management'),
);
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php esc_html_e('Package Settings', 'dhr-hotel-management'); ?></h1>
    <p class="description"><?php esc_html_e('Choose one or more categories and a design, then generate a shortcode. Paste the shortcode on any page to display only packages from the selected categories.', 'dhr-hotel-management'); ?></p>

    <div class="dhr-package-settings-box" style="max-width: 600px; margin-top: 20px;">
        <h2 style="margin-top: 0;"><?php esc_html_e('Generate category-wise package shortcode', 'dhr-hotel-management'); ?></h2>

        <div class="form-field">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;"><?php esc_html_e('Select categories', 'dhr-hotel-management'); ?></label>
            <?php if (empty($categories)) : ?>
                <p><?php esc_html_e('No categories found. Add categories from Category List first.', 'dhr-hotel-management'); ?></p>
            <?php else : ?>
                <div style="display: flex; flex-wrap: wrap; gap: 12px 24px;">
                    <?php foreach ($categories as $cat) : ?>
                        <label style="display: inline-flex; align-items: center; gap: 6px;">
                            <input type="checkbox" class="dhr-package-settings-category" name="dhr_package_categories[]" value="<?php echo esc_attr($cat->id); ?>">
                            <?php echo esc_html($cat->title); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description" style="margin-top: 8px;"><?php esc_html_e('Leave all unchecked to show packages from all categories.', 'dhr-hotel-management'); ?></p>
            <?php endif; ?>
        </div>

        <div class="form-field" style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;"><?php esc_html_e('Display design', 'dhr-hotel-management'); ?></label>
            <select id="dhr-package-settings-design" class="regular-text">
                <?php foreach ($designs as $value => $label) : ?>
                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <p style="margin-top: 24px;">
            <button type="button" id="dhr-package-settings-generate" class="button button-primary"><?php esc_html_e('Generate Shortcode', 'dhr-hotel-management'); ?></button>
        </p>

        <div id="dhr-package-settings-output" style="display: none; margin-top: 20px; padding: 12px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 4px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;"><?php esc_html_e('Shortcode (paste into any page or post)', 'dhr-hotel-management'); ?></label>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="text" id="dhr-package-settings-shortcode" class="large-text" readonly style="background: #fff;">
                <button type="button" id="dhr-package-settings-copy" class="button"><?php esc_html_e('Copy', 'dhr-hotel-management'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var generateBtn = document.getElementById('dhr-package-settings-generate');
    var outputBox = document.getElementById('dhr-package-settings-output');
    var shortcodeInput = document.getElementById('dhr-package-settings-shortcode');
    var copyBtn = document.getElementById('dhr-package-settings-copy');
    var designSelect = document.getElementById('dhr-package-settings-design');

    if (generateBtn && outputBox && shortcodeInput) {
        generateBtn.addEventListener('click', function() {
            var checked = document.querySelectorAll('.dhr-package-settings-category:checked');
            var ids = [];
            checked.forEach(function(cb) { ids.push(cb.value); });
            var design = designSelect ? designSelect.value : 'first_design';
            var atts = [];
            if (ids.length > 0) {
                atts.push('categories="' + ids.join(',') + '"');
            }
            atts.push('design="' + design + '"');
            var shortcode = '[dhr_packages ' + atts.join(' ') + ']';
            shortcodeInput.value = shortcode;
            outputBox.style.display = 'block';
        });
    }

    if (copyBtn && shortcodeInput) {
        copyBtn.addEventListener('click', function() {
            shortcodeInput.select();
            document.execCommand('copy');
            var oldText = copyBtn.textContent;
            copyBtn.textContent = '<?php echo esc_js(__('Copied!', 'dhr-hotel-management')); ?>';
            setTimeout(function() { copyBtn.textContent = oldText; }, 2000);
        });
    }
})();
</script>
