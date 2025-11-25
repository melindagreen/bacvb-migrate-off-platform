<?php
namespace MaddenMedia\KrakenEvents;

class Helpers {
    /**
     * Initialize the class functionalities.
     */
    public static function init() {
        add_action('admin_notices', [self::class, 'notify_missing_plugins']);
    }

    /**
     * Get the week number from a date.
     */
    public static function week_number_to_label($week_number) {
        $labels = ['first', 'second', 'third', 'fourth', 'fifth'];
        return $labels[$week_number - 1] ?? null;
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
        $log_file = KRAKEN_EVENTS_PLUGIN_DIR . '/debug.log';
        error_log($message . PHP_EOL, 3, $log_file);
    }

    public static function get_page_url($page) {
        $pageId = get_option('kraken_events_page_'.$page);
        if (!$pageId && self::check_kraken_crm_status()) {
            $pageId = get_option('kraken_crm_page_'.$page);
        }
        return get_permalink($pageId);
    }

    public static function get_taxonomy_term_names($term_ids, $tax_slug) {
        $term_names = array();

        if (is_array($term_ids) && !empty($term_ids)) {
            //array w/ multiple ids
            foreach ($term_ids as $term_id) {
                $term = get_term($term_id, $tax_slug);
                if ($term) {
                    $term_names[] = $term->name;
                }
            }
        } elseif ($term_ids) {
            //if a single id
            $term = get_term($term_ids, $tax_slug);
            if ($term) {
                $term_names[] = $term->name;
            }
        }

        return implode(', ', $term_names);
    }

    /*
    Function to check if kraken crm integration is enabled
    */
    public static function check_kraken_crm_status() {
        $active = false;
        if (class_exists(__NAMESPACE__.'\KrakenCRM')) {
            $active = true;
        }
        return $active;
    }

    /*
    Function to check if string is a date
    */
    public static function is_valid_date($date, $format = 'Ymd') {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date ? $d : false;
    }

    /*
    Function to check if string is a valid time
    */
    public static function is_valid_time($value) {
        // Check if the value matches the format HH:MM:SS
        $pattern = '/^(2[0-3]|[01]?[0-9]):([0-5][0-9]):([0-5][0-9])$/';
        if (preg_match($pattern, $value)) {
            try {
                // Create a DateTime object from the time
                return new \DateTime($value);
            } catch (Exception $e) {
                // If DateTime creation fails, return false
                return false;
            }
        }
        return false;
    }
}