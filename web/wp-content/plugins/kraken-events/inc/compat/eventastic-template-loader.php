<?php
namespace KrakenEvents\Compatibility;

class TemplateLoader {

    public static function init() {

        add_action('init', [ __CLASS__, 'add_eventastic_blocks'], 20 );
        add_filter('template_include', [__CLASS__, 'intercept_templates']);
        add_action('wp_enqueue_scripts',[__CLASS__, 'enqueue_eventastic_template_scripts_styles'] );

        add_filter('theme_page_templates', function($templates) {
            if ( get_option( 'kraken_events_site_has_eventastic_templates' ) ) {
                $templates['eventastic-theme-files/templates/eventastic-events.php']  = 'Eventastic Events Page (Kraken Events)';
            }
            return $templates;
        });

        add_filter('page_template', function($template) {

            if ( get_option( 'kraken_events_site_has_eventastic_templates' ) ) {
                $selected_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
        
                if (strpos($selected_template, 'eventastic-theme-files/templates/') === 0) {
                    // Check if theme has override
                    $theme_override = get_stylesheet_directory() . '/' . $selected_template;
                    if (file_exists($theme_override)) {
                        return $theme_override;
                    }
            
                    // Fallback to plugin-provided template
                    $plugin_template = KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/' . $selected_template;
                    if (file_exists($plugin_template)) {
                        return $plugin_template;
                    }
                }
            }

            return $template;
        });        
        
        /**
         * Preventing any queries on the front end of the site from ordering by eventastic start/end date fields
         * or meta querying based on eventastic start/end date fields.
         * 
         */
        add_action( 'pre_get_posts', function( \WP_Query $query ) {
           if ( is_admin()  ) {
                return;
            }
        
            $args     = $query->query_vars;
            $modified = false;
        
            // Handle meta_query
            if ( isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
                $meta_query = $args['meta_query'];
        
                foreach ( $meta_query as &$clause ) {
                    if ( isset( $clause['key'] ) && preg_match( '/^eventastic_(.+)_date$/', $clause['key'] ) ) {
                        $clause['key'] = preg_replace( '/^eventastic_(.+)_date$/', 'event_${1}_date', $clause['key'] );
                        $modified = true;
                    }
                }
        
                if ( $modified ) {
                    $query->set( 'meta_query', $meta_query );
                }
            }
        
            // Handle meta_key
            if ( isset( $args['meta_key'] ) && preg_match( '/^eventastic_(.+)_date$/', $args['meta_key'] ) ) {
                $new_meta_key = preg_replace( '/^eventastic_(.+)_date$/', 'event_${1}_date', $args['meta_key'] );
                $query->set( 'meta_key', $new_meta_key );
                $modified = true;
            }
        
            // Log if any replacements were made
            if ( $modified ) {
                $backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
                $template  = 'unknown';
        
                foreach ( $backtrace as $frame ) {
                    if ( isset( $frame['file'] ) && strpos( $frame['file'], get_stylesheet_directory() ) === 0 ) {
                        $template = str_replace( ABSPATH, '', $frame['file'] );
                        break;
                    }
                }
        
                error_log( '[Kraken Events] Legacy meta field in query intercepted in: ' . $template );
            }
        });
        
        


    }

    /**
     * Override the template if Eventastic-style templates are requested.
     */
    public static function intercept_templates($template) {
       
        if (is_singular('event') && get_option( 'kraken_events_site_has_eventastic_templates' ) ) {
            $custom = self::locate_template('eventastic-single.php');
            if ($custom) return $custom;
        }
        return $template;
    }

    /**
     * Locate theme override or fallback to plugin template
     */
    protected static function locate_template($template_name) {
        $theme_template = get_stylesheet_directory() . '/eventastic-theme-files/templates/' . $template_name;
        $plugin_template = KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-theme-files/templates/' . $template_name;

        if (file_exists($theme_template)) {
            return $theme_template;
        }

        if (file_exists($plugin_template)) {
            return $plugin_template;
        }

        return false;
    }

    public static function enqueue_eventastic_template_scripts_styles() {

        // for the main template    
        if ( is_page_template( 'eventastic-theme-files/templates/eventastic-events.php' ) && get_option( 'kraken_events_site_has_eventastic_templates' ) ) {
            //check for the files in the theme before loading from the plugin
            $cssFile = '/eventastic-theme-files/styles/eventastic-events.css';
            $filePath = ( LegacyUtilities::is_file_in_theme( $cssFile ) ) ? get_stylesheet_directory_uri().$cssFile : KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-theme-files/styles/eventastic-events.css';

            wp_enqueue_style( 'eventastic-events-css', $filePath );

            wp_enqueue_style( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
            wp_enqueue_script( 'mm-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true );
            wp_enqueue_script( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true );

            $jsFile = '/eventastic-theme-files/scripts/eventastic-events.js';
            $filePath = ( LegacyUtilities::is_file_in_theme( $jsFile ) ) ? get_stylesheet_directory_uri().$jsFile : KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-theme-files/scripts/eventastic-events.js';
            wp_enqueue_script( 'eventastic-events-js', $filePath, array( 'jquery', 'mm-moment', 'daterangepicker' ), '', true );
            wp_localize_script( 'eventastic-events-js', 'wp_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }

        // for the singular template
        if ( is_singular( LegacyUtilities::getPluginPostType() ) && get_option( 'kraken_events_site_has_eventastic_templates' ) ) {
            wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/61fcc94f36.js');

            //check for file in theme first
            $cssFile = '/eventastic-theme-files/styles/eventastic-single.css';
            $filePath = ( LegacyUtilities::is_file_in_theme( $cssFile ) ) ? get_stylesheet_directory_uri().$cssFile : KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-theme-files/styles/eventastic-single.css';
            wp_enqueue_style( 'eventastiv-single-css', $filePath );

            //include js
            $jsFile = '/eventastic-theme-files/scripts/eventastic-single.js';
            $filePath = ( LegacyUtilities::is_file_in_theme( $jsFile ) ) ? get_stylesheet_directory_uri().$jsFile : KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-theme-files/scripts/eventastic-single.js';
            wp_enqueue_script( 'eventastic-single-js', $filePath, array( 'jquery' ), '', true );
        }
        $jsAjaxFile = '/eventastic-theme-files/scripts/eventastic-ajax.js';
        $ajaxFilePath = ( LegacyUtilities::is_file_in_theme( $jsAjaxFile ) ) ? get_stylesheet_directory_uri().$jsAjaxFile : KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-theme-files/scripts/eventastic-ajax.js';
        wp_enqueue_script('eventastic-ajaxscript', $ajaxFilePath, ['jquery']);
        $variable_to_js = [
            'ajax_url' => admin_url('admin-ajax.php')
        ];
        wp_localize_script('eventastic-ajaxscript', 'Eventastic_Variables', $variable_to_js);
    }


    public static function add_eventastic_blocks() {
        $block_name = 'madden-media/kraken-calendar';

        if ( get_option('kraken_events_enable_kraken_calendar') == false) {
            return; // Kraken Events calendar is not activated
        }

        // Check if block is already registered
        $registry = \WP_Block_Type_Registry::get_instance();
        if ( $registry->get_registered( $block_name ) ) {
            return; // Block is already registered, exit early
        }
        
        $plugin_block_json = KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/blocks/kraken-calendar/build/block.json';
        if ( file_exists( $plugin_block_json ) ) {
            register_block_type( $plugin_block_json );
        }
        
    }    

}
