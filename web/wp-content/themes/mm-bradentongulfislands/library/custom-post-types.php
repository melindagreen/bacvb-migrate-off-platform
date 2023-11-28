<?php /**
* Code for registering custom post types and taxonomies. Extends MaddenMadre.
*/

namespace MaddenNino\Library;
use \MaddenMadre\Library\CustomPostTypes as MadreCPT;
class CustomPostTypes extends MadreCPT {
	// Define all CPTs here!
	protected const POST_TYPES = [
		// 'example' => [
		// 	'singular_label' => 'Example type',
		// 	'plural_label' => 'Example types',
		// 	'args' => [
		// 		'description' => 'An example post type',
		// 		'menu_icon' => 'dashicons-smiley',
		// 		'taxonomies' => [],
		// 	],
		// ],
		'listing' => [
			'singular_label' => 'Listing',
			'plural_label' => 'Listings',
			'args' => [
				'description' => '',
				'menu_icon' => 'dashicons-store',
				'taxonomies' => ['team', 'listing_categories'],
			],
			'show_in_rest' => true,
		],
	];

	// Define all custom taxonomies here!
	protected const TAXONOMIES = [
		'team' => [
			'singular_label' => 'Team',
			'plural_label' => 'Teams',
			'object_type' => [ 'team' ],
			'args' => [
				'description' => 'teams',
			],
			'showAdminColumn' => true
		],
	];

	function __construct() {
		// Call parent constructor to register CPTs and custom taxonomies
		parent::__construct( self::POST_TYPES, self::TAXONOMIES );
	}
}
