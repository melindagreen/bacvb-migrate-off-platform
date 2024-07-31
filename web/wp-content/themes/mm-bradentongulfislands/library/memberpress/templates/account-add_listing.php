<?php 
include_once get_stylesheet_directory() . '/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

    $form_handler = new FormHandler();
    $listing_id = intval($_GET['listing_id']);
    // Set up query arguments
    $args = array(
        'post_type' => 'listing',
        'p' => $listing_id,
        'post_status' => 'publish',
        'posts_per_page' => 1
    );

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $form_handler->addListing();
            include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-listing-form.php';
        }
        // Restore global post data
        wp_reset_postdata();
    } else {
        echo 'Listing not found.';
    }

?>
