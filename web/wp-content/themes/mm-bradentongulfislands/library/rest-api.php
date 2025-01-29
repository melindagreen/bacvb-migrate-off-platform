<?php
namespace MaddenNino\Library;

use function cli\err;

class RestApi {
    function __construct() {
        add_action( 'rest_api_init', array( get_called_class(), 'register_custom_rest_fields' ) );
        add_action( 'rest_listing_query', array( get_called_class(), 'filter_by_amenities' ), 10, 2 );
        add_filter( 'rest_event_query', array( get_called_class(), 'custom_date_filters' ), 10, 2 );
        add_action( 'rest_listing_query', array( get_called_class(), 'filter_by_rooms' ), 10, 2 );
        add_action ( 'rest_listing_query', array( get_called_class(), 'filter_by_activity' ), 10, 2 );
        // add_action( 'rest_listing_query', array( get_called_class(), 'filter_by_parent_category' ), 10, 3 );
    }

    /**
     * Add custom fields to REST API for listings grid
     */
    public static function register_custom_rest_fields() {

        // meta
        $meta_types = array( 'listing', 'event', 'attachment' );
        foreach( $meta_types as $type ) {
            register_rest_field( $type, 'meta_fields', array(
                'get_callback' => function( $object ) use( $type ) {
                    $meta = get_post_meta( $object['id'] );

                    return is_array( $meta )
                        ? array_map( function( $meta_arr ) {
                            return maybe_unserialize( $meta_arr[0] );
                        }, $meta )
                        : array();
                }
            ) );
        }

        // filter
        $location_types = array( 'listing' );
        foreach( $location_types as $type ) {
            register_rest_field( $type, 'accommodations', array(
                'get_callback' => function( $object ) use( $type ) {
                    $meta_value = get_post_meta( $object['id'], 'partnerportal_accomodations-location', true );
                    return $meta_value;
                }
            ) );
        }

        $amenities_types = array( 'listing' );
        foreach( $amenities_types as $type ) {
            register_rest_field( $type, 'amenities', array(
                'get_callback' => function( $object ) use( $type ) {
                    $facility_amenities = get_post_meta( $object['id'], 'partnerportal_accomodations-facility-amenities', true );
                    return $facility_amenities;
                }
            ) );
        }

        // category name 
        $cat_types = array( 'listing', 'event' );
        foreach( $cat_types as $type ) {
            register_rest_field( $type, 'cat_name', array(
                'get_callback' => function( $object ) use( $type ) {
                    $cat = get_the_terms($object['id'], 'category');;
                    return $cat;
                }
            ) );
        }

        // category name 
        $venue_types = array( 'event' );
        foreach( $venue_types as $type ) {
            register_rest_field( $type, 'venue_name', array(
                'get_callback' => function( $object ) use( $type ) {
                    $venue = get_the_terms($object['id'], 'eventastic_venues');;
                    return $venue;
                }
            ) );
        }

        // thumb
        $thumb_types = array( 'listing', 'event', 'post' );
        foreach( $thumb_types as $type ) {
            register_rest_field( $type, 'thumb_url', array(
                'get_callback' => function( $object ) {
                    return get_the_post_thumbnail_url( $object['id'], 'large' );   
                }
            ) );
        }

    }

    /**
     * Set tax query args to allow child terms
     * @param array $args                   The existing query args
     * @param array $request                The incomming REST request
     * @return array                        The modified query args
     */
    public static function filter_by_parent_category( $args, $request ) {
        if( isset( $request['include_child_terms'] ) ) {
            foreach( $args['tax_query'] as &$tax_query ) {
                $tax_query['include_children'] = true;
            }
            error_log(print_r($args['tax_query'], true));
        }
        return $args;
    }

    /**
     * Filters by listings accomodations
     * @param array $args                   The existing query args
     * @param array $request                The incomming REST request
     * @return array                        The modified query args
     */
    function filter_by_amenities($args, $request) {

        $meta_queries = ['accomodations-location','accomodations-facility-amenities', 'recreation-recreation-type'];

        foreach($meta_queries as $key) {
            if (isset($request[$key]) && !empty($request[$key])) {
                $amenities_array = explode(',', $request[$key]);
            
                if (count($amenities_array) > 1) {
                    $meta_queries = [];
                    
                    foreach ($amenities_array as $amenity) {
                        $meta_queries[] = array(
                            'key'     => 'partnerportal_'.$key,
                            'value'   => $amenity,
                            'compare' => 'LIKE',
                        );
                    }
                    
                    $args['meta_query'][] = array(
                        'relation' => 'AND',
                        $meta_queries 
                    );
                } else {
                    $args['meta_query'][] = array(
                        'key'     => 'partnerportal_'.$key,
                        'value'   => $request[$key],
                        'compare' => 'LIKE', 
                    );
                }

            }
        }

        return $args;
    }

        /**
     * Filters by listings room count
     * @param array $args                   The existing query args
     * @param string $request                The incomming REST request
     * @return array                        The modified query args
     */
    function filter_by_rooms($args, $request) {

            if (isset($request['rooms']) && !empty($request['rooms'])) {
                $roomRange = explode('-', $request['rooms']);
            
                if (count($roomRange) == 2) {
                    $meta_queries = [];
                    
                        $meta_queries[] = array(
                            'key'     => 'partnerportal_room-count',
                            'value'   => (int)$roomRange[0],
                            'compare' => '>=',
                            'type'    => 'NUMERIC'
                        );

                        $meta_queries[] = array(
                            'key'     => 'partnerportal_room-count',
                            'value'   => (int)$roomRange[1],
                            'compare' => '<=',
                            'type'    => 'NUMERIC'
                        );
                    
                    $args['meta_query'][] = array(
                        'relation' => 'AND',
                        $meta_queries 
                    );
                } 

                else {
                    $args['meta_query'][] = array(
                        'key'     => 'partnerportal_room-count',
                        'value'   => (int)$request['rooms'],
                        'compare' => '>=', 
                        'type'    => 'NUMERIC'
                    );
                }

            }
        
        return $args;
    
    }

     /**
     * Filters by activity
     * @param array $args                   The existing query args
     * @param array $request                The incomming REST request
     * @return array                        The modified query args
     */
    function filter_by_activity($args, $request) {

        if (isset($request['activity'])) {
      
                $args['meta_query'][] = array(
                    'key'     => 'partnerportal_activity',
                    'value'   => $request['activity'],
                    'compare' => '=', 
                );
        }
    

        return $args;
    }

    /**
     * Add custom start and end date paramters to event query
     */
    public static function custom_date_filters( $args, $request ) {

        global $wpdb;

        // set our time zone to get the right time
        date_default_timezone_set(Constants::TIME_ZONE);

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_name
                FROM $wpdb->posts
                WHERE post_type IN ('event')
                AND post_status = 'publish'"
            )
        );

        // start our meta query
        $args['meta_query'] = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
    
        // order by start date (newest first)
        $args['meta_key'] = 'eventastic_start_date';
        $args['orderby'] = array(
            'eventastic_start_date' => 'ASC', // Orders by startdate in ascending order
            'eventastic_end_date'   => 'ASC', // If startdate is the same, order by enddate in ascending order
        );

        // add start and end date filters
        if (isset($request['eventastic_start_date'])) {
            error_log('eventastic_start_date received: ' . print_r($request['eventastic_start_date'], true));
        }
        if (isset($request['eventastic_end_date'])) {
            error_log('eventastic_end_date received: ' . print_r($request['eventastic_end_date'], true));
        }

        // update dates to reflect today
        $today = new \DateTime();
        if ($start_date_parsed != null) {
            $start_date_parsed = ($start_date_parsed <= $today)
                ? $today->format('Y-m-d')
                : $start_date_parsed->format('Y-m-d');
        }
        if ($end_date_parsed != null) {
            $end_date_parsed = ($end_date_parsed <= $today)
                ? $today->format('Y-m-d')
                : $end_date_parsed->format('Y-m-d');
        }

        // now build meta query based on date values
        if( ($start_date_parsed != null) && ($end_date_parsed != null) ) {
            // find any event that is encompassed by or overlaps the range
            array_push( $args['meta_query'], array(
                'relation' => 'AND',
                array(
                    'key' => 'eventastic_start_date',
                    'value' => $end_date_parsed,
                    'compare' => '<=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'eventastic_end_date',
                    'value' => $start_date_parsed,
                    'compare' => '>=',
                    'type' => 'DATE',
                )
            ) );

        } else if( ($start_date_parsed != null) && ($end_date_parsed == null) ) {
            // only start date was set - find all items starting after that, or that start before and end after
            array_push( $args['meta_query'], array(
                'relation' => 'OR',
                array(
                    'key' => 'eventastic_start_date',
                    'value' => $start_date_parsed,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'eventastic_start_date',
                        'value' => $start_date_parsed,
                        'compare' => '<=',
                        'type' => 'DATE'
                    ),
                    array(
                        'key' => 'eventastic_end_date',
                        'value' => $start_date_parsed,
                        'compare' => '>=',
                        'type' => 'DATE'
                    )
                )
            ) );

        } else if( ($start_date_parsed == null) && ($end_date_parsed != null) ) {
            // only end date was set - find all ending before that, or that start before and end after
            array_push( $args['meta_query'], array(
                'relation' => 'OR',
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'eventastic_end_date',
                        'value' => $end_date_parsed,
                        'compare' => '<=',
                        'type' => 'DATE',
                    ),
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'eventastic_start_date',
                        'value' => $end_date_parsed,
                        'compare' => '<=',
                        'type' => 'DATE'
                    ),
                    array(
                        'key' => 'eventastic_end_date',
                        'value' => $end_date_parsed,
                        'compare' => '>=',
                        'type' => 'DATE'
                    )
                )
            ) );

        } else {
            // Default to hiding past events
            array_push( $args['meta_query'], array(
                'key' => 'eventastic_end_date', 
                'value' => date( 'Y-m-d' ),
                'compare' => '>=',
                'type' => 'DATE',
            ) );
        }

        // Filter To Exclude Category
        $args['tax_query'][] = array(
            'taxonomy' => 'eventastic_categories',
            'terms' => array($request['eventastic_categories_exclude']),
            'field' => 'term_id',
            'operator' => 'NOT IN',
        );
                
        return $args;
    }

}
