<?php /**
 * Asset handler for PartnerPortal
 */


namespace PartnerPortal\Assets;

use PartnerPortal\Library\Constants as Constants;
use PartnerPortal\Admin\AdminConstants as AdminConstants;

class AssetHandler {
    /**
     * Construct new AssetHandler
     */
    public function __construct() {
        // Enqueues
        add_action('wp_enqueue_scripts', array( get_called_class(), 'enqueue_front_scripts_and_styles' ) ); // Front
        add_action('admin_enqueue_scripts', array( get_called_class(), 'enqueue_admin_scripts_and_styles' ) ); // Admin
    }

    /**
     * Enqueues for non-admin pages
     */
    public static function enqueue_front_scripts_and_styles() {
        if(!is_admin()) {
            // Enqueue scripts
            wp_enqueue_script(
                'madden-plugin-front-js', // handle
                plugin_dir_url( __FILE__ ).'build/front.js', // path
                [], // deps
                1, // ver (update for cache bust)
                true // enqueue in footer
            );

            // Localize options to front script
            $options = get_option(Constants::PLUGIN_SETTING_SLUG);
            wp_localize_script('madden-plugin-front-js', Constants::PLUGIN_ADMIN_MENU_SLUG, $options);
            $option_defaults = AdminConstants::get_settings_fields();
            wp_localize_script('madden-plugin-front-js', Constants::PLUGIN_ADMIN_MENU_SLUG . '_defaults', $option_defaults);

        }
    }

    /**
     * Enqueue all admin scripts and styles
     */
    public static function enqueue_admin_scripts_and_styles() {
        wp_enqueue_style('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
        wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/61fcc94f36.js');
        wp_enqueue_script('moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true);
        wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true);
        wp_enqueue_script('validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js', array(), '', true);         // Enqueue scripts
         wp_enqueue_script(
            'madden-plugin-admin-js', // handle
            plugin_dir_url( __FILE__ ).'build/admin.js', // path
            ['jquery'], // deps
            1, // ver (update for cache bust)
            true // enqueue in footer
        );

        // Localize default options for admin script
        $option_defaults = AdminConstants::get_settings_fields();
        wp_localize_script('madden-plugin-admin-js', Constants::PLUGIN_ADMIN_MENU_SLUG . '_defaults', $option_defaults);

        // Enqueue styles
        wp_enqueue_style(
            'madden-plugin-admin-css', // handle
            plugin_dir_url( __FILE__ ).'src/styles/admin.css', // path
            [], // deps
            1, // ver (update for cache bust)
            'screen' // media query
        );
    }
}