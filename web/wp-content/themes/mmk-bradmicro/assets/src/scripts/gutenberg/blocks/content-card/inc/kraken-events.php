<?php
use MaddenMedia\KrakenEvents\Utilities as E;

function displayEventDates($id) { 
    ob_start();

    echo '<div class="event-date">';

        $recurrence = get_field('events_recurrence_options', $id);
        switch ($recurrence) {
            case "specific_dates":
                $today = date("Ymd");
                $dates = get_field('event_specific_dates', $id);
                if ($dates) {

                    $pastDates = [];
                    $futureDates = [];

                    foreach ($dates as $date) {
                        $date = $date['date'];
                        if ($date && $today <= $date) {
                            array_push($futureDates, $date);
                        } else {
                            array_push($pastDates, $date);
                        }
                    }

                    //display future dates if any exist
                    $i = 0;
                    if (!empty($futureDates)) {
                        foreach($futureDates as $date) {
                            $date = \DateTime::createFromFormat('Ymd', $date);
                            if ($i > 0) {
                                echo ', ';
                            }
                            echo $date->format('M j');
                            if ($i > 3) {
                                echo ' & more';
                                break;
                            }
                            $i++;
                        }
                    } else {
                        //display all past dates if no dates exist in the future
                        foreach($pastDates as $date) {
                            $date = \DateTime::createFromFormat('Ymd', $date);
                            if ($i > 0) {
                                echo ', ';
                            }
                            echo $date->format('M j');
                            if ($i > 3) {
                                echo ' & more';
                                break;
                            }
                            $i++;
                        }
                    }
                }
                break;
            case "weekly":
            case "monthly_by_date":
            case "monthly_by_dotw":						
                $recurring_dates = E::recurringDatesList(get_field('event_repeat_dates', $id));
                if (!empty($recurring_dates)) {
                    $i = 0;
                    foreach ($recurring_dates as $date) {
                        $date = E::formatSingleEventDate($date['date'], 'M j');
                        if ($i > 0) {
                            echo ', ';
                        }
                        echo $date;
                        if ($i > 3) {
                            echo ' & more';
                            break;
                        }
                        $i++;
                    }
                }
                break;
            default:
                $startDate 	= get_field('event_start_date', $id);
                $endDate 	= get_field('event_end_date', $id);
                if ($startDate) {
                    echo E::formatSingleEventDate($startDate, 'M j');
                    if ($endDate && $startDate !== $endDate) {			
                        echo ' - ';
                        echo E::formatSingleEventDate($endDate, 'M j');
                    }
                }		
                break;			
        }

    echo '</div>';

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

function displayEventTimes($id) {
    ob_start();

    $isAllDay   = get_field('event_all_day', $id);
    $start_time = get_field('event_start_time', $id);
    $end_time   = get_field('event_end_time', $id);

    $time_output = E::formatEventTimeRange($start_time, $end_time, $isAllDay);
    
    if ($time_output) {
        echo '<div class="event-time">';
        echo $time_output; 
        echo '</div>';
    }

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

function displayEventLocation($id) {
    ob_start();

    $addrMulti = get_field('events_addr_multi', $id);
    if ($addrMulti) {
        echo '<div class="event-location">';
        echo $addrMulti;
        echo '</div>';
    } else {
        $addr1 = get_field('events_addr1', $id);
        if ($addr1) {
            $addr2  = get_field('events_addr2', $id);
            $city   = get_field('events_city', $id);
            $state  = get_field('events_state', $id);
            $zip    = get_field('events_zip', $id);

            echo '<address class="event-location">';
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
    }

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}