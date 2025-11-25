<?php
/**
 * Plugin Name: Kraken Core
 * Description: Core Gutenberg functionality including blocks, filters, & more
 * Version: 1.1.0.2
 * Author: Madden Media
 * Author URI: https://maddenmedia.com
 */

namespace MaddenMedia\KrakenCore;

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KRAKEN_CORE_PLUGIN_VERSION', '1.1.0.2');
define('KRAKEN_CORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KRAKEN_CORE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/mtphr-settings/index.php';

require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/class-helpers.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/class-utilities.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/admin/class-admin-setup.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/admin/class-admin-settings.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/admin/class-admin-dashboard.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/core/class-assets.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/core/class-blocks.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/api.php';
require_once KRAKEN_CORE_PLUGIN_DIR . 'inc/upgrades.php';

// Initialize classes
add_action('plugins_loaded', function () {
    if (function_exists('acf')) {
        Helpers::init();

        //Admin setup
        AdminSetup::init();
        AdminSettings::init();
        AdminDashboard::init();

        //Core functionality
        Assets::init();
        Blocks::init();
    } else {
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>Kraken CMS:</strong> Please install and activate <a href="https://www.advancedcustomfields.com/">Advanced Custom Fields</a>, <a href="https://www.gravityforms.com/">Gravity Forms</a>, and <a href="https://www.gravityforms.com/add-ons/user-registration/">Gravity Forms User Registration Add-On</a> to use this plugin.</p></div>';
        });
    }

});
