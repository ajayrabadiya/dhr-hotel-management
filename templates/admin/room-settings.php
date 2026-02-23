<?php
/**
 * Room Settings – copy shortcodes for [hotel_rooms] and [hotel_rooms_cards]
 */

if (!defined('ABSPATH')) {
    exit;
}

$shortcode_rooms = '[hotel_rooms]';
$shortcode_cards = '[hotel_rooms_cards]';
$usage_php = "echo do_shortcode('[hotel_rooms]');\necho do_shortcode('[hotel_rooms_cards]');";
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php _e('Room Settings', 'dhr-hotel-management'); ?></h1>
    <p class="description"><?php _e('Copy the shortcodes below to display hotel rooms on pages or in templates. Hotel code is taken from Book Your Stay → Settings.', 'dhr-hotel-management'); ?></p>

    <div class="dhr-room-shortcodes" style="max-width: 700px; margin-top: 20px;">
        <div class="dhr-shortcode-box" style="background: #f6f7f7; border: 1px solid #c3c4c7; border-radius: 4px; padding: 16px 20px; margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?php _e('1. Grid layout (specs, amenities, description)', 'dhr-hotel-management'); ?></label>
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <input type="text" class="dhr-shortcode-input regular-text" value="<?php echo esc_attr($shortcode_rooms); ?>" readonly style="flex: 1; min-width: 200px;">
                <button type="button" class="button dhr-copy-shortcode" data-shortcode="<?php echo esc_attr($shortcode_rooms); ?>"><?php _e('Copy', 'dhr-hotel-management'); ?></button>
            </div>
        </div>

        <div class="dhr-shortcode-box" style="background: #f6f7f7; border: 1px solid #c3c4c7; border-radius: 4px; padding: 16px 20px; margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?php _e('2. Card overlay layout', 'dhr-hotel-management'); ?></label>
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <input type="text" class="dhr-shortcode-input regular-text" value="<?php echo esc_attr($shortcode_cards); ?>" readonly style="flex: 1; min-width: 200px;">
                <button type="button" class="button dhr-copy-shortcode" data-shortcode="<?php echo esc_attr($shortcode_cards); ?>"><?php _e('Copy', 'dhr-hotel-management'); ?></button>
            </div>
        </div>
    </div>

    <div class="dhr-usage-section" style="max-width: 700px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #c3c4c7;">
        <h2 style="font-size: 14px; margin: 0 0 10px;"><?php _e('Display both on page (e.g. in theme template)', 'dhr-hotel-management'); ?></h2>
        <p class="description" style="margin-bottom: 12px;"><?php _e('Use this in your page template (e.g. home.php) to show both room layouts. Copy the code below.', 'dhr-hotel-management'); ?></p>
        <div style="display: flex; gap: 8px; align-items: flex-start; flex-wrap: wrap;">
            <textarea id="dhr-usage-php" class="large-text" rows="3" readonly style="flex: 1; min-width: 300px; font-family: monospace; font-size: 13px;"><?php echo esc_textarea($usage_php); ?></textarea>
            <button type="button" class="button dhr-copy-usage"><?php _e('Copy', 'dhr-hotel-management'); ?></button>
        </div>
    </div>
</div>

<script>
(function() {
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            return Promise.resolve();
        } finally {
            document.body.removeChild(ta);
        }
    }

    document.querySelectorAll('.dhr-copy-shortcode').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var shortcode = this.getAttribute('data-shortcode');
            var label = this.textContent;
            copyToClipboard(shortcode).then(function() {
                btn.textContent = '<?php echo esc_js(__('Copied!', 'dhr-hotel-management')); ?>';
                setTimeout(function() { btn.textContent = label; }, 2000);
            });
        });
    });

    var usageBtn = document.querySelector('.dhr-copy-usage');
    if (usageBtn) {
        usageBtn.addEventListener('click', function() {
            var text = document.getElementById('dhr-usage-php').value;
            var label = usageBtn.textContent;
            copyToClipboard(text).then(function() {
                usageBtn.textContent = '<?php echo esc_js(__('Copied!', 'dhr-hotel-management')); ?>';
                setTimeout(function() { usageBtn.textContent = label; }, 2000);
            });
        });
    }
})();
</script>
