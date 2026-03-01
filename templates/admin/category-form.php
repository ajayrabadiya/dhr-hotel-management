<?php
/**
 * Category add/edit form
 */
if (!defined('ABSPATH')) {
    exit;
}
$is_edit = $category !== null;
$title = $is_edit ? __('Edit Category', 'dhr-hotel-management') : __('Add New Category', 'dhr-hotel-management');
$cat_title = $is_edit ? $category->title : '';
$cat_subtitle = $is_edit ? (isset($category->subtitle) ? $category->subtitle : '') : '';
$cat_description = $is_edit ? $category->description : '';
$image_url = $is_edit ? $category->image_url : '';
$icon_url = $is_edit ? $category->icon_url : '';
$view_package_url = $is_edit ? (isset($category->view_package_url) ? $category->view_package_url : '') : '';
$is_active = $is_edit ? (int) $category->is_active : 1;
?>
<div class="wrap dhr-hotel-admin">
    <h1><?php echo esc_html($title); ?></h1>
    <p><a href="<?php echo admin_url('admin.php?page=dhr-hotel-categories'); ?>">&larr; <?php _e('Back to Category List', 'dhr-hotel-management'); ?></a></p>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('dhr_category_nonce'); ?>
        <input type="hidden" name="action" value="dhr_save_category">
        <?php if ($is_edit): ?>
            <input type="hidden" name="category_id" value="<?php echo esc_attr($category->id); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th><label for="subtitle"><?php _e('Subtitle', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="subtitle" name="subtitle" class="regular-text" value="<?php echo esc_attr($cat_subtitle); ?>" placeholder="<?php esc_attr_e('e.g. FUN TOGETHER', 'dhr-hotel-management'); ?>">
                </td>
            </tr>
            <tr>
                <th><label for="title"><?php _e('Title', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="title" name="title" class="regular-text" value="<?php echo esc_attr($cat_title); ?>" required>
                </td>
            </tr>
            <tr>
                <th><label for="description"><?php _e('Description', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <textarea id="description" name="description" rows="4" class="large-text"><?php echo esc_textarea($cat_description); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Image', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="hidden" id="image_url" name="image_url" value="<?php echo esc_attr($image_url); ?>">
                    <button type="button" class="button dhr-upload-image" data-target="image_url" data-preview="image_preview"><?php _e('Select Image', 'dhr-hotel-management'); ?></button>
                    <button type="button" class="button dhr-clear-image <?php echo $image_url ? '' : 'hidden'; ?>" data-target="image_url" data-preview="image_preview"><?php _e('Remove', 'dhr-hotel-management'); ?></button>
                    <div id="image_preview" class="dhr-media-preview" style="margin-top: 8px;">
                        <?php if ($image_url): ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 200px; max-height: 150px; border: 1px solid #ccc;">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Icon', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="hidden" id="icon_url" name="icon_url" value="<?php echo esc_attr($icon_url); ?>">
                    <button type="button" class="button dhr-upload-icon" data-target="icon_url" data-preview="icon_preview"><?php _e('Select Icon', 'dhr-hotel-management'); ?></button>
                    <button type="button" class="button dhr-clear-icon <?php echo $icon_url ? '' : 'hidden'; ?>" data-target="icon_url" data-preview="icon_preview"><?php _e('Remove', 'dhr-hotel-management'); ?></button>
                    <div id="icon_preview" class="dhr-media-preview" style="margin-top: 8px;">
                        <?php if ($icon_url): ?>
                            <img src="<?php echo esc_url($icon_url); ?>" alt="" style="max-width: 64px; max-height: 64px; border: 1px solid #ccc;">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="view_package_url"><?php _e('View Package Page URL', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url" id="view_package_url" name="view_package_url" class="regular-text" value="<?php echo esc_attr($view_package_url); ?>" placeholder="<?php esc_attr_e('https://example.com/packages', 'dhr-hotel-management'); ?>">
                    <p class="description"><?php _e('URL for the "View Package" button. Opens in a new tab on the frontend.', 'dhr-hotel-management'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="is_active"><?php _e('Status', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <label><input type="checkbox" id="is_active" name="is_active" value="1" <?php checked($is_active, 1); ?>>
                        <?php _e('Active (only active categories are available for package mapping and frontend)', 'dhr-hotel-management'); ?></label>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php echo $is_edit ? __('Update Category', 'dhr-hotel-management') : __('Add Category', 'dhr-hotel-management'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-categories'); ?>" class="button"><?php _e('Cancel', 'dhr-hotel-management'); ?></a>
        </p>
    </form>
</div>
<script>
jQuery(function($) {
    function openMedia(targetId, previewId, allowAllTypes) {
        var libOpts = allowAllTypes ? {} : { type: 'image' };
        var frame = wp.media({
            title: '<?php echo esc_js(__('Select or Upload', 'dhr-hotel-management')); ?>',
            library: libOpts,
            multiple: false,
            button: { text: '<?php echo esc_js(__('Use this', 'dhr-hotel-management')); ?>' }
        });
        frame.on('select', function() {
            var att = frame.state().get('selection').first().toJSON();
            $('#' + targetId).val(att.url);
            var isSvg = att.url && att.url.toLowerCase().match(/\.svgz?(\?|$)/);
            var imgStyle = (previewId === 'icon_preview') ? 'max-width: 64px; max-height: 64px; border: 1px solid #ccc;' : 'max-width: 200px; max-height: 150px; border: 1px solid #ccc;';
            if (isSvg) {
                $('#' + previewId).html('<img src="' + att.url + '" alt="" style="' + imgStyle + ' background:#f9f9f9;">');
            } else {
                $('#' + previewId).html('<img src="' + att.url + '" alt="" style="' + imgStyle + '">');
            }
            $('button[data-preview="' + previewId + '"]').removeClass('hidden');
        });
        frame.open();
    }
    $('.dhr-upload-image').on('click', function() { openMedia('image_url', 'image_preview', false); });
    $('.dhr-upload-icon').on('click', function() { openMedia('icon_url', 'icon_preview', true); });
    $('.dhr-clear-image').on('click', function() {
        $('#image_url').val('');
        $('#image_preview').empty();
        $(this).addClass('hidden');
    });
    $('.dhr-clear-icon').on('click', function() {
        $('#icon_url').val('');
        $('#icon_preview').empty();
        $(this).addClass('hidden');
    });
});
</script>
