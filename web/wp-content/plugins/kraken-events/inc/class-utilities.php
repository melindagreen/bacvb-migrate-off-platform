<?php
/**
 * Reusable utility functions intended for use in front-end templates and blocks
 */
namespace MaddenMedia\KrakenEvents;
use RRule\RRule;
use RRule\RSet;

class Utilities {

    private static function build_rule_set_from_meta($id) {
        $meta = get_post_meta($id, 'event_recurrence_rule', true);
        $data = json_decode($meta, true);
        if (empty($data) || !is_array($data)) return null;

        $rule_set = new RSet();
        foreach($data['rrules'] ?? [] as $rrule_string) $rule_set->addRRule($rrule_string);
		foreach($data['rdates'] ?? [] as $rdate_data) $rule_set->addDate(new \DateTime($rdate_data));
		foreach($data['exdates'] ?? [] as $exdate_data) $rule_set->addExDate(new \DateTime($exdate_data));

        return $rule_set;
    }

    /**
     * Gets a human-readable string describing the event's recurrence.
     * @param int $id The post ID of the event.
     * @param string $locale The language for the output, e.g., 'en', 'fr', 'es'.
     * @return string A description of the recurrence pattern.
     */
    public static function get_event_recurrence_pattern($id, $locale = 'en') {
        $rule_set = self::build_rule_set_from_meta($id);

        // If there's no rule set, the event doesn't repeat.
        if (!$rule_set) { return ''; }

        $patterns = [];

        // Get human-readable text for the main recurrence rules
        $rrules = $rule_set->getRRules();
        if (!empty($rrules)) {
            foreach ($rrules as $rule) {
                // Use the humanReadable() method to get the text
                $patterns[] = $rule->humanReadable([
					'locale' 		=> $locale,
					'date_format' 	=> \IntlDateFormatter::MEDIUM,
					'explicit_infinite' => false,
					'include_start' => false,
					'include_until'	=> false
				]);
            }
        }

		/*
		// This could be used to get the full list of occurrences
		// and exclusions
        $rdates = $rule_set->getDates();
        if (!empty($rdates)) {
            // Format each DateTime object into a readable string
            $date_strings = array_map(function($date) {
                return $date->format('F j, Y'); // e.g., "September 18, 2025"
            }, $rdates);
            $patterns[] = '<strong>Also occurs on:</strong> ' . implode(', ', $date_strings);
        }

        // List any specifically excluded dates (ExDates)
        $exdates = $rule_set->getExDates();
        if (!empty($exdates)) {
            // Format each DateTime object into a readable string
            $date_strings = array_map(function($date) {
                return $date->format('F j, Y');
            }, $exdates);
            $patterns[] = '<strong>Excluding:</strong> ' . implode(', ', $date_strings);
        }
		*/

        // If after all checks there are no patterns, it's a non-repeating event.
        if (empty($patterns)) {
            return '';
        }

        // Combine all the collected strings with line breaks for clean display.
        return implode('<br>', $patterns);
    }

    /**
     * Fetches event ids that occur within a date range using the custom occurrences table.
     * This function is now more flexible and accepts either a DateTime object or a date string.
     *
     * @param string|object $start_date The start of the date range (DateTime object or string like 'Y-m-d').
     * @param string|object $end_date   The end of the date range (DateTime object or string like 'Y-m-d').
     * @return array An array of post IDs for events that occur in the range.
     */
    public static function get_events_between_dates($start_date = "", $end_date = "") {
        // Start and end dates are required.
        if (empty($start_date) || empty($end_date)) {
            return [];
        }

        global $wpdb;
        $occurrences_table = $wpdb->prefix . 'kraken_event_occurrences';

        try {
            // Check if the provided start_date is already a DateTime object. If not, create one.
            $start = ($start_date instanceof \DateTime) ? $start_date : new \DateTime($start_date);

            // Do the same for the end_date.
            $end = ($end_date instanceof \DateTime) ? $end_date : new \DateTime($end_date);

        } catch (\Exception $e) {
            // Handle invalid date strings or other errors gracefully
            return [];
        }

        // Format the dates into the 'Y-m-d' format required for SQL's BETWEEN clause.
        $start_sql = $start->format('Y-m-d');
        $end_sql   = $end->format('Y-m-d');

        // This is a direct, fast SQL query to get the unique post IDs.
        $post_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT post_id FROM {$occurrences_table} WHERE occurrence_date BETWEEN %s AND %s",
            $start_sql,
            $end_sql
        ));

        // The query returns an array of strings, so we convert them to integers.
        return array_map('intval', $post_ids);
    }

	/**
	* Format an event start & end date
	* @param object $start
    * start date as returned by ACF
	* @param object $end
    * end date as returned by ACF
	* @param string $separator
    * separator shown between start and end dates
	* @param string $formatShort
    * date format for start date when start & end are both shown
	* @param string $formatLong
    * date format when only one date is show and for end date
	* @return string
	*/
    public static function formatEventDateRange(
        $start = "",
        $end = "",
        $separator = '—',
        $formatShort = 'M j',
        $formatLong = 'M j, Y'
        ) {

        if ($start) {
            $start_date = \DateTime::createFromFormat('Ymd', $start);
        }

        if ($end) {
            $end_date   = \DateTime::createFromFormat('Ymd', $end);
        }

        $string = "";

        if ($start) {
            if ($end && $start != $end && $end > $start) {
                $string .= $start_date->format($formatShort).$separator.$end_date->format($formatLong);
            } else {
                $string .= $start_date->format($formatLong);
            }
        } elseif ($end) {
            $string .= $end_date->format($formatLong);
        }

        return $string;
    }

    /**
	* Format a single date
	* @param object $date
    * date as returned by ACF
	* @param string $format
	* @return string
	*/
    public static function formatSingleEventDate(
        $date = "",
        $format = 'Y-m-d'
        ) {

        if ($date) {
            $new_date = \DateTime::createFromFormat('Ymd', $date);
            return $new_date->format($format);
        }

        return;
    }

    /**
     * Format event time range with start & end times
	* @param object $start
    * start time as returned by ACF
	* @param object $end
    * end time as returned by ACF
	* @param boolean $allDay
    * if event runs all day
	* @param string $separator
    * separator shown between start and end times
	* @param string $format
    * time format
	* @param string $startTimePrefix
    * text shown when only start time is available
	* @param string $endTimePrefix
    * text shown when only end time is available
	* @param string $allDayText
    * text shown for all day events
	* @return string
	*/
    public static function formatEventTimeRange(
        $start = "",
        $end = "",
        $allDay = false,
        $separator = '—',
        $format = 'g:ia',
        $startTimePrefix = "Starts at ",
        $endTimePrefix = "Ends at ",
        $allDayText = "All day"
        ) {

        $string = '';

        if ($allDay) {
            $string = $allDayText;
        } else {
            //these are the default values set when a start/end time are not set in the plugin
            //if both start & end time are the default values, don't display a time
            if ($start === "00:00:00" && $end === "23:59") {
                return $string;
            }
            if ($start) {
                if ($end && $start !== $end) {
                    $string .= date($format, strtotime($start));
                    $string .= $separator;
                    $string .= date($format, strtotime($end));
                } else {
                    $string .= $startTimePrefix.date($format, strtotime($start));
                }
            } elseif ($end) {
                $string = $endTimePrefix.date($format, strtotime($end));
            }
        }

        return $string;
    }

    /**
     * Returns all recurring repeat dates in the future
	 * Defaults to today
	 * Optionally uses a defined start date to return dates after the specified date instead
	 * Optionally uses a defined end date to restrict the range of dates returned
	* @param array $dates
    * array of all dates as returned by ACF field event_repeat_dates
	* @param string $start_date
	* @param string $end_date
	* @return array
	*/
    public static function recurringDatesList($dates, $start_date = "", $end_date = "") {

        if (is_string($dates)) {
            $dates = json_decode($dates, true);
        }

        if (!$dates || !is_array($dates)) {
            return null;
        }

		if (!$start_date) {
        	$start_date = date('Ymd');
		} else {
            // Check if the provided start_date is already a DateTime object. If not, create one.
			//make sure start_date is Ymd format
            $start_date = ($start_date instanceof \DateTime) ? $start_date : new \DateTime($start_date);
			$start_date = $start_date->format('Ymd');
		}

		if ($end_date) {
			//make sure end_date is Ymd format
            $end_date = ($end_date instanceof \DateTime) ? $end_date : new \DateTime($end_date);
			$end_date = $end_date->format('Ymd');
		}

        $futureDates = [];

        foreach ($dates as $date) {
            if (!isset($date['date'])) { continue; }
            if ($date['date'] < $start_date) { continue; }
			if ($end_date && $date['date'] > $end_date) { continue; }
            array_push($futureDates, $date);
        }

		// Sort by date before returning
        usort($futureDates, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return $futureDates;
    }

    /**
     * Format the weekly recurrence option
	* @param array $days
    * repeater field returned by ACF field event_weekly
	* @return string
	*/
    public static function formatWeeklyRecurrence($days) {
        $days = array_map('ucfirst', $days);
        return implode(', ', $days);
    }

    /**
     * Format the monthly by date option
	* @param array $day
    * day number as returned by ACF
	* @param string $prefix
    * text displayed before the number
	* @return string
	*/
    public static function formatMonthlyDateRecurrence($day, $prefix = 'Every month on the ') {
        //formats the number with the ordinal
        $ordinal = date('jS', mktime(0, 0, 0, 0, $day));
        return $prefix.$ordinal;
    }

    /**
     * Format the monthly by day of the week option
	* @param array $day
    * day number as returned by ACF
	* @param array $week
    * week number as returned by ACF
	* @return string
	*/
    public static function formatMonthlyDotwRecurrence($day, $week) {

        $ordinal = "";
        switch($week) {
            case "1":
                $ordinal = "1st";
                break;
            case "2":
                $ordinal = "2nd";
                break;
            case "3":
                $ordinal = "3rd";
                break;
            case "4":
                $ordinal = "4th";
                break;
        }

        return $ordinal.' '.ucfirst($day);
    }

    /**
     * Used in the Eventastic template
     */
    public static function displayEventDates($id) {
        ob_start();

        echo '<div class="event-date">';
        if ( file_exists( __DIR__ . '/../icons/calendar.php' ) ) :
            include __DIR__ . '/../icons/calendar.php';
        endif;

        $next_occurrence = get_field('event_next_occurrence', $id);
        if ( $next_occurrence ) :
            echo self::formatSingleEventDate($next_occurrence, 'M j');
        endif;

        $display_recurring  = false;
        $recurring_output   = '';

        $recurrence = get_field('events_recurrence_options', $id);
        switch ($recurrence) {
            case "daily":
                $display_recurring = true;
                break;
            case "specific_dates":
                $display_recurring = true;
                break;
            case "weekly":
                $display_recurring = true;
                break;
            case "monthly_by_date":
                $display_recurring = true;
                break;
            case "monthly_by_dotw":
                $display_recurring = true;
                break;
            default:
                $display_recurring = false;
                break;
        }

        if ($display_recurring) {
            $recurring_dates = self::recurringDatesList(get_field('event_repeat_dates', $id));
            if (!empty($recurring_dates)) {
                $i = 0;
                foreach ($recurring_dates as $date) {
                    $recurring_output .= self::formatSingleEventDate($date['date'], 'M j') ;
                    if ( $i < count($recurring_dates) && $i < 6) :
                        $recurring_output .= ', ';
                    endif;
                    if ( $i == 6 ) :

                        $recurring_output .= ' and ' . (int) count($recurring_dates) - $i . ' more';
                        break;
                    endif;
                    $i++;
                }
            } else {
                $display_recurring = false;
            }
        }

        if ($display_recurring) {
            //echo '<span class="small">'. $recurring_output.'</span>';
            echo '<span class="small">This event occurs on multiple dates</span>';
        }

        echo '</div>';

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
