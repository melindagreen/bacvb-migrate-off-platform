<?php

include_once get_stylesheet_directory() . '/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

if (isset($_GET['listing_id'])) {

    $post_id = intval($_GET['listing_id']);
    $meta_data = get_post_meta($post_id);
    $args = array(
        'post_type' => 'listing',
        'p' => $post_id,
        'posts_per_page' => 1
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $form_handler = new FormHandler();
            $form_handler->updateListing($post_id);
        }
        wp_reset_postdata();
    }
    include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-listing-form.php';
}
