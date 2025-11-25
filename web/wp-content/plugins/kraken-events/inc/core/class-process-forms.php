<?php
namespace MaddenMedia\KrakenEvents;

class ProcessForms {

    public static $postTypes = [];

    public static function init() {
        self::$postTypes[] = get_option('kraken_events_event_slug', 'event');
        
        add_action('acf/save_post', [__CLASS__, 'process_acf_form_submit'], 5);
    }
    
    // Store edits in ACF 'pending_edits' field for admin approval
    public static function process_acf_form_submit($post_id) {

        //do not process pending edits if using the admin editor
        if (is_admin()) return;

        //Helpers::log_error(json_encode($_POST));

        if (isset($_POST['acf'])) {
            if (isset($_POST['_acf_post_id']) && $_POST['_acf_post_id'] === "new_post") {
                //for creating a new post
                self::create_new_acf_post($post_id);
            }    
        } 
    }

    private static function create_new_acf_post($post_id) {
        //only continue for the correct post type event
        if (!in_array(get_post_type($post_id), self::$postTypes)) return;

        $post_updates = array(
            'ID'            => $post_id,
            'post_status'   => 'pending'
        );

        foreach ($_POST['acf'] as $field_key => $value) {
            $field = get_field_object($field_key, $post_id);
            if ($field) {
                $field_name = $field['name'] ?? '';
                if ($field_name === 'post_title') {
                    $post_updates['post_title'] = $value;
                } elseif ($field_name === 'post_content') {
                    $post_updates['post_content'] = $value;
                } elseif ($field_name === 'post_excerpt') {
                    $post_updates['post_excerpt'] = $value;
                } elseif ($field_name === 'post_thumbnail') {
                    if ($value !== "") {
                        set_post_thumbnail($post_id, $value);
                    }
                }
            }
        }

        //Helpers::log_error(json_encode($post_updates));
        
        //remove action while updating post
        remove_action('acf/save_post', [__CLASS__, 'process_acf_form_submit'], 5);

        wp_update_post($post_updates);

        //send admin notification about the new post
        $author_id      = get_post_field('post_author', $post_id);
        $author_email   = get_the_author_meta('user_email', $author_id);
        Notifications::notify_admin("new-submission", $author_email, $post_updates['post_title']);

        //re-add after update
        add_action('acf/save_post', [__CLASS__, 'process_acf_form_submit'], 5);

        return $post_id;
    }
}