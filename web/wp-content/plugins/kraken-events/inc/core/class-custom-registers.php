<?php
namespace MaddenMedia\KrakenEvents;

class CustomRegisters {

    public static $event_slug = null;

    public static function init() {
        self::$event_slug = get_option('kraken_events_event_slug', 'event');

        add_action('init', [__CLASS__, 'register_post_types']);
        add_action('init', [__CLASS__, 'register_taxonomies']);
        add_action('admin_menu', [__CLASS__, 'remove_taxonomy_metaboxes']);
        add_action('after_setup_theme', [__CLASS__, 'hide_partner_admin_bar']);

        //Add event information to the admin columns
        add_filter('manage_edit-'.self::$event_slug.'_columns', [__CLASS__, 'add_event_columns']);
        add_action('manage_'.self::$event_slug.'_posts_custom_column', [__CLASS__, 'event_info_column'], 10, 2);
        add_filter('manage_edit-'.self::$event_slug.'_sortable_columns', [__CLASS__, 'event_sortable_columns']);
        add_action('pre_get_posts', [__CLASS__, 'event_info_orderby']);
    }

    public static function register_post_types() {
        // Retrieve the slug from the settings or use 'event' as the default
        $base_slug = self::$event_slug;
        $singular_name = get_option('kraken_events_event_singular_name', 'Event');
        $plural_name = get_option('kraken_events_event_plural_name', 'Events');

        $enable_default_editor = get_option('kraken_events_event_enable_editor', true);
        $enable_featured_image = get_option('kraken_events_event_enable_featured_image', false);
        $support = ['author', 'title', 'excerpt'];

        if ($enable_default_editor) {
            $support = array_merge($support, ['editor']);
        }

        if ($enable_featured_image) {
            $support = array_merge($support, ['thumbnail']);
        }

        // Set the initial post type name
        $post_type_name = $base_slug;

        // Check if the post type already exists, append '_custom' if necessary
        if (post_type_exists($post_type_name)) {
            $post_type_name = $base_slug . '_custom';
        }

        register_post_type($post_type_name, [
            'labels' => [
                'name' => __($plural_name),
                'singular_name' => __($singular_name),
                'add_new' => __('Add New ' . $singular_name),
                'add_new_item' => __('Add New ' . $singular_name),
                'edit_item' => __('Edit ' . $singular_name),
                'new_item' => __('New ' . $singular_name),
                'view_item' => __('View ' . $singular_name),
                'search_items' => __('Search ' . $plural_name),
                'not_found' => __('No ' . strtolower($plural_name) . ' found'),
                'not_found_in_trash' => __('No ' . strtolower($plural_name) . ' found in Trash'),
            ],
            'menu_icon' => 'dashicons-calendar-alt',
            'public' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => $base_slug,
                'with_front' => false
            ],
            'supports' => $support,
            'show_in_rest' => true,
        ]);
    }

    public static function register_taxonomies() {
        $post_type_slug = self::$event_slug;
        $taxonomies = get_option('kraken_events_event_taxonomies', []);

        if (!is_array($taxonomies)) {
            $taxonomies = json_decode($taxonomies, true) ?: [];
        }

        foreach ($taxonomies as $taxonomy) {
            $slug = sanitize_title($taxonomy['slug']);
            $labels = [
                'name' => $taxonomy['plural'],
                'singular_name' => $taxonomy['singular']
            ];

            if (!taxonomy_exists($slug)) {
                register_taxonomy($slug, $post_type_slug, [
                    'labels'                => $labels,
                    'hierarchical'          => true,
                    'show_ui'               => true,
                    'show_admin_column'     => true,
                    'show_in_rest'          => true,
                    'rewrite' => ['slug' => $slug],
                ]);
            } else {
                //if the taxonomy already exists; enable it on this post type
                register_taxonomy_for_object_type($slug, $post_type_slug);
            }
        }
    }

    public static function remove_taxonomy_metaboxes() {
        $post_type_slug = self::$event_slug;
        $taxonomies = get_option('kraken_events_event_taxonomies', []);

        if (!is_array($taxonomies)) {
            $taxonomies = json_decode($taxonomies, true) ?: [];
        }

        foreach ($taxonomies as $taxonomy) {
            $slug = sanitize_title($taxonomy['slug']);
            remove_meta_box($slug.'div', $post_type_slug, 'side');
        }
    }

    public static function hide_partner_admin_bar() {
        // Hide admin bar only for the frontend and for users with 'partner' role
        if (is_user_logged_in() && current_user_can('partner') && !is_admin() && !is_super_admin()) {
            show_admin_bar(false);
        }
    }

    public static function add_event_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new_columns[$key] = $value; // Keep the title first
                $new_columns['event_start_date'] = 'Start Date';
                $new_columns['event_end_date'] = 'End Date';
                $new_columns['event_recurrence'] = 'Recurrence';
            } elseif ($key === 'date') {
                continue; // Remove the default date column (it will be added last)
            } else {
                $new_columns[$key] = $value; // Add the remaining columns
            }
        }
        $new_columns['date'] = $columns['date']; // Re-add the date column at the end
        return $new_columns;
    }

    public static function event_info_column($column, $post_id) {
        if ($column == 'event_start_date') {
            $date = get_field('event_start_date', $post_id);
            if ($date) {
                echo date('F j, Y', strtotime($date));
            } else {
                echo '—';
            }
        }
        if ($column == 'event_end_date') {
            $date = get_field('event_end_date', $post_id);
            if ($date) {
                echo date('F j, Y', strtotime($date));
            } else {
                echo '—';
            }
        }
        if ($column == 'event_recurrence') {
            $recurrence = get_field('events_recurrence_options', $post_id);
            if ($recurrence) {
                echo $recurrence;
            } else {
                echo '—';
            }
        }
    }

    public static function event_sortable_columns($columns) {
        $columns['event_start_date'] = 'event_start_date';
        $columns['event_end_date'] = 'event_end_date';
        return $columns;
    }

    public static function event_info_orderby($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $orderby = $query->get('orderby');

        if ('event_start_date' == $orderby) {
            $query->set('meta_key', 'event_start_date');
            $query->set('orderby', 'meta_value');
            $query->set('meta_type', 'DATE');
        }

        if ('event_end_date' == $orderby) {
            $query->set('meta_key', 'event_end_date');
            $query->set('orderby', 'meta_value');
            $query->set('meta_type', 'DATE');
        }
    }
}
