<?php 

include_once get_stylesheet_directory() .'/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;


    $listing_id = intval($_GET['listing_id']);
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
            $form_handler = new FormHandler();
            $form_handler->addListing();
            include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-listing-form.php';
        }
        wp_reset_postdata();
    }

