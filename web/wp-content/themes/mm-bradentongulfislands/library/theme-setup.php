<?php /**
 * This code adds base theme options
 */

namespace MaddenNino\Library;

class ThemeSetup {
	function __construct () {
		add_action( 'after_setup_theme', array( get_called_class(), 'madden_theme_support' ) );
		add_action( 'init', 'add_partner_role' );
	}

	public static function madden_theme_support() {
		// Register default menus
		register_nav_menus(
			array(
				'main-nav' => __( 'Main Menu', 'mmnino' ),   // main nav in header
				'secondary-nav' => __( 'Secondary Menu', 'mmnino' ),   // secondary nav in header
				'footer-nav' => __( 'Footer Menu', 'mmnino' ), // footer nav
			)
		);

		// Add custom logo support
		add_theme_support( 'custom-logo', array(
			'width' => 300,
			'height' => 200,
			'flex-width' => true,
			'flex-height' => true,
		) );
	}

	function add_partner_role() {
		// Get the contributor role object
		$contributor = get_role( 'contributor' );
		
		// Create the partner role as a copy of the contributor role
		$partner = add_role(
		  'partner',
		  __( 'Partner', 'textdomain' ),
		  $contributor->capabilities // use the same capabilities as the contributor role
		);
		
		// Add the 'edit_published_posts' capability to the partner role
		$partner->add_cap( 'edit_published_posts' );
	  }  
}
