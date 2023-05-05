<?php
/* THIS FILE HAS NO ACTUAL CODE IMPACT ON THE THEME
 *
 * The following code can be copy/pasted in functions.php (or another function file like layouts.php)
 * Once placed in your themes functions file editing the main function will override the default
 * ajax function called by the example eventstic-events.js file. This is needed if the structure
 * of the event boxes is changed
*/

/**
     * Returns a json array with html for the main events templat
     * By default it looks for the post variables startDate, endDate, keyword, and categories
     *
     * @return json The html output
     */
remove_action( 'wp_ajax_nopriv_eventasticGetEvents', 'eventasticGetEvents' );
remove_action( 'wp_ajax_eventasticGetEvents', 'eventasticGetEvents' );
add_action( 'wp_ajax_nopriv_eventasticGetEvents', 'eventasticGetEventsNew' );
add_action( 'wp_ajax_eventasticGetEvents', 'eventasticGetEventsNew' );
function eventasticGetEventsNew() {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $keyword = $_POST['keyword'];
    $categories = $_POST['categories'];
    $args = array(
        'posts_per_page' => -1
    );

    if ($startDate) $args['eventastic_start_date'] = $startDate;
    if ($endDate) $args['eventastic_end_date'] = $endDate;
    if ($categories) $args['tax_query'] = array(
        array(
            'taxonomy' => 'eventastic_categories',
            'field' => 'term_id',
            'terms' => $categories
        )
    );
    if ($keyword) $args['s'] = $keyword;
    $events = eventastic_get_events($args);
    $html = '';
    if ($events) {
        $i = 0;
        foreach($events as $event){
            $i++;
            $eventID = $event->ID;
            $eventMeta = eventastic_get_event_meta($eventID);
            $image = get_the_post_thumbnail_url($eventID, 'full');
            $startDate = strtotime($eventMeta['start_date']);
            $endDate = strtotime($eventMeta['end_date']);
            $recurring = ($eventMeta['event_end'] == 'infinite') ? true : false;
            if ($recurring) {
                $recurringDays = $eventMeta['recurring_days'];
                $recurringRepeat = $eventMeta['recurring_repeat'];
            }
            $allDay = $eventMeta['event_all_day'];
            if (!$allDay) {
                $startTime = $eventMeta['start_time'];
                $endTime = $eventMeta['end_time'];
            }
            $html .= '<a href="'.get_the_permalink($eventID).'" class="event';
            if ($i > 20) $html .= ' hidden';
            $html .= '">';
            if ($image) {
                $html .= '<div class="image">';
                $html .= '<img src="'.$image.'" />';
                $html .= '</div>';
            }
            $html .= '<div class="text">';
            $html .= '<h3>'.get_the_title($eventID).'</h3>';

            $html .= '<div class="date">';
            if ($recurring) {
                if ($endDate && $startDate != $endDate) {
                    $html .= date('M j, Y', $startDate).' to '.date('M j, Y', $endDate).'<br>';
                }
                $html .= 'every ';
                switch ($recurringRepeat) {
                    case '1':
                        $html .= '1st ';
                        break;
                    case '2':
                        $html .= '2nd ';
                        break;
                    case '3':
                        $html .= '3rd ';
                        break;
                    case '4':
                        $html .= '4th ';
                        break;
                }
                $i = 0;
                foreach($recurringDays as $day) {
                    $i++;
                    if ($i > 1) $html .= ', ';
                    $html .= $day;
                }
            } else {
                $html .= date('M j, Y', $startDate);
                if ($endDate && $startDate != $endDate) $html .= ' to '.date('M j, Y', $endDate);
            }
            $html .= '</div>';
            $html .= '<p>'.wp_trim_words(strip_tags(get_the_content(null, null, $eventID)), 50).'</p>';
            $html .= '<button class="alt grey small">View Details</button>';
            $html .= '</div>';
            $html .= '</a>';
        }
        if (count($events > 20)) $html .= '<div class="showMoreButton">Show More</div>';
    } else $html .= '<p class="noResults">No Events were found, try adjusting your filters.</p>';

    echo json_encode(array('html' => $html));
    wp_die();
}
