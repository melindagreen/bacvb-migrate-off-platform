<?php

namespace MaddenMadre\Library;

use MaddenMadre\Library\Utilities as U;

class MaddenTheme {
	private $json_settings;
		
	function __construct () {
		$this->json_settings = U::get_json_settings( 'themeSetup' );

		add_action( 'after_setup_theme', array( $this, 'madden_theme_support' ) );
		add_action( 'upload_mimes', array( get_called_class(), 'add_file_types_to_uploads' ) );
		
		// Media sizes
		add_image_size( 'madden_inline_small', 400, 300 );
		add_image_size( 'madden_inline_tall', 400, 600 );
		add_image_size( 'madden_hero_sm', 600, 900 );
		add_image_size( 'madden_hero_md', 1100, 600 );
		add_image_size( 'madden_hero_lg', 1600, 1000 );

		// Customazations
		add_filter( 'image_size_names_choose', array( get_called_class(), 'add_custom_image_sizes' ) );
		add_filter( 'excerpt_length',array( get_called_class(), 'madden_excerpt_length' ) , 999 );
		add_filter( 'excerpt_more',array( get_called_class(), 'madden_excerpt_more' )  );
		
		//Disable authentication-based XML-RPC methods
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}

	/*********************
	THEME SUPPORT
	*********************/
	public function madden_theme_support() {
		// wp thumbnails
		add_theme_support( 'post-thumbnails' );

		// nav menus (needed explicitly since 6.0
		add_theme_support( 'menus' );

		// default thumb size
		set_post_thumbnail_size( 
			$this->json_settings['postThumbnailSize']['width'],
			$this->json_settings['postThumbnailSize']['height'],
			$this->json_settings['postThumbnailSize']['crop']
		);

		// remove default block patterns
		remove_theme_support( 'core-block-patterns' );

		// Disable custom colors
		add_theme_support( 'disable-custom-colors' );
	}

	/**
	 * Add some default custom image sizes for heroes
	 */
	public static function add_custom_image_sizes( $sizes ) {
		unset( $sizes['medium'] );
		unset( $sizes['large'] );

		$sizes = array_merge( $sizes, array(
			'madden_inline_wide' => __( 'Wide inline story image' ),
			'madden_hero_sm' => __( 'Small hero image' ),
			'madden_hero_md' => __( 'Medium hero image' ),
			'madden_hero_lg' => __( 'Large hero image' )
		) );

		return $sizes;
	}

	/**
	 * This removes the annoying […] to a Read More link
	 * @param string $more						The original suffix string for readmore links
	 * @return string							The modified suffix strign for readmore links
	 */
	public static function madden_excerpt_more( $more ) {
		global $post;
		// edit here if you like
		return '...';
	}

	/**
	 * Update the excerpt length to default to 50
	 * @param int $length						The original excerpt length
	 * @return int								The modified length
	 */
	public static function madden_excerpt_length( $length ) {
		if ( is_admin() ) {
			return $length;
		}
		return 50;
	}

	/**
	 * Add support for additional image types during upload
	 * @param array[] $file_types				Existing filetypes
	 * @return array[]  						The modified filetype array
	 */
	public static function add_file_types_to_uploads( $file_types ){
		$new_filetypes = array();
		$new_filetypes['svg'] = 'image/svg+xml';
		$file_types = array_merge( $file_types, $new_filetypes );
		return $file_types;
	}
}
