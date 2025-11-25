<?php
namespace MaddenMedia\KrakenEvents;

use WP_Query;

class KrakenCRM {

    public static $event_slug = null;

    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);

        self::$event_slug = get_option('kraken_events_event_slug', 'event');
    }

    public static function register_settings() {
        add_settings_section('kraken_events_crm_integration', 'Kraken CRM Integration', 
        [__CLASS__, 'crm_section_info_callback'], 'kraken-events');

        register_setting('kraken_events_settings', 'kraken_events_enable_crm', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        register_setting('kraken_events_settings', 'kraken_events_restrict_to_partners', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false
        ));

        add_settings_field(
            'kraken_events_enable_crm', 
            'Enable CRM Features', 
            [__CLASS__, 'enable_crm_callback'], 
            'kraken-events', 
            'kraken_events_crm_integration'
        );

        add_settings_field(
            'kraken_events_restrict_to_partners', 
            'Restrict Event Management to Only Approved Partners', 
            [__CLASS__, 'restrict_to_partners_callback'], 
            'kraken-events', 
            'kraken_events_crm_integration'
        );
    }

    public static function crm_section_info_callback() {
        ?>
        <p>Enabling CRM features will allow partners to add & edit their own events.</br>Restricting to admin & partners only will make the add event page private and require a login to access.</br>Edit event functionality is always restricted to the event owner.</p>
        <?php
    }

    public static function enable_crm_callback() {
        $currentValue = get_option('kraken_events_enable_crm', false);
        ?>
        <input type="checkbox" name="kraken_events_enable_crm" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }

    public static function restrict_to_partners_callback() {
        $currentValue = get_option('kraken_events_restrict_to_partners', false);
        ?>
        <input type="checkbox" name="kraken_events_restrict_to_partners" value="1" <?php checked($currentValue, true); ?> />
        <?php
    }
}