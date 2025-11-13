<?php // Code for registering custom post types and taxonomies.

namespace MaddenTheme\Library;
use MaddenTheme\Library\Constants as C;

/**
 * Add custom taxonomies
 */
add_action( 'init', __NAMESPACE__ . '\custom_taxonomies' );
function custom_taxonomies() {

	/*
	//Example
	$labels = array(
		'name'              => __( 'Categories', C::THEME_PREFIX ),
		'singular_name'     => __( 'Category', C::THEME_PREFIX ),
		'search_items'      =>  __( 'Search Categories', C::THEME_PREFIX ),
		'all_items'         => __( 'All Categories', C::THEME_PREFIX ),
		'parent_item'       => __( 'Parent', C::THEME_PREFIX ),
		'parent_item_colon' => __( 'Parent:', C::THEME_PREFIX ),
		'edit_item'         => __( 'Edit Category', C::THEME_PREFIX ), 
		'update_item'       => __( 'Update Category', C::THEME_PREFIX ),
		'add_new_item'      => __( 'Add New Category', C::THEME_PREFIX ),
		'new_item_name'     => __( 'New Category', C::THEME_PREFIX ),
		'menu_name'         => __( 'Categories', C::THEME_PREFIX ),
	); 	 	
	
	// Create the arguments
	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'publicly_queryable'  => true,
		'show_in_nav_menus'   => false,
		'show_admin_column'   => true,
    	'show_in_rest'        => true,
    	'rewrite'             => [
      		'slug'        => 'amenities',
      		'with_front'  => false,
    	],
	); 
	
	register_taxonomy( 'example_category', 'example', $args );
	*/
}