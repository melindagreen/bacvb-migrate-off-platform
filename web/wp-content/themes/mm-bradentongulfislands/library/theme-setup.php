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

		add_filter('render_block', array( get_called_class(), 'add_photo_credit' ), 10, 2);
		add_filter('wp_get_attachment_url', array( get_called_class(), 'photo_credit_url_param' ), 10, 2);

		// global override for from emails
        add_filter( 'wp_mail_from', array( get_called_class(), 'custom_wp_mail_from' ) );
        add_filter( 'wp_mail_from_name', array( get_called_class(), 'custom_wp_mail_from_name' ) );
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
	 * Photo Credit for Core Block
	 */
	public static function add_photo_credit($block_content, $block) {

		if (($block['blockName'] === 'core/image' || $block['blockName'] === 'core/cover') && $block['attrs']['photoCredit'] ) {
			$imageId = $block['attrs']['id'];
			$photoCredit = get_field('photo_credit', $imageId);
			$position = strpos($block_content, '<img ');
			$photoIcon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 302.5 214.5" style="enable-background:new 0 0 302.5 214.5;" xml:space="preserve"> <style type="text/css"> .st0{fill:#FFFFFF;} </style> <path class="st0" d="M279.8,44.5h-50.4l-7.9-23.7c-3.2-9.8-12.4-16.3-22.6-16.3h-95.6c-10.3,0-19.4,6.6-22.6,16.3l-7.9,23.7H22.2 C9.7,44.5-0.5,54.7-0.5,67.2v124.6c0,12.6,10.2,22.7,22.7,22.7h257.6c12.5,0,22.7-10.2,22.7-22.7V67.2 C302.5,54.7,292.3,44.5,279.8,44.5z M151,191.7c-37.7,0-68.2-30.6-68.2-68.2s30.6-68.2,68.2-68.2s68.2,30.6,68.2,68.3 S188.7,191.7,151,191.7z"/> </svg>';
			$photoCreditContent = '<div class="photocredit" data-photocredit="'. $photoCredit .'">'. $photoIcon .'</div>';
			$modifiedContent = substr_replace($block_content, $photoCreditContent . '<img ', $position, 0);
			$block_content = $position !== false ? $modifiedContent : $block_content;
		}
	
		return $block_content;
	}

	public static function photo_credit_url_param($url, $attachment_id) {

		$photoCredit = get_field('photo_credit', $attachment_id);

		if(isset($photoCredit) && $photoCredit !== '') {

			$url = $url .'?photocredit='.urlencode($photoCredit);
		}

		return $url;
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
     * Change default email for all sends to the domain
     */
    public static function custom_wp_mail_from ($original_email_address) {
        $urlparts = wp_parse_url(home_url());
		// $host = str_replace("www.", "", $urlparts['host']);
		$host = $urlparts['host'];
        return "nobody@{$host}";
	}

	/**
     * Change default email from name for all sends to the domain
     */
    public static function custom_wp_mail_from_name ($original_email_from) {
        return "Do Not Reply";
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
