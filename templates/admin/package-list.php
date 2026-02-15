<?php
/**
 * Package list template
 */
if (!defined('ABSPATH')) {
    exit;
}
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$messages = array(
    'added'   => array('type' => 'success', 'text' => __('Package added successfully.', 'dhr-hotel-management')),
    'updated' => array('type' => 'success', 'text' => __('Package updated successfully.', 'dhr-hotel-management')),
    'deleted' => array('type' => 'success', 'text' => __('Package deleted.', 'dhr-hotel-management')),
    'error'   => array('type' => 'error', 'text' => __('An error occurred. Please try again.', 'dhr-hotel-management')),
);
?>
<div class="wrap dhr-hotel-admin">
    <h1 class="wp-heading-inline"><?php _e('Package List', 'dhr-hotel-management'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dhr-hotel-packages&action=add'); ?>" class="page-title-action"><?php _e('Add New', 'dhr-hotel-management'); ?></a>

    <?php if ($message && isset($messages[$message])): ?>
        <div class="notice notice-<?php echo esc_attr($messages[$message]['type']); ?> is-dismissible">
            <p><?php echo esc_html($messages[$message]['text']); ?></p>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Package Code', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Hotel Code', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Category', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Valid From', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Valid To', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Status', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Actions', 'dhr-hotel-management'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($packages)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;"><?php _e('No packages found. Add your first package.', 'dhr-hotel-management'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td><?php echo esc_html($pkg->id); ?></td>
                        <td><strong><?php echo esc_html($pkg->package_code); ?></strong></td>
                        <td><?php echo esc_html($pkg->hotel_code); ?></td>
                        <td><?php echo esc_html(isset($pkg->category_title) ? $pkg->category_title : '&ndash;'); ?></td>
                        <td><?php echo esc_html($pkg->valid_from); ?></td>
                        <td><?php echo esc_html($pkg->valid_to); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $pkg->is_active ? 'active' : 'inactive'; ?>">
                                <?php echo $pkg->is_active ? __('Active', 'dhr-hotel-management') : __('Inactive', 'dhr-hotel-management'); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-packages&action=edit&package_id=' . $pkg->id); ?>" class="button button-small"><?php _e('Edit', 'dhr-hotel-management'); ?></a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=dhr_delete_package&package_id=' . $pkg->id), 'dhr_delete_package_nonce'); ?>"
                               class="button button-small button-link-delete"
                               onclick="return confirm('<?php echo esc_attr(__('Are you sure you want to delete this package?', 'dhr-hotel-management')); ?>');"><?php _e('Delete', 'dhr-hotel-management'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <p class="description" style="margin-top: 10px;"><?php _e('Only packages that are Active and within the Valid From / Valid To date range are considered available for frontend and mapping.', 'dhr-hotel-management'); ?></p>
</div>
