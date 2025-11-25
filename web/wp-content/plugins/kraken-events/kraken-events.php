<?php
/**
 * Plugin Name: Kraken Events
 * Description: A custom Events, originally Eventastic.
 * Version: 1.6.2
 * Author: Madden Media
 * Author URI: https://maddenmedia.com
 */

namespace MaddenMedia\KrakenEvents;

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KRAKEN_EVENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KRAKEN_EVENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KRAKEN_EVENTS_DB_VERSION', '1.0');

if ( file_exists( KRAKEN_EVENTS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once KRAKEN_EVENTS_PLUGIN_DIR . 'vendor/autoload.php';
}

// Include necessary files
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/class-helpers.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/class-utilities.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/admin/class-admin-setup.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/admin/class-admin-new-submissions.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/admin/class-admin-sync-acf-fields.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/admin/class-admin-eventastic-conversion.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-settings.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-custom-registers.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-process-forms.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-events.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-notifications.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-rest-api.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-kraken-crm.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/events/class-event-pages.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/events/class-event-fields.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-html-wrapper.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-cron.php';
if ( get_option('kraken_events_enable_legacy_support') )  :
    require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-legacy.php';
    require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-class-shims.php';
    require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/eventastic-template-loader.php';
endif;


// Initialize classes
add_action('plugins_loaded', function () {
    if (function_exists('acf')) {
        Helpers::init();

        //Admin setup
        AdminSetup::init();
        AdminNewSubmissions::init();
        AdminSettings::init();
        AdminSyncACF::init();
        AdminEventasticConversion::init();

        //Other
        CustomRegisters::init();
        ProcessForms::init();
        Notifications::init();

        //Initial Setup Items
        EventFields::init();
        EventPages::init();

        //Events integration
        PartnerEvents::init();
        EventRestApi::init();
        EventRecurrenceCron::init();

        if ( get_option( 'kraken_events_enable_legacy_support' ) ) :
           \KrakenEvents\Compatibility\TemplateLoader::init();
        endif;

        if (is_plugin_active('kraken-crm/kraken-crm.php')) {
            KrakenCRM::init();
        }

        //Create the database table for event occurrences
        $current_db_version = get_option('kraken_events_db_version', '0.1');
        if (version_compare($current_db_version, KRAKEN_EVENTS_DB_VERSION, '<')) {
            PartnerEvents::create_event_occurrences_table();
            update_option('kraken_events_db_version', KRAKEN_EVENTS_DB_VERSION);
        }
    } else {
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>Kraken CMS:</strong> Please install and activate <a href="https://www.advancedcustomfields.com/">Advanced Custom Fields</a>, <a href="https://www.gravityforms.com/">Gravity Forms</a>, and <a href="https://www.gravityforms.com/add-ons/user-registration/">Gravity Forms User Registration Add-On</a> to use this plugin.</p></div>';
        });
    }
});

/**
 * Register WP-CLI commands.
 */
add_action('cli_init', function() {
    require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-cli-commands.php';
    \WP_CLI::add_command('kraken-events', 'MaddenMedia\KrakenEvents\CLI_Commands');
});

/**
 * The function that runs on plugin activation.
 * This is used to create the custom database table.
 */
register_activation_hook(__FILE__, function() {
    require_once KRAKEN_EVENTS_PLUGIN_DIR . 'inc/core/class-events.php';
    PartnerEvents::create_event_occurrences_table();
});
