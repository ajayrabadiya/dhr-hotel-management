<?php
/**
 * Sample data insertion template
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$result = get_transient('dhr_sample_data_result');
delete_transient('dhr_sample_data_result');

global $wpdb;
$table_name = $wpdb->prefix . 'dhr_hotels';
$existing_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php _e('Insert Sample Hotel Data', 'dhr-hotel-management'); ?></h1>
    
    <?php if ($message === 'inserted' && $result): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php _e('Sample data insertion completed!', 'dhr-hotel-management'); ?></strong><br>
                <?php printf(__('Inserted: %d hotels', 'dhr-hotel-management'), $result['inserted']); ?><br>
                <?php if ($result['skipped'] > 0): ?>
                    <?php printf(__('Skipped (already exist): %d hotels', 'dhr-hotel-management'), $result['skipped']); ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    
    <div class="card" style="max-width: 800px;">
        <h2><?php _e('About Sample Data', 'dhr-hotel-management'); ?></h2>
        <p>
            <?php _e('This will insert 10 sample hotels into your database. These are fake hotels located in the Western Cape, South Africa, perfect for testing the plugin functionality.', 'dhr-hotel-management'); ?>
        </p>
        
        <p>
            <strong><?php _e('Current hotels in database:', 'dhr-hotel-management'); ?></strong> 
            <?php echo esc_html($existing_count); ?>
        </p>
        
        <h3><?php _e('Sample Hotels Include:', 'dhr-hotel-management'); ?></h3>
        <ul style="list-style: disc; margin-left: 20px;">
            <li>Le Franschhoek Hotel & Spa</li>
            <li>Cape Town Waterfront Hotel</li>
            <li>Stellenbosch Vineyard Estate</li>
            <li>Hermanus Ocean View Resort</li>
            <li>Knysna Lagoon Lodge</li>
            <li>Paarl Mountain Retreat</li>
            <li>Cape Winelands Boutique Hotel</li>
            <li>Table Mountain View Hotel</li>
            <li>Garden Route Safari Lodge</li>
            <li>Robben Island Heritage Hotel</li>
        </ul>
        
        <p class="description">
            <?php _e('Note: Hotels with the same name will be skipped to prevent duplicates.', 'dhr-hotel-management'); ?>
        </p>
        
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top: 20px;">
            <?php wp_nonce_field('dhr_insert_sample_data_nonce'); ?>
            <input type="hidden" name="action" value="dhr_insert_sample_data">
            <p>
                <input type="submit" class="button button-primary button-large" 
                       value="<?php _e('Insert Sample Data', 'dhr-hotel-management'); ?>">
                <a href="<?php echo admin_url('admin.php?page=dhr-hotel-management'); ?>" class="button">
                    <?php _e('Cancel', 'dhr-hotel-management'); ?>
                </a>
            </p>
        </form>
    </div>
    
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php _e('Alternative Methods', 'dhr-hotel-management'); ?></h2>
        <h3><?php _e('Method 1: SQL File', 'dhr-hotel-management'); ?></h3>
        <p>
            <?php _e('You can also import the SQL file directly via phpMyAdmin:', 'dhr-hotel-management'); ?>
        </p>
        <code><?php echo DHR_HOTEL_PLUGIN_PATH; ?>sample-data.sql</code>
        
        <h3><?php _e('Method 2: WP-CLI', 'dhr-hotel-management'); ?></h3>
        <p>
            <?php _e('If you have WP-CLI installed, you can run:', 'dhr-hotel-management'); ?>
        </p>
        <code style="display: block; background: #f5f5f5; padding: 10px; margin: 10px 0;">
            cd <?php echo ABSPATH; ?><br>
            wp eval-file wp-content/plugins/dhr-hotel-management/insert-sample-data.php
        </code>
        <p class="description">
            <?php _e('Note: The WP-CLI command requires the file to be modified to work without WordPress loading. The admin button method above is recommended.', 'dhr-hotel-management'); ?>
        </p>
    </div>
</div>


