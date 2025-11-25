<?php

/**
 * Functions for rendering events on site pages
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

// no direct load
if (! defined('ABSPATH')) {
    die('-1');
}

require_once(__DIR__.'/../admin/SettingsAdminLayout.php');
require_once(__DIR__.'/../library/Query.php');
require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Library\Query as Query;
use Eventastic\Admin\SettingsAdminLayout as SettingsAdminLayout;
use Eventastic\Library\Utilities as Utilities;

if (! class_exists('Eventastic\Eventastic')) {
    die('Eventastic class missing');
} else {

    /**
     * Get all current events
     *
     * All standard WP query args are accepted, plus the following:
     *
     * @param array $args {
     *        @type string $start_date        Minimum start date for matching events
     *        @type string $end_date            Maximum end date for matching events
     *        @type string $exact_start_date    A specific start date
     *        @type string $category_slug        A specific event category slug
     * }
     * @param boolean  $includePostMeta Include the post meta for each event?
     * @param boolean  $full If the full query object is required or just an array of event posts
     * @return array List of event posts
     */
    function eventastic_get_events ($args=array(), $includePostMeta=false, $full=false) {

        return apply_filters('eventastic_get_events', Query::get_events($args, $includePostMeta, $full), $args, $includePostMeta, $full);
    }

    function eventastic_get_events_date_ordered ($args=array(), $includePostMeta=false, $full=false) {

        return apply_filters('eventastic_get_events_date_ordered', Query::get_events_date_ordered($args), $args );
    }

    /**
     * Returns all meta data about a listing - usually used on a listing template page
     *
     * @param int  $postId The post id
     * @return array List of event meta
     */
    function eventastic_get_event_meta ($postId) {

        return apply_filters('eventastic_get_event_meta', Query::get_event_meta($postId), $postId);
    }

    /**
     * Returns all event venues
     *
     * @param boolean  $hideEmpty Hide empty terms?
     * @return array List of event venues
     */
    function eventastic_get_venues ($hideEmpty=false) {

        return apply_filters('eventastic_get_venues', Query::get_venues($hideEmpty), $hideEmpty);
    }

    /**
     * Returns all event organizers
     *
     * @param boolean  $hideEmpty Hide empty terms?
     * @return array List of event organizers
     */
    function eventastic_get_organizers ($hideEmpty=false) {

        return apply_filters('eventastic_get_organizers', Query::get_organizers($hideEmpty), $hideEmpty);
    }

    /**
     * Returns all event categories
     *
     * @param boolean  $hideEmpty Hide empty terms?
     * @param boolean  $assignColors Assign a hex color from settings to each color?
     * @return array List of event categories
     */
    function eventastic_get_categories ($hideEmpty=false, $assignColors=true) {

        return apply_filters('eventastic_get_categories', Query::get_categories($hideEmpty, $assignColors), $hideEmpty, $assignColors);
    }

    /**
     * Returns a rendered event location map for the passed post id
     *
     * PENDING: The user probably got back a lat/lng coordinate set with the id already - this
     *    requires a db query to load it again. Probably not the most efficient, but the code
     *    feels cleaner.
     *
     * @param int  $postId The post id
     * @param string $mapCSSId The CSS Id to use for the map div
     * @param string $mapIconLeafletJSON The options for a custom Leaflet L.divIcon map icon - build these
     *                                     as a PHP array, and this function will json_encode those automatically
     * @return string The event map
     */
    function eventastic_render_event_map ($postId, $mapCSSId="map", $mapIconLeafletJSON=null) {

        if (get_option(SettingsAdminLayout::SETTING_KEY_GOOGLE_API["key"], "") == "") {
            // use leaflet for maps
            wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.css');
            wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.js', array(), '', true);
        }

        return apply_filters('eventastic_render_event_map', Query::render_event_map($postId, $mapCSSId, $mapIconLeafletJSON), $postId, $mapCSSId, $mapIconLeafletJSON);
    }

    /**
     * Returns a rendered event calendar
     *
     * @param string $calendarCSSId The CSS Id to use for the calendar div
     * @param object $calendarEvents The object array of events to add (if not provided, all will be
     *                                 loaded) - this should include all post meta for each item as well
     * @param array $imageSizes array of sizes ('Full','Large', etc); ['All'] will return all (resource heavy, recommend against). 
     * If empty, no images returned     
     * @return string The event calendar
     */
    function eventastic_render_event_calendar ($calendarCSSId="calendar", $calendarEvents=null, $imageSizes = []) {

        // our scripts that we need
        wp_enqueue_style('fullcalendar', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.css');
        wp_enqueue_style('fullcalendar_daygrid', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.css');
        wp_enqueue_style('fullcalendar_timegrid', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/timegrid/main.min.css');

        wp_enqueue_script('fullcalendar', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.js', array(), '', true);
        wp_enqueue_script('fullcalendar_daygrid', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.js', array(), '', true);
        wp_enqueue_script('fullcalendar_timegrid', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/timegrid/main.min.js', array(), '', true);
        wp_enqueue_script('fullcalendar_interaction', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/interaction/main.min.js', array(), '', true);
        wp_enqueue_script('fullcalendar_rrule_cdn', 'https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js', array(), '', true);
        wp_enqueue_script('fullcalendar_rrule', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/rrule/main.min.js', array(), '', true);

        return apply_filters('eventastic_render_event_map', Query::render_event_calendar($calendarCSSId, $calendarEvents, $imageSizes), $calendarCSSId, $calendarEvents,$imageSizes);    
    }

    /**
     * Returns a rendered event calendar Version 2
     *
     */
    function eventastic_render_calendar ($calendarCSSId="calendar", $calendarEvents=null, $imageSizes = []) {
        // our scripts that we need
        wp_enqueue_script('fullcalendar_full', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js', array(), '', true);
        if( !Utilities::getRecurrenceVersion() ){
            return apply_filters('eventastic_render_event_map', Query::render_event_calendar($calendarCSSId, $calendarEvents, $imageSizes), $calendarCSSId, $calendarEvents,$imageSizes);
        } 
    }


    /**
     * Returns a json array with html for the main events templat
     * By default it looks for the post variables startDate, endDate, keyword, and categories
     *
     * @return json The html output
     */
    add_action( 'wp_ajax_nopriv_eventasticGetEvents', 'eventasticGetEvents' );
    add_action( 'wp_ajax_eventasticGetEvents', 'eventasticGetEvents' );
    function eventasticGetEvents() {
        $startDate = (isset($_POST['startDate'])) ? $_POST['startDate'] : null;
        $endDate = (isset($_POST['endDate'])) ? $_POST['endDate'] : null;
        $keyword = (isset($_POST['keyword'])) ? $_POST['keyword'] : null;
        $categories = (isset($_POST['categories'])) ? $_POST['categories'] : '';
        $args = array(
            'posts_per_page' => -1
        );

        if ($startDate) $args['start_date'] = $startDate;
        if ($endDate) $args['end_date'] = $endDate;
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
            $eventCount = 0;
            $fullCalendarEventsSource = [];
            foreach($events as $event){
                $eventCount++;
                $eventID = $event->ID;

                $eventMeta = eventastic_get_event_meta($eventID);
                $image = get_the_post_thumbnail_url($eventID, 'full');
                $startDate = strtotime($eventMeta['start_date']);
                $endDate = strtotime($eventMeta['end_date']);
                $recurring = ( (isset($eventMeta['event_end'])) && ($eventMeta['event_end'] == 'infinite') ) ? true : false;
                if ( !Utilities::getRecurrenceVersion() ){
                    if ($recurring) {
                        $recurringDays = $eventMeta['recurring_days'];
                        $recurringRepeat = $eventMeta['recurring_repeat'];
                    }
                }
                $allDay = (isset($eventMeta['event_all_day'])) ? $eventMeta['event_all_day'] : false;
                if (!$allDay) {
                    $startTime = (isset($eventMeta['start_time'])) ? $eventMeta['start_time'] : null;
                    $endTime = (isset($eventMeta['end_time'])) ? $eventMeta['end_time'] : null; 
                }
                $html .= '<a href="'.get_the_permalink($eventID).'" class="event';
                if ($eventCount > 20) $html .= ' hidden';
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
                    $days = 0;
                    foreach($recurringDays as $day) {
                        $days++;
                        if ($days > 1) $html .= ', ';
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
                if ( Utilities::getRecurrenceVersion() ){
                    if( "specific_days" == $eventMeta['recurrence_options'] || "pattern" == $eventMeta['recurrence_options'] ){
//error_log('has spec days');
//error_log(print_r($eventMeta,true));
                        $foreachDates = $eventMeta['repeat_dates'];
                        if( "pattern" == $eventMeta['recurrence_options'] ){
                            $foreachDates = $eventMeta['pattern_dates'];
                        }
                        foreach( $foreachDates  as $specific_date ){
                            $eventSource = (object)[ 
                                'title' => get_the_title($eventID),
                                'url' => get_the_permalink($eventID),
                                'start' => $specific_date,
                                'end' =>  $specific_date  
                            ];
                            $fullCalendarEventsSource[] = $eventSource;

                        }

                    }
                    else{
                        $eventSource = (object)[ 
                            'title' => get_the_title($eventID),
                            'url' => get_the_permalink($eventID),
                            'start' => date('Y-m-j', $startDate),
                            'end' => date('Y-m-j', ( $endDate + 60*60*24 )) // adds a date for exclusive date reckoning   
                        ];                    
                        $fullCalendarEventsSource[] = $eventSource;

                    }
                }
                else{
                    $eventSource = (object)[ 
                        'title' => get_the_title($eventID),
                        'url' => get_the_permalink($eventID),
                        'start' => date('Y-m-j', $startDate),
                        'end' =>  date('Y-m-j',$endDate)  
                    ];                    
                    $fullCalendarEventsSource[] = $eventSource;
                }

            }
            if ($eventCount > 20) $html .= '<div class="showMoreButton">Show More</div>';
        } else $html .= '<p class="noResults">No Events were found, try adjusting your filters.</p>';

            echo json_encode(array(
                'html' => $html,
                'fullCalendarEventsSource' => $fullCalendarEventsSource
            ));
            wp_die();
        }
    }

    function event_schema_for_events( $post_types ) {
        $post_types[] = 'event';
        return $post_types;
    }
    function change_webpage_to_event_posting( $data ) {
       
        global $post;

        if ( $post->post_type == Utilities::getPluginPostType() && is_singular() ) {

            $data['@type'] = 'Event';
        
            $meta = eventastic_get_event_meta($post->ID);

            $addr1 = (isset($meta['addr1'])) ? $meta['addr1'] : '';
            $addr2 = (isset($meta['addr2'])) ? $meta['addr2'] : '';
            $city = (isset($meta['city'])) ? $meta['city'] : '';
            $zip = (isset($meta['zip'])) ? $meta['zip'] : '';
            $state = (isset($meta['state'])) ? $meta['state'] : '';

            $place = array(
                    "@type" => "Place",
                    "name" => get_bloginfo( 'name' ),
                    "address" => array(
                        "@type" => "PostalAddress",
                        "streetAddress" =>  $addr1. " " . $addr2,
                        "addressLocality" => $city,
                        "postalCode" => $zip,
                        "addressRegion" => $state,
                        "addressCountry" => array(
                            "@type" => "Country",
                            "name" => "US"
                            )
        
                        )
                    );
                    
            $data['startDate'] = (isset($meta['start_date'])) ? $meta['start_date'] : '';
            $data['endDate'] = (isset($meta['end_date'])) ? $meta['end_date'] : '';
            $data['description'] = trim(strip_tags($post->post_content));
            $data['image'] = get_the_post_thumbnail_url($post->ID, 'full');
            $data['eventStatus'] = "http://schema.org/EventScheduled";
            $data['location'] = $place;
        
            $remove = ['isPartOf', 'thumbnailUrl', 'datePublished', 'dateModified', 'primaryImageOfPage', 'breadcrumb'];
            $data = array_diff_key($data, array_flip($remove));

        }
    
        return $data;
    }
    function eventastic_add_schema(){
        global $post;
        if (is_singular() && isset($post) && isset($post->post_type)) {
            if ($post->post_type == Utilities::getPluginPostType()) {
                if(class_exists('WPSEO_Options')){   
                    add_filter( 'wpseo_schema_webpage', 'change_webpage_to_event_posting' );    
                    add_filter( 'wpseo_schema_article_post_types', 'event_schema_for_events' );
                }
                if(class_exists('RankMath')){   
                    // no customizing needed
                }
                else{
                eventastic_schema_creator();
                }
            }
        }
    }
    add_action('wp_head', 'eventastic_add_schema');
    function eventastic_schema_creator(){
            global $post;

            $data = [
                "@context" => "http://schema.org",
                "@type" => 'Event'
            ];
        
            $meta = eventastic_get_event_meta($post->ID);

            $addr1 = (isset($meta['addr1'])) ? $meta['addr1'] : '';
            $addr2 = (isset($meta['addr2'])) ? $meta['addr2'] : '';
            $city = (isset($meta['city'])) ? $meta['city'] : '';
            $zip = (isset($meta['zip'])) ? $meta['zip'] : '';
            $state = (isset($meta['state'])) ? $meta['state'] : '';

            $place = array(
                    "@type" => "Place",
                    "name" => get_bloginfo( 'name' ),
                    "address" => array(
                        "@type" => "PostalAddress",
                        "streetAddress" =>  $addr1. " " . $addr2,
                        "addressLocality" => $city,
                        "postalCode" => $zip,
                        "addressRegion" => $state,
                        "addressCountry" => array(
                            "@type" => "Country",
                            "name" => "US"
                            )
        
                        )
                    );
                    
            $data['startDate'] = (isset($meta['start_date'])) ? $meta['start_date'] : '';
            $data['endDate'] = (isset($meta['end_date'])) ? $meta['end_date'] : '';
            $data['description'] = trim(strip_tags($post->post_content));
            $data['image'] = get_the_post_thumbnail_url($post->ID, 'full');
            $data['eventStatus'] = "http://schema.org/EventScheduled";
            $data['location'] = $place;
            echo '<script type="application/ld+json">' . json_encode( $data ) . "</script>";
}
