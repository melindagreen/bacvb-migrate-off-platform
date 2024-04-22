<?php 

namespace MaddenNino\Library\Memberpress;

use MaddenNino\Library\Constants as C;
use WP_Query;

class MemberPressPortal {

    public static $account_actions;

	function __construct () {

        self::$account_actions = ['listings','events','add_listing','edit_event','add_event', 'edit_listing'];

		add_filter( 'mepr_account_nav_content', array(get_called_class(), 'nav_tabs'), 10, 1);
        add_action('mepr_enqueue_scripts', array(get_called_class(),'mepr_enqueue_scripts'), 10, 3);
        add_action( 'transition_post_status', array(get_called_class(),'post_status_notification'), 10, 3 );
        add_action('mepr_account_nav', array(get_called_class(),'mepr_add_some_tabs'));
        add_action( 'transition_post_status', array(get_called_class(),'handle_post_status'), 10, 3 );
	}

    public static function mepr_add_some_tabs($action) {
        $support_active = (isset($_GET['action']) && $_GET['action'] == 'premium-support')?'mepr-active-nav-tab':'';
        ?>
          <span class="mepr-nav-item listing <?php echo $support_active; ?>">
            <a href="/account/?action=listings">Listings</a>
          </span>
          <span class="mepr-nav-item events <?php echo $support_active; ?>">
            <a href="/account/?action=events">Events</a>
          </span>
          <?php
    }

    public static function nav_tabs($action) {
        
         // Memberpress Account styles
         wp_enqueue_style(
            C::THEME_PREFIX . "-memberpress-account-css", // handle
            get_stylesheet_directory_uri()."/assets/build/memberpress-account.css", // src
            [], // dependencies
            null
        );

        // Retrieves template of defined account action
        if(in_array($_GET['action'], self::$account_actions)) {

            include get_stylesheet_directory() . '/library/memberpress/templates/account-'.$_GET['action'].'.php';
        }
    }

    public static function mepr_enqueue_scripts($is_product_page, $is_group_page, $is_account_page) {

        $assets_file = include(get_template_directory()."/assets/build/admin.asset.php" );
        // Memberpress Account script
        wp_enqueue_script(
            C::THEME_PREFIX . "-memberpress-account-js", // handle
            get_stylesheet_directory_uri()."/assets/build/memberpress-account.js", // src
            $assets_file["dependencies"], // dependencies
            $assets_file["version"], // version
            true // in footer?
        );
    }

    public static function post_status_notification ( $new_status, $old_status, $post ) {

        // Check if the post is transitioning from pending to publish
        if ( ($old_status === 'pending' && $new_status === 'publish') && ($post->post_type === 'event' || $post->post_type === 'listing') ) {
            // Get post author's email
            $author_id = $post->post_author;
            $author_email = get_the_author_meta( 'user_email', $author_id );
    
            // Set up email parameters
            $to = $author_email;
            $subject = 'Your listing has been published';
            $message = '<!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
              <meta charset="UTF-8">
              <title>Approved</title>
            </head>
            <body>
              <p>Approved</p>
            </body>
            </html>';
            $headers = array('Content-Type: text/html; charset=UTF-8');
    
            // Send email
            wp_mail($to, $subject, $message, $headers);
        }

        else if ( ($old_status === 'pending' && $new_status === 'trash') && ($post->post_type === 'event' || $post->post_type === 'listing') ) {

            // Get post author's email
            $author_id = $post->post_author;
            $author_email = get_the_author_meta( 'user_email', $author_id );
    
            // Set up email parameters
            $to = $author_email;
            $subject = 'Your listing has been rejected';
            $message = '<!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
              <meta charset="UTF-8">
              <title>Rejected</title>
            </head>
            <body>
              <p>Rejected</p>
            </body>
            </html>';
            $headers = array('Content-Type: text/html; charset=UTF-8');
    
            // Send email
            wp_mail($to, $subject, $message, $headers);
        }
    }

    public static function handle_post_status($new_status, $old_status, $post) {

       if($post->post_type === 'event' || $post->post_type === 'listing') {

            if ($old_status === 'pending' && $new_status === 'publish') {

                $original_post_id = get_post_meta($post->ID, 'original_post_id', true);

                if ($original_post_id) {
                    // Get the original post data
                    $original_post = get_post($original_post_id);

                    // Copy all post data to the post with original_post_id
                    $post_data = array(
                        'ID' => $original_post_id,
                        'post_title' => $post->post_title,
                        'post_content' => $post->post_content,
                        'post_excerpt' => $post->post_excerpt,
                        'post_status' => 'publish'
                    );

                    // Update the post with original_post_id with the data from the original post
                    wp_update_post($post_data);

                    // Transfer all metadata from original post to the new post
                    $post_meta = get_post_meta($post->ID);
                    foreach ($post_meta as $meta_key => $meta_values) {
                        foreach ($meta_values as $meta_value) {
                            update_post_meta($original_post_id, $meta_key, $meta_value);
                        }
                    }

                    // Remove cloned_post_id meta data
                    delete_post_meta($original_post_id, 'cloned_post_id');

                    // Remove original_post_id post meta from the original post
                    delete_post_meta($post->ID, 'original_post_id');

                    // Delete the original post
                    wp_delete_post($post->ID, true); // Set the second parameter to true to bypass trash
                }

            }

            else if ($old_status === 'pending' && $new_status === 'draft') {

                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_status' => 'pending' 
                ));
            }

            //Prevents post status from changing to draft
            else if($old_status === 'pending' && $new_status === 'trash') {
                
            // If post is moved to trash, change its title
            $new_title = '[REJECTED] ' . $post->post_title;
            wp_update_post(array(
                'ID' => $post->ID,
                'post_title'   => $new_title
            ));
            
            // Get original_post_id
            $original_post_id = get_post_meta($post->ID, 'original_post_id', true);
            if ($original_post_id) {
           
                // Remove cloned_post_id meta data
                delete_post_meta($original_post_id, 'cloned_post_id');
                
                // Remove original_post_id post meta from the first post
                delete_post_meta($post->ID, 'original_post_id');
            }

            }
        }
    }
}