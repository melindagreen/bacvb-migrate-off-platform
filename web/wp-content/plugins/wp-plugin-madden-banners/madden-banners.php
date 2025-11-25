<?php

/**
 * Plugin Name: MaddenBanners
 * Version: 1.1.6
 * Description: A plugin to add custom banners to a site
 * Author: Madden Media
 * Author URI: https://maddenmedia.com
 * License: Attribution-NonCommercial-NoDerivs 3.0 Unported ( CC BY-NC-ND 3.0)
 *
 * Copyright ( c) 2020 Madden Media
 */

namespace MaddenBanners;

require_once(__DIR__ . '/admin/AdminConstants.php');
require_once(__DIR__ . '/admin/SettingsLayout.php');
require_once(__DIR__ . '/assets/AssetHandler.php');
require_once(__DIR__ . '/library/Constants.php');

use MaddenBanners\Admin\SettingsLayout as SettingsLayout;
use MaddenBanners\Assets\AssetHandler as AssetHandler;
use MaddenBanners\Library\Constants as Constants;

class MaddenBanners
{
    function __construct()
    {
        // Init assets handler
        new AssetHandler();

        // Setup settings page
        add_action('admin_init', array(get_called_class(), 'setup_settings'));
        add_action('admin_menu', array(get_called_class(), 'add_settings_page'));

        add_action('wp_ajax_nopriv_gf_get_form', array(get_called_class(), 'gf_get_form'));
        add_action('wp_ajax_gf_get_form', array(get_called_class(), 'gf_get_form'));
    }

    /**
     * Init settings layout
     */
    public static function setup_settings()
    {
        if (!wp_doing_ajax()) {
            $admin_settings = new SettingsLayout();
        }
    }

    /**
     * Register the options page
     */
    public static function add_settings_page()
    {
        add_options_page(
            Constants::PLUGIN_ADMIN_PAGE_TITLE,
            Constants::PLUGIN_ADMIN_MENU_TITLE,
            'edit_posts',
            Constants::PLUGIN_ADMIN_MENU_SLUG,
            array(get_called_class(), "show_settings")
        );
    }

    /**
     * Render the settings page template
     */
    public static function show_settings()
    {
        include(plugin_dir_path(__FILE__) . 'templates/settings.php');
    }

    /**
     * setting up gravity forms
     */
    public static function gf_get_form()
    {

        $form_id = isset($_POST['form_id']) ? absint($_POST['form_id']) : 0;
        gravity_form(
            $form_id, // form id or title
            false, // display title?
            false, // display description?
            false, // display if inactive?
            false, // field values to prepolulate
            true // ajax
        );
        die();
    }
}

// Let 'er rip!
new MaddenBanners();
