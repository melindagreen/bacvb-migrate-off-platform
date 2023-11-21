<?php
namespace MaddenNino\Library;

use function cli\err;

class RestApi {
    function __construct() {
        add_action( 'rest_api_init', array( get_called_class(), 'register_custom_rest_fields' ) );
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

        // category name 
        // meta
        $cat_types = array( 'listing', 'event' );
        foreach( $cat_types as $type ) {
            register_rest_field( $type, 'cat_name', array(
                'get_callback' => function( $object ) use( $type ) {
                    $cat = get_the_terms($object['id'], 'industry');;
                    return $cat;
                }
            ) );
        }

        // thumb
        $thumb_types = array( 'listing', 'event' );
        foreach( $thumb_types as $type ) {
            register_rest_field( $type, 'thumb_url', array(
                'get_callback' => function( $object ) {
                    return get_the_post_thumbnail_url( $object['id'] );   
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

}