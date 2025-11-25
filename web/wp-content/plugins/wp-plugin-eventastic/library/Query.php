<?php

namespace Eventastic\Library;

/**
 * Queries against the database
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/../admin/SettingsAdminLayout.php');
require_once(__DIR__.'/../admin/MetaBoxAddress.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Admin\SettingsAdminLayout as SettingsAdminLayout;
use Eventastic\Admin\MetaBoxAddress as MetaBoxAddress;
use Eventastic\Admin\MetaBoxDates as MetaBoxDates;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\Utilities as Utilities;

/**
 * Query class that external function script can work with to perform the queries
 *    needed to support custom functions
 */
class Query {
    /**
     * Gets all the events in the database
     *
     * @param object $args The query arguments
     * @param boolean $includePostMeta Include the post meta for each event?
     * @param boolean $full If the full query object is required or just an array of event posts
     * @return array The matching events
     */
    public static function get_events ($args=array(), $includePostMeta=false, $full=false, $config_args = [] ) {

        $defaults = array(
            'post_type'            => Utilities::getPluginPostType(),
            'order'                => 'ASC',
            'posts_per_page'       => -1,
        );
        $args = wp_parse_args($args, $defaults);
        if (isset( $args['keyword'] ) && $args['keyword'] ){
            $args['s'] = $keyword;
        }
        
        // our start and end dates have our set prefix in the db, but are passed w/o it
        $noPrefixStartArg = str_replace(Constants::WP_POST_META_KEY_PREPEND, "", MetaBoxDates::META_KEY_START_DATE["key"]);
        $noPrefixEndArg = str_replace(Constants::WP_POST_META_KEY_PREPEND, "", MetaBoxDates::META_KEY_END_DATE["key"]);
        $exactDateArg = "exact_start_date";

        // add in post meta data if present in args
        $meta_query = null;
        $safe_start_date = date(Constants::DATE_FORMAT_MYSQL, strtotime('1970-01-01'));
        $safe_end_date = date(Constants::DATE_FORMAT_MYSQL, strtotime('2100-12-31')); // safe bet that I'm dead by then
        if (isset($args[$exactDateArg])) {
            $query_start_date = date(Constants::DATE_FORMAT_MYSQL, strtotime($args[$exactDateArg]));
            $query_end_date = $query_start_date;
        } else {
            // no exact date, so see if we have start or finish - if not, use our defaults
            $query_start_date = (! empty($args[$noPrefixStartArg]))
                ? date(Constants::DATE_FORMAT_MYSQL, strtotime($args[$noPrefixStartArg]))
                : $safe_start_date;
            $query_end_date = (! empty($args[$noPrefixEndArg]))
                ? date(Constants::DATE_FORMAT_MYSQL, strtotime($args[$noPrefixEndArg]))
                : $safe_end_date;
        }

        // meta query for dates
        $meta_query = array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array(
                    'key' => MetaBoxDates::META_KEY_END_DATE["key"],
                    'value' =>  $query_start_date,
                    'type'  => 'date',
                    'compare' => '>='
                ),
                array(
                    'key' => MetaBoxDates::META_KEY_END_DATE["key"],
                    'value' =>  "",
                    'type'  => 'text',
                    'compare' => '='
                )
            ),
            array(
                'relation' => 'AND',
                array(
                    'key' => MetaBoxDates::META_KEY_START_DATE["key"],
                    'value' =>  $query_end_date,
                    'type'  => 'date',
                    'compare' => '<='
                ),
                array(
                    'key' => MetaBoxDates::META_KEY_START_DATE["key"],
                    'value' =>  '',
                    'type'  => 'text',
                    'compare' => '!='
                )  
            )
        );

        // any luck?
        if ($meta_query != null) {
            $args['meta_query'] = $meta_query;
            // we don't need theae any longer
            unset($args[MetaBoxDates::META_KEY_START_DATE["key"]]);
            unset($args[MetaBoxDates::META_KEY_END_DATE["key"]]);
            unset($args['exact_event_date']);
        }

        // add in post meta data if present in args
        $tax_query = null;
        if (isset($args['category_slug']) && $args['category_slug']) {
            // add all the taxonomies
            if( is_array($args['category_slug']) ){
                $category_slugs = $args['category_slug'];
            }
            else{
                $category_slugs = explode( ",", $args['category_slug'] );
            }
            $taxes = array();
            if( count( $category_slugs ) > 0 ){
                foreach (array_keys(Constants::PLUGIN_TAXONOMIES) as $taxonomy) {
                    if( 'eventastic_categories' == $taxonomy){
                        $taxes[] = array(
                            'taxonomy' => $taxonomy,
                            'field' => 'slug',
                            'terms' => $category_slugs,
                        );
                    }
                }
                // now the query
                $tax_query = array(
                    'relation' => 'OR',
                    $taxes
                );
            }
        }

        // fixed params
        $args['meta_key'] = MetaBoxDates::META_KEY_START_DATE["key"];
        $args['orderby'] = 'meta_value';
        $args['order'] = 'ASC';
        $args['meta_type'] = 'meta_value_date';
        $args['post_status'] = 'publish';

        // any luck
        if ($tax_query != null) {
            $args['tax_query'] = $tax_query;
            // we don't need theae any longer
            unset($args['categpry-slug']);
        }
        $return_found_posts = ! empty($args['found_posts'] );

        if ($return_found_posts) {
            $args['posts_per_page'] = 1;
            $args['paged']          = 1;
        }

        // debug?
        if (Utilities::getDebugMode()) {
            Utilities::doLogDebug(__FUNCTION__, ["ARGS" => $args]);
        }
        $result = new \WP_Query($args);
        $found_posts = $result->found_posts;

        // a wrinkle on exact dates - if they picked one, we will have found events
        //  that had a range that encapsulated that date, but we don't want to return
        //  those events unless they had a recurrence on that date itself

        if ( (! empty($result->posts)) && (isset($args[$exactDateArg])) ) {

            $result_posts = array();
            for ($n=0; $n < count($result->posts); $n++) {
                $meta = self::get_event_meta($result->posts[$n]->ID, $config_args);

                // if "recurring_days" is set, then we have to see if the pattern matches the exact date
                if ( (! empty($meta["recurring_repeat"])) && (is_array($meta["recurring_days"])) ) {

                    // figure out what occurence it is
                    foreach ($meta["recurring_days"] as $recurring_day) {
                        $occur_date = new \DateTime($args[$exactDateArg]);
                        $date_str = Utilities::numToOrdinalWord($meta["recurring_repeat"])." {$recurring_day} of this month";
                        $occur_date->modify($date_str);
                        // is the found date ours?
                        if ($occur_date->format("Y-m-d") == $args[$exactDateArg]) {
                            // keep it
                            $result->posts[$n]->meta = $meta;
                            $result_posts[] = $result->posts[$n];
                        }
                    }
                } else if ( ($args[$exactDateArg] == $meta["start_date"]) || ($args[$exactDateArg] == $meta["end_date"]) ) {
                    // if the start or end date match our date, we add it too
                    $result_posts[] = $result->posts[$n];
                }
            }
            // how many did we find now?
            $found_posts = count($result_posts);

            // reset back what we kept
            $result->posts = $result_posts;
        }
        // check if is using v2 recurrence; if so, check patterns and determine if matches query

        if ( Utilities::getRecurrenceVersion() ){
            if ( (! empty($result->posts))) {
                $result_posts_v2 = array();
                for ($n=0; $n < count($result->posts); $n++) {
                    $meta = self::get_event_meta($result->posts[$n]->ID, $config_args);

                    if( array_key_exists( 'recurrence_options', $meta)){
                        $recurrence_options = $meta['recurrence_options'];
                        if( 'pattern' == $recurrence_options  || 'specific_days' == $recurrence_options ){

                            if( array_key_exists('repeat_dates', $meta) && is_array($meta['repeat_dates']) && count($meta['repeat_dates']) > 0 ){
                                $days_to_check = $meta['repeat_dates'];
                            }
                            if( array_key_exists('pattern_dates', $meta) && is_array($meta['pattern_dates']) && count($meta['pattern_dates']) > 0 ){
                                $days_to_check = $meta['pattern_dates'];
                            }                        
                            if( is_array($days_to_check) ){
                                $one_date_matches = 0;
                                foreach( $days_to_check as $day_to_check ){
                                    if( !$one_date_matches && ($day_to_check >= $query_start_date) && ($day_to_check <= $query_end_date) ){
                                        $result_posts_v2[] = $result->posts[$n];
                                        $one_date_matches = 1;
                                    }                                
                                }
                            }
                        }
                        if( 'one_day' == $recurrence_options  || 'daily' == $recurrence_options ){
                            if( ($meta['end_date'] >= $query_start_date) && ($meta['start_date'] <= $query_end_date) ){
                                $result_posts_v2[] = $result->posts[$n];
                                $one_date_matches = 1;
                            }                                
                        }
                    }
                    // event was created as a version 1 so include
                    else{
                        $result_posts_v2[] = $result->posts[$n];
                    }
                }
                $result->posts = $result_posts_v2;
            }
        }


        if ($return_found_posts) {
            // MAY EXIT THIS BLOCK
            return $found_posts;
        }
        if (! empty($result->posts)) {
            // get the meta for each post?
            if ($includePostMeta) {
                for ($n=0; $n < count($result->posts); $n++) {
                    // we might have gotten it above
                    $result->posts[$n]->alternative_excerpt = mb_strimwidth(wp_strip_all_tags($result->posts[$n]->post_content), 0, 100, '...');
                    if (! isset($result->posts[$n]->meta)) {
                        $result->posts[$n]->meta = self::get_event_meta($result->posts[$n]->ID, $config_args );
                    }
                }
            }
            // MAY EXIT THIS BLOCK
            return ($full) ? $result : $result->posts;
        } else {
            // MAY EXIT THIS BLOCK
            return ($full) ? $result : array();
        }
    }

    /**
     * Returns meta data for the event
     *
     * @param int $postId The post Id to look up
     * @return void
     */
    public static function get_event_meta ($postId, $config_args = [] ) {

        $rhett = array();
        $postMeta = get_post_meta($postId);
        $keyRoot = str_replace("[KEY]", "", Constants::WP_POST_META_KEY_DETAILS);

        $rhett['thisVenues'] = get_the_terms( $postId, 'eventastic_venues');
        $rhett['thisCategories'] = get_the_terms( $postId, 'eventastic_categories');

        $image_size = ( array_key_exists( 'image_size' , $config_args ) && $config_args['image_size'] ) ? $config_args['image_size'] : 'thumbnail';

        if( isset($config_args['use_pattern_string'] ) ){
            $args['meta'] = [
                'start_date' => (isset($postMeta['eventastic_start_date']) && isset($postMeta['eventastic_start_date'][0])) ? $postMeta['eventastic_start_date'][0] : null,
                'end_date' => (isset($postMeta['eventastic_end_date']) && isset($postMeta['eventastic_end_date'][0])) ? $postMeta['eventastic_end_date'][0] : null,                                 
                'pattern_dates' => (isset($postMeta['eventastic_pattern_dates']) && isset($postMeta['eventastic_pattern_dates'][0])) ? $postMeta['eventastic_pattern_dates'][0] : null,
                'repeat_pattern' => (isset($postMeta['eventastic_repeat_pattern']) && isset($postMeta['eventastic_repeat_pattern'][0])) ? $postMeta['eventastic_repeat_pattern'][0] : null,
                'repeat_type' => (isset($postMeta['eventastic_repeat_type']) && isset($postMeta['eventastic_repeat_type'][0])) ? $postMeta['eventastic_repeat_type'][0] : null,                
                'repeat_number' => (isset($postMeta['eventastic_repeat_number']) && isset($postMeta['eventastic_repeat_number'][0])) ? $postMeta['eventastic_repeat_number'][0] : null,
                'recurring_days_v2' => (isset($postMeta['eventastic_recurring_days_v2']) && isset($postMeta['eventastic_recurring_days_v2'][0])) ? $postMeta['eventastic_recurring_days_v2'][0] : null
            ];
            $rhett['patternString'] = Utilities::recurrenceToString( $args );
        }
        $rhett['thumbnail'] = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ) , $image_size);
        // do some cleanup and only return ours
        foreach ($postMeta as $key => $val) {
            $cleanKey = str_replace($keyRoot, "", $key);
            if (strstr($key, $keyRoot) !== false) {
                if (is_array($val)) {
                    $rhett[$cleanKey] = maybe_unserialize($val[0]);
                } else {
                    $rhett[$cleanKey] = $val;
                }
            }
        }

        return $rhett;
    }

    /**
     * Renders a map for the passed event post Id.
     *
     * @param int $postId The post Id to look up
     * @param string $mapCSSId The CSS Id to use for the map div
     * @param string $mapIconLeafletJSON The options for a custom Leaflet L.divIcon map icon - build these
     *    as a PHP array, and this function will json_encode those automatically
     * @return void
     */
    public static function render_event_map ($postId, $mapCSSId="map", $mapIconLeafletJSON=null) {

        $postMeta = get_post_meta($postId);

        // do we have data?
        if ( (! isset($postMeta[MetaBoxAddress::META_KEY_LAT["key"]][0])) ||
                (! isset($postMeta[MetaBoxAddress::META_KEY_LNG["key"]][0])) ) {
            // MAY EXIT THIS BLOCK
            return;
        }

        // leaflet or google?
        $googleAPIKey = get_option(SettingsAdminLayout::SETTING_KEY_GOOGLE_API["key"], "");
        if ($googleAPIKey == "") {
            // leaflet
            echo '<div id="'.$mapCSSId.'"></div>';
            ?>
            <script>
            jQuery(document).ready(function(){
                var center = [<?php echo $postMeta[MetaBoxAddress::META_KEY_LAT["key"]][0] ?>, <?php echo $postMeta[MetaBoxAddress::META_KEY_LNG["key"]][0] ?>];
                var map = L.map('<?php echo $mapCSSId ?>').setView(center, 13);
                <?php
                // custom tile library?
                $tl = get_option(SettingsAdminLayout::SETTING_LEAFLET_TILE_LIBRARY["key"]);
                if ($tl) {
                    echo stripslashes($tl).PHP_EOL;
                } else {
                    echo "L.tileLayer(".PHP_EOL
                        ."'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {".PHP_EOL
                        ."maxZoom: 18".PHP_EOL
                    ."}).addTo(map);".PHP_EOL;
                }
                // custom marker?
                if ($mapIconLeafletJSON != null) {
                    echo "L.marker(center, { icon: L.divIcon(".json_encode($mapIconLeafletJSON).") }).addTo(map);".PHP_EOL;
                } else {
                    echo "L.marker(center).addTo(map);".PHP_EOL;
                }
                ?>
            });
            </script>
            <?php
        } else {
            // google
            echo '<iframe id="'.$mapCSSId.'" frameborder="0" style="border:0" '
                .'src="https://www.google.com/maps/embed/v1/place?key='.$googleAPIKey.'&q='
                .$postMeta[MetaBoxAddress::META_KEY_LAT["key"]][0].','.$postMeta[MetaBoxAddress::META_KEY_LNG["key"]][0].'" '
                .'allowfullscreen></iframe>';
        }
    }


    /**
     * Renders an event calendar
     *
     * @param string $calendarCSSId The CSS Id to use for the calendar div
     * @param object $calendarEvents The object array of events to add (if not provided, all will be
     *    loaded) - this should include all post meta for each item as well
     * @param array $imageSizes array of sizes ('Full','Large', etc); ['All'] will return all (resource heavy, recommend against). 
     * If empty, no images returned     
     * @return string The event calendar
     */
    public static function render_event_calendar ($calendarCSSId="calendar", $calendarEvents=null, $imageSizes = [] ) {

        // our category colors
        $eventCategories = self::get_categories(true);
        global $post;
        $post_slug = $post->post_name;
        // render the filter bar
        echo '<div class="fc-filter-bar">';
        echo '<div class="fc-search"><p class="title">'.__('Search').'</p>';
        echo '<input type="text" id="eventasticSearch" placeholder="" />';
        echo '</div>';
        echo '<div class="fc-category-filter categoryFilters"><div class="filterToggle">'.__('Categories: ').'</div>';
        foreach ($eventCategories as $ec) {
            if( ('events' == $post_slug && !$post->post_parent) || ('chamber-of-commerce' != $ec->slug && 'convention-visitors-bureau' != $ec->slug )){
                echo '<button type="button" class="fc-key-item on" data-category="'.$ec->slug.'">';
                // echo '<span class="fc-color" style="background-color:'.$ec->color.'">&nbsp;</span>';
                echo '<input type="checkbox" class="checkbox">';
                echo '<span class="fc-text">'.$ec->name.'</span>';
                echo '</button>';
            }
        }
        echo '</div>';
        echo '<button type="button" class="filterSubmit" onclick="loadAndSetEventasticCalendarEvents()">'.__('Search').'</button>';
        // echo '<button type="button" onclick="jQuery(\'#eventasticSearch\').val(\'\'),loadAndSetEventasticCalendarEvents()">'.__('Reset').'</button>';
        echo '</div>';
        ?>
        <script type="text/javascript">
        var eventasticCalendar;
        var EVENTASTIC_EVENTS_ALL = [
            <?php
            foreach ($calendarEvents as $event) {
                // make the css class the categories
                $categories = array();
                $color = "";
                $cssClass = "";
                $cats = get_the_terms($event->ID, "eventastic_categories");
                if ($cats) {
                    // get a primary event color and each event's categories
                    foreach ($cats as $cat) {
                        // color match
                        foreach ($eventCategories as $ec) {
                            if ( ($ec->slug == $cat->slug) && ($color == "") ) {
                                $color = $ec->color;
                                break;
                            }
                        }
                        // add the category to the list
                        $categories[] = $cat->slug;
                    }
                }
                // now render
                echo '{'.PHP_EOL;
                echo 'title: "'.addcslashes($event->post_title, '"').'",'.PHP_EOL;
                echo 'categories: '.json_encode($categories).','.PHP_EOL;
                echo 'backgroundColor: "'.$color.'",'.PHP_EOL;
                echo 'excerpt: "' . get_the_excerpt($event->ID ).'",'.PHP_EOL;                
                echo 'meta: '.json_encode($event->meta).','.PHP_EOL;            
                if (is_array($imageSizes ) && count( $imageSizes ) > 0 ){
                    $images = [];
                    // get featured image
                    if( $thumbnail_id = get_post_thumbnail_id( $event->ID ) ){
                        $thumbnail_srcs = [];
                        foreach( $imageSizes as $size ){
                            $thumbnail_srcs[$size] = wp_get_attachment_image_src( $thumbnail_id, $size);                            
                        }
                        if( count( $thumbnail_srcs ) > 0 ){
                            echo 'featured_image: '. json_encode( $thumbnail_srcs ) .','.PHP_EOL;
                        }
                    }

                    // get gallery images
                    if ( (isset($event->meta["gallery_images"])) && (is_array($event->meta["gallery_images"])) ) {
                        foreach($event->meta["gallery_images"] as $image_id){
                            $image_srcs = [];
                            foreach( $imageSizes as $size ){
                                $image_srcs[$size] = wp_get_attachment_image_src( $image_id, $size);                            
                            }
                            $images[] = $image_srcs;
                        }
                    }
                    if( count( $images ) > 0 ){
                        echo 'images: '. json_encode( $images) .','.PHP_EOL;
                    }
                }           
                // times
                $startTime = ( (isset($event->meta["start_time"])) && ($event->meta["start_time"] != "") )
                        ? date("\TH:i:00", strtotime($event->meta["start_time"]))
                        : "";
                $endTime = ( (isset($event->meta["end_time"])) && ($event->meta["end_time"] != "") )
                        ? date("\TH:i:00", strtotime($event->meta["end_time"]))
                        : $startTime;
                // start and end dates
                if ( (isset($event->meta["recurring_days"])) && (is_array($event->meta["recurring_days"])) && ( "finite" != $event->meta["event_end"] ) ) {
                    // recurring - set up differently
                    $dow = Utilities::generateDaysOfWeek(true);
                    $dowRrule = array( 'mo', 'tu', 'we', 'th', 'fr', 'sa','su');
                    $daysOfWeek = array();
                    foreach ($event->meta["recurring_days"] as $r) {
                        $daysOfWeek[] = $dowRrule[array_search($r, $dow)];
                    }

                    echo 'rrule: {'.PHP_EOL;
                    echo 'freq: "weekly",'.PHP_EOL;
                    echo ( (isset($event->meta["recurring_repeat"])) && ($event->meta["recurring_repeat"] != "") ) ? "interval: {$event->meta["recurring_repeat"]}," : "";
                    echo 'byweekday: '.json_encode($daysOfWeek).','.PHP_EOL;
                    echo ( (isset($event->meta["start_date"])) && ($event->meta["start_date"] != "") )
                            ? 'dtstart: "'.$event->meta["start_date"].$startTime.'",'.PHP_EOL
                            : "";
                    // echo 'until: "2020-12-31T00:00:00",'.PHP_EOL
                    echo ( (isset($event->meta["end_date"])) && ($event->meta["end_date"] != "") )
                            ? 'until: "'.$event->meta["end_date"].$endTime.'",'.PHP_EOL
                            : "";
                    echo '},'.PHP_EOL;
                } else {
                    // non-recurring
                    echo ( (isset($event->meta["start_date"])) && ($event->meta["start_date"] != "") )
                            ? 'start: "'.$event->meta["start_date"].$startTime.'",'.PHP_EOL
                            : "";
                    echo ( (isset($event->meta["end_date"])) && ($event->meta["end_date"] != "") )
                            ? 'end: "'.$event->meta["end_date"].$endTime.'",'.PHP_EOL
                            : "";
                }
                // url
                // echo ( (isset($event->meta["url"])) && ($event->meta["url"] != "") )
                //         ? 'url: "'.$event->meta["url"].'",'.PHP_EOL
                //         : "";
                echo ( (!empty(get_the_permalink($event->ID))) )
                        ? 'url: "'.get_the_permalink($event->ID).'",'.PHP_EOL
                        : "";
                echo '},'.PHP_EOL;
            }
            ?>
        ];

        // kick it off
        jQuery("document").ready(function() {
            // listen to the category buttons
            jQuery(".fc-key-item").on("click", function() {
                jQuery(this).toggleClass("on");
                // get new events based on click
                loadAndSetEventasticCalendarEvents();
            });

            // now set up the calendar
            eventasticCalendar = new FullCalendar.Calendar(document.getElementById('<?php echo $calendarCSSId ?>'), {
                plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'rrule' ],
                defaultView: 'dayGridMonth',
                weekNumbers: false,
                height: 'auto',
                header: {
                    left: 'prev,next,today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                timeFormat: '<?php echo Constants::TIME_FORMAT_JS_PRETTY ?>',
                minTime: '<?php echo Constants::CALENDAR_VIEW_MIN_TIME_VIEW ?>'
            });

            eventasticCalendar.addEventSource(EVENTASTIC_EVENTS_ALL);
            eventasticCalendar.render();
        });

        // loads and sets events
        function loadAndSetEventasticCalendarEvents () {

            var filterText = jQuery('#eventasticSearch').val();

            // remove all current
            var eventSources = eventasticCalendar.getEventSources();
            var len = eventSources.length;
            for (var i=0; i < len; i++) {
                eventSources[i].remove();
            }
            var currentCategories = [];
            // which categories are we showing
            jQuery(".fc-key-item").each(function() {
                if (jQuery(this).hasClass("on")) {
                    currentCategories.push(jQuery(this).data("category"));
                }
            });

            // now go through the current full set and filter out based on current filters
            var newEvents = [];
            for (var e in EVENTASTIC_EVENTS_ALL) {
                var include = true;
                // is there a text filter?
                if (filterText != "") {
                    if (EVENTASTIC_EVENTS_ALL[e].title.toLowerCase().indexOf(filterText.toLowerCase()) == -1) {
                        include = false;
                    }
                }
                // does this event have a valid selected category?
                if (include) {
                    var catInclude = false;
                    for (var c in EVENTASTIC_EVENTS_ALL[e].categories) {
                        if (currentCategories.includes(EVENTASTIC_EVENTS_ALL[e].categories[c])) {
                            catInclude = true;
                            break;
                        }
                    }
                    include = catInclude;
                }
                // using it?
                if (include) {
                    newEvents.push(EVENTASTIC_EVENTS_ALL[e]);
                }
            }

            eventasticCalendar.addEventSource(newEvents);
        }
        </script>
        <?php
    }

    /**
     * Helper function for get_events_date_ordered
     */
    private static function weekOfMonth($date) {
        $firstOfMonth = strtotime(date("Y-m-01", $date));
        return self::weekOfYear($date) - self::weekOfYear($firstOfMonth) + 1;
    }

    /**
     * Helper function for get_events_date_ordered
     */
    private static function weekOfYear($date) {
        $weekOfYear = intval(date("W", $date));
        if (date('n', $date) == "1" && $weekOfYear > 51) {
            return 0;
        }
        else if (date('n', $date) == "12" && $weekOfYear == 1) {
            return 53;
        }
        else {
            return $weekOfYear;
        }
    }

    /**
     * Returns all events across the queried date range
     *
     * @param boolean $hideEmpty Hide empty terms?
     * @return array List of events keyed by date
     */
    public static function get_events_date_ordered( $args=[] ) {
        $start = microtime(true);
        $defaults = array(
            'post_type'            => Utilities::getPluginPostType(),
            'order'                => 'ASC',
            'posts_per_page'       => -1,
        );
        if( isset($args) && is_array($args) && count($args) > 0 ){
            $args = wp_parse_args($args, $defaults);
        }
        else{
            $args = wp_parse_args($_POST, $defaults);
        }

        if( array_key_exists( 'start_date' , $args ) ){
            $start_date = date(Constants::DATE_FORMAT_MYSQL, strtotime( $args['start_date'] ) );
        }
        else{
            $start_date = date(Constants::DATE_FORMAT_MYSQL, strtotime('yesterday')); // why bother with timezones, just be generous
        }
        if( array_key_exists( 'end_date' , $args ) ){
            $end_date = date(Constants::DATE_FORMAT_MYSQL, strtotime( $args['end_date'] ) );
        }
        else{
            $yearFromToday= strtotime('+1 year', strtotime($start_date));
                        Utilities::doLogDebug(__FUNCTION__, ["yearfromtoday" => $yearFromToday]);

            $end_date = date(Constants::DATE_FORMAT_MYSQL, $yearFromToday);
                        Utilities::doLogDebug(__FUNCTION__, ["enddate" => $end_date]);
        }
        if( array_key_exists( 'exact_start_date' , $args ) ){
            $query_args['exact_start_date'] = date(Constants::DATE_FORMAT_MYSQL, strtotime( $args['exact_start_date'] ) );
        }
        $config_args = [
            'use_categories' => ( (array_key_exists( 'use_categories' , $args ) ) ? $args['use_categories'] : null ),
            'image_size' => ( (array_key_exists( 'image_size' , $args ) ) ? $args['image_size'] : 'thumbnail' ),
            'use_pattern_string' => ( (array_key_exists( 'use_pattern_string' , $args ) ) ? $args['use_pattern_string'] : false ),
        ];

        $query_args['start_date'] = $start_date;
        $query_args['end_date'] = $end_date;

        $category_slug = (array_key_exists( 'category_slug' , $args ) ) ? $args['category_slug'] : null;
        if( 'all' != $category_slug ){
            $query_args['category_slug'] = $category_slug;
        }    
        $events = self::get_events( $query_args , true, null, $config_args );
        $return_events = new \stdClass();
        $days = [];
        $recurring_events = [];
        $dayInSeconds = 60 * 60 * 24; 
        $event_objects = [];        
        $fullCalendarSource = [];
        $fullCalendarEventsSource = [];     
        $startDateTimestamp = strtotime( $start_date );   
        $endDateTimestamp = strtotime( $end_date );  
        if( $events ){
            foreach( $events as $event ){
                $event_objects[$event->ID] = $event;
                $event->permalink = get_permalink($event->ID);
                $eventMeta = $event->meta;                
                $event_start_date = ( strtotime( $start_date ) > strtotime( $event->meta['start_date']) ) ? strtotime( $start_date ) : strtotime( $event->meta['start_date']); 
                $event_end_date = strtotime( $event->meta['end_date']); 
                $startTime = ( (isset($event->meta["start_time"])) && ($event->meta["start_time"] != "") )
                        ? date("\TH:i:00", strtotime($event->meta["start_time"]))
                        : "";
                $endTime = ( (isset($event->meta["end_time"])) && ($event->meta["end_time"] != "") )
                        ? date("\TH:i:00", strtotime($event->meta["end_time"]))
                        : $startTime;         

                if ( Utilities::getRecurrenceVersion() ){
                    if( isset( $eventMeta['recurrence_options'] ) && ("specific_days" == $eventMeta['recurrence_options'] || "pattern" == $eventMeta['recurrence_options'] ) ){
                        $foreachDates = isset($eventMeta['repeat_dates']) ? $eventMeta['repeat_dates'] : null;
                        if( "pattern" == $eventMeta['recurrence_options'] ){
                            $foreachDates = isset($eventMeta['pattern_dates']) ? $eventMeta['pattern_dates'] : null;
                        }
                        if( is_array( $foreachDates ) && count( $foreachDates ) > 0 ){
                            foreach( $foreachDates  as $specific_date ){
                                $specificDateTimestamp = strtotime( $specific_date );
                                if( $specificDateTimestamp >= $startDateTimestamp && $specificDateTimestamp <= $endDateTimestamp   ){
                                    $eventSource = (object)[ 
                                        'title' => get_the_title($event->ID),
                                        'url' => get_the_permalink($event->ID),
                                        'start' => $specific_date.$startTime,
                                        'end' =>  $specific_date.$endTime  
                                    ];

                                    if( !array_key_exists( strtotime($specific_date), $days) ){
                                        $days[strtotime($specific_date)] = [ 
                                            'meta' => [
                                                'date' => date(Constants::DATE_FORMAT_MYSQL, strtotime($specific_date))
                                            ],
                                            'events' => [] 
                                        ];
                                    }
                                    $days[strtotime($specific_date)]['events'][] = $event->ID;

                                    $fullCalendarEventsSource[] = $eventSource;
                                }
                            }
                        }
                    }
                    else{
                        $eventSource = (object)[ 
                            'title' => get_the_title($event->ID),
                            'url' => get_the_permalink($event->ID),
                            'start' => date('Y-m-d', $event_start_date).$startTime,
                            'end' => date('Y-m-d', ( $event_end_date + 60*60*24 - 1 )).$endTime // adds a date for exclusive date reckoning   
                        ];    
                        // need to iterate from start to end
                        $iterateDate = $event_start_date; 
                        while( $iterateDate <= $event_end_date ){

                            if( !array_key_exists($iterateDate, $days) ){
                                $days[$iterateDate] = [ 
                                    'meta' => [
                                        'date' => date(Constants::DATE_FORMAT_MYSQL, $iterateDate)
                                    ],
                                    'events' => [] 
                                ];
                            }
                            $days[$iterateDate]['events'][] = $event->ID;
                            $iterateDate = $iterateDate + $dayInSeconds;
                        }

                        $fullCalendarEventsSource[] = $eventSource;
                    }
                }
                else{
                    $buildRecurring = false;                       
                    // check if recurring (old method and new)
                    if( array_key_exists( 'recurring_days' , $event->meta ) ){
                        $recurring_events[] = $event->ID;
                        $buildRecurring = true;                       
                    }
                    // add event to each day within the search range
                    if( $buildRecurring ){
                        $iterateDate = $event_start_date; 
                        $recurringDays = $event->meta['recurring_days'];
                        $weekly_frequency = $event->meta['recurring_repeat'] ? $event->meta['recurring_repeat'] : null;
                        if( !$event_end_date ){ // event has no end date; iterate through end of query's projected dates to create all occurences of recurring event
                            $event_end_date = strtotime($end_date);
                        }
                        while( $iterateDate <= $event_end_date ){
                            // check weekly recurring frequency 
                            if( !$weekly_frequency || is_null($weekly_frequency) || ( $weekly_frequency >= 1 && $weekly_frequency == self::weekOfMonth($iterateDate) ) ){
                                $dayOfWeek = date('l', $iterateDate);
                                if( in_array( $dayOfWeek, $recurringDays ) ){
                                    if( !array_key_exists($iterateDate, $days) ){
                                        $days[$iterateDate] = [ 
                                            'meta' => [
                                                'dayOfWeek' => $dayOfWeek,
                                                'date' => date(Constants::DATE_FORMAT_MYSQL, $iterateDate)
                                            ],
                                            'events' => [] 
                                        ];
                                    }
                                    $days[$iterateDate]['events'][] = $event->ID;
                                }
                            }
                            $iterateDate = $iterateDate + $dayInSeconds;
                        }
                    }
                    else{
                        $iterateDate = $event_start_date; 
                        if( !$event_end_date ){
                            $event_end_date = $event_start_date;
                        }
                        while( $iterateDate <= $event_end_date ){
                            $dayOfWeek = date('l', $iterateDate);
                            if( !array_key_exists($iterateDate, $days) ){
                                $days[$iterateDate] = [ 
                                    'meta' => [
                                        'dayOfWeek' => $dayOfWeek,
                                        'date' => date(Constants::DATE_FORMAT_MYSQL, $iterateDate)
                                    ],
                                    'events' => [] 
                                ];
                            }
                            $days[$iterateDate]['events'][] = $event->ID;
                            $iterateDate = $iterateDate + $dayInSeconds;
                        }                    
                    }
                    $eventSource = (object)[ 
                        'title' => get_the_title($event->ID),
                        'url' => get_the_permalink($event->ID),
                        'start' => date('Y-m-j', $event_start_date).$startTime,
                        'end' =>  date('Y-m-j',$event_end_date).$endTime  
                    ];                    
                    $fullCalendarEventsSource[] = $eventSource;                    
                }
            }
        }
        ksort($days);
        $result = [
            'event_objects' => $event_objects,
            'days' => $days,
            'fullCalendarEventsSource' => $fullCalendarEventsSource,
            'php_time_elapsed_secs' =>  number_format( (microtime(true) - $start), 3 )
        ];
        if( isset($_POST['action']) && 'get_events_date_ordered' == $_POST['action'] ){
           echo json_encode($result);
           wp_die();
        }
        else{
            return $result;
        }        
    }

    /**
     * Returns all event venues
     *
     * @param boolean $hideEmpty Hide empty terms?
     * @return array List of event venues
     */
    public static function get_venues ($hideEmpty=false) {

        return get_terms(array(
            'taxonomy' => 'eventastic_venues',
            'hide_empty' => $hideEmpty,
        ));
    }

    /**
     * Returns all event organizers
     *
     * @param boolean $hideEmpty Hide empty terms?
     * @return array List of event organizers
     */
    public static function get_organizers ($hideEmpty=false) {

        return get_terms(array(
            'taxonomy' => 'eventastic_organizers',
            'hide_empty' => $hideEmpty,
        ));
    }

    /**
     * Returns all event categories
     *
     * @param boolean $hideEmpty Hide empty terms?
     * @param boolean $assignColors Assign a hex color from settings to each color?
     * @return array List of event categories
     */
    public static function get_categories ($hideEmpty=false, $assignColors=true) {

        $rhett = get_terms(array(
            'taxonomy' => 'eventastic_categories',
            'hide_empty' => $hideEmpty,
        ));

        // assign colors before handing back?
        if ($assignColors) {
            // get them
            $catColors = get_option(
                SettingsAdminLayout::SETTING_CATEGORY_COLORS["key"],
                Constants::PLUGIN_SETTING_CATEGORY_COLORS
            );
            try {
                if (is_string($catColors)) {
                    $catColors = json_decode($catColors);
                }
            } catch (Exception $e) {}

            // add from set
            $cLoop = 0;
            foreach ($rhett as $r) {
                $r->color = $catColors[$cLoop];
                $cLoop = (($cLoop + 1) == count($catColors)) ? 0 : ($cLoop + 1);
            }
        }

        return $rhett;
    }
}
?>
