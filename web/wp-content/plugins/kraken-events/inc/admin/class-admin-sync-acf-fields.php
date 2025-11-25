<?php
namespace MaddenMedia\KrakenEvents;

class AdminSyncACF {

    public static $event_slug = null;

    public static function init() {
        self::$event_slug = get_option('kraken_events_event_slug', 'event');

        // Disable ACF fields in the admin editor
        add_filter('acf/prepare_field/name=post_title', [__CLASS__, 'disable_acf_fields_in_admin']);
        add_filter('acf/prepare_field/name=post_content', [__CLASS__, 'disable_acf_fields_in_admin']);
        add_filter('acf/prepare_field/name=post_excerpt', [__CLASS__, 'disable_acf_fields_in_admin']);
        add_filter('acf/prepare_field/name=post_thumbnail', [__CLASS__, 'disable_acf_fields_in_admin']);
        // add_filter('acf/prepare_field/name=categories', [__CLASS__, 'disable_acf_fields_in_admin']);
        // add_filter('acf/prepare_field/name=venue', [__CLASS__, 'disable_acf_fields_in_admin']);
        // add_filter('acf/prepare_field/name=organizer', [__CLASS__, 'disable_acf_fields_in_admin']); 
        add_filter('acf/prepare_field/name=event_repeat_dates', [__CLASS__, 'disable_acf_fields_in_admin']); 

        // Save and sync the ACF fields with the core title, content, excerpt, and thumbnail on post save
        add_action('save_post', [__CLASS__, 'save_and_sync_acf_fields'], 10, 3);

        //disables all blocks except classic editor to work with ACF wyswyg field
        if (get_option('kraken_events_disable_all_blocks', false)) {
            add_filter('allowed_block_types_all', [__CLASS__, 'restrict_gutenberg_to_classic_block'], 10, 2);
        }
    }

    /**
     * Disable specific ACF fields in the Gutenberg editor only for the specified post type.
     */
    public static function disable_acf_fields_in_admin($field) {   
        // Force hide the 'event_repeat_dates' field at all times
        if (is_array($field) && $field['name'] === 'event_repeat_dates') {
            $field['disabled'] = true;
            $field['wrapper']['style'] = 'display:none;';
            return $field;
        }

        // Only apply this inside the WordPress admin panel
        if (!is_admin()) {
            return $field;
        }

        if (function_exists('get_current_screen')) {
            $current_screen = get_current_screen();

            // Return false to prevent the field from being output & prevent any errors with required fields
            if ($current_screen->post_type === self::$event_slug) {         
                return false;
            }
        }

        return $field;
    }

    public static function save_and_sync_acf_fields($post_id, $post, $update) {
        // Check if this is an autosave or a revision
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        if ($post->post_type !== self::$event_slug) {
            return;
        }

        $title      = get_the_title($post_id);
        $content    = get_the_content($post_id);
        $excerpt    = get_the_excerpt($post_id);
        $thumbnail  = get_post_thumbnail_id($post_id);
        
        update_field('post_title', $title, $post_id);
        update_field('post_content', $content, $post_id);
        update_field('post_excerpt', $excerpt, $post_id);
        update_field('post_thumbnail', $thumbnail, $post_id);
    }
    
    public static function restrict_gutenberg_to_classic_block($allowed_blocks, $editor_context) {
        // Check if we are in the admin and editing the specific post type
        if (!empty($editor_context->post) && $editor_context->post->post_type === self::$event_slug) {
            return ['core/freeform']; // Only allow the Classic Editor block
        }
        return $allowed_blocks;
    }
}
