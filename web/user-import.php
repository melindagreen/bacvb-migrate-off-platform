<?php
// Include WordPress bootstrap file
require_once('wp-load.php');

// Function to update ACF field for user
function update_user_acf_field($user_id, $field_key, $field_value) {
    update_field($field_key, $field_value, 'user_' . $user_id);
}

// Read CSV file
$csv_file = site_url().'/bacvb-partners.csv';
$csv_data = array_map('str_getcsv', file($csv_file));

// Iterate through each row in the CSV
foreach ($csv_data as $row) {
    // Extract data from CSV
    list($id, $is_active, $is_verified, $is_super, $email, $first_name, $last_name, $mi, $username, $password, $remember_token, $created_at, $updated_at, $is_opted_in, $has_agreed, $places_slug, $team_name, $team_id, $team_slug) = $row;

    // Check if the user already exists by username or email
    if (username_exists($username) || email_exists($email)) {
        echo "User with username or email already exists: $username / $email. Skipping...\n";
        continue;
    }

    // Create the user
    $user_id = wp_insert_user(array(
        'user_email'     => $email,
        'user_login'     => $username,
        'user_pass'      => $password,
        'first_name'     => $first_name,
        'last_name'      => $last_name,
        'role'           => 'partner'
        // Add other user fields as needed
    ));

    // Check if the user was created successfully
    if (is_wp_error($user_id)) {
        echo "Error creating user: " . $user_id->get_error_message();
        continue;
    }

    // Query for the group post by its slug
$group_query = new WP_Query(array(
    'post_type' => 'memberpressgroup',
    'name' => $team_slug,
    'posts_per_page' => 1
));

// Check if the group post exists
if ($group_query->have_posts()) {
    // The group post exists
    $group_post = $group_query->posts[0];
    $group_post_id = $group_post->ID;
} else {
    // Group post doesn't exist, create it
    $group_post_id = wp_insert_post(array(
        'post_title'   => $team_name,
        'post_name'    => $team_slug,
        'post_type'    => 'memberpressgroup',
        'post_status'  => 'publish'
        // Add other post settings as needed
    ));
}

    // Reset the query
    wp_reset_postdata();

    // Query for the listing post by its slug
    $listing_query = new WP_Query(array(
        'post_type' => 'listing',
        'name' => $places_slug,
        'posts_per_page' => 1
    ));

    // Check if the listing post exists
    if ($listing_query->have_posts()) {
        // The listing post exists
        $listing = $listing_query->posts[0];
        $listing_id = $listing->ID;
    } else {
        // Listing post doesn't exist
        $listing_id = null;
    }

    // Reset the query
    wp_reset_postdata();

    // Update ACF fields
    update_field('partner_group', $group_post_id, 'user_' . $user_id); 
    update_field('group_listing', $listing_id, $group_post_id);

}
?>
