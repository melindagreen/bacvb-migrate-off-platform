<?php 

namespace MaddenNino\Library\Memberpress;

use WP_Query;

class NavTabs {
	function __construct () {
		add_filter( 'mepr_account_nav_content', array(get_called_class(), 'nav_tabs'), 10, 1);
	}

    public static function nav_tabs($action) {
        

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
            
            // Update other post meta fields
            update_post_meta($post_id, 'partnerportal_description', sanitize_text_field($_POST['partnerportal_description'] ?? ''));
            update_post_meta($post_id, 'partnerportal_business_name', sanitize_text_field($_POST['partnerportal_business_name'] ?? ''));
            update_post_meta($post_id, 'partnerportal_website_link', sanitize_text_field($_POST['partnerportal_website_link'] ?? ''));
            update_post_meta($post_id, 'partnerportal_phone_number', sanitize_text_field($_POST['partnerportal_phone_number'] ?? ''));
            update_post_meta($post_id, 'partnerportal_contact_email_for_visitors', sanitize_text_field($_POST['partnerportal_contact_email_for_visitors'] ?? ''));

            // Redirect to the same page with update=true
            wp_redirect(add_query_arg('update', 'true'));
            exit;
        }


            if (isset($_GET['update']) && $_GET['update'] === 'true') {
                echo '<div style="background-color: #c96a39db; border: 0.5px solid #c96a39; padding: 0.2rem; width:33%;margin-bottom:1rem" class="notice notice-success is-dismissible"><p style="color:white;margin:0;margin-left:1rem;">Listing has been updated successfully.</p></div>';
            }
        
        ?>

        <form method="post" action="">
            <?php wp_nonce_field('update_post_meta', 'update_post_nonce'); ?>
            
            <!-- Post Title -->
            <label for="post_title">Title:</label><br>
            <input type="text" name="post_title" id="post_title" style="width: 100%;margin-bottom:1rem" value="<?php echo esc_attr(get_the_title($post_id)); ?>"><br>

            <!-- Description -->
            <label for="partnerportal_description">Description:</label><br>
            <textarea name="partnerportal_description" id="partnerportal_description" style="width: 100%;margin-bottom:1rem"><?php echo esc_html($meta_data['partnerportal_description'][0] ?? ''); ?></textarea><br>

            <div style="display: inline-block; width: 45%;margin-bottom:1rem">
                <!-- Business Name -->
                <label for="partnerportal_business_name">Business Name:</label><br>
                <input type="text" name="partnerportal_business_name" id="partnerportal_business_name" style="width: 100%;margin-bottom:1rem" value="<?php echo esc_attr($meta_data['partnerportal_business_name'][0] ?? ''); ?>"><br>

                <!-- Website Link -->
                <label for="partnerportal_website_link">Website Link:</label><br>
                <input type="url" name="partnerportal_website_link" id="partnerportal_website_link" style="width: 100%;margin-bottom:1rem" value="<?php echo esc_attr($meta_data['partnerportal_website_link'][0] ?? ''); ?>"><br>
            </div>

            <div style="display: inline-block; width: 45%; vertical-align: top;">
                <!-- Phone Number -->
                <label for="partnerportal_phone_number">Phone Number:</label><br>
                <input type="tel" name="partnerportal_phone_number" id="partnerportal_phone_number" style="width: 100%;margin-bottom:1rem" value="<?php echo esc_attr($meta_data['partnerportal_phone_number'][0] ?? ''); ?>"><br>

                <!-- Contact Email for Visitors -->
                <label for="partnerportal_contact_email_for_visitors">Contact Email for Visitors:</label><br>
                <input type="email" name="partnerportal_contact_email_for_visitors" id="partnerportal_contact_email_for_visitors" style="width: 100%;margin-bottom:1rem" value="<?php echo esc_attr($meta_data['partnerportal_contact_email_for_visitors'][0] ?? ''); ?>"><br>
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

}