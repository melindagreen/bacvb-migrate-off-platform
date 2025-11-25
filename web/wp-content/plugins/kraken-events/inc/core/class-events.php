<?php
namespace MaddenMedia\KrakenEvents;

use RRule\RRule;
use RRule\RSet;
use WP_Query;

class PartnerEvents {

    public static $event_slug = null;

    public static function init() {
        self::$event_slug = get_option('kraken_events_event_slug', 'event');

        //shortcodes & conversions
        add_shortcode('partner_add_event_form', [__CLASS__, 'partner_add_event_form']);
        add_action('save_post', [__CLASS__, 'process_acf_event_meta'], 10, 2);
        add_filter('posts_clauses', [__CLASS__, 'custom_event_query_clauses'], 10, 2);
    }

    public static function partner_add_event_form() {

        $classes = 'add-event';

        if (Helpers::check_kraken_crm_status() && get_option('kraken_events_enable_crm') && get_option('kraken_events_restrict_to_partners')) {
            $loginPageUrl   = Helpers::get_page_url('login');

            $is_allowed = (current_user_can('administrator') || current_user_can('partner'));

            if (!is_user_logged_in() || !$is_allowed) {
                wp_redirect($loginPageUrl);
                exit();
            }

            $classes .= ' kraken-crm-partner-portal';
        }

        ob_start();
        echo EventHTMLWrapper::partner_html_wrapper_start($classes);

        if (isset($_GET['updated'])) {
            $accountPageUrl = Helpers::get_page_url('account');
            echo '<p class="kraken-form-notification success">Thank you, your event has been submitted for review. You will be notified once the event has been published. <a href="'.$accountPageUrl.'">Return to account page</a>.</p>';
        }

        /* only output on the frontend; the acf form is causing issues in the editor */
        if (!is_admin() && function_exists('acf_form')) {
            $eventFields    = get_option('kraken_events_event_field_group');
            if ($eventFields) {
                // Display the ACF form
                echo acf_form_head();
                echo acf_form(array(
                    'id'                => 'add-event',
                    'post_id'           => 'new_post',
                    'new_post'          => array(
                        'post_type'     => self::$event_slug,
                        'post_status'   => 'pending'
                    ),
                    'field_groups'      => array($eventFields),
                    'form'              => true,
                    'submit_value'      => 'Submit New Event',
                    'updated_message'   => ''
                ));
            } else {
                echo '<p class="kraken-form-notification error">Please select a field group in the plugin settings.</p>';
            }
        }
        echo EventHTMLWrapper::partner_html_wrapper_end();

        return ob_get_clean();
    }

    /*
    This function converts the acf date / time / recurring fields to whatever format Events is expecting
    This only runs when saved in the editor.
    */
    public static function process_acf_event_meta($post_id, $post) {
        // Prevent execution on autosave, AJAX, or revisions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        if (wp_is_post_revision($post_id)) return;

        // Skip if saving options page or if it's not an admin save
        if ($post_id === 'options' || !is_admin()) return;

        // Ensure correct post type
        if (get_post_type($post_id) !== self::$event_slug) return;

        // Ensure ACF fields exist before proceeding
        if (!function_exists('get_field_object')) return;

        // Retrieve all ACF fields for this post
        $data = [];
        $fields = get_fields($post_id);
        if ($fields) {
            foreach ($fields as $key => $value) {
                $field = get_field_object($key, $post_id);
                if ($field) {
                    $data[$key] = [
                        "field_name"    => $field["name"],
                        "value"         => $value
                    ];
                }
            }
        }

        // Process and update event meta
        self::updateEventsMeta($post_id, $data);
    }

    /*
    Process ACF data and convert to Events date & time formats
    */
    public static function updateEventsMeta($post_id, $data) {
        if (!is_array($data) || empty($data) || !$post_id) return;

        foreach ($data as $field_key => $field_data) {
            $field_name = $field_data['field_name'];
            $value      = $field_data['value'];

            if ($field_name == 'events_recurrence_options') {

                $rrules 				= [];
                $rdates 				= [];
                $exdates 				= [];
				$all_event_dates 		= [];

				$start_date_str 	= $data['event_start_date']['value'] ?? null;
				$end_date_str 		= $data['event_end_date']['value'] ?? null;

				//fallback start date
				if (empty($start_date_str)) {
                	$start_date_str = date('Ymd');
            	}

                // For single day events, ensure end date matches start date
                if($value === 'one_day' && $end_date_str !== $start_date_str) {
                    $end_date_str = $start_date_str;
                    // Update the ACF value to keep in sync (necessary in the event that event_end_date field was previously set when recurrence was different)
                    update_field('event_end_date', $start_date_str, $post_id);
                } 

                $start_date = new \DateTime($start_date_str);

				//fallback end date
				if (empty($end_date_str) && in_array($value, ['daily', 'weekly', 'monthly_by_date', 'monthly_by_dotw'])) {
					$end_date = (clone $start_date)->modify('+1 year');
				} elseif ($end_date_str) {
					$end_date = new \DateTime($end_date_str);
				} else {
					$end_date = clone $start_date;
				}

				$exclusion_dates_ymd = [];
				if (!empty($data['event_pattern_has_exclusions']['value']) && !empty($data['exclusion_dates']['value'])) {
					foreach($data['exclusion_dates']['value'] as $ex) {
						if (!empty($ex['date'])) {
							$exdate_obj = new \DateTime($ex['date']);
							$exdates[] = $exdate_obj->format('Y-m-d H:i:s');
							$exclusion_dates_ymd[] = $exdate_obj->format('Ymd');
						}
					}
				}

                switch ($value) {
                    case "one_day":
                    case "daily":
                		if (!$start_date_str) break;
						if ($value === "daily" && !$end_date_str) break;

						$is_all_day = $data['events_event_all_day']['value'] ?? null;
                        $start_time = $is_all_day ? '00:00:00' : ($data['event_start_time']['value'] ?? '00:00:00');
                        $end_time = $is_all_day ? '23:59:59' : ($data['event_end_time']['value'] ?? '23:59:59');

						$rule_params = [
                            'FREQ' => 'DAILY',
                            'DTSTART' => $start_date->format('Y-m-d') . ' ' . $start_time,
                        ];

                        if ($value === 'one_day') {
                            $rule_params['COUNT'] = 1;
                        } elseif ($end_date_str) {
                            $rule_params['UNTIL'] = (new \DateTime($end_date_str))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
                        }

                        $rrules[] = $rule_params;

						//legacy event_repeat_dates logic
                        $temp_rule = new RRule($rule_params);
						foreach($temp_rule as $occurrence) {
							if (in_array($occurrence->format('Ymd'), $exclusion_dates_ymd)) continue;
							$all_event_dates[] = ['date' => $occurrence->format('Ymd'), 'all_day' => !!$is_all_day, 'start_time' => $is_all_day ? null : $occurrence->format('H:i:s'), 'end_time' => $is_all_day ? null : (clone $occurrence)->modify('+'.(strtotime($end_time) - strtotime($start_time)).' seconds')->format('H:i:s')];
						}

                        break;
                    case "specific_dates":
                        foreach ($data['event_specific_dates']['value'] ?? [] as $row) {
                            if (empty($row['date'])) continue;
                            $start_time = ($row['all_day'] ?? null) ? '00:00:00' : ($row['start_time'] ?? '00:00:00');
                            $rdates[] = $row['date'] . ' ' . $start_time;
                            $all_event_dates[] = ['date' => $row['date'], 'all_day' => !!$row['all_day'], 'start_time' => $row['start_time'], 'end_time' => $row['end_time']];
                        }

                        $first_occurence = reset($all_event_dates);
                        $last_occurence = end($all_event_dates);
                        update_post_meta($post_id, 'event_start_date', $first_occurence['date']);
                        update_post_meta($post_id, 'event_end_date', $last_occurence['date']);

                        break;
                    case "weekly":
                    case "monthly_by_date":
                    case "monthly_by_dotw":
						//we can't create an rrule without an end date
						if (!$end_date_str) break;

						$until = (new \DateTime($end_date_str))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
						$repeater_key = ['weekly' => 'event_weekly', 'monthly_by_date' => 'monthly_by_dates', 'monthly_by_dotw' => 'monthly_by_dotw'][$value];

						// Directly loop through each row of the repeater. Each row is a new rule.
						foreach ($data[$repeater_key]['value'] ?? [] as $row) {
							$start_time = ($row['all_day'] ?? null) ? '00:00:00' : ($row['start_time'] ?? '00:00:00');
							$rule_params = [
								'FREQ'    => ($value === 'weekly') ? 'WEEKLY' : 'MONTHLY',
								'DTSTART' => $start_date->format('Y-m-d') . ' ' . $start_time,
								'UNTIL'   => $until,
							];

							if ($value === 'weekly') {
								if (empty($row['days_of_the_week'])) continue;
								$rule_params['BYDAY'] = array_map(fn($d) => strtoupper(substr($d, 0, 2)), $row['days_of_the_week']);
							}

							if ($value === 'monthly_by_date') {
								if (empty($row['day_number'])) continue;
								$rule_params['BYMONTHDAY'] = $row['day_number'];
							}

							if ($value === 'monthly_by_dotw') {
								if (empty($row['week_number']) || empty($row['day_of_the_week'])) continue;
								$rule_params['BYDAY'] = $row['week_number'] . strtoupper(substr($row['day_of_the_week'], 0, 2));
							}

							$rrules[] = $rule_params;

							// Populate legacy array for this specific rule
							$temp_rule = new RRule($rule_params);
							$end_time = ($row['all_day'] ?? null) ? '23:59:59' : ($row['end_time'] ?? '23:59:59');
							foreach ($temp_rule as $occurrence) {
								if (in_array($occurrence->format('Ymd'), $exclusion_dates_ymd)) continue;
								$all_event_dates[] = [
									'date' => $occurrence->format('Ymd'),
									'all_day' => !!$row['all_day'],
									'start_time' => ($row['all_day'] ?? null) ? null : $occurrence->format('H:i:s'),
									'end_time' => ($row['all_day'] ?? null) ? null : (clone $occurrence)->modify('+'.(strtotime($end_time) - strtotime($start_time)).' seconds')->format('H:i:s')
								];
							}

							//update the start/end dates based on the actual occurrence dates
							$first_occurence = reset($all_event_dates);
							$last_occurence = end($all_event_dates);
							update_post_meta($post_id, 'event_start_date', $first_occurence['date']);
							update_post_meta($post_id, 'event_end_date', $last_occurence['date']);
						}
						break;
                    default:
                        Helpers::log_error('Unhandled recurrence option: ' . $value);
                        break;
                }

				//Update all of these values for all recurrence patterns
                $full_rrules = ['rrules' => $rrules, 'rdates' => $rdates, 'exdates' => $exdates];

                update_post_meta($post_id, 'event_recurrence_rule', wp_json_encode($full_rrules));
				update_post_meta($post_id, 'event_repeat_dates', wp_json_encode($all_event_dates));
				self::set_next_occurrence($post_id, $all_event_dates);
                self::update_event_occurrences_table($post_id, $all_event_dates);
            }
        }
    }

    public static function set_next_occurrence($id, $dates) {
        if (!empty($dates) && is_array($dates)) {
            $now = current_time('timestamp');

            $next_occurrence_date = null;
            $next_occurrence = null;

            foreach ($dates as $date) {
                if (!isset($date['date']) || empty($date['date'])) {
                    continue;
                }

                $event_timestamp = strtotime($date['date']);

                if ($event_timestamp >= $now && ($next_occurrence_date === null || $event_timestamp < $next_occurrence_date)) {
                    $next_occurrence_date = $date['date'];
					$next_occurrence = $date;
                }
            }

            $current_value = get_post_meta($id, 'event_next_occurrence', true);
            if ($next_occurrence_date && is_array($next_occurrence)) {
                update_post_meta($id, 'event_next_occurrence', $next_occurrence['date']);
                update_post_meta($id, 'event_next_occurrence_all_day', $next_occurrence['all_day']);
                update_post_meta($id, 'event_next_occurrence_start_time', $next_occurrence['start_time']);
                update_post_meta($id, 'event_next_occurrence_end_time', $next_occurrence['end_time']);
            } else {
                $last = end($dates);
                if ($current_value !== $last['date']) {
                    update_post_meta($id, 'event_next_occurrence', $last['date']);
                    update_post_meta($id, 'event_next_occurrence_all_day', $last['all_day']);
                    update_post_meta($id, 'event_next_occurrence_start_time', $last['start_time']);
                    update_post_meta($id, 'event_next_occurrence_end_time', $last['end_time']);
                }
            }
        }
    }

    public static function custom_event_query_clauses($clauses, $query) {
        // If the 'show all occurrences' setting is enabled, do nothing here.
        if (get_option('kraken_events_show_all_occurrences')) {
            return $clauses;
        }

        // Check if we are on the frontend, querying for our event post type, and a start_date is provided
        if (!is_admin() && $query->get('post_type') === self::$event_slug && !empty($query->get('kraken_event_start_date'))) {
            $start_date_str = $query->get('kraken_event_start_date');

            // Validate the date. Expecting 'Ymd' format.
            $start_date = \DateTime::createFromFormat('Ymd', $start_date_str);
            if (!$start_date) {
                return $clauses; // Invalid date format, return original clauses
            }

            global $wpdb;
            $occurrences_table = $wpdb->prefix . 'kraken_event_occurrences';
            $start_date_sql = $start_date->format('Y-m-d');

            // This new, more advanced subquery finds the single next occurrence for each event,
            // respecting both date and time, and provides all its details for sorting.
            $clauses['join'] .= "
                INNER JOIN (
                    SELECT
                        t1.post_id,
                        t1.occurrence_date AS next_occurrence_date,
                        t1.occurrence_start_time,
                        t1.is_all_day
                    FROM
                        {$occurrences_table} AS t1
                    WHERE t1.id = (
                        SELECT id
                        FROM {$occurrences_table} AS t2
                        WHERE t2.post_id = t1.post_id AND t2.occurrence_date >= '{$start_date_sql}'
                        ORDER BY t2.occurrence_date ASC, t2.is_all_day ASC, t2.occurrence_start_time ASC
                        LIMIT 1
                    )
                ) AS next_occurrences ON {$wpdb->posts}.ID = next_occurrences.post_id";

            // Now, we construct the multi-level ORDER BY clause.
            $clauses['orderby'] = 'next_occurrences.next_occurrence_date ASC, next_occurrences.is_all_day ASC, next_occurrences.occurrence_start_time ASC';
        }

        return $clauses;
    }

    public static function create_event_occurrences_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kraken_event_occurrences';
        $charset_collate = $wpdb->get_charset_collate();

        // Check if the table already exists before creating it.
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                post_id BIGINT(20) NOT NULL,
                occurrence_date DATE NOT NULL,
                occurrence_start_time TIME,
                occurrence_end_time TIME,
                is_all_day BOOLEAN NOT NULL DEFAULT 0,
                PRIMARY KEY  (id),
                KEY post_id (post_id),
                KEY occurrence_date (occurrence_date)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Runs the backfill process for event occurrences and meta.
     * This function calls updateEventsMeta to ensure that the new 'event_recurrence_rule'
     * meta field is created and that the occurrences table is populated correctly.
     *
     * @return int The number of events processed.
     */
    public static function run_backfill_process() {
        $args = [
            'post_type'      => self::$event_slug,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids', // Only get post IDs to be more efficient
        ];
        $event_ids = get_posts($args);

        if (empty($event_ids)) {
            return 0;
        }

        foreach ($event_ids as $event_id) {
            // We need to build the same data structure that updateEventsMeta expects.
            // This logic is borrowed from the process_acf_event_meta function.
            if (!function_exists('get_field_object')) {
                continue;
            }

            $data = [];
            $fields = get_fields($event_id);
            if ($fields) {
                foreach ($fields as $key => $value) {
                    $field = get_field_object($key, $event_id);
                    if ($field) {
                        $data[$key] = [
                            "field_name" => $field["name"],
                            "value"      => $value
                        ];
                    }
                }
            }

            // By calling updateEventsMeta, we trigger the entire processing logic,
            // which handles everything we need in one go.
            if (!empty($data)) {
                self::updateEventsMeta($event_id, $data);
            }
        }

        return count($event_ids);
    }

    public static function update_event_occurrences_table($post_id, $all_event_dates) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kraken_event_occurrences';

        // First, delete all existing occurrences for this post to prevent duplicates
        $wpdb->delete($table_name, ['post_id' => $post_id], ['%d']);

        if (empty($all_event_dates)) { return; }

        foreach ($all_event_dates as $occurrence) {
            if (empty($occurrence['date'])) { continue; }

            // The date is in 'Ymd' format, convert it to 'Y-m-d' for the DATE column
            $date_obj = \DateTime::createFromFormat('Ymd', $occurrence['date']);
            if (!$date_obj) { continue; }

            $wpdb->insert(
                $table_name,
                [
                    'post_id'               => $post_id,
                    'occurrence_date'       => $date_obj->format('Y-m-d'),
                    'occurrence_start_time' => $occurrence['start_time'],
                    'occurrence_end_time'   => $occurrence['end_time'],
                    'is_all_day'            => !empty($occurrence['all_day']) ? 1 : 0,
                ],
                [
                    '%d', // post_id
                    '%s', // occurrence_date
                    '%s', // occurrence_start_time
                    '%s', // occurrence_end_time
                    '%d', // is_all_day
                ]
            );
        }
    }
}
