<?php
/**
 * Map Management Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$map_configs = DHR_Hotel_Database::get_all_map_configs();
$all_hotels = DHR_Hotel_Database::get_all_hotels();
$all_hotels_for_js = array();
foreach ($all_hotels as $h) {
    $all_hotels_for_js[] = array('id' => (int) $h->id, 'name' => $h->name, 'hotel_code' => isset($h->hotel_code) ? $h->hotel_code : '');
}
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php _e('Map Management', 'dhr-hotel-management'); ?></h1>
    
    <?php if ($message === 'updated'): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Map configuration updated successfully!', 'dhr-hotel-management'); ?></p>
        </div>
    <?php elseif ($message === 'error'): ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('Error updating map configuration.', 'dhr-hotel-management'); ?></p>
        </div>
    <?php elseif ($message === 'maps_created'): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Default maps created successfully!', 'dhr-hotel-management'); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (empty($map_configs)): ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php _e('No maps configured yet.', 'dhr-hotel-management'); ?></strong>
                <?php _e('Click the button below to create all 7 default map configurations.', 'dhr-hotel-management'); ?>
            </p>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top: 10px;">
                <?php wp_nonce_field('dhr_create_default_maps_nonce'); ?>
                <input type="hidden" name="action" value="dhr_create_default_maps">
                <button type="submit" class="button button-primary">
                    <?php _e('Create Default Maps', 'dhr-hotel-management'); ?>
                </button>
            </form>
        </div>
    <?php endif; ?>
    
    <!-- All Shortcodes Display Section -->
    <?php if (!empty($map_configs)): ?>
    <div class="dhr-all-shortcodes-section">
        <h2><?php _e('All Map Shortcodes', 'dhr-hotel-management'); ?></h2>
        <p class="description"><?php _e('Copy any shortcode below to use in your pages or posts:', 'dhr-hotel-management'); ?></p>
        
        <div class="dhr-shortcodes-grid">
                <?php foreach ($map_configs as $map): ?>
                    <div class="dhr-shortcode-card">
                        <div class="dhr-shortcode-card-header">
                            <h3><?php echo esc_html($map->map_name); ?></h3>
                            <span class="dhr-status-badge dhr-status-<?php echo esc_attr($map->status); ?>">
                                <?php echo esc_html(ucfirst($map->status)); ?>
                            </span>
                        </div>
                        <div class="dhr-shortcode-card-body">
                            <label><?php _e('Shortcode:', 'dhr-hotel-management'); ?></label>
                            <div class="dhr-shortcode-wrapper">
                                <input type="text" 
                                       class="dhr-shortcode-input-full" 
                                       value="[<?php echo esc_attr($map->shortcode); ?>]" 
                                       readonly>
                                <button type="button" 
                                        class="button button-primary dhr-copy-btn" 
                                        data-shortcode="[<?php echo esc_attr($map->shortcode); ?>]">
                                    <span class="dhr-copy-text"><?php _e('Copy', 'dhr-hotel-management'); ?></span>
                                    <span class="dhr-copied-text" style="display: none;"><?php _e('Copied!', 'dhr-hotel-management'); ?></span>
                                </button>
                            </div>
                            <div class="dhr-shortcode-examples">
                                <strong><?php _e('Examples:', 'dhr-hotel-management'); ?></strong>
                                <code>[<?php echo esc_html($map->shortcode); ?>]</code>
                                <?php if ($map->shortcode === 'dhr_hotel_map'): ?>
                                    <br><code>[<?php echo esc_html($map->shortcode); ?> province="Western Cape"]</code>
                                    <br><code>[<?php echo esc_html($map->shortcode); ?> height="800px"]</code>
                                <?php else: ?>
                                    <br><code>[<?php echo esc_html($map->shortcode); ?> height="600px"]</code>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="dhr-map-management-container">
        <div class="dhr-maps-list">
            <h2><?php _e('Map Management', 'dhr-hotel-management'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Map Name', 'dhr-hotel-management'); ?></th>
                        <th><?php _e('Shortcode', 'dhr-hotel-management'); ?></th>
                        <th><?php _e('Status', 'dhr-hotel-management'); ?></th>
                        <th><?php _e('Actions', 'dhr-hotel-management'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($map_configs)): ?>
                        <?php foreach ($map_configs as $map): ?>
                            <tr>
                                <td><strong><?php echo esc_html($map->map_name); ?></strong></td>
                                <td>
                                    <div class="dhr-shortcode-wrapper">
                                        <input type="text" 
                                               class="dhr-shortcode-input" 
                                               value="[<?php echo esc_attr($map->shortcode); ?>]" 
                                               readonly>
                                        <button type="button" 
                                                class="button dhr-copy-btn" 
                                                data-shortcode="[<?php echo esc_attr($map->shortcode); ?>]">
                                            <span class="dhr-copy-text"><?php _e('Copy', 'dhr-hotel-management'); ?></span>
                                            <span class="dhr-copied-text" style="display: none;"><?php _e('Copied!', 'dhr-hotel-management'); ?></span>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="dhr-status-badge dhr-status-<?php echo esc_attr($map->status); ?>">
                                        <?php echo esc_html(ucfirst($map->status)); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="button dhr-edit-map-btn" 
                                            data-map-id="<?php echo esc_attr($map->id); ?>">
                                        <?php _e('Edit Settings', 'dhr-hotel-management'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"><?php _e('No maps found.', 'dhr-hotel-management'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="dhr-map-settings-panel" id="dhr-map-settings-panel" style="display: none;">
            <h2><?php _e('Map Settings', 'dhr-hotel-management'); ?></h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="dhr-map-settings-form">
                <?php wp_nonce_field('dhr_map_config_nonce'); ?>
                <input type="hidden" name="action" value="dhr_save_map_config">
                <input type="hidden" name="map_id" id="dhr-map-id" value="">
                
                <div id="dhr-map-settings-content">
                    <!-- Settings will be loaded here dynamically -->
                </div>
                
                <p class="submit">
                    <input type="submit" class="button button-primary" value="<?php _e('Save Settings', 'dhr-hotel-management'); ?>">
                    <button type="button" class="button dhr-cancel-edit-btn"><?php _e('Cancel', 'dhr-hotel-management'); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var mapConfigs = <?php echo json_encode($map_configs); ?>;
    var dhrAllHotels = <?php echo json_encode($all_hotels_for_js); ?>;
    
    // Copy shortcode functionality
    $('.dhr-copy-btn').on('click', function() {
        var $btn = $(this);
        var shortcode = $btn.data('shortcode');
        var $input = $btn.siblings('.dhr-shortcode-input');
        
        $input.select();
        document.execCommand('copy');
        
        $btn.find('.dhr-copy-text').hide();
        $btn.find('.dhr-copied-text').show();
        
        setTimeout(function() {
            $btn.find('.dhr-copy-text').show();
            $btn.find('.dhr-copied-text').hide();
        }, 2000);
    });
    
    // Edit map button
    $('.dhr-edit-map-btn').on('click', function() {
        var mapId = $(this).data('map-id');
        var map = mapConfigs.find(function(m) {
            return m.id == mapId;
        });
        
        if (map) {
            loadMapSettings(map);
        }
    });
    
    // Cancel edit
    $('.dhr-cancel-edit-btn').on('click', function() {
        $('#dhr-map-settings-panel').hide();
    });
    
    function loadMapSettings(map) {
        var settings = JSON.parse(map.settings || '{}');
        var selectedIds = Array.isArray(settings.selected_hotel_ids) ? settings.selected_hotel_ids : [];
        var html = '<input type="hidden" name="map_name" value="' + escapeHtml(map.map_name) + '">';
        html += '<table class="form-table">';
        
        // Generate form fields based on map type
        for (var key in settings) {
            if (key === 'selected_hotel_ids') continue;
            var value = settings[key] || '';
            var fieldName = 'setting_' + key;
            var label = key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            
            html += '<tr>';
            html += '<th scope="row"><label for="' + fieldName + '">' + label + '</label></th>';
            html += '<td>';
            
            if (key.includes('description') || key.includes('text')) {
                html += '<textarea id="' + fieldName + '" name="' + fieldName + '" class="large-text" rows="4">' + escapeHtml(value) + '</textarea>';
            } else if (key.includes('url') || key.includes('link')) {
                html += '<input type="url" id="' + fieldName + '" name="' + fieldName + '" class="regular-text" value="' + escapeHtml(value) + '">';
            } else if (key === 'show_numbers' || key === 'show_list') {
                // Boolean fields
                html += '<label><input type="checkbox" id="' + fieldName + '" name="' + fieldName + '" value="1"' + (value == true || value == '1' ? ' checked' : '') + '> ' + label + '</label>';
            } else {
                html += '<input type="text" id="' + fieldName + '" name="' + fieldName + '" class="regular-text" value="' + escapeHtml(value) + '">';
            }
            
            html += '</td>';
            html += '</tr>';
        }
        
        // Selected Hotels (multi-select for this map)
        html += '<tr>';
        html += '<th scope="row"><label>' + escapeHtml('<?php echo esc_js(__("Hotels on this map", "dhr-hotel-management")); ?>') + '</label></th>';
        html += '<td><p class="description" style="margin-bottom: 10px;">' + escapeHtml('<?php echo esc_js(__("Select which hotels appear on this map. Leave all unchecked to show all active hotels.", "dhr-hotel-management")); ?>') + '</p>';
        html += '<div class="dhr-map-hotels-checkboxes" style="max-height: 220px; overflow-y: auto; border: 1px solid #8c8f94; padding: 10px; background: #fff;">';
        if (dhrAllHotels && dhrAllHotels.length) {
            dhrAllHotels.forEach(function(hotel) {
                var checked = selectedIds.indexOf(parseInt(hotel.id, 10)) !== -1 ? ' checked' : '';
                html += '<label style="display: block; margin-bottom: 6px;">';
                html += '<input type="checkbox" name="setting_selected_hotels[]" value="' + parseInt(hotel.id, 10) + '"' + checked + '> ';
                html += escapeHtml(hotel.name) + (hotel.hotel_code ? ' (' + escapeHtml(hotel.hotel_code) + ')' : '');
                html += '</label>';
            });
        } else {
            html += '<p>' + escapeHtml('<?php echo esc_js(__("No hotels yet. Add hotels from DHR Hotel Management first.", "dhr-hotel-management")); ?>') + '</p>';
        }
        html += '</div></td>';
        html += '</tr>';
        
        html += '</table>';
        
        $('#dhr-map-id').val(map.id);
        $('#dhr-map-settings-content').html(html);
        $('#dhr-map-settings-panel').show();
        $('html, body').animate({
            scrollTop: $('#dhr-map-settings-panel').offset().top
        }, 500);
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
});
</script>

<style>
.dhr-all-shortcodes-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.dhr-all-shortcodes-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #2271b1;
}

.dhr-shortcodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.dhr-shortcode-card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    transition: all 0.3s ease;
}

.dhr-shortcode-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
    border-color: #2271b1;
}

.dhr-shortcode-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.dhr-shortcode-card-header h3 {
    margin: 0;
    font-size: 16px;
    color: #2271b1;
}

.dhr-shortcode-card-body label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.dhr-shortcode-wrapper {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.dhr-shortcode-input-full {
    flex: 1;
    padding: 8px 12px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.dhr-shortcode-examples {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.dhr-shortcode-examples strong {
    display: block;
    margin-bottom: 8px;
    font-size: 12px;
    color: #666;
}

.dhr-shortcode-examples code {
    display: block;
    padding: 5px 8px;
    margin: 5px 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 12px;
    color: #d63384;
}

.dhr-map-management-container {
    margin-top: 20px;
}

.dhr-shortcode-wrapper {
    display: flex;
    gap: 10px;
    align-items: center;
}

.dhr-shortcode-input {
    flex: 1;
    max-width: 300px;
}

.dhr-status-badge {
    padding: 4px 12px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.dhr-status-active {
    background: #00a32a;
    color: #fff;
}

.dhr-status-inactive {
    background: #d63638;
    color: #fff;
}

.dhr-map-settings-panel {
    margin-top: 30px;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.dhr-maps-list {
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .dhr-shortcodes-grid {
        grid-template-columns: 1fr;
    }
}
</style>

