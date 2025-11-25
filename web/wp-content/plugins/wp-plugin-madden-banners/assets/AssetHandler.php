<?php

/**
 * Asset handler for MaddenBanners
 */


namespace MaddenBanners\Assets;

use MaddenBanners\Library\Constants as Constants;
use MaddenBanners\Admin\AdminConstants as AdminConstants;
use GFAPI;


class AssetHandler
{
    /**
     * Construct new AssetHandler
     */
    public function __construct()
    {
        // Enqueues
        add_action('wp_enqueue_scripts', array(get_called_class(), 'enqueue_front_scripts_and_styles')); // Front
        add_action('admin_enqueue_scripts', array(get_called_class(), 'enqueue_admin_scripts_and_styles')); // Admin
    }

    /**
     * Enqueues for non-admin pages
     */
    public static function enqueue_front_scripts_and_styles()
    {
        $options = get_option(Constants::PLUGIN_SETTING_SLUG);

        if(isset($options['flyins']) && isset($options['flyins']['all_flyins'])) {
            foreach ($options['flyins']['all_flyins'] as $value) {
                if ($value['template'] == 'survey') {
                    $link = $_SERVER['REQUEST_URI'];
                    $isConditionMatch = true;
                    if(isset($value['conditions'])) {
                        foreach ($value['conditions'] as $condition) {
                            if ($isConditionMatch) {
                                switch ($condition['0']['condition_match']) {
                                    case 'match_exactly':

                                        $isConditionMatch = $link === $condition['0']['condition_value'];
                                        if ($isConditionMatch) {
                                            $condition['0']['matchtype'] === 'is_true' ? $isConditionMatch = true : $isConditionMatch = false;
                                        } elseif (!$isConditionMatch) {
                                            $condition['0']['matchtype'] === 'is_false' ? $isConditionMatch = true : $isConditionMatch = false;
                                        }
                                        break;
                                    case 'contains':
                                        $isConditionMatch = str_contains($link, $condition['0']['condition_value']);
                                        if ($isConditionMatch) {
                                            $condition['0']['matchtype'] === 'is_true' ? $isConditionMatch = true : $isConditionMatch = false;
                                        } elseif (!$isConditionMatch) {
                                            $condition['0']['matchtype'] === 'is_false' ? $isConditionMatch = true : $isConditionMatch = false;
                                        }
                                        break;
                                    case 'regex':
                                        $isConditionMatch = preg_match($condition['0']['condition_value'], $link);
                                        if ($isConditionMatch) {
                                            $condition['0']['matchtype'] === 'is_true' ? $isConditionMatch = true : $isConditionMatch = false;
                                        } elseif (!$isConditionMatch) {
                                            $condition['0']['matchtype'] === 'is_false' ? $isConditionMatch = true : $isConditionMatch = false;
                                        }
                                        break;
                                }
                            }
                        }
                    }
                    // echo $isConditionMatch  ? 'true' : 'false';
                    if ($isConditionMatch) {
                        gravity_form_enqueue_scripts($value['form'], true);
                    }
                }
            }
        }
        // die;
        if (!is_admin()) {
            // Enqueue scripts
            wp_enqueue_script(
                'madden-banners-front-js', // handle
                plugin_dir_url(__FILE__) . 'build/front.js', // path
                ['jquery'], // deps
                1, // ver (update for cache bust)
                true // enqueue in footer
            );

            // Localize options to front script
            $options = get_option(Constants::PLUGIN_SETTING_SLUG);
            if (is_array($options)) wp_localize_script('madden-banners-front-js', Constants::PLUGIN_ADMIN_MENU_SLUG, $options);
            $option_defaults = AdminConstants::get_settings_fields();
            if (is_array($option_defaults)) wp_localize_script('madden-banners-front-js', Constants::PLUGIN_ADMIN_MENU_SLUG . '_defaults', $option_defaults);
            wp_localize_script('madden-banners-front-js', 'ajaxdata', array('url' => admin_url('admin-ajax.php')));
            // Enqueue styles
            wp_enqueue_style(
                'madden-banners-front-css', // handle
                plugin_dir_url(__FILE__) . 'build/front.css', // path
                [], // deps
                1, // ver (update for cache bust)
                'screen' // media query
            );
        }
    }

    /**
     * Enqueue all admin scripts and styles
     */
    public static function enqueue_admin_scripts_and_styles()
    {
        // Enqueue scripts
        wp_enqueue_script(
            'madden-banners-admin-js', // handle
            plugin_dir_url(__FILE__) . 'build/admin.js', // path
            ['jquery'], // deps
            1, // ver (update for cache bust)
            true // enqueue in footer
        );

        // Localize default options for admin script
        $option_defaults = AdminConstants::get_settings_fields();
        if (is_array($option_defaults)) wp_localize_script('madden-banners-admin-js', Constants::PLUGIN_ADMIN_MENU_SLUG . '_defaults', $option_defaults);

        // Enqueue styles
        wp_enqueue_style(
            'madden-banners-admin-css', // handle
            plugin_dir_url(__FILE__) . 'build/admin.css', // path
            [], // deps
            1, // ver (update for cache bust)
            'screen' // media query
        );
    }
}
