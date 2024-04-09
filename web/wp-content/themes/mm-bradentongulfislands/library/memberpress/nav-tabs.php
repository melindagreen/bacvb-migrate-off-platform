<?php 

namespace MaddenNino\Library\Memberpress;

use MaddenNino\Library\Constants as C;
use WP_Query;

class NavTabs {
	function __construct () {
		add_filter( 'mepr_account_nav_content', array(get_called_class(), 'nav_tabs'), 10, 1);
        add_action('mepr_enqueue_scripts', array(get_called_class(),'mepr_enqueue_scripts'), 10, 3);
	}

    public static function nav_tabs($action) {
        
         // Memberpress Account styles
         wp_enqueue_style(
            C::THEME_PREFIX . "-memberpress-account-css", // handle
            get_stylesheet_directory_uri()."/assets/build/memberpress-account.css", // src
            [], // dependencies
            null
        );

        if($_GET['action'] === 'tab1') {

            // Retrieve the current user's group
            $current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

            // Retrieve Group Listing
            $group_listing = get_field('group_listing', $current_user_group[0]->ID);
            $group_listing_ID = $group_listing[0]->ID;
            $listings = new WP_Query(array(
                'post_type'      => 'listing',
                'post__in'       => array($group_listing_ID), 
                'posts_per_page' => 1, 
            ));
        
            // Display frontend form for editing listings
            ?>
            <?php
            if ($listings->have_posts()) :
                while ($listings->have_posts()) : $listings->the_post();
                    
                $post_id = get_the_ID();

                // Get all post meta data
        
                $meta_data = get_post_meta($post_id);

    // Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {
    // Update post title
    $post_title = sanitize_text_field($_POST['post_title'] ?? ''); // Sanitize post title
    wp_update_post(array('ID' => $post_id, 'post_title' => $post_title)); // Update post title
    
    // Update post meta fields
    $fields = array(
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
    );

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
                'post_title' => $_FILES['partnerportal_gallery_square_featured_image']['name'],
                'post_content' => '',
                'post_status' => 'inherit'
            ), $upload['file'], $post_id);
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
    }

    // Redirect to the same page with update=true
    wp_redirect(add_query_arg('update', 'true'));
    exit;
}


if (isset($_GET['update']) && $_GET['update'] === 'true') {
    echo '<div style="background-color: #c96a39db; border: 0.5px solid #c96a39; padding: 0.2rem; width:33%;margin-bottom:1rem" class="notice notice-success is-dismissible"><p style="color:white;margin:0;margin-left:1rem;">Listing has been updated successfully.</p></div>';
}
?>

<form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>
    
    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="partnerportal_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="partnerportal_gallery_square_featured_image" id="partnerportal_gallery_square_featured_image">       
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== --> 
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Title:</label>
    <input type="text" name="post_title" id="post_title" value="<?php echo esc_attr(get_the_title($post_id)); ?>">

    <!-- Description -->
    <label for="partnerportal_description">Description:</label>
    <textarea name="partnerportal_description" id="partnerportal_description"><?php echo esc_html($meta_data['partnerportal_description'][0] ?? ''); ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="partnerportal_business_name">Business Name:</label>
        <input type="text" name="partnerportal_business_name" id="partnerportal_business_name" value="<?php echo esc_attr($meta_data['partnerportal_business_name'][0] ?? ''); ?>">

        <!-- Website Link -->
        <label for="partnerportal_website_link">Website Link:</label>
        <input type="url" name="partnerportal_website_link" id="partnerportal_website_link" value="<?php echo esc_attr($meta_data['partnerportal_website_link'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Phone Number -->
        <label for="partnerportal_phone_number">Phone Number:</label><br>
        <input type="tel" name="partnerportal_phone_number" id="partnerportal_phone_number" value="<?php echo esc_attr($meta_data['partnerportal_phone_number'][0] ?? ''); ?>"><br>

        <!-- Contact Email for Visitors -->
        <label for="partnerportal_contact_email_for_visitors">Contact Email for Visitors:</label><br>
        <input type="email" name="partnerportal_contact_email_for_visitors" id="partnerportal_contact_email_for_visitors" value="<?php echo esc_attr($meta_data['partnerportal_contact_email_for_visitors'][0] ?? ''); ?>"><br>
    </div>

    <!-- ==== HOURS ==== -->
    <h2 class="mepr-account-form__section-title">Hours</h2>
    
    <!-- Hours Description -->
    <label for="partnerportal_hours_description">Hours Description:</label>
    <textarea name="partnerportal_hours_description" id="partnerportal_hours_description"><?php echo esc_html($meta_data['partnerportal_hours_description'][0] ?? ''); ?></textarea>
    
    <!-- ==== ADDRESS INFORMATION ==== -->
    <h2 class="mepr-account-form__section-title">Address Information</h2>
    
    <div class="mepr-account-form__col-2">
        <!-- Address Line 1 -->
        <label for="partnerportal_address_1">Address Line 1:</label><br>
        <input type="text" name="partnerportal_address_1" id="partnerportal_address_1" value="<?php echo esc_attr($meta_data['partnerportal_address_1'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-2">
         <!-- Address Line 2 -->
         <label for="partnerportal_address_2">Address Line 2:</label><br>
        <input type="text" name="partnerportal_address_2" id="partnerportal_address_2" value="<?php echo esc_attr($meta_data['partnerportal_address_2'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="partnerportal_city">City:</label><br>
        <input type="text" name="partnerportal_city" id="partnerportal_city" value="<?php echo esc_attr($meta_data['partnerportal_city'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="partnerportal_zip">Zip Code:</label><br>
        <input type="text" name="partnerportal_zip" id="partnerportal_zip" value="<?php echo esc_attr($meta_data['partnerportal_zip'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="partnerportal_state">State:</label><br>
        <input type="text" name="partnerportal_state" id="partnerportal_state" value="<?php echo esc_attr($meta_data['partnerportal_state'][0] ?? ''); ?>">
    </div> 

    <!-- ==== SOCIAL ==== -->
    <h2 class="mepr-account-form__section-title">SOCIAL</h2>
    
    <div class="mepr-account-form__col-2">
        <!-- Facebook -->
        <label for="partnerportal_facebook">Facebook</label><br>
        <input type="url" name="partnerportal_facebook" id="partnerportal_facebook" value="<?php echo esc_attr($meta_data['partnerportal_facebook'][0] ?? ''); ?>">

        <!-- Instagram -->
        <label for="partnerportal_instagram">Instagram</label><br>
        <input type="url" name="partnerportal_instagram" id="partnerportal_instagram" value="<?php echo esc_attr($meta_data['partnerportal_instagram'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Twitter -->
        <label for="partnerportal_twitter">Twitter</label><br>
        <input type="url" name="partnerportal_twitter" id="partnerportal_twitter" value="<?php echo esc_attr($meta_data['partnerportal_twitter'][0] ?? ''); ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="Update">
    <br>
    <a style="margin-top:2rem;" href="<?php echo esc_url(add_query_arg(array('action' => 'add_listing'))); ?>" class="mepr-button btn-outline btn btn-outline">Add New Listing</a>
</form>

        <?php
            endwhile;
            endif;
            wp_reset_postdata();

          }

         else if($_GET['action'] === 'tab0') {

           // Retrieve the current user's group
            $current_user_group = get_field('partner_group', 'user_' . get_current_user_id());

            // Retrieve Group Event
            $group_events = get_field('group_events', $current_user_group[0]->ID);
            $group_events_ID = array();

            if (!empty($group_events)) {
                foreach ($group_events as $event) {
                    $group_events_ID[] = $event->ID;
                }
            }
            $events = new WP_Query(array(
                'post_type'      => 'event',
                'post__in'       => $group_events_ID, 
                'posts_per_page' => -1,
                'post_status'    => array('publish', 'pending'), // Include both published and pending review posts
            ));
        
            
            // Display frontend form for editing events
            if ($events->have_posts() && !empty($group_events)) : ?>
                <div class="mepr-event-cards">
            <?php
                while ($events->have_posts()) : $events->the_post();
                    ?>
                    <div class="mepr-event-cards__card">
                        <h3><?php the_title(); ?></h3>
                        <h4 class="event-card-status<?php echo get_post_status() === 'publish' ? '--green' : '--red' ?>">Status: <span><?php echo get_post_status(); ?></span></h4>
                        <div class="event-content">
                            <?php
                            // Display trimmed content or excerpt
                            if (!empty(get_the_excerpt())) {
                                echo wp_trim_words(get_the_excerpt(), 20); // Display excerpt with maximum of 20 words
                            } else {
                                echo wp_trim_words(get_the_content(), 20); // Display content with maximum of 20 words
                            }
                            ?>
                        </div>
                        <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit_event', 'event_id' => get_the_ID()), $_SERVER['REQUEST_URI'])); ?>" class="mepr-button btn-outline btn btn-outline">Edit Event</a>
                    </div>
                    <?php
                endwhile; ?>
                </div>
            <?php
            else :
                echo '<p>No events found.</p>';
            endif;
            wp_reset_postdata();  
            ?>  
            <br>        
            <a href="<?php echo esc_url(add_query_arg('action', 'add_event', $_SERVER['REQUEST_URI'])); ?>" class="mepr-button btn">Create New Event</a>
            <?php
        }
        else if ($_GET['action'] === 'add_event') { 
             
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {
                // Sanitize post title
                $post_title = sanitize_text_field($_POST['post_title'] ?? '');
                $post_content = sanitize_text_field($_POST['eventastic_description'] ?? '');
            
                // Prepare post data
                $post_data = array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_status'   => 'pending', // Set status to pending
                    'post_type'     => 'event' // Adjust post type as needed
  
                );
            
                // Insert the post
                $post_id = wp_insert_post($post_data);
            
                if (!is_wp_error($post_id)) {
                    // Update post meta fields
                    $fields = array(
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
                        'eventastic_twitter'
                    );
            
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


            
                    // Redirect to the same page with action=tab0
                    wp_redirect(add_query_arg('action', 'tab0', $_SERVER['REQUEST_URI']));
                    exit;
                }
            }
                ?>
        

<form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>

    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="eventastic_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="eventastic_gallery_square_featured_image" id="eventastic_gallery_square_featured_image">
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== -->
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Title:</label>
    <input type="text" name="post_title" id="post_title" value="<?php echo esc_attr(get_the_title($post_id)); ?>">

    <!-- Description -->
    <label for="eventastic_description">Description:</label>
    <textarea name="eventastic_description" id="eventastic_description"><?php echo esc_html($meta_data['eventastic_description'][0] ?? ''); ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="eventastic_business_name">Business Name:</label>
        <input type="text" name="eventastic_business_name" id="eventastic_business_name" value="<?php echo esc_attr($meta_data['eventastic_business_name'][0] ?? ''); ?>">

        <!-- Website Link -->
        <label for="eventastic_website_link">Website Link:</label>
        <input type="url" name="eventastic_website_link" id="eventastic_website_link" value="<?php echo esc_attr($meta_data['eventastic_website_link'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Phone Number -->
        <label for="eventastic_phone_number">Phone Number:</label><br>
        <input type="tel" name="eventastic_phone_number" id="eventastic_phone_number" value="<?php echo esc_attr($meta_data['eventastic_phone_number'][0] ?? ''); ?>"><br>

        <!-- Contact Email for Visitors -->
        <label for="eventastic_contact_email_for_visitors">Contact Email for Visitors:</label><br>
        <input type="email" name="eventastic_contact_email_for_visitors" id="eventastic_contact_email_for_visitors" value="<?php echo esc_attr($meta_data['eventastic_contact_email_for_visitors'][0] ?? ''); ?>"><br>
    </div>

    <!-- ==== HOURS ==== -->
    <h2 class="mepr-account-form__section-title">Hours</h2>

    <!-- Hours Description -->
    <label for="eventastic_hours_description">Hours Description:</label>
    <textarea name="eventastic_hours_description" id="eventastic_hours_description"><?php echo esc_html($meta_data['eventastic_hours_description'][0] ?? ''); ?></textarea>

    <!-- ==== ADDRESS INFORMATION ==== -->
    <h2 class="mepr-account-form__section-title">Address Information</h2>

    <div class="mepr-account-form__col-2">
        <!-- Address Line 1 -->
        <label for="eventastic_address_1">Address Line 1:</label><br>
        <input type="text" name="eventastic_address_1" id="eventastic_address_1" value="<?php echo esc_attr($meta_data['eventastic_address_1'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-2">
        <!-- Address Line 2 -->
        <label for="eventastic_address_2">Address Line 2:</label><br>
        <input type="text" name="eventastic_address_2" id="eventastic_address_2" value="<?php echo esc_attr($meta_data['eventastic_address_2'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="eventastic_city">City:</label><br>
        <input type="text" name="eventastic_city" id="eventastic_city" value="<?php echo esc_attr($meta_data['eventastic_city'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="eventastic_zip">Zip Code:</label><br>
        <input type="text" name="eventastic_zip" id="eventastic_zip" value="<?php echo esc_attr($meta_data['eventastic_zip'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="eventastic_state">State:</label><br>
        <input type="text" name="eventastic_state" id="eventastic_state" value="<?php echo esc_attr($meta_data['eventastic_state'][0] ?? ''); ?>">
    </div>

    <!-- ==== SOCIAL ==== -->
    <h2 class="mepr-account-form__section-title">SOCIAL</h2>

    <div class="mepr-account-form__col-2">
        <!-- Facebook -->
        <label for="eventastic_facebook">Facebook</label><br>
        <input type="url" name="eventastic_facebook" id="eventastic_facebook" value="<?php echo esc_attr($meta_data['eventastic_facebook'][0] ?? ''); ?>">

        <!-- Instagram -->
        <label for="eventastic_instagram">Instagram</label><br>
        <input type="url" name="eventastic_instagram" id="eventastic_instagram" value="<?php echo esc_attr($meta_data['eventastic_instagram'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Twitter -->
        <label for="eventastic_twitter">Twitter</label><br>
        <input type="url" name="eventastic_twitter" id="eventastic_twitter" value="<?php echo esc_attr($meta_data['eventastic_twitter'][0] ?? ''); ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="Add Event">
</form>
<?php 
        }

        else if ($_GET['action'] === 'edit_event' && isset($_GET['event_id'])) {
            // Get the event ID from the URL parameter
            $event_id = intval($_GET['event_id']);
        
            // Retrieve the event post using the event ID
            $event_post = get_post($event_id);
        
            // Check if the event post exists and is of type 'event'
            if ($event_post && $event_post->post_type === 'event') {
                // Display the edit event form
                ?>
                <form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>

    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="eventastic_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="eventastic_gallery_square_featured_image" id="eventastic_gallery_square_featured_image">
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== -->
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Title:</label>
    <input type="text" name="post_title" id="post_title" value="<?php echo esc_attr(get_the_title($post_id)); ?>">

    <!-- Description -->
    <label for="eventastic_description">Description:</label>
    <textarea name="eventastic_description" id="eventastic_description"><?php echo esc_html($meta_data['eventastic_description'][0] ?? ''); ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="eventastic_business_name">Business Name:</label>
        <input type="text" name="eventastic_business_name" id="eventastic_business_name" value="<?php echo esc_attr($meta_data['eventastic_business_name'][0] ?? ''); ?>">

        <!-- Website Link -->
        <label for="eventastic_website_link">Website Link:</label>
        <input type="url" name="eventastic_website_link" id="eventastic_website_link" value="<?php echo esc_attr($meta_data['eventastic_website_link'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Phone Number -->
        <label for="eventastic_phone_number">Phone Number:</label><br>
        <input type="tel" name="eventastic_phone_number" id="eventastic_phone_number" value="<?php echo esc_attr($meta_data['eventastic_phone_number'][0] ?? ''); ?>"><br>

        <!-- Contact Email for Visitors -->
        <label for="eventastic_contact_email_for_visitors">Contact Email for Visitors:</label><br>
        <input type="email" name="eventastic_contact_email_for_visitors" id="eventastic_contact_email_for_visitors" value="<?php echo esc_attr($meta_data['eventastic_contact_email_for_visitors'][0] ?? ''); ?>"><br>
    </div>

    <!-- ==== HOURS ==== -->
    <h2 class="mepr-account-form__section-title">Hours</h2>

    <!-- Hours Description -->
    <label for="eventastic_hours_description">Hours Description:</label>
    <textarea name="eventastic_hours_description" id="eventastic_hours_description"><?php echo esc_html($meta_data['eventastic_hours_description'][0] ?? ''); ?></textarea>

    <!-- ==== ADDRESS INFORMATION ==== -->
    <h2 class="mepr-account-form__section-title">Address Information</h2>

    <div class="mepr-account-form__col-2">
        <!-- Address Line 1 -->
        <label for="eventastic_address_1">Address Line 1:</label><br>
        <input type="text" name="eventastic_address_1" id="eventastic_address_1" value="<?php echo esc_attr($meta_data['eventastic_address_1'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-2">
        <!-- Address Line 2 -->
        <label for="eventastic_address_2">Address Line 2:</label><br>
        <input type="text" name="eventastic_address_2" id="eventastic_address_2" value="<?php echo esc_attr($meta_data['eventastic_address_2'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="eventastic_city">City:</label><br>
        <input type="text" name="eventastic_city" id="eventastic_city" value="<?php echo esc_attr($meta_data['eventastic_city'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="eventastic_zip">Zip Code:</label><br>
        <input type="text" name="eventastic_zip" id="eventastic_zip" value="<?php echo esc_attr($meta_data['eventastic_zip'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="eventastic_state">State:</label><br>
        <input type="text" name="eventastic_state" id="eventastic_state" value="<?php echo esc_attr($meta_data['eventastic_state'][0] ?? ''); ?>">
    </div>

    <!-- ==== SOCIAL ==== -->
    <h2 class="mepr-account-form__section-title">SOCIAL</h2>

    <div class="mepr-account-form__col-2">
        <!-- Facebook -->
        <label for="eventastic_facebook">Facebook</label><br>
        <input type="url" name="eventastic_facebook" id="eventastic_facebook" value="<?php echo esc_attr($meta_data['eventastic_facebook'][0] ?? ''); ?>">

        <!-- Instagram -->
        <label for="eventastic_instagram">Instagram</label><br>
        <input type="url" name="eventastic_instagram" id="eventastic_instagram" value="<?php echo esc_attr($meta_data['eventastic_instagram'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Twitter -->
        <label for="eventastic_twitter">Twitter</label><br>
        <input type="url" name="eventastic_twitter" id="eventastic_twitter" value="<?php echo esc_attr($meta_data['eventastic_twitter'][0] ?? ''); ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="Add Event">
</form>
                <?php
                
            } else {
                // Handle case where the event post does not exist or is not of type 'event'
                echo '<p>Error: Event not found.</p>';
            }
        }

        else if ($_GET['action'] === 'add_listing') { 
             
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post_nonce']) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_meta')) {
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
            
                if (!is_wp_error($post_id)) {
                    // Update post meta fields
                    $fields = array(
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
                    );
            
                    // Loop through each field and update post meta
                    foreach ($fields as $field) {
                        if (isset($_POST[$field])) {
                            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                        }
                    }
            
                    // Handle image upload and update partnerportal_gallery_square_featured_image
                    if (!empty($_FILES['partnerportal_gallery_square_featured_image']['name'])) {
                        $upload = wp_upload_bits($_FILES['partnerportal_gallery_square_featured_image']['name'], null, file_get_contents($_FILES['eventastic_gallery_square_featured_image']['tmp_name']));
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


            
                    // Redirect to the same page with action=tab0
                    wp_redirect(add_query_arg('action', 'tab1', $_SERVER['REQUEST_URI']));
                    exit;
                }
            }
                ?>
        

        <form class="mepr-account-form" method="post" enctype="multipart/form-data" action="">
    <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>
    
    <!-- Upload Image -->
    <div class="mepr-account-form__featured-image">
        <label for="partnerportal_gallery_square_featured_image">Featured Image:</label>
        <?php
        $post_thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
        if ($post_thumbnail_url) : ?>
            <img src="<?php echo esc_url($post_thumbnail_url); ?>" alt="Featured Image" style="max-width: 100px;">
        <?php endif; ?>
        <input type="file" name="partnerportal_gallery_square_featured_image" id="partnerportal_gallery_square_featured_image">       
        <hr class="mepr-account-form__separator">
    </div>
    <!-- ==== GENERAL INFO ==== --> 
    <h2 class="mepr-account-form__section-title">General Info</h2>

    <!-- Post Title -->
    <label for="post_title">Title:</label>
    <input type="text" name="post_title" id="post_title" value="<?php echo esc_attr(get_the_title($post_id)); ?>">

    <!-- Description -->
    <label for="partnerportal_description">Description:</label>
    <textarea name="partnerportal_description" id="partnerportal_description"><?php echo esc_html($meta_data['partnerportal_description'][0] ?? ''); ?></textarea>

    <div class="mepr-account-form__col-2">
        <!-- Business Name -->
        <label for="partnerportal_business_name">Business Name:</label>
        <input type="text" name="partnerportal_business_name" id="partnerportal_business_name" value="<?php echo esc_attr($meta_data['partnerportal_business_name'][0] ?? ''); ?>">

        <!-- Website Link -->
        <label for="partnerportal_website_link">Website Link:</label>
        <input type="url" name="partnerportal_website_link" id="partnerportal_website_link" value="<?php echo esc_attr($meta_data['partnerportal_website_link'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Phone Number -->
        <label for="partnerportal_phone_number">Phone Number:</label><br>
        <input type="tel" name="partnerportal_phone_number" id="partnerportal_phone_number" value="<?php echo esc_attr($meta_data['partnerportal_phone_number'][0] ?? ''); ?>"><br>

        <!-- Contact Email for Visitors -->
        <label for="partnerportal_contact_email_for_visitors">Contact Email for Visitors:</label><br>
        <input type="email" name="partnerportal_contact_email_for_visitors" id="partnerportal_contact_email_for_visitors" value="<?php echo esc_attr($meta_data['partnerportal_contact_email_for_visitors'][0] ?? ''); ?>"><br>
    </div>

    <!-- ==== HOURS ==== -->
    <h2 class="mepr-account-form__section-title">Hours</h2>
    
    <!-- Hours Description -->
    <label for="partnerportal_hours_description">Hours Description:</label>
    <textarea name="partnerportal_hours_description" id="partnerportal_hours_description"><?php echo esc_html($meta_data['partnerportal_hours_description'][0] ?? ''); ?></textarea>
    
    <!-- ==== ADDRESS INFORMATION ==== -->
    <h2 class="mepr-account-form__section-title">Address Information</h2>
    
    <div class="mepr-account-form__col-2">
        <!-- Address Line 1 -->
        <label for="partnerportal_address_1">Address Line 1:</label><br>
        <input type="text" name="partnerportal_address_1" id="partnerportal_address_1" value="<?php echo esc_attr($meta_data['partnerportal_address_1'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-2">
         <!-- Address Line 2 -->
         <label for="partnerportal_address_2">Address Line 2:</label><br>
        <input type="text" name="partnerportal_address_2" id="partnerportal_address_2" value="<?php echo esc_attr($meta_data['partnerportal_address_2'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-3">
        <!-- City -->
        <label for="partnerportal_city">City:</label><br>
        <input type="text" name="partnerportal_city" id="partnerportal_city" value="<?php echo esc_attr($meta_data['partnerportal_city'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- Zip Code -->
        <label for="partnerportal_zip">Zip Code:</label><br>
        <input type="text" name="partnerportal_zip" id="partnerportal_zip" value="<?php echo esc_attr($meta_data['partnerportal_zip'][0] ?? ''); ?>">
    </div>
    <div class="mepr-account-form__col-3">
        <!-- State -->
        <label for="partnerportal_state">State:</label><br>
        <input type="text" name="partnerportal_state" id="partnerportal_state" value="<?php echo esc_attr($meta_data['partnerportal_state'][0] ?? ''); ?>">
    </div> 

    <!-- ==== SOCIAL ==== -->
    <h2 class="mepr-account-form__section-title">SOCIAL</h2>
    
    <div class="mepr-account-form__col-2">
        <!-- Facebook -->
        <label for="partnerportal_facebook">Facebook</label><br>
        <input type="url" name="partnerportal_facebook" id="partnerportal_facebook" value="<?php echo esc_attr($meta_data['partnerportal_facebook'][0] ?? ''); ?>">

        <!-- Instagram -->
        <label for="partnerportal_instagram">Instagram</label><br>
        <input type="url" name="partnerportal_instagram" id="partnerportal_instagram" value="<?php echo esc_attr($meta_data['partnerportal_instagram'][0] ?? ''); ?>">
    </div>

    <div class="mepr-account-form__col-2">
        <!-- Twitter -->
        <label for="partnerportal_twitter">Twitter</label><br>
        <input type="url" name="partnerportal_twitter" id="partnerportal_twitter" value="<?php echo esc_attr($meta_data['partnerportal_twitter'][0] ?? ''); ?>">
    </div>

    <!-- ==== SUBMIT FORM ==== -->
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="Add Listing">
    </form>
<?php 
        }
    }

    public static function mepr_enqueue_scripts($is_product_page, $is_group_page, $is_account_page) {

    
            // Memberpress Account script
            wp_enqueue_script(
                C::THEME_PREFIX . "-memberpress-account-js", // handle
                get_stylesheet_directory_uri()."/assets/build/memberpress-account.js", // src
                $assets_file["dependencies"], // dependencies
                $assets_file["version"], // version
                true // in footer?
            );
    }

}