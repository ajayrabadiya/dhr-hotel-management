<?php
/**
 * Display All Shortcodes - Can be included anywhere
 * 
 * Usage: include this file and call dhr_display_all_map_shortcodes()
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display all map shortcodes in a formatted way
 * 
 * @param string $format - 'html', 'array', or 'list'
 * @return string|array
 */
function dhr_display_all_map_shortcodes($format = 'html') {
    $map_configs = DHR_Hotel_Database::get_all_map_configs();
    
    if (empty($map_configs)) {
        return $format === 'array' ? array() : 'No maps configured.';
    }
    
    if ($format === 'array') {
        $shortcodes = array();
        foreach ($map_configs as $map) {
            $shortcodes[] = array(
                'name' => $map->map_name,
                'shortcode' => '[' . $map->shortcode . ']',
                'status' => $map->status
            );
        }
        return $shortcodes;
    }
    
    if ($format === 'list') {
        $output = '<ul class="dhr-shortcodes-list">';
        foreach ($map_configs as $map) {
            $output .= '<li>';
            $output .= '<strong>' . esc_html($map->map_name) . ':</strong> ';
            $output .= '<code>[' . esc_html($map->shortcode) . ']</code>';
            $output .= ' <span class="status-' . esc_attr($map->status) . '">(' . esc_html($map->status) . ')</span>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
    }
    
    // Default: HTML format
    $output = '<div class="dhr-all-shortcodes-display">';
    $output .= '<h3>All Map Shortcodes</h3>';
    $output .= '<div class="dhr-shortcodes-grid">';
    
    foreach ($map_configs as $map) {
        $output .= '<div class="dhr-shortcode-item">';
        $output .= '<h4>' . esc_html($map->map_name) . '</h4>';
        $output .= '<div class="dhr-shortcode-value">';
        $output .= '<input type="text" value="[' . esc_attr($map->shortcode) . ']" readonly class="dhr-shortcode-input" onclick="this.select();">';
        $output .= '<button class="dhr-copy-btn" data-shortcode="[' . esc_attr($map->shortcode) . ']">Copy</button>';
        $output .= '</div>';
        $output .= '<p class="dhr-shortcode-status">Status: <span class="status-' . esc_attr($map->status) . '">' . esc_html(ucfirst($map->status)) . '</span></p>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

/**
 * Get all shortcodes as simple array
 */
function dhr_get_all_map_shortcodes() {
    $map_configs = DHR_Hotel_Database::get_all_map_configs();
    $shortcodes = array();
    
    foreach ($map_configs as $map) {
        $shortcodes[$map->shortcode] = array(
            'name' => $map->map_name,
            'full_shortcode' => '[' . $map->shortcode . ']',
            'status' => $map->status
        );
    }
    
    return $shortcodes;
}

/**
 * Display shortcodes in a simple table format
 */
function dhr_display_shortcodes_table() {
    $map_configs = DHR_Hotel_Database::get_all_map_configs();
    
    if (empty($map_configs)) {
        return '<p>No maps configured.</p>';
    }
    
    $output = '<table class="dhr-shortcodes-table" style="width: 100%; border-collapse: collapse;">';
    $output .= '<thead>';
    $output .= '<tr style="background: #f0f0f0;">';
    $output .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Map Name</th>';
    $output .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Shortcode</th>';
    $output .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Status</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    
    foreach ($map_configs as $map) {
        $output .= '<tr>';
        $output .= '<td style="padding: 10px; border: 1px solid #ddd;">' . esc_html($map->map_name) . '</td>';
        $output .= '<td style="padding: 10px; border: 1px solid #ddd;"><code>[' . esc_html($map->shortcode) . ']</code></td>';
        $output .= '<td style="padding: 10px; border: 1px solid #ddd;"><span class="status-' . esc_attr($map->status) . '">' . esc_html(ucfirst($map->status)) . '</span></td>';
        $output .= '</tr>';
    }
    
    $output .= '</tbody>';
    $output .= '</table>';
    
    return $output;
}

/**
 * Echo all shortcodes directly
 */
function dhr_echo_all_shortcodes() {
    $map_configs = DHR_Hotel_Database::get_all_map_configs();
    
    if (empty($map_configs)) {
        echo '<p>No maps configured.</p>';
        return;
    }
    
    echo '<div class="dhr-shortcodes-output">';
    echo '<h2>All Available Map Shortcodes</h2>';
    echo '<ul>';
    
    foreach ($map_configs as $map) {
        echo '<li>';
        echo '<strong>' . esc_html($map->map_name) . ':</strong> ';
        echo '<code>[' . esc_html($map->shortcode) . ']</code>';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</div>';
}

