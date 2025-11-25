<?php
namespace MaddenMedia\KrakenEvents;

class AdminSetup {

    public static $post_types = [];

    public static function init() {
        self::$post_types[] = get_option('kraken_events_event_slug', 'event');

        add_action('admin_init', [__CLASS__, 'restrict_partner_access']);
        add_action('admin_menu', [__CLASS__, 'register_menu_pages']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_styles']);
    }

    public static function register_menu_pages() {
        add_menu_page('Events Portal', 'Events Portal', 'manage_options', 'kraken-events', [__CLASS__, 'admin_menu_page_callback'], 'dashicons-building');
    }

    public static function admin_menu_page_callback() {
        echo '<div class="wrap"><h2>Kraken Events</h2>';
        echo '<p>Content.</p>';
        echo '</div>';
    }

    public static function enqueue_admin_styles() {
        wp_enqueue_style(
            'kraken-events-admin-style',
            plugin_dir_url(__FILE__) . '../../assets/css/admin-style.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'kraken-events-admin-script',
            plugin_dir_url(__FILE__) . '../../assets/js/admin.js',
            ['wp-edit-post', 'wp-data'],
            null,
            true
        );

        wp_localize_script('kraken-events-admin-script', 'krakenEvents', [
            'postTypes' => self::$post_types
        ]);
    }

    // Restrict partner access to dashboard
    public static function restrict_partner_access() {
        if (current_user_can('partner') && is_admin() && !defined('DOING_AJAX') && !is_super_admin()) {
            wp_redirect(home_url());
            exit;
        }
    }
}
