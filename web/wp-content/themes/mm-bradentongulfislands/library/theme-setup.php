<?php /**
 * This code adds base theme options
 */

namespace MaddenNino\Library;

class ThemeSetup {
	function __construct () {
		add_action( 'after_setup_theme', array( get_called_class(), 'madden_theme_support' ) );
		add_action( 'init', array( get_called_class(), 'add_custom_rewrites' ) );
		add_filter( 'pre_post_link', array(get_called_class(), 'prepend_post_permalinks'), 10, 2);
		add_action( 'template_redirect', array(get_called_class(), 'redirect_single_posts'));
		// add_action('gform_after_submission', array( get_called_class(), 'add_to_newsletter' ), 10, 2);

		// tell yoast to not show some sitemaps
		add_filter( 'wpseo_sitemap_exclude_taxonomy', array( get_called_class(), 'sitemap_exclude_taxonomy' ), 10, 2 );
	}

	public static function madden_theme_support() {
		// Register default menus
		register_nav_menus(
			array(
				'shortcuts' => __( 'Shortcuts', 'mmnino' ), // top nav 
				'main-nav' => __( 'Main Menu', 'mmnino' ),   // main nav in header
				'secondary-nav' => __( 'Secondary Menu', 'mmnino' ),   // secondary nav in header
				'footer-one' => __( 'Footer One', 'mmnino' ), // footer nav
				'footer-two' => __( 'Footer Two', 'mmnino' ), // footer nav
				'footer-three' => __( 'Footer Three', 'mmnino' ), // footer nav
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

	/**
	 * Custom rewrite rules for the site
	 */
	public static function add_custom_rewrites() {

		global $wp_rewrite;
		
        add_rewrite_rule('blogs/([^/]+)/?$', 'index.php?post_type=post&name=$matches[1]', 'top');

		// kick it in
		flush_rewrite_rules();	
	}

	/**
     * Prepend /blog/ to post URLs
     */
    public static function prepend_post_permalinks($permalink, $post) {
        if ($post->post_type === 'post') {
			// $permalink = str_replace( $post->post_name, 'blogs/' . $post->post_name, $permalink );
			$permalink = 'blogs'.$permalink;
        }

        return $permalink;
    }

	public static function redirect_single_posts() {
		if ( is_main_query() && is_single() && ( empty( get_post_type() ) || (get_post_type() === 'post') ) ) {
		  if ( strpos( trim( add_query_arg( array() ), '/' ), 'blogs' ) !== 0 ) {
			global $post;
			$url = get_permalink( $post );
			wp_safe_redirect( $url, 301 );
			exit(); 
		  }
		}
	  }


	public static function add_to_newsletter() {

		$source_form_id = 1; // Replace '1' with the ID of your primary form
		$checkbox_field_id = 2; // Replace '2' with the ID of your checkbox field
		$target_form_id = 2; // Replace '3' with the ID of your target form
	
		// Check if the submitted entry is from the primary form and if the checkbox is checked
		if ($form['id'] == $source_form_id) {
			$checkbox_value = rgar($entry, $checkbox_field_id);
			
			// Check if the checkbox is selected
			if ($checkbox_value == '1') {
				$data_to_transfer = array(
					'input_1' => $entry['1'], // Replace '1' with the field ID you want to transfer
					'input_2' => $entry['2'], // Replace '2' with another field ID you want to transfer
					// Add more fields as needed
				);
				
				// Submit data to the target form programmatically
				$result = GFAPI::submit_form($target_form_id, $data_to_transfer);
				
				// Check if submission to the target form was successful
				if (is_wp_error($result)) {
					// Handle error if needed
				} else {
					// Submission successful
				}
			}
		}
	}

	/**
	 * Remove unwanted category taxonomies from the Yoast sitemap 
	 * @param array $value				A value? I'm honestly not sure - but we don't need it here
	 * @param string $taxonomy			The taxonomy to evaluate
	 * @return boolean					Exclude the sitemap?
	 */
	public static function sitemap_exclude_taxonomy( $value, $taxonomy ) {

		$skipTaxonomies = array(
			'category',
			'post_tag',
			'listing_categories',
			'eventastic_categories'
		);

		if ( in_array( $taxonomy, $skipTaxonomies ) ) return true;
	}		
}
