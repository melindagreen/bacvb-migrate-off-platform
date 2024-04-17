<?php

include_once get_stylesheet_directory() .'/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

if (isset($_GET['listing_id'])) {

    // Get the listing ID from the URL parameter
    $post_id = intval($_GET['listing_id']);
    $meta_data = get_post_meta($post_id);
    // Retrieve the listing post using the listing ID
    $listing = get_post($post_id);
            
    // Check if the listing post exists and is of type 'listing'
    if ($listing && $listing->post_type === 'listing') {
        $form_handler = new FormHandler();
        $form_handler->updateListing($post_id);
        include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-listing-form.php';
    }
}