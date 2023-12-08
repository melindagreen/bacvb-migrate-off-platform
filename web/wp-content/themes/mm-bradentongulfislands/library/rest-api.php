<?php
namespace MaddenNino\Library;

use function cli\err;

class RestApi {
    function __construct() {
        add_action( 'rest_api_init', array( get_called_class(), 'register_custom_rest_fields' ) );
        add_action( 'rest_listing_query', array( get_called_class(), 'filter_by_amenities' ), 10, 2 );
        add_action( 'rest_listing_query', array( get_called_class(), 'filter_by_rooms' ), 10, 2 );
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

        // thumb
        $thumb_types = array( 'listing', 'event' );
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

        $meta_queries = ['accomodations-location','accomodations-facility-amenities'];

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
            
                if (count($roomFilter) == 2) {
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

}