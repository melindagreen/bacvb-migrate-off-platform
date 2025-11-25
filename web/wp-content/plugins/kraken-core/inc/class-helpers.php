<?php
namespace MaddenMedia\KrakenCore;

class Helpers {
    /**
     * Initialize the class functionalities.
     */
    public static function init() {
        add_action('admin_notices', [self::class, 'notify_missing_plugins']);
    }

    /**
     * Check if required plugins are active.
     *
     * @return bool True if all required plugins are active, false otherwise.
     */
    public static function check_required_plugins() {
        return function_exists('acf');
    }

    /**
     * Notify admin if required plugins are not active.
     */
    public static function notify_missing_plugins() {
        if (!self::check_required_plugins()) {
            echo '<div class="error"><p><strong>Kraken CMS:</strong> ACF is required. Please install and activate this plugin.</p></div>';
        }
    }

    /**
     * Log an error message to the debug.log file.
     *
     * @param string $message The message to log.
     */
    public static function log_error($message) {
        $log_file = KRAKEN_CORE_PLUGIN_DIR . '/debug.log';
        error_log($message . PHP_EOL, 3, $log_file);
    }

    /*
    Function to check if kraken crm is active
    */
    public static function check_kraken_crm_status() {
        $active = false;
        if (is_plugin_active('kraken-crm/kraken-crm.php')) {
            $active = true;
        }
        return $active;
    }

	/*
    Function to return kraken crm listing slug
    */
    public static function get_kraken_crm_listing_slug() {
		if (self::check_kraken_crm_status()) {
			$listing_slug = get_option('kraken_crm_listing_slug', 'listing');
			return $listing_slug;
		} else {
			return false;
		}
	}

    /*
    Function to check if kraken events is active
    */
    public static function check_kraken_events_status() {
        $active = false;
        if (is_plugin_active('kraken-events/kraken-events.php')) {
            $active = true;
        }
        return $active;
    }

	/*
    Function to return the current events plugin
    */
    public static function get_events_plugin() {
		// Kraken
        if ( is_plugin_active( 'kraken-events/kraken-events.php' ) ) {
			return 'kraken-events';

		// Eventastic
		} elseif( is_plugin_active( 'wp-plugin-eventastic/madden-eventastic.php' ) ) {
			return 'eventastic';

		// TEC
		} elseif( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {
			return 'the-events-calendar';
		}
    }

		/*
    Function to return the current events plugin
    */
    public static function get_events_slug() {
		$events_plugin = self::get_events_plugin();
		switch( $events_plugin ) {
			case 'kraken-events':
				$kraken_event_slug = get_option('kraken_events_event_slug', 'event');
				return $kraken_event_slug;

			case 'eventastic':
				return 'event';

			case 'the-events-calendar':
				return 'tribe_events';

			default:
				return false;
		}
    }
}
