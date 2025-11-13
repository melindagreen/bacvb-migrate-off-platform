<?php /**
 * This code adds base theme options
 */

namespace MaddenTheme\Library;

class ThemeSetup {
	function __construct () {
		add_action( 'after_setup_theme', array( get_called_class(), 'madden_theme_support' ) );
		add_filter( 'body_class', array(get_called_class(), 'body_class'));
		add_filter( 'block_categories_all', array(get_called_class(), 'add_block_categories'), 10, 2);
		add_filter( 'rest_endpoints', array( get_called_class(), 'disable_rest_endpoints' ) );
	}

	public static function madden_theme_support() {
    add_theme_support( 'align-wide' );
    add_theme_support( 'menus' );

		// Add custom logo support
		add_theme_support( 'custom-logo', array(
			'width' => 300,
			'height' => 200,
			'flex-width' => true,
			'flex-height' => true,
		) );
	}
	
	public static function body_class($classes) {
		global $post;

		//the body class will already have tax-, term-, post-type-archive-, single- classes applied. we only need to add the slug for pages and/or custom class.
		$newBodyClass = array();

		if (is_page_template()) {
			array_push($newBodyClass, str_replace(".php", "", basename(get_page_template_slug())));
		}

		if (is_page() || is_single()) { 
			array_push($newBodyClass, $post->post_name); 
			if ($post->post_parent) {
				array_push($newBodyClass, get_page($post->post_parent)->post_name);
			}
		}

		/* if you want to add a custom class, you can use ACF and uncomment this
		if (get_field('body_class_custom')) { 
			array_push($newBodyClass, get_field('body_class_custom')); 
		}
		*/

		return array_merge($classes, $newBodyClass);
	}
	
	/**
	 * Adds custom "Madden Media" block category
	 * Removes this console warning:
	 * The block "madden-theme/example-block" is registered with an invalid category "madden-media".
	 */
	public static function add_block_categories( $categories, $context ) {
	    $categories[] = [
	      'slug' => 'madden-media',
	      'title' => __( 'Madden Media', 'madden-theme' ),
	    ];
		return $categories;
	}
	/**
    * Disable REST API endpoints for non-logged in users. Danke https://stackoverflow.com/a/62430375
    *
    * @param array $endpoints      The original endpoints
    * @return array $endpoints     The updated endpoints
    */
    public static function disable_rest_endpoints ( $endpoints ) {
	$endpointsToRemove = [
		"/wp/v2/media",
		"/wp/v2/types",
		"/wp/v2/statuses",
		"/wp/v2/taxonomies",
		"/wp/v2/tags",
		"/wp/v2/users",
		"/wp/v2/comments",
		"/wp/v2/settings",
		"/wp/v2/themes",
		"/wp/v2/blocks",
		"/wp/v2/oembed",
		"/wp/v2/block-renderer",
		"/wp/v2/search",
		"/wp/v2/categories"
	];		

	if ( ! is_user_logged_in() ) {
	    foreach ( $endpointsToRemove as $endpoint ) {
				unset( $endpoints[ $endpoint ] );
	    }
	}
	return $endpoints;
    }
}
