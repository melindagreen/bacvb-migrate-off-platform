<?php

namespace MaddenNino\Library\Memberpress;
use WP_Query;
use MaddenNino\Library\MemberPress\Utilities as MeprU;

class MemberPressFormHandler {

    private $listing_fields;
    private $event_fields;

    function __construct () {

        if (session_status() == PHP_SESSION_NONE) {

            session_start();
        }

		$this->listing_fields = [
            'partnerportal_description',
            'partnerportal_business_name',
            'partnerportal_website_link',
            'partnerportal_phone_number',
            'partnerportal_contact_email_for_visitors',
            'partnerportal_hours_description',
            'partnerportal_address_1',
            'partnerportal_address_2',
            'partnerportal_city',
            'partnerportal_zip',
            'partnerportal_state',
            'partnerportal_facebook',
            'partnerportal_instagram',
            'partnerportal_twitter'
        ];
        $this->event_fields = [
            'eventastic_description',
            'eventastic_business_name',
            'eventastic_website_link',
            'eventastic_phone_number',
            'eventastic_contact_email_for_visitors',
            'eventastic_hours_description',
            'eventastic_address_1',
            'eventastic_address_2',
            'eventastic_city',
            'eventastic_zip',
            'eventastic_state',
            'eventastic_facebook',
            'eventastic_instagram',
            'eventastic_twitter',
            'eventastic_price',
            'eventastic_price_varies',
            'eventastic_ticket_link',
            'eventastic_start_date',
            'eventastic_end_date',
            'eventastic_event_end',
            'eventastic_event_all_day',
            'eventastic_start_time',
            'eventastic_end_time'
        ];
	}

    public function addEvent() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {

            if(!$_SESSION['post_creation_attempted'] || !isset($_SESSION['post_creation_attempted'])) {

                $_SESSION['post_creation_attempted'] = true;
                // Sanitize post title
                $post_title = sanitize_text_field($_POST['post_title'] ?? '');
                $post_content = sanitize_text_field($_POST['eventastic_description'] ?? '');

                // Insert the post
                // error_log($_POST['eventastic_website_link']);
                $post_id = wp_insert_post(array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_status'   => 'pending', // Set status to pending
                    'post_type'     => 'event' // Adjust post type as needed
                ));
                $_SESSION['post_creation_attempted'] = $post_id;
                // error_log('Post Insert');
        }

        $post_id = $_SESSION['post_creation_attempted'];
        var_dump('Test:'. $post_id);

            if (!is_wp_error($post_id)) {
                // Update post meta fields
                $fields = $this->event_fields;

                // Loop through each field and update post meta
                foreach ($fields as $field) {
                    if (isset($_POST[$field])) {
                        update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                    }
                }

                // Handle image upload and update eventastic_gallery_square_featured_image
                if (!empty($_FILES['eventastic_gallery_square_featured_image']['name'])) {
                    $upload = wp_upload_bits($_FILES['eventastic_gallery_square_featured_image']['name'], null, file_get_contents($_FILES['eventastic_gallery_square_featured_image']['tmp_name']));
                    if (!$upload['error']) {
                        update_post_meta($post_id, 'eventastic_gallery_square_featured_image', $upload['url']);
                        // Set the uploaded image as the post thumbnail
                        $attachment_id = wp_insert_attachment(array(
                            'post_mime_type' => $_FILES['eventastic_gallery_square_featured_image']['type'],
                            'post_title'     => $_FILES['eventastic_gallery_square_featured_image']['name'],
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        ), $upload['file'], $post_id);
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);
                            set_post_thumbnail($post_id, $attachment_id);
                        }
                    }
                }

                // Retrieve the current user's group
                $current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

                // Update the group_events ACF field
                // Retrieve Group Event
                $group_events = get_field('group_events', $current_user_group[0]->ID);
                $group_events_ID = array();
                if (!empty($group_events)) {
                    foreach ($group_events as $event) {
                        $group_events_ID[] = $event->ID;
                    }
                }

                $updated_group_events = array_merge($group_events_ID, array($post_id)); // Merge the arrays
                update_field('group_events', $updated_group_events, $current_user_group[0]->ID);
            }
            $_SESSION['post_creation_attempted'] = false;
             // Redirect to the same page with action=events
             wp_redirect(add_query_arg('action', 'events', $_SERVER['REQUEST_URI']));
             exit;
        }
    }

    public function updateEvent($post_id) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {
            // $test = MeprU::is_cloned_post($post_id);
            // error_log($test);

            if(!$_SESSION['post_creation_attempted'] || empty($_SESSION['post_creation_attempted'])) {

                $_SESSION['post_creation_attempted'] = true;
                // Sanitize post title
                $post_title = sanitize_text_field($_POST['post_title'] ?? '');
                $post_content = sanitize_text_field($_POST['eventastic_description'] ?? '');

                // Clone the post
                $cloned_post_id = wp_insert_post(array(
                    'post_title'   => $post_title,
                    'post_content' => $post_content,
                    'post_status'  => 'pending', // Set status to pending
                    'post_type'    => 'event' // Adjust post type as needed
                ));
                $_SESSION['post_creation_attempted'] = $cloned_post_id;
            }

            $cloned_post_id = $_SESSION['post_creation_attempted'];

            //Replace Cloned Post ID with the original
            if(MeprU::is_cloned_post($post_id) || get_post_status($post_id) === 'pending') {

                $old_post_id = $post_id;
                $post_id = MeprU::get_original_post_id($post_id);

                // error_log('Event Test');
                // error_log($old_post_id);
                // error_log($post_id);
                // error_log(get_post_status($old_post_id));
                // error_log(MeprU::is_cloned_post($old_post_id));

                // Remove cloned_post_id meta data
                delete_post_meta($post_id, 'cloned_post_id');
                // Remove original_post_id post meta from the original post
                delete_post_meta($old_post_id, 'original_post_id');
                wp_delete_post($old_post_id, true);
            }

            // Original post ID
            $original_post_id = $post_id;

            // Check if post was cloned successfully
            if (!is_wp_error($cloned_post_id)) {
                // Update post meta fields
                $fields = $this->event_fields;
                // Loop through each field and update post meta
                foreach ($fields as $field) {
                    if (isset($_POST[$field])) {
                        update_post_meta($cloned_post_id, $field, sanitize_text_field($_POST[$field]));
                    }
                }
            }

                // Store the original post ID as meta data in the cloned post
                add_post_meta($cloned_post_id, 'original_post_id', $original_post_id);
                add_post_meta($original_post_id, 'cloned_post_id', $cloned_post_id);

                // Handle image upload and set as post thumbnail for cloned post
                if (!empty($_FILES['eventastic_gallery_square_featured_image']['name'])) {
                    $upload = wp_upload_bits($_FILES['eventastic_gallery_square_featured_image']['name'], null, file_get_contents($_FILES['eventastic_gallery_square_featured_image']['tmp_name']));
                    if (!$upload['error']) {
                        update_post_meta($cloned_postt_id, 'eventastic_gallery_square_featured_image', $upload['url']);
                        // Set the uploaded image as the post thumbnail
                        $attachment_id = wp_insert_attachment(array(
                            'post_mime_type' => $_FILES['eventastic_gallery_square_featured_image']['type'],
                            'post_title' => $_FILES['eventastic_gallery_square_featured_image']['name'],
                            'post_content' => '',
                            'post_status' => 'inherit'
                        ), $upload['file'], $cloned_post_id);
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);
                            set_post_thumbnail($cloned_post_id, $attachment_id);
                        }
                    }
                }

                // Retrieve the current user's group
                $current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

               // Update the group_event ACF field
               // Retrieve Group Events
                $group_events = get_field('group_events', $current_user_group[0]->ID);
                $group_events_ID = array();
                if (!empty($group_events)) {
                    foreach ($group_events as $listing) {
                        $group_events_ID[] = $listing->ID;
                    }
                }
                $updated_group_events = array_merge($group_events_ID, array($cloned_post_id)); // Merge the arrays
                update_field('group_events', $updated_group_events, $current_user_group[0]->ID);

                $_SESSION['post_creation_attempted'] = false;
                // Redirect to the same page with action=events
                wp_redirect(site_url(). '/account/?action=events');
                exit;
            }
    }

    public function addListing() {
        //error_log('Test');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {
            //error_log('Running');
            if(!$_SESSION['post_creation_attempted'] || empty($_SESSION['post_creation_attempted'])) {
                $_SESSION['post_creation_attempted'] = true;
            // Sanitize post title
            $post_title = sanitize_text_field($_POST['post_title'] ?? '');
            $post_content = sanitize_text_field($_POST['partnerportal_description'] ?? '');

            // Prepare post data
            $post_data = array(
                'post_title'    => $post_title,
                'post_content'  => $post_content,
                'post_status'   => 'pending', // Set status to pending
                'post_type'     => 'listing' // Adjust post type as needed

            );

            // Insert the post
            $post_id = wp_insert_post($post_data);
            $_SESSION['post_creation_attempted'] = $post_id;
        }

        $post_id = $_SESSION['post_creation_attempted'];

            if (!is_wp_error($post_id)) {
                // Update post meta fields
                $fields = $this->listing_fields;

                // Loop through each field and update post meta
                foreach ($fields as $field) {
                    if (isset($_POST[$field])) {
                        update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                    }
                }

                // Handle image upload and update partnerportal_gallery_square_featured_image
                if (!empty($_FILES['partnerportal_gallery_square_featured_image']['name'])) {
                    $upload = wp_upload_bits($_FILES['partnerportal_gallery_square_featured_image']['name'], null, file_get_contents($_FILES['partnerportal_gallery_square_featured_image']['tmp_name']));
                    if (!$upload['error']) {
                        update_post_meta($post_id, 'partnerportal_gallery_square_featured_image', $upload['url']);
                        // Set the uploaded image as the post thumbnail
                        $attachment_id = wp_insert_attachment(array(
                            'post_mime_type' => $_FILES['partnerportal_gallery_square_featured_image']['type'],
                            'post_title'     => $_FILES['partnerportal_gallery_square_featured_image']['name'],
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        ), $upload['file'], $post_id);
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);
                            set_post_thumbnail($post_id, $attachment_id);
                        }
                    }
                }

                // Assign Categories if selected
                //error_log($_POST['listing_categories']);
                if (isset($_POST['listing_categories'])) {
                    // Sanitize the category slugs
                    $category_slugs = array_map('sanitize_text_field', $_POST['listing_categories']);

                    // Get term IDs for the slugs
                    $term_ids = [];
                    foreach ($category_slugs as $slug) {
                        $term = get_term_by('slug', $slug, 'listing_categories');
                        if ($term && !is_wp_error($term)) {
                            $term_ids[] = $term->term_id;
                        }
                    }

                    // Set the terms for the post
                    wp_set_post_terms($post_id, $term_ids, 'listing_categories');
                } else {
                    // If no categories are checked, remove all terms
                    wp_set_post_terms($post_id, [], 'listing_categories');
                }

                // Retrieve the current user's group
                $current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

               // Update the group_listing ACF field
               // Retrieve Group Listing
                $group_listing = get_field('group_listing', $current_user_group[0]->ID);
                $group_listing_ID = array();
                if (!empty($group_listing)) {
                    foreach ($group_listing as $listing) {
                        $group_listing_ID[] = $listing->ID;
                    }
                }
                $updated_group_listing = array_merge($group_listing_ID, array($post_id)); // Merge the arrays
                update_field('group_listing', $updated_group_listing, $current_user_group[0]->ID);
            }

            $_SESSION['post_creation_attempted'] = false;
            // Redirect to the same page with action=listings
            wp_redirect(site_url(). '/account/?action=listings&update=true');
            exit;
        }
    }

    public function updateListing($post_id) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {

            if(!$_SESSION['post_creation_attempted'] || empty($_SESSION['post_creation_attempted'])) {

                $_SESSION['post_creation_attempted'] = true;
                // Sanitize post title
                $post_title = sanitize_text_field($_POST['post_title'] ?? '');
                $post_content = sanitize_text_field($_POST['partnerportal_description'] ?? '');

                // Clone the post
                $cloned_post_id = wp_insert_post(array(
                    'post_title'   => $post_title,
                    'post_content' => $post_content,
                    'post_status'  => 'pending', // Set status to pending
                    'post_type'    => 'listing' // Adjust post type as needed
                ));
                $_SESSION['post_creation_attempted'] = $cloned_post_id;
            }

            $cloned_post_id = $_SESSION['post_creation_attempted'];

            //error_log(MeprU::get_original_post_id($post_id));
             //Replace Cloned Post ID with the original
             if(MeprU::is_cloned_post($post_id) || get_post_status($post_id) === 'pending') {
                //error_log(MeprU::is_cloned_post($post_id));
                $old_post_id = $post_id;
                $post_id = MeprU::get_original_post_id($post_id);

                // error_log('Listing Test');
                // error_log($old_post_id);
                // error_log($post_id);
                // error_log(get_post_status($old_post_id));
                // Remove cloned_post_id meta data
                delete_post_meta($post_id, 'cloned_post_id');
                // Remove original_post_id post meta from the original post
                delete_post_meta($old_post_id, 'original_post_id');
                wp_delete_post($old_post_id, true);
            }


            // Original post ID
            $original_post_id = $post_id;

            // Check if post was cloned successfully
            if (!is_wp_error($cloned_post_id)) {
                // Update post meta fields
                $fields = $this->listing_fields;
                // Loop through each field and update post meta
                foreach ($fields as $field) {
                    if (isset($_POST[$field])) {
                        update_post_meta($cloned_post_id, $field, sanitize_text_field($_POST[$field]));
                    }
                }
            }

                // Store the original post ID as meta data in the cloned post
                add_post_meta($cloned_post_id, 'original_post_id', $original_post_id);
                add_post_meta($original_post_id, 'cloned_post_id', $cloned_post_id);

                // Handle image upload and set as post thumbnail for cloned post
                if (!empty($_FILES['partnerportal_gallery_square_featured_image']['name'])) {
                    $upload = wp_upload_bits($_FILES['partnerportal_gallery_square_featured_image']['name'], null, file_get_contents($_FILES['partnerportal_gallery_square_featured_image']['tmp_name']));
                    if (!$upload['error']) {
                        update_post_meta($cloned_postt_id, 'partnerportal_gallery_square_featured_image', $upload['url']);
                        // Set the uploaded image as the post thumbnail
                        $attachment_id = wp_insert_attachment(array(
                            'post_mime_type' => $_FILES['partnerportal_gallery_square_featured_image']['type'],
                            'post_title' => $_FILES['partnerportal_gallery_square_featured_image']['name'],
                            'post_content' => '',
                            'post_status' => 'inherit'
                        ), $upload['file'], $cloned_post_id);
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);
                            set_post_thumbnail($cloned_post_id, $attachment_id);
                        }
                    }
                }

                // Assign Categories if selected
                //($_POST['listing_categories']);
                if (isset($_POST['listing_categories'])) {
                    // Sanitize the category slugs
                    $category_slugs = array_map('sanitize_text_field', $_POST['listing_categories']);

                    // Get term IDs for the slugs
                    $term_ids = [];
                    foreach ($category_slugs as $slug) {
                        $term = get_term_by('slug', $slug, 'listing_categories');
                        if ($term && !is_wp_error($term)) {
                            $term_ids[] = $term->term_id;
                        }
                    }

                    // Set the terms for the post
                    wp_set_post_terms($cloned_post_id, $term_ids, 'listing_categories');
                } else {
                    // If no categories are checked, remove all terms
                    wp_set_post_terms($cloned_post_id, [], 'listing_categories');
                }

                // Retrieve the current user's group
                $current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

               // Update the group_listing ACF field
               // Retrieve Group Listing
                $group_listing = get_field('group_listing', $current_user_group[0]->ID);
                $group_listing_ID = array();
                if (!empty($group_listing)) {
                    foreach ($group_listing as $listing) {
                        $group_listing_ID[] = $listing->ID;
                    }
                }
                $updated_group_listing = array_merge($group_listing_ID, array($cloned_post_id)); // Merge the arrays
                update_field('group_listing', $updated_group_listing, $current_user_group[0]->ID);

                $_SESSION['post_creation_attempted'] = false;
                // Redirect to the same page with action=listings
                wp_redirect(site_url(). '/account/?action=listings&update=true');
                exit;
            }
        }
}
