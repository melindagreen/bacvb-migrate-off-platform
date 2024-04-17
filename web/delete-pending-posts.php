<?php
// Include WordPress bootstrap file
require_once('wp-load.php');

  // Define the post type
  $post_type = 'event';

  // Get pending posts of the specified post type
  $pending_posts = get_posts(array(
      'post_type'      => $post_type,
      'post_status'    => 'pending',
      'posts_per_page' => -1, // Get all pending posts
  ));

  // Loop through pending posts and delete them
  foreach ($pending_posts as $pending_post) {
      wp_delete_post($pending_post->ID, true); // Set the second parameter to true to bypass trash
  }

    // Reset the query
    wp_reset_postdata();


?>
