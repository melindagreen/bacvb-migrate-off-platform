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
	];

	// Define all custom taxonomies here!
	protected const TAXONOMIES = [
		// 'example_tax' => [
		// 	'singular_label' => 'Example taxonomy term',
		// 	'plural_label' => 'Example taxonomy terms',
		// 	'object_type' => [ 'example' ],
		// 	'args' => [
		// 		'description' => 'An example custom taxonomy',
		// 	],
		// ],
	];

	function __construct() {
		// Call parent constructor to register CPTs and custom taxonomies
		parent::__construct( self::POST_TYPES, self::TAXONOMIES );
	}
}
