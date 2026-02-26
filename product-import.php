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

// Include Spout library
require_once 'Spout/Autoloader/autoload.php';
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

$msg = $erroe_msg = '';

if (!empty($_FILES['file']['name'])) { // ||  !empty($_GET['myfile']) && $_GET['myfile'] == "read_start" to read file from server for faster processing
    $pathinfo = pathinfo($_FILES['file']['name']);
    //if(1==1) { //always execute for myfile=read
    if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['file']['size'] > 0) {

        // Main categories
        $main_cat = 'JJ Tools Hard Milling End Mills (Metric)';
        $sub_cat = 'Corner Radius End Mill';

        $inputFileName = $_FILES['file']['tmp_name'];
        //$inputFileName = get_template_directory() . '/pimp/'.$sub_cat.'.xlsx';
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($inputFileName);
        

        $main_cat_term = term_exists($main_cat, 'product_cat') ?: wp_insert_term($main_cat, 'product_cat');
        $main_cat_id = is_array($main_cat_term) ? $main_cat_term['term_id'] : $main_cat_term;

        $sub_cat_term = term_exists($sub_cat, 'product_cat') ?: wp_insert_term($sub_cat, 'product_cat', ['parent' => $main_cat_id]);
        $sub_cat_id = is_array($sub_cat_term) ? $sub_cat_term['term_id'] : $sub_cat_term;

        $inserted = false;
        $log = [];

        $sheetNo=$_GET['sheet'] ?? 1;
        $ii=1;
        foreach ($reader->getSheetIterator() as $sheet) {
            /*if($sheetNo != $ii) {
                $ii++;
                continue;
            } */           
            $sheet_name = $sheet->getName();
            $sub_sub_cat_term = term_exists($sheet_name, 'product_cat');
            if (!$sub_sub_cat_term) {
                $sub_sub_cat_term = wp_insert_term($sheet_name, 'product_cat', ['parent' => $sub_cat_id]);
            }
            $sub_sub_cat_id = is_array($sub_sub_cat_term) ? $sub_sub_cat_term['term_id'] : $sub_sub_cat_term;

            // Add/Update Term Meta
            update_term_meta($sub_sub_cat_id, 'category_listing_type', 'list');

            $first_row = true; // Flag to skip header row
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();

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
                // $post_id = 14294;

                if (!$post_id) {
                    $post_id = wp_insert_post([
                        'post_title' => $product_title,
                        'post_type' => 'product',
                        'post_content' => $cells[12] ?? '',
                        'post_status' => 'publish'
                    ]);
                }

                // Assign categories using IDs
                wp_set_object_terms($post_id, [$main_cat, $sub_cat, $sheet_name], 'product_cat');

                // Product type
                wp_set_object_terms($post_id, 'simple', 'product_type');

                // Price
                if (!empty($cells[15])) {
                    $price = round(str_replace(search: '$', '', $cells[15]));
                    update_post_meta($post_id, '_price', $price);
                    update_post_meta($post_id, '_regular_price', $price);
                }

                // ACF / Custom fields
                update_field('part_number', $product_title, $post_id);
                update_field('specifications', $cells[1] ?? '', $post_id);
                update_field('od_r_x_d', $cells[2] ?? '', $post_id);
                update_field('length_of_cut_l1', $cells[3] ?? '', $post_id);
                update_field('effective_length_leff', $cells[4] ?? '', $post_id);
                update_field('diameter', $cells[5] ?? '', $post_id);
                update_field('chamfer', $cells[6] ?? '', $post_id);
                update_field('angle', $cells[7] ?? '', $post_id);
                update_field('overall_length_l', $cells[8] ?? '', $post_id);
                update_field('type', $cells[9] ?? '', $post_id);
                update_field('shank_diameter_ds', $cells[10] ?? '', $post_id);
                update_field('flutes', $cells[11] ?? '', $post_id);                
                update_field('no_of_flutes', $cells[11] ?? '', $post_id);
                update_field('series', $cells[13] ?? '', $post_id);

                // Set image
                if (!empty($cells[14])) {
                    $image_name = array_slice(explode('/', $cells[14]), -1)[0];
                    $upload_dir = wp_upload_dir();
                    $file_path = $upload_dir['path'].'/'.$image_name;
                    add_filter( 'intermediate_image_sizes_advanced', 'disable_image_resizing_during_import', 10, 1 );// Remove the filter after import (optional)
                    create_attachment_from_path( $file_path, $post_id );
                    remove_filter( 'intermediate_image_sizes_advanced', 'disable_image_resizing_during_import' );
                    // echo $image_name.'<br>';
                    // echo "<br>";
                    // echo "$file_path";
                    // uploadRemoteImageAndAttach($cells[14], $post_id);
                }
                if (!$post_id && !$product_title) {
                $log[] = "Skipped: Empty Product Name (Row: $row_count, Sheet: $sheet_name)";
            } elseif ($post_id && get_post_status($post_id) === 'publish') {
                $log[] = "Inserted: $product_title (Sheet: $sheet_name)";
            } else {
                $log[] = "Skipped or updated: $product_title (Sheet: $sheet_name)";
            }

                $inserted = true;
            }
            //break;
        }

        $reader->close();
        //file_put_contents(__DIR__ . '/import_log.txt', implode("\n", $log));
        if ($inserted) {
            $msg = "Products inserted successfully!";
        } else {
            $erroe_msg = "No product found in the Excel file.";
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
