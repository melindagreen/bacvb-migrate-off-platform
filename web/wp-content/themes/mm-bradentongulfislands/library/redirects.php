<?php /**
This file contains custom redirects and rewrite rules
 */

namespace MaddenNino\Library;
class Redirects {
    function __construct () {

        // TEMP DURING DEV - if you need to muck with rewrites, uncomment this to flush out each time
        flush_rewrite_rules(false);

        add_filter( 'register_post_type_args', array( get_called_class(), 'add_rewrite_to_post' ), 10, 2 );
        add_filter( 'pre_post_link', array( get_called_class(), 'add_rewrite_to_post_permalink' ), -1, 2 );
    }

    /**
     * Add rewrite to posts post type for /blogs
     */
    public static function add_rewrite_to_post( $args, $post_type ) {
        if ($post_type !== 'post') {
            return $args;
        }

        $args['rewrite'] = [
            'slug' => 'blogs',
            'with_front' => true,
        ];

        return $args;
    }

    /**
     * Update posts permalink to /blogs
     */
    public static function add_rewrite_to_post_permalink( $permalink, $post ) {
        if ($post->post_type !== 'post') {
            return $permalink;
        }

        return '/blogs/%postname%/';
    }

}
