<?php
namespace MaddenMedia\KrakenEvents;

class EventFields {
    public static function init() {
        add_action('acf/init', [__CLASS__, 'import_acf_field_groups']);
    }

    public static function import_acf_field_groups() {
        $event_fields = get_option('kraken_events_event_field_group');
        if (!$event_fields) {
            self::import_acf_json('acf-events.json', 'kraken_events_event_field_group');
        }
    }

    private static function import_acf_json($file_name, $option_name) {
        // Path to your JSON file (update with your specific file path)
        $json_file_path = KRAKEN_EVENTS_PLUGIN_DIR .'jsons/'.$file_name;

        // Check if file exists
        if (!file_exists($json_file_path)) {
            error_log('ACF JSON file not found at ' . $json_file_path);
            return;
        }

        // Read JSON file contents
        $json_data = file_get_contents($json_file_path);
        $field_groups = json_decode($json_data, true);

        if (is_array($field_groups)) {
            foreach ($field_groups as $field_group) {
                // Import each field group to ACF
                if (function_exists('acf_import_field_group')) {
                    acf_import_field_group($field_group);
                    update_option($option_name, $field_group['key']);
                } else {
                    error_log('ACF function acf_import_field_group not found.');
                }
            }
        } else {
            error_log('Invalid JSON format in ' . $json_file_path);
        }
    }
}
