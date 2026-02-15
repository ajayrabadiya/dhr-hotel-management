<?php
/**
 * Hotel form template (add/edit)
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = $hotel !== null;
$title = $is_edit ? __('Edit Hotel', 'dhr-hotel-management') : __('Add New Hotel', 'dhr-hotel-management');
?>

<div class="wrap dhr-hotel-admin">
    <h1><?php echo $title; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="dhr-hotel-form">
        <?php wp_nonce_field('dhr_hotel_nonce'); ?>
        <input type="hidden" name="action" value="dhr_save_hotel">
        <?php if ($is_edit): ?>
            <input type="hidden" name="hotel_id" value="<?php echo esc_attr($hotel->id); ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="hotel_code"><?php _e('Hotel Code', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="hotel_code" name="hotel_code" class="regular-text"
                           value="<?php echo $is_edit ? esc_attr($hotel->hotel_code) : ''; ?>"
                           placeholder="<?php esc_attr_e('e.g. DRE013', 'dhr-hotel-management'); ?>">
                    <p class="description">
                        <?php _e('Optional code from the external CRS (e.g. SHR). This can be used for API-based sync.', 'dhr-hotel-management'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="name"><?php _e('Hotel Name', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="name" name="name" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->name) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="description"><?php _e('Description', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <textarea id="description" name="description" rows="5" class="large-text"><?php echo $is_edit ? esc_textarea($hotel->description) : ''; ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th><label for="address"><?php _e('Address', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="address" name="address" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->address) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="city"><?php _e('City', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="city" name="city" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->city) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="province"><?php _e('Province', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="province" name="province" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->province) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="country"><?php _e('Country', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="country" name="country" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->country) : 'South Africa'; ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="latitude"><?php _e('Latitude', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="latitude" name="latitude" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->latitude) : ''; ?>" required>
                    <p class="description"><?php _e('Use Google Maps to find coordinates', 'dhr-hotel-management'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th><label for="longitude"><?php _e('Longitude', 'dhr-hotel-management'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="longitude" name="longitude" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->longitude) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="phone"><?php _e('Phone', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="text" id="phone" name="phone" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->phone) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="email"><?php _e('Email', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="email" id="email" name="email" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->email) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="website"><?php _e('Website', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url" id="website" name="website" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->website) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="image_url"><?php _e('Image URL', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url" id="image_url" name="image_url" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->image_url) : ''; ?>">
                    <button type="button" class="button" id="upload-image-btn"><?php _e('Upload Image', 'dhr-hotel-management'); ?></button>
                </td>
            </tr>
            
            <tr>
                <th><label for="google_maps_url"><?php _e('Google Maps URL', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <input type="url" id="google_maps_url" name="google_maps_url" class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($hotel->google_maps_url) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="status"><?php _e('Status', 'dhr-hotel-management'); ?></label></th>
                <td>
                    <select id="status" name="status">
                        <option value="active" <?php echo ($is_edit && $hotel->status === 'active') ? 'selected' : ''; ?>>
                            <?php _e('Active', 'dhr-hotel-management'); ?>
                        </option>
                        <option value="inactive" <?php echo ($is_edit && $hotel->status === 'inactive') ? 'selected' : ''; ?>>
                            <?php _e('Inactive', 'dhr-hotel-management'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php echo $is_edit ? __('Update Hotel', 'dhr-hotel-management') : __('Add Hotel', 'dhr-hotel-management'); ?>">
            <a href="<?php echo admin_url('admin.php?page=dhr-hotel-management'); ?>" class="button">
                <?php _e('Cancel', 'dhr-hotel-management'); ?>
            </a>
        </p>
    </form>
</div>


