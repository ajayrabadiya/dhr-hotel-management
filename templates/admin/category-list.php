<?php
/**
 * Category list template
 */
if (!defined('ABSPATH')) {
    exit;
}
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$messages = array(
    'added'   => array('type' => 'success', 'text' => __('Category added successfully.', 'dhr-hotel-management')),
    'updated' => array('type' => 'success', 'text' => __('Category updated successfully.', 'dhr-hotel-management')),
    'deleted' => array('type' => 'success', 'text' => __('Category deleted.', 'dhr-hotel-management')),
    'error'   => array('type' => 'error', 'text' => __('An error occurred. Please try again.', 'dhr-hotel-management')),
);
?>
<div class="wrap dhr-hotel-admin">
    <h1 class="wp-heading-inline"><?php _e('Category List', 'dhr-hotel-management'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dhr-hotel-categories&action=add'); ?>" class="page-title-action"><?php _e('Add New', 'dhr-hotel-management'); ?></a>

    <?php if ($message && isset($messages[$message])): ?>
        <div class="notice notice-<?php echo esc_attr($messages[$message]['type']); ?> is-dismissible">
            <p><?php echo esc_html($messages[$message]['text']); ?></p>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Image', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Icon', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Title', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Description', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Status', 'dhr-hotel-management'); ?></th>
                <th><?php _e('Actions', 'dhr-hotel-management'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;"><?php _e('No categories found. Add your first category.', 'dhr-hotel-management'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo esc_html($cat->id); ?></td>
                        <td>
                            <?php if (!empty($cat->image_url)): ?>
                                <img src="<?php echo esc_url($cat->image_url); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <span style="display: inline-flex; width: 50px; height: 50px; background: #f0f0f1; color: #787c82; border-radius: 4px; font-size: 11px; align-items: center; justify-content: center;"><?php _e('No image', 'dhr-hotel-management'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($cat->icon_url)): ?>
                                <img src="<?php echo esc_url($cat->icon_url); ?>" alt="" style="width: 32px; height: 32px; object-fit: contain;">
                            <?php else: ?>
                                <span style="color: #787c82;">&ndash;</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo esc_html($cat->title); ?></strong></td>
                        <td><?php echo esc_html(wp_trim_words($cat->description, 10)); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $cat->is_active ? 'active' : 'inactive'; ?>">
                                <?php echo $cat->is_active ? __('Active', 'dhr-hotel-management') : __('Inactive', 'dhr-hotel-management'); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-categories&action=edit&category_id=' . $cat->id); ?>" class="button button-small"><?php _e('Edit', 'dhr-hotel-management'); ?></a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=dhr_delete_category&category_id=' . $cat->id), 'dhr_delete_category_nonce'); ?>"
                               class="button button-small button-link-delete"
                               onclick="return confirm('<?php echo esc_attr(__('Are you sure you want to delete this category?', 'dhr-hotel-management')); ?>');"><?php _e('Delete', 'dhr-hotel-management'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
