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
            $assets_file["version"] // version
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
    update_post_meta($post_id, 'partnerportal_description', sanitize_text_field($_POST['partnerportal_description'] ?? ''));
    update_post_meta($post_id, 'partnerportal_business_name', sanitize_text_field($_POST['partnerportal_business_name'] ?? ''));
    update_post_meta($post_id, 'partnerportal_website_link', sanitize_text_field($_POST['partnerportal_website_link'] ?? ''));
    update_post_meta($post_id, 'partnerportal_phone_number', sanitize_text_field($_POST['partnerportal_phone_number'] ?? ''));
    update_post_meta($post_id, 'partnerportal_contact_email_for_visitors', sanitize_text_field($_POST['partnerportal_contact_email_for_visitors'] ?? ''));

    // Handle image upload and update partnerportal_gallery_square_featured_image
    if (!empty($_FILES['partnerportal_gallery_square_featured_image']['name'])) {
        $upload = wp_upload_bits($_FILES['partnerportal_gallery_square_featured_image']['name'], null, file_get_contents($_FILES['partnerportal_gallery_square_featured_image']['tmp_name']));
        if (!$upload['error']) {
            update_post_meta($post_id, 'partnerportal_gallery_square_featured_image', $upload['url']);
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
    <label for="partnerportal_gallery_square_featured_image">Featured Image:</label><br>
    <input type="file" name="partnerportal_gallery_square_featured_image" id="partnerportal_gallery_square_featured_image"><br>
    <?php if ($meta_data['partnerportal_gallery_square_featured_image'][0] ?? '') : ?>
        <img src="<?php echo esc_url($meta_data['partnerportal_gallery_square_featured_image'][0]); ?>" alt="Featured Image" style="max-width: 100px;">
    <?php endif; ?>

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
    
    <br style="clear:both;">
    <input class="mepr-button btn-outline btn btn-outline" type="submit" value="Update">
</form>

        <?php
            endwhile;
            endif;
            wp_reset_postdata();

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