<?php
use MaddenMedia\KrakenEvents\Utilities as E;

function displayEventDates($id, $start_date, $end_date, $attrs) {

    ob_start();

	$date_string 	= "";	//the next occurrence date
	$date_count 	= 0;	//if more than 1 date we show +X more
	$date_pattern 	= "";	//for displaying recurrence patterns

	$date_format = apply_filters('kraken-core/content-card/event_date_format', 'M j', $id, $attrs);

	$recurrence = get_field('events_recurrence_options', $id);

	switch ($recurrence) {
		case "specific_dates":
		case "weekly":
		case "monthly_by_date":
		case "monthly_by_dotw":
			//Utility function that will get all recurring dates after today or the specified date
			$recurring_dates = E::recurringDatesList(get_field("event_repeat_dates", $id), $start_date, $end_date);

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
			$startDate 	= get_field('event_start_date', $id);
			$endDate 	= get_field('event_end_date', $id);
			if ($startDate) {
				$date_string = E::formatSingleEventDate($startDate, $date_format);
				if ($endDate && $startDate !== $endDate) {
					$date_string .= '<span class="event-end-date"> - ';
					$date_string .= E::formatSingleEventDate($endDate, $date_format);
					$date_string .= '</span>';
				}
			}
			break;
	}

	if ($date_string) {
    	echo '<div class="event-date">';
			include __DIR__ . '/../icons/calendar.php';
			echo '<span class="next-event-date">'.$date_string.'</span>';
			if ($date_count > 0) {
				echo '<span class="event-occurrence-count"> +'.$date_count.' more</span>';
			}
    	echo '</div>';
		if ($date_pattern) {
			echo '<div class="event-occurrence-pattern">Occurs '.$date_pattern.'</div>';
		}
	}

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

function displayEventTimes($id) {
    ob_start();

    $isAllDay   = get_field('event_next_occurrence_all_day', $id);
    $start_time = get_field('event_next_occurrence_start_time', $id);
    $end_time   = get_field('event_next_occurrence_end_time', $id);

    $time_output = E::formatEventTimeRange($start_time, $end_time, $isAllDay);

    if ($time_output) {
        echo '<div class="event-time">';
		include __DIR__ . '/../icons/clock.php';
        echo $time_output;
        echo '</div>';
    }

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

function displayEventLocation($id) {
	ob_start();

	$event_address_keys = apply_filters('kraken-core/content-card/event_address_keys', [
		'multi' => 'events_addr_multi',
		'venue'	=> 'events_venue',
		'addr1'	=> 'events_addr1',
		'addr2' => 'events_addr2',
		'city'	=> 'events_city',
		'state'	=> 'events_state',
		'zip'	=> 'events_zip'
	]);

	$addrMulti = get_field($event_address_keys['multi'], $id);
	if ($addrMulti) {
		echo '<div class="event-location">';
		include __DIR__ . '/../icons/location.php';
		echo $addrMulti;
		echo '</div>';
	} else {
        $venue = get_field($event_address_keys['venue'], $id);
        $addr1 = get_field($event_address_keys['addr1'], $id);
        if ($venue || $addr1) {

            echo '<div class="event-location">';
			include __DIR__ . '/../icons/location.php';

            if ($venue) {
                echo '<span class="event-venue">'.$venue.'</span>';
            }

            if ($addr1) {
                $addr2  = get_field($event_address_keys['addr2'], $id);
                $city   = get_field($event_address_keys['city'], $id);
                $state  = get_field($event_address_keys['state'], $id);
                $zip    = get_field($event_address_keys['zip'], $id);

                echo '<address class="event-address">';
                echo $addr1;
                if ($addr2) {
                    echo '<br>' . $addr2;
                }
                if ($city) {
                    echo '<br>' . $city;
                }
                if ($state) {
                    echo ', ' . $state;
                }
                if ($zip) {
                    echo ' ' . $zip;
                }
                echo '</address>';
            }

            echo '</div>';
        }
    }

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}
