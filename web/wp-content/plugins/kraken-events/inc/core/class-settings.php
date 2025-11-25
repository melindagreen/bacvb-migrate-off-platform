<?php
namespace MaddenMedia\KrakenEvents;

class AdminSettings {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_settings_page']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_init', [__CLASS__, 'handle_database_actions']);
        add_action('init', [__CLASS__, 'add_event_rewrite_rules']);
    }

    public static function register_settings_page() {
        add_submenu_page('kraken-events', 'Settings', 'Settings', 'manage_options', 'kraken-events-settings', [__CLASS__, 'settings_page_callback'], 100);
    }

    public static function register_settings() {

        register_setting('kraken_events_settings', 'kraken_events_event_slug', [
            'default' => 'event'
        ]);
        register_setting('kraken_events_settings', 'kraken_events_event_singular_name', [
            'default' => 'Event'
        ]);
        register_setting('kraken_events_settings', 'kraken_events_event_plural_name', [
            'default' => 'Events'
        ]);
        register_setting('kraken_events_settings', 'kraken_events_event_taxonomies', [
            'default' => json_encode([]),
            'sanitize_callback' => [__CLASS__, 'sanitize_taxonomies']
        ]);

        add_settings_section('kraken_events_main_section', 'Main Settings', null, 'kraken-events');

        //admin notification email
        register_setting('kraken_events_settings', 'kraken_events_notification_email');
        add_settings_field('kraken_events_notification_email', 'Notification Email', [__CLASS__, 'notification_email_callback'], 'kraken-events', 'kraken_events_main_section');
        
        //cpt & taxonomy setup
        add_settings_field('kraken_events_event_slug', 'Event Slug', [__CLASS__, 'event_slug_callback'], 'kraken-events', 'kraken_events_main_section');
        add_settings_field('kraken_events_event_singular_name', 'Event Singular Name', [__CLASS__, 'event_singular_name_callback'], 'kraken-events', 'kraken_events_main_section');
        add_settings_field('kraken_events_event_plural_name', 'Event Plural Name', [__CLASS__, 'event_plural_name_callback'], 'kraken-events', 'kraken_events_main_section');
        add_settings_field('kraken_events_event_taxonomies', 'Event Taxonomies', [__CLASS__, 'event_taxonomies_callback'], 'kraken-events', 'kraken_events_main_section');

        //page selects
        add_settings_section('kraken_events_pages', 'Pages', null, 'kraken-events');

        register_setting('kraken_events_settings', 'kraken_events_enable_pages', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));
        add_settings_field(
            'kraken_events_enable_pages', 
            'Enable Add Events Page', 
            [__CLASS__, 'enable_pages_callback'], 
            'kraken-events', 
            'kraken_events_pages'
        );

        if (get_option('kraken_events_enable_pages')) {
            $pages = EventPages::$pages;

            foreach($pages as $page) {
                register_setting('kraken_events_settings', 'kraken_events_page_'.$page['settings_value']);
                add_settings_field(
                    'kraken_events_page_'.$page['settings_value'], 
                    $page['settings_label'], 
                    [__CLASS__, 'page_selects_callback'], 
                    'kraken-events', 
                    'kraken_events_pages',
                    array('field_slug' => $page['settings_value'])
                );
            }
        }

        //acf field group selects
        add_settings_section('kraken_events_acf_field_groups', 'ACF Field Groups', null, 'kraken-events');

        register_setting('kraken_events_settings', 'kraken_events_event_field_group');
        add_settings_field(
            'kraken_events_event_field_group', 
            'Event Fields', 
            [__CLASS__, 'acf_field_group_callback'], 
            'kraken-events', 
            'kraken_events_acf_field_groups',
            array('type' => 'event')
        );

        //customization options
        add_settings_section('kraken_events_customization', 'Customization', null, 'kraken-events');
        register_setting('kraken_events_settings', 'kraken_events_load_stylesheet', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => true
        ));

        register_setting('kraken_events_settings', 'kraken_events_event_enable_editor', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        register_setting('kraken_events_settings', 'kraken_events_disable_all_blocks', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        register_setting('kraken_events_settings', 'kraken_events_event_enable_featured_image', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        register_setting('kraken_events_settings', 'kraken_events_enable_kraken_calendar', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        register_setting('kraken_events_settings', 'kraken_events_site_has_eventastic_templates', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        register_setting('kraken_events_settings', 'kraken_events_enable_legacy_support', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));
        

        add_settings_field(
            'kraken_events_load_stylesheet', 
            'Load Default Stylesheet', 
            [__CLASS__, 'load_stylesheet_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        add_settings_field(
            'kraken_events_event_enable_editor', 
            'Load Event Gutenberg Editor', 
            [__CLASS__, 'load_event_editor_check_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        add_settings_field(
            'kraken_events_disable_all_blocks', 
            'Disable All Blocks Except Classic Editor', 
            [__CLASS__, 'disable_all_blocks_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        add_settings_field(
            'kraken_events_event_enable_featured_image', 
            'Load Event Featured Image', 
            [__CLASS__, 'load_event_featured_image_check_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        add_settings_field(
            'kraken_events_enable_legacy_support', 
            'Enable Eventastic Legacy Support', 
            [__CLASS__, 'enable_legacy_support_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        add_settings_field(
            'kraken_events_enable_kraken_calendar', 
            'Enable Kraken Calendar Block', 
            [__CLASS__, 'enable_kraken_calendar_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        add_settings_field(
            'kraken_events_site_has_eventastic_templates', 
            'Enable backwards compatibile Eventastic templates', 
            [__CLASS__, 'site_has_eventastic_templates_callback'], 
            'kraken-events', 
            'kraken_events_customization'
        );

        //Database upgrade tool
        add_settings_section(
            'kraken_events_tools_section',
            'Database Tools',
            [__CLASS__, 'tools_section_callback'],
            'kraken-events-tools'
        );

        add_settings_field(
            'kraken_events_database_tool',
            'Sync Event Occurrences',
            [__CLASS__, 'database_tool_button_html'],
            'kraken-events-tools',
            'kraken_events_tools_section'
        );
    }

    public static function sanitize_taxonomies($input) {
        if (is_array($input)) {
        // Ensure the input is a properly formatted array
            $sanitized = array_map(function($taxonomy) {
                return [
                    'singular' => sanitize_text_field($taxonomy['singular']),
                    'plural' => sanitize_text_field($taxonomy['plural']),
                    'slug' => sanitize_title($taxonomy['slug'])
                ];
            }, $input);
            return json_encode($sanitized);
        }
    }

    public static function notification_email_callback() {
        $notification_email = get_option('kraken_events_notification_email');
        if (!$notification_email) {
            $notification_email = get_option('admin_email');
        }
        echo '<input type="text" id="kraken_events_notification_email" name="kraken_events_notification_email" value="' . esc_attr($notification_email) . '" />';
    }

    public static function event_slug_callback() {
        $event_slug = get_option('kraken_events_event_slug', 'event');
        echo '<input type="text" id="kraken_events_event_slug" name="kraken_events_event_slug" value="' . esc_attr($event_slug) . '" />';
    }

    public static function event_singular_name_callback() {
        $singular_name = get_option('kraken_events_event_singular_name', 'Event');
        echo '<input type="text" id="kraken_events_event_singular_name" name="kraken_events_event_singular_name" value="' . esc_attr($singular_name) . '" />';
    }

    public static function event_plural_name_callback() {
        $plural_name = get_option('kraken_events_event_plural_name', 'Events');
        echo '<input type="text" id="kraken_events_event_plural_name" name="kraken_events_event_plural_name" value="' . esc_attr($plural_name) . '" />';
    }

    public static function event_taxonomies_callback() {
        // Decode the JSON option to retrieve taxonomy fields, and ensure it is an array
        $taxonomies = json_decode(get_option('kraken_events_event_taxonomies', json_encode([])), true);
    
        // If decoding failed or returned null, set $taxonomies to an empty array
        if (!is_array($taxonomies)) {
            $taxonomies = [];
        }
    
        echo '<div id="taxonomy-fields">';
        foreach ($taxonomies as $index => $taxonomy) {
            echo '<div class="taxonomy-field">';
            echo '<input type="text" name="kraken_events_event_taxonomies[' . $index . '][singular]" value="' . esc_attr($taxonomy['singular'] ?? '') . '" placeholder="Enter singular name" />';
            echo '<input type="text" name="kraken_events_event_taxonomies[' . $index . '][plural]" value="' . esc_attr($taxonomy['plural'] ?? '') . '" placeholder="Enter plural name" />';
            echo '<input type="text" name="kraken_events_event_taxonomies[' . $index . '][slug]" value="' . esc_attr($taxonomy['slug'] ?? '') . '" placeholder="Enter taxonomy slug" />';
            echo '<button type="button" class="remove-taxonomy button">Remove</button>';
            echo '</div>';
        }
        echo '</div>';
    
        echo '<button type="button" id="add-taxonomy" class="button">Add Taxonomy</button>';
    
        echo '<script>
            document.getElementById("add-taxonomy").addEventListener("click", function() {
                const index = document.querySelectorAll(".taxonomy-field").length;
                const field = document.createElement("div");
                field.classList.add("taxonomy-field");
                field.innerHTML = `
                    <input type="text" required name="kraken_events_event_taxonomies[${index}][singular]" placeholder="Enter singular name" />
                    <input type="text" required name="kraken_events_event_taxonomies[${index}][plural]" placeholder="Enter plural name" />
                    <input type="text" required name="kraken_events_event_taxonomies[${index}][slug]" placeholder="Enter taxonomy slug" />
                    <button type="button" class="remove-taxonomy button">Remove</button>
                `;
                document.getElementById("taxonomy-fields").appendChild(field);
            });
    
            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-taxonomy")) {
                    event.target.closest(".taxonomy-field").remove();
                }
            });
        </script>';
    }

    public static function enable_pages_callback() {
        $currentValue = get_option('kraken_events_enable_pages', false);
        ?>
        <input type="checkbox" name="kraken_events_enable_pages" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function page_selects_callback($args) {
        $fieldName = 'kraken_events_page_'.$args['field_slug'];
        $currentId = get_option($fieldName, '');
        wp_dropdown_pages(array(
            'name'             => $fieldName,
            'selected'         => $currentId,
            'show_option_none' => __('Select a page', 'kraken-events'),
        ));
        if ($currentId) {
            echo '<a href="'.get_the_permalink($currentId).'" target="_blank">View page</a>';
            echo ' | ';
            echo '<a href="'.get_edit_post_link($currentId).'" target="_blank">Edit page</a>';
        }
    }

    public static function acf_field_group_callback($args) {
        $type = $args['type'];

        $selected_group = get_option('kraken_events_'.$type.'_field_group', '');

        if (function_exists('acf_get_field_groups')) {
            $field_groups = acf_get_field_groups();

            echo '<select name="kraken_events_'.$type.'_field_group">';
            echo '<option value="">' . __( 'Select a field group', 'kraken-events' ) . '</option>';
            
            // Loop through each field group and add it to the dropdown
            foreach ( $field_groups as $group ) {
                $selected = selected( $selected_group, $group['key'], false );
                echo '<option value="' . esc_attr( $group['key'] ) . '" ' . $selected . '>' . esc_html( $group['title'] ) . '</option>';
            }
    
            echo '</select>';
        }
    }

    public static function load_event_editor_check_callback() {
        $currentValue = get_option('kraken_events_event_enable_editor', false);
        ?>
        <input type="checkbox" name="kraken_events_event_enable_editor" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function disable_all_blocks_callback() {
        $currentValue = get_option('kraken_events_disable_all_blocks', false);
        ?>
        <input type="checkbox" name="kraken_events_disable_all_blocks" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function load_event_featured_image_check_callback() {
        $currentValue = get_option('kraken_events_event_enable_featured_image', false);
        ?>
        <input type="checkbox" name="kraken_events_event_enable_featured_image" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function load_stylesheet_callback() {
        $currentValue = get_option('kraken_events_load_stylesheet', true);
        ?>
        <input type="checkbox" name="kraken_events_load_stylesheet" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function enable_legacy_support_callback() {
        $currentValue = get_option('kraken_events_enable_legacy_support');
        ?>
        <input type="checkbox" name="kraken_events_enable_legacy_support" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function enable_kraken_calendar_callback() {
        $currentValue = get_option('kraken_events_enable_kraken_calendar');
        ?>
        <input type="checkbox" name="kraken_events_enable_kraken_calendar" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function site_has_eventastic_templates_callback() {
        $currentValue = get_option('kraken_events_site_has_eventastic_templates');
        ?>
        <input type="checkbox" name="kraken_events_site_has_eventastic_templates" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function tools_section_callback() {
        echo '<p>Use these tools to manage your event data.</p>';
    }

    public static function database_tool_button_html() {
        ?>
        <p class="description">
            This will create the necessary database table for event occurrences and populate it with data from all of your existing published events. It is safe to run this multiple times.
        </p>
        <?php
        // Add nonce for security
        wp_nonce_field('kraken_events_run_sync', 'kraken_events_sync_nonce');
        submit_button('Create Table & Sync Events', 'secondary', 'kraken_events_run_sync_submit', false);
    }

    /**
     * Handle the database sync button submission.
     */
    public static function handle_database_actions() {
        if (
            isset($_POST['kraken_events_run_sync_submit']) &&
            isset($_POST['kraken_events_sync_nonce']) &&
            wp_verify_nonce($_POST['kraken_events_sync_nonce'], 'kraken_events_run_sync')
        ) {
            // 1. Create the table (it will check if it exists)
            PartnerEvents::create_event_occurrences_table();

            // 2. Run the backfill process
            $processed_count = PartnerEvents::run_backfill_process();

            // Add an admin notice to show the result
            add_action('admin_notices', function() use ($processed_count) {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        printf(
                            esc_html__('Event synchronization complete. Processed %d events.', 'kraken-events'),
                            absint($processed_count)
                        );
                        ?>
                    </p>
                </div>
                <?php
            });
        }
    }

    public static function settings_page_callback() {
        ?>
        <div class="wrap">
            <h1>Kraken Events Settings</h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('kraken_events_settings');
                do_settings_sections('kraken-events'); // This renders all your original settings
                submit_button();
                ?>
            </form>

            <form method="post" action="">
                <?php
                // This renders the new "Database Tools" section
                do_settings_sections('kraken-events-tools');
                ?>
            </form>
        </div>
        <?php
    }

    public static function add_event_rewrite_rules() {
        add_rewrite_rule(
            '^event/([^/]+)/([0-9]{4}-[0-9]{2}-[0-9]{2})/?$', 
            'index.php?post_type=event&name=$matches[1]&event_date=$matches[2]', 
            'top'
        );
    }
    
}
