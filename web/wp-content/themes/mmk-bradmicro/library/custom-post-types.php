<?php // Code for registering custom post types and taxonomies.

namespace MaddenTheme\Library;
use MaddenTheme\Library\Constants as C;

/**
 * Add custom post types
 */
add_action( 'init', __NAMESPACE__ . '\custom_post_types' );
function custom_post_types() {

	/*
  	//Example
	$labels = array(
		'name' 						=> __( 'Examples', C::THEME_PREFIX ),
		'singular_name' 			=> __( 'Example', C::THEME_PREFIX ),
		'add_new' 					=> __( 'Add New', C::THEME_PREFIX ),
		'add_new_item' 				=> __( 'Add New Example', C::THEME_PREFIX ),
		'edit_item' 				=> __( 'Edit Example', C::THEME_PREFIX ),
		'new_item' 					=> __( 'New Example', C::THEME_PREFIX ),
		'view_item' 				=> __( 'View Example', C::THEME_PREFIX ),
		'search_items' 				=> __( 'Search Examples', C::THEME_PREFIX ),
		'not_found' 				=> __( 'No Examples Found', C::THEME_PREFIX ),
		'not_found_in_trash' 		=> __( 'No Examples Found In Trash', C::THEME_PREFIX ),
		'parent_item_colon' 		=> '',
		'menu_name' 				=> __( 'Examples', C::THEME_PREFIX )
	);

	// Create the arguments
	$args = array(
		'labels' 			=> $labels,
		'public' 			=> true,
		'show_ui' 			=> true,
		'query_var' 		=> true,
		'menu_icon'     	=> 'dashicons-media-document',
		'supports' 			=> array( 'title', 'thumbnail', 'editor', 'excerpt', 'align' ),
		'show_in_rest' 		=> true,
		'rewrite'       	=> [
			'slug'        	=> 'example',
			'with_front'  	=> false,
		],
	);

	register_post_type( 'example', $args );
	*/
}
