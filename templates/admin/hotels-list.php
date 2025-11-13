<?php
/**
 * Hotels list template
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$messages = array(
    'added' => array('type' => 'success', 'text' => __('Hotel added successfully!', 'dhr-hotel-management')),
    'updated' => array('type' => 'success', 'text' => __('Hotel updated successfully!', 'dhr-hotel-management')),
    'deleted' => array('type' => 'success', 'text' => __('Hotel deleted successfully!', 'dhr-hotel-management')),
    'error' => array('type' => 'error', 'text' => __('An error occurred. Please try again.', 'dhr-hotel-management'))
);
?>

<div class="wrap dhr-hotel-admin">
    <h1 class="wp-heading-inline"><?php _e('DHR Hotel Management', 'dhr-hotel-management'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dhr-hotel-add'); ?>" class="page-title-action">
        <?php _e('Add New Hotel', 'dhr-hotel-management'); ?>
    </a>
    
    <?php if ($message && isset($messages[$message])): ?>
        <div class="notice notice-<?php echo $messages[$message]['type']; ?> is-dismissible">
            <p><?php echo $messages[$message]['text']; ?></p>
        </div>
    <?php endif; ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Name', 'dhr-hotel-management'); ?></th>
                <th><?php _e('City', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Province', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Phone', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Status', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Actions', 'dhr-hotel-management'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hotels)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">
                        <?php _e('No hotels found. Add your first hotel!', 'dhr-hotel-management'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($hotels as $hotel): ?>
                    <tr>
                        <td><?php echo esc_html($hotel->id); ?></td>
                        <td><strong><?php echo esc_html($hotel->name); ?></strong></td>
                        <td><?php echo esc_html($hotel->city); ?></td>
                        <td><?php echo esc_html($hotel->province); ?></td>
                        <td><?php echo esc_html($hotel->phone); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($hotel->status); ?>">
                                <?php echo esc_html(ucfirst($hotel->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-management&action=edit&hotel_id=' . $hotel->id); ?>" 
                               class="button button-small">
                                <?php _e('Edit', 'dhr-hotel-management'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=dhr_delete_hotel&hotel_id=' . $hotel->id), 'dhr_delete_hotel_nonce'); ?>" 
                               class="button button-small button-link-delete"
                               onclick="return confirm('<?php _e('Are you sure you want to delete this hotel?', 'dhr-hotel-management'); ?>');">
                                <?php _e('Delete', 'dhr-hotel-management'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


