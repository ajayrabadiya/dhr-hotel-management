<?php
set_time_limit(0);
ini_set('max_execution_time', 0);
get_header(); /* Template Name: Product Import */

/**
 * Upload remote image and attach to product
 */
function uploadRemoteImageAndAttach($image_url, $parent_id) {
    $get = wp_remote_get($image_url);
    $type = wp_remote_retrieve_header($get, 'content-type');
    if (!$type) return false;

    $mirror = wp_upload_bits(basename($image_url), '', wp_remote_retrieve_body($get));
    $attachment = [
        'post_title' => basename($image_url),
        'post_mime_type' => $type
    ];

    $attach_id = wp_insert_attachment($attachment, $mirror['file'], $parent_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $mirror['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($parent_id, $attach_id);
    return $attach_id;
}
// --------------
function create_attachment_from_path( $file_path, $post_id = 0 ) {

    // Get file info
    $filetype = wp_check_filetype( basename( $file_path ), null );

    $attachment = array(
        'guid'           => wp_upload_dir()['url'] . '/' . basename( $file_path ),
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    // Insert attachment into media library
    $attach_id = wp_insert_attachment( $attachment, $file_path, $post_id );
    // echo $attach_id;
    // echo "<br>";
    // Generate metadata (thumbnails etc.)
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    // $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
    // wp_update_attachment_metadata( $attach_id, $attach_data );
    set_post_thumbnail($post_id, $attach_id);
    return $attach_id;
}
function disable_image_resizing_during_import( $sizes ) {
    return array();
}

// --------------

// PhpSpreadsheet – Excel import (.xlsx, .xls)
$vendor_autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($vendor_autoload)) {
    wp_die('Excel library not installed. Run <code>composer install</code> in the plugin directory: ' . __DIR__);
}
require_once $vendor_autoload;

use PhpOffice\PhpSpreadsheet\IOFactory;

$msg = $erroe_msg = '';

if (!empty($_FILES['file']['name'])) {
    $pathinfo = pathinfo($_FILES['file']['name']);
    if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['file']['size'] > 0) {

        // Main categories
        $main_cat = 'JJ Tools Hard Milling End Mills (Metric)';
        $sub_cat = 'Corner Radius End Mill';

        $inputFileName = $_FILES['file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($inputFileName);
        } catch (Exception $e) {
            $erroe_msg = 'Invalid or corrupted Excel file: ' . esc_html($e->getMessage());
            $spreadsheet = null;
        }

        if ($spreadsheet) {
            $main_cat_term = term_exists($main_cat, 'product_cat') ?: wp_insert_term($main_cat, 'product_cat');
            $main_cat_id = is_array($main_cat_term) ? $main_cat_term['term_id'] : $main_cat_term;

            $sub_cat_term = term_exists($sub_cat, 'product_cat') ?: wp_insert_term($sub_cat, 'product_cat', ['parent' => $main_cat_id]);
            $sub_cat_id = is_array($sub_cat_term) ? $sub_cat_term['term_id'] : $sub_cat_term;

            $inserted = false;
            $log = [];

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $sheet_name = $sheet->getTitle();
                $sub_sub_cat_term = term_exists($sheet_name, 'product_cat');
                if (!$sub_sub_cat_term) {
                    $sub_sub_cat_term = wp_insert_term($sheet_name, 'product_cat', ['parent' => $sub_cat_id]);
                }
                $sub_sub_cat_id = is_array($sub_sub_cat_term) ? $sub_sub_cat_term['term_id'] : $sub_sub_cat_term;

                // Add/Update Term Meta
                update_term_meta($sub_sub_cat_id, 'category_listing_type', 'list');

                /*
                 * Spreadsheet column mapping (0-based index):
                 * A=0: Product Number    -> part_number, post_title
                 * B=1: Specifications   -> specifications
                 * C=2: OD R X D          -> od_r_x_d
                 * D=3: Length of Cut L1  -> length_of_cut_l1
                 * E=4: Effective Length L2 -> effective_length_leff, length_of_cut_l2
                 * F=5: Neck Diameter     -> diameter
                 * G=6: Chamfer Angle     -> chamfer_angle
                 * H=7: Overall Length L  -> overall_length_l
                 * I=8: Type              -> type
                 * J=9: Shank d           -> shank_diameter_ds
                 * K=10: No. of Flutes    -> flutes
                 * L=11: Product Description -> post_content
                 * M=12: Series           -> series
                 * N=13: List Price       -> _price, _regular_price
                 * O=14: Image path       -> (optional) featured image
                 */
                $rows = $sheet->toArray();
                $first_row = true;
                foreach ($rows as $cells) {
                    // Normalize: ensure numeric keys and trim string values
                    $cells = array_map(function ($v) { return is_scalar($v) ? trim((string) $v) : ''; }, array_values($cells ?: []));

                    // Skip header row
                    if ($first_row) { $first_row = false; continue; }

                    // Skip completely empty rows
                    if (!array_filter($cells)) continue;

                    $product_title = trim($cells[0] ?? '');
                    if (!$product_title) continue;

                    // Check if product exists
                    $posts = get_posts([
                        'post_type' => 'product',
                        'post_status' => 'publish',
                        'title' => $product_title,
                        'posts_per_page' => 1,
                        'fields' => 'ids'
                    ]);
                    $post_id = !empty($posts) ? $posts[0] : false;

                    if (!$post_id) {
                        $post_id = wp_insert_post([
                            'post_title' => $product_title,
                            'post_type' => 'product',
                            'post_content' => $cells[11] ?? '', // L: Product Description
                            'post_status' => 'publish'
                        ]);
                    }

                    // Assign categories using IDs
                    wp_set_object_terms($post_id, [$main_cat, $sub_cat, $sheet_name], 'product_cat');

                    // Product type
                    wp_set_object_terms($post_id, 'simple', 'product_type');

                    // Price (N=13: List Price)
                    if (isset($cells[13]) && $cells[13] !== '') {
                        $price = preg_replace('/[^0-9.]/', '', $cells[13]);
                        if ($price !== '') {
                            $price = round((float) $price, 2);
                            update_post_meta($post_id, '_price', $price);
                            update_post_meta($post_id, '_regular_price', $price);
                        }
                    }

                    // ACF / Custom fields – column indices match spreadsheet A–N
                    update_field('part_number', $product_title, $post_id);
                    update_field('specifications', $cells[1] ?? '', $post_id);           // B
                    update_field('od_r_x_d', $cells[2] ?? '', $post_id);                  // C
                    update_field('length_of_cut_l1', $cells[3] ?? '', $post_id);          // D
                    update_field('effective_length_leff', $cells[4] ?? '', $post_id);    // E
                    update_field('length_of_cut_l2', $cells[4] ?? '', $post_id);         // E (same as Effective Length L2)
                    update_field('diameter', $cells[5] ?? '', $post_id);                  // F: Neck Diameter
                    update_field('chamfer_angle', $cells[6] ?? '', $post_id);             // G: Chamfer Angle
                    update_field('chamfer', $cells[6] ?? '', $post_id);                   // G (legacy ACF field name)
                    update_field('overall_length_l', $cells[7] ?? '', $post_id);          // H
                    update_field('type', $cells[8] ?? '', $post_id);                      // I
                    update_field('shank_diameter_ds', $cells[9] ?? '', $post_id);         // J: Shank d
                    update_field('flutes', $cells[10] ?? '', $post_id);                   // K: No. of Flutes
                    update_field('series', $cells[12] ?? '', $post_id);                  // M

                    // Set image (optional column O=14)
                    if (!empty($cells[14])) {
                        $image_name = array_slice(explode('/', $cells[14]), -1)[0];
                        $upload_dir = wp_upload_dir();
                        $file_path = $upload_dir['path'].'/'.$image_name;
                        add_filter( 'intermediate_image_sizes_advanced', 'disable_image_resizing_during_import', 10, 1 );
                        create_attachment_from_path( $file_path, $post_id );
                        remove_filter( 'intermediate_image_sizes_advanced', 'disable_image_resizing_during_import' );
                    }

                    if (!$post_id && !$product_title) {
                        $log[] = "Skipped: Empty Product Name (Sheet: $sheet_name)";
                    } elseif ($post_id && get_post_status($post_id) === 'publish') {
                        $log[] = "Inserted: $product_title (Sheet: $sheet_name)";
                    } else {
                        $log[] = "Skipped or updated: $product_title (Sheet: $sheet_name)";
                    }

                    $inserted = true;
                }
            }

            if ($inserted) {
                $msg = "Products inserted successfully!";
            } else {
                $erroe_msg = "No product found in the Excel file.";
            }
        }
    } else {
        $erroe_msg = "Please select a valid Excel file (.xlsx or .xls)";
    }
}
?>

<div id="excelsucess"><?php echo $msg; ?></div>
<div class="upload_error"><?php echo $erroe_msg; ?></div>

<form action="#" method="post" name="myForm" enctype="multipart/form-data" class="upload_excel" style="text-align: center; margin: 100px 0px;">
    <input type="file" name="file" id="upload_file">
    <input type="submit" value="Upload" class="submit excel_btn">
</form>

<script>
jQuery('.submit').click(function () {
    if (jQuery('#upload_file').val().length == 0) {
        jQuery('#excelsucess').html('Please select file');
        return false;
    }
});
</script>

<?php get_footer(); ?>
