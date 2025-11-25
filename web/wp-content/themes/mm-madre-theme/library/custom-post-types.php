<?php /**
* Code for registering custom post types and taxonomies. Intended to be expanded upon in child theme.
*/

namespace MaddenMadre\Library;

class CustomPostTypes {
	protected const DEFAULT_CPT_ARGS = [
		'public' => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'supports' => [
			'title',
			'editor',
			'revisions',
			'author',
			'excerpt',
			'page-attributes',
			'custom-fields',
			'thumbnail',
		],
	];

	protected const DEFAULT_TAX_ARGS = [
		'public' => true,
		'hierarchical' => true,
		'show_in_rest' => true,
	];

	protected $post_types = [];
	protected $taxonomies = [];

	function __construct ( $post_types, $taxonomies ) {
		$this->post_types = $post_types;
		$this->taxonomies = $taxonomies;

		// register custom post types & taxonomies
		add_action( 'init', array( $this, 'register_theme_cpts' ) );
		add_action( 'init', array( $this, 'register_theme_taxonomies' ) );

		// add default categories & tags to pages
		add_action( 'init', array( $this, 'add_taxes_to_pages' ) );
	}

	/**
	 * Build labels for a custom post type from a singular and plural noun
	 * @param string $singular						The singular noun
	 * @param string $plural						The plural noun
	 * @param boolean $read_only					Are these labels for a read-only post-type?
	 */
	public static function build_cpt_labels( $singular, $plural, $read_only = false ) {
		$action = ( $read_only ) ? 'View' : 'Edit';

		return array(
			'name' => _x( $plural, 'post type general name' ),
			'singular_name' => __( $singular, 'post type singular name' ),
			'add_new' => __( 'Add New '.$singular ),
			'add_new_item' => __( 'Add New '.$singular ),
			'edit_item' => __( $action.' '.$singular ),
			'new_item' => __( 'New '.$singular ),
			'view_item' => __( 'View '.$singular ),
			'search_items' => __( 'Search '.$singular ),
			'not_found' =>  __( 'No '.$plural.' found' ),
			'not_found_in_trash' => __( 'No '.$plural.' found in Trash' ),
			'parent_item_colon' => ''
		);
	}

	/**
	 * Build labels for a custom taxonomy from a singular and plural noun
	 * @param string $singular						The singular noun
	 * @param string $plural						The plural noun
	 * @param boolean $read_only					Are these labels for a read-only taxonomy?
	 */
	public static function build_custom_tax_labels( $singular, $plural, $read_only = false ) {
		$action = ( $read_only ) ? 'View' : 'Edit';

		return array(
			'name' => _x( $plural, 'post type general name' ),
			'singular_name' => __( $singular, 'post type singular name' ),
			'add_new' => __( 'Add New '.$singular ),
			'add_new_item' => __( 'Add New '.$singular ),
			'edit_item' => __( $action.' '.$singular ),
			'new_item' => __( 'New '.$singular ),
			'view_item' => __( 'View '.$singular ),
			'search_items' => __( 'Search '.$singular ),
			'not_found' =>  __( 'No '.$plural.' found' ),
			'not_found_in_trash' => __( 'No '.$plural.' found in Trash' ),
			'parent_item_colon' => '',
		);
	}

	/**
	 * Register all custom post types for this theme.
	 * @return null
	 */
	public function register_theme_cpts() {
		foreach( $this->post_types as $name => $post_type ) {
			// Build labels
			$post_type['args']['labels'] = self::build_cpt_labels( 
				$post_type['singular_label'],
				$post_type['plural_label']
			);

			// and register!
			register_post_type(
				$name,
				array_merge( self::DEFAULT_CPT_ARGS, $post_type['args'] )
			);
		}
	}

	/**
	 * Register all custom taxonomies for this theme
	 * @return null
	 */
	public function register_theme_taxonomies() {
		foreach( $this->taxonomies as $name => $taxonomy ) {
			// Build labels
			$taxonomy['args']['labels'] = self::build_custom_tax_labels(
				$taxonomy['singular_label'],
				$taxonomy['plural_label']
			);

			// and register!
			register_taxonomy(
				$name,
				$taxonomy['object_type'],
				array_merge(
					self::DEFAULT_TAX_ARGS,
					$taxonomy['args']
				)
			);
		}
	}
	
	/**
	 * Add categories and tags to pages
	 */
	public static function add_taxes_to_pages() {
		register_taxonomy_for_object_type( 'post_tag', 'page' );
		register_taxonomy_for_object_type( 'category', 'page' ); 
	}
}
