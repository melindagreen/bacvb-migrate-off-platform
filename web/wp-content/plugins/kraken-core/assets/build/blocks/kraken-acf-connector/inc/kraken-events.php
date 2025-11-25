<?php
use MaddenMedia\KrakenEvents\Utilities as E;

function getEventDates($id, $attrs) {
	//dates
	$today 		= new \DateTime();
	$start_date = get_field('event_start_date', $id);
	$end_date   = get_field('event_end_date', $id);

	//show the next occurrence
	if ($start_date < $today->format('Ymd')) {
		$start_date = $today->format('Ymd');
	}

	$block_output		= "";   //returned output
	$date_string 		= "";	//the next occurrence date
	$date_count 		= 0;	//if more than 1 date we show +X more
	$date_pattern 		= "";	//for displaying recurrence patterns
	$recurring_dates 	= [];	//recurrence

	//Get date format from filter, default to 'M j'
	$date_format = apply_filters('kraken-core/kraken-acf-connector/event_date_format', 'M j', $id, $attrs);

	$recurrence = get_field('events_recurrence_options', $id);
	switch ($recurrence) {
		case "specific_dates":
		case "weekly":
		case "monthly_by_date":
		case "monthly_by_dotw":
			//Utility function that will get all recurring dates after today or the specified date
			$recurring_dates = E::recurringDatesList(get_field("event_repeat_dates"), $start_date, $end_date);

			if (!empty($recurring_dates)) {
				$next_date = reset($recurring_dates);
				$date_string = E::formatSingleEventDate($next_date['date'], $date_format);
				$date_count = count($recurring_dates) - 1;
				$date_pattern = E::get_event_recurrence_pattern($id);
			}

			break;
		default:
			//Single Day Events
			//Consecutive Date Events
			if ($start_date) {
				$date_string = E::formatSingleEventDate($start_date, $date_format);
				if ($end_date && $start_date !== $end_date) {
					$date_string .= ' - ';
					$date_string .= E::formatSingleEventDate($end_date, $date_format);
				}
			}
			break;
	}

	if (!empty($date_string)) {

		if (count($recurring_dates) - 1 > 0) {
			$date_string .= '<span class="event-occurrence-count"> +'.$date_count.' more</span>';
		}

		if ($date_pattern) {
			$date_pattern = apply_filters('kraken-core/kraken-acf-connector/event_date_pattern', '<br/><span class="event-occurrence-pattern">Occurs '.$date_pattern.'</span>', $date_pattern);
			$date_string .= $date_pattern;
		}

		$label = false;
		if ($attrs['displayLabel']) {
			$label = $attrs['customLabelText'] !== "" ? $attrs['customLabelText'] : 'Date:';
		}
		$date_title = "";
		if ($label) {
			$date_title = apply_filters('kraken-core/kraken-acf-connector/event_date_title', '<strong>'.$label.' </strong>');
		}

		$block_output = '<!-- wp:paragraph --><p>'.$date_title.''.$date_string.'</p><!-- /wp:paragraph -->';
	}

	return $block_output;
}

function getAllUpcomingDates($id, $attrs) {
	//recurring dates
	$today 		= new \DateTime();
	$start_date = get_field('event_start_date', $id);
	$end_date   = get_field('event_end_date', $id);

	//show upcoming dates only
	if ($start_date < $today->format('Ymd')) {
		$start_date = $today->format('Ymd');
	}

	$block_output		= "";   //returned output
	$display_recurring  = false;
	$recurring_dates	= [];

	//final pretty output
	$recurring_output   = '';

	$recurrence = get_field('events_recurrence_options', $id);
	switch ($recurrence) {
		case "specific_dates":
		case "weekly":
		case "monthly_by_date":
		case "monthly_by_dotw":
			//Utility function that will get all recurring dates after today or the specified date
			$recurring_dates = E::recurringDatesList(get_field("event_repeat_dates"), $start_date, $end_date);

			if (!empty($recurring_dates)) {
				$display_recurring = true;
			}

			break;
		default:
			break;
	}

	if ($display_recurring && !empty($recurring_dates)) {
		$recurring_output .= '<!-- wp:list {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} --><ul style="margin-top:0;margin-bottom:0" class="wp-block-list">';
		foreach ($recurring_dates as $date) {
			$recurring_output .= '<!-- wp:list-item --><li>';
			$recurring_output .= E::formatSingleEventDate($date['date'], 'M j, Y');
			$time_string = E::formatEventTimeRange($date['start_time'], $date['end_time'], $date['all_day']);
			if ($time_string) {
				$recurring_output .= ' ('.$time_string.')';
			}
			$recurring_output .= '</li><!-- /wp:list-item -->';
		}
		$recurring_output .= '</ul><!-- /wp:list -->';

		//Label can't be disabled for this field.
		$label = $attrs['customLabelText'] !== "" ? $attrs['customLabelText'] : 'View All Upcoming Dates';

		$recurring_title = apply_filters('kraken-core/kraken-acf-connector/event_recurring_title', $label);

		$block_output = '<!-- wp:details --><details class="wp-block-details"><summary>'.$recurring_title.'</summary>'.$recurring_output.'</details><!-- /wp:details -->';
	}

	return $block_output;
}

function getEventTimes($id, $attrs) {

	$block_output		= "";   //returned output
	$time_output = '';
	$recurrence = get_field('events_recurrence_options', $id);

	$is_all_day   	= get_field('event_next_occurrence_all_day', $id);
	$start_time 	= get_field('event_next_occurrence_start_time', $id);
	$end_time   	= get_field('event_next_occurrence_end_time', $id);

	//time fallback
	if ($end_time === "") {
		$end_time = $start_time;
	}

	$time_output = E::formatEventTimeRange($start_time, $end_time, $is_all_day);

	if (!empty($time_output)) {
		$label = false;
		if ($attrs['displayLabel']) {
			$label = $attrs['customLabelText'] !== "" ? $attrs['customLabelText'] : 'Time:';
		}
		$time_title = "";
		if ($label) {
			$time_title = apply_filters('kraken-core/kraken-acf-connector/event_time_title', '<strong>'.$label.' </strong>');
		}
		$block_output = '<!-- wp:paragraph --><p>'.$time_title.''.$time_output.'</p><!-- /wp:paragraph -->';
	}

	return $block_output;
}
