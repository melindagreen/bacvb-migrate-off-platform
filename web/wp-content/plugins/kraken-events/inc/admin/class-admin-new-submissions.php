<?php
namespace MaddenMedia\KrakenEvents;

class AdminNewSubmissions {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_submission_page']);
        add_action('transition_post_status', [__CLASS__, 'notify_partners_on_publish'], 10, 3);
    }

    public static function register_submission_page() {
        add_submenu_page('kraken-events', 'New Submissions', 'New Submissions', 'manage_options', 'kraken-events-submissions', [__CLASS__, 'submissions_page_callback'], 2);
        //or should this just link to the edit admin page:
        ///wp-admin/edit.php?post_status=pending&post_type=listings
    }

    /*
    Check for posts going from pending -> published that match our plugin post types and send partner notifications if necessary.
    */
    public static function notify_partners_on_publish($new_status, $old_status, $post) {
        if ($new_status === 'publish' && $old_status === 'pending' && in_array($post->post_type, ProcessForms::$postTypes)) {
            $author_id = $post->post_author;
            //confirm this was a partner submitted post
            if (user_can($author_id, 'partner')) {
                //send the author/partner a notification that their submission was published
                $author_email = get_the_author_meta('user_email', $author_id);
                Notifications::notify_partner("approve-new", $author_email, get_the_title($post->ID));
            }
        }
    }
    
    public static function submissions_page_callback() {
        echo '<div class="wrap"><h2>New Submissions</h2>';

  
        $event_slug = get_option('events_post_slug', 'event');
        echo '<h3>New Pending Events</h3>';
        self::display_new_submissions($event_slug);

        echo '</div>';
    }

    private static function display_new_submissions($post_type) {
        $args = [
            'post_type'         => $post_type,
            'post_status'       => 'pending',
            'posts_per_page'    => -1
        ];

        $pending_posts = new \WP_Query($args);

        if ($pending_posts->have_posts()) {

            while ($pending_posts->have_posts()) {
                $pending_posts->the_post();
                $post_id = get_the_ID();
                $post_title = get_the_title();   

                $author_id      = get_post_field('post_author', $post_id);
                $author_name    = get_the_author_meta('display_name', $author_id);

                echo '<div class="single-post">';
                echo '<h4>' . esc_html($post_title) . '</h4>';
                echo '<p>Submitted by '.$author_name.'</p>';           
                echo '<a href="'.get_edit_post_link($post_id).'" class="button button-primary">Review & Publish</a>';
                echo '</div>';
            }

        } else {
            echo '<p>No pending submissions.</p>';
        }

        wp_reset_postdata();
    }
}