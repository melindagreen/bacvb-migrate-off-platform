<?php
// Include WordPress bootstrap file
require_once('wp-load.php');

// Function to update post publish date
function update_post_publish_date($post_slug, $created_at) {
    $args = array(
        'name'           => $post_slug,
        'post_type'      => 'post',
        'post_status'    => 'any',
        'posts_per_page' => 1
    );

    $posts = get_posts($args);

    if (!empty($posts)) {
        $post_id = $posts[0]->ID;
        $post_data = array(
            'ID'            => $post_id,
            'post_date'     => $created_at,
            'post_date_gmt' => get_gmt_from_date($created_at)
        );

        wp_update_post($post_data);
    }
}

// Read CSV file
$csv_file = site_url().'/bacvb-pages.csv';
$csv_data = array_map('str_getcsv', file($csv_file));

// Iterate through each row in the CSV
foreach ($csv_data as $row) {
    // Extract data from CSV
    list($id, $subtype, $is_active, $name, $slug, $body, $meta_title, $meta_description, $meta_keywords, $searchable, $deleted_at, $created_at, $updated_at, $is_converted) = $row;

    // Update post publish date
    update_post_publish_date($slug, $created_at);
}
?>

