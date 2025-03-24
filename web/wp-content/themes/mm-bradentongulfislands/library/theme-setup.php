<?php /**
 * This code adds base theme options
 */

namespace MaddenNino\Library;

class ThemeSetup {
	function __construct () {
		 flush_rewrite_rules(false);
		add_action( 'after_setup_theme', array( get_called_class(), 'madden_theme_support' ) );
		add_action( 'init', array( get_called_class(), 'add_custom_rewrites' ) );
		add_filter( 'pre_post_link', array(get_called_class(), 'prepend_post_permalinks'), 10, 2);
		add_action( 'template_redirect', array(get_called_class(), 'redirect_single_posts'));
		add_action( 'wp', array(get_called_class(),'fareharbor_scripts') );
		// add_action('gform_after_submission', array( get_called_class(), 'add_to_newsletter' ), 10, 2);

		add_action( 'template_redirect', array(get_called_class(), 'disable_author_archives'));
		add_action( 'template_redirect', array(get_called_class(), 'disable_team_archives'));
		//add_action( 'template_redirect', array(get_called_class(), 'redirect_non_logged_in_users'));

			
		// tell yoast to not show some sitemaps
		add_filter( 'wpseo_sitemap_exclude_taxonomy', array( get_called_class(), 'sitemap_exclude_taxonomy' ), 10, 2 );

		add_filter('render_block', array( get_called_class(), 'add_photo_credit' ), 10, 2);
		add_filter('wp_get_attachment_url', array( get_called_class(), 'photo_credit_url_param' ), 10, 2);

		add_filter('the_content', array( get_called_class(),'add_raf_trademark'));

		// User Role
		add_filter( 'init', array( get_called_class(), 'add_custom_roles' ) );
		add_action('admin_menu', array( get_called_class(), 'restrict_admin_menu'), 999);
		add_action('pre_get_posts', array( get_called_class(), 'filter_posts_by_author'));

		// global override for from emails
        add_filter( 'wp_mail_from', array( get_called_class(), 'custom_wp_mail_from' ) );
        add_filter( 'wp_mail_from_name', array( get_called_class(), 'custom_wp_mail_from_name' ) );

		// Bypass Nonce
		add_filter('acf/form_data', array(get_called_class(), 'modify_acf_form_data'), 10, 1);
	}

	public static function modify_acf_form_data($form_data) {
		// Log the form submission
		error_log('Form Submitted: ' . print_r($form_data, true));

		// Check if the current user is the user with the email 'info@gulfislandsferry.com'.
		$user = get_user_by('email', 'info@gulfislandsferry.com');
		$isValidUser = $user && $user->ID == get_current_user_id();

		// Verify if the ACF group: Ferry Banner exists in the form submission
		$isFerryBanner = isset($_POST['acf_field_group']) && $_POST['acf_field_group'] === 'group_673ae8f7c13d1';

		// If the user is valid and the correct ACF group is detected
		if ($isValidUser && $isFerryBanner) {
			error_log('ACF validation: Valid user and Ferry Banner group detected.');

			// Optionally, modify the form data or add custom nonce handling here
			$custom_nonce = wp_create_nonce('custom_acf_form_action');
			$form_data['fields'][] = [
				'type'  => 'hidden',
				'name'  => '_acf_nonce',
				'value' => $custom_nonce,
			];
		} else {
			// If validation fails, log and return false to prevent form submission
			error_log('ACF validation: Invalid user or Ferry Banner group not detected.');
			return false;
		}

		// Return the modified form data
		return $form_data;
	}



	public static function fareharbor_scripts() {
		// Check if the page/post contains the specific FareHarbor URL
		if (is_page() || is_singular()) {
			global $post;
			// Check if the content contains the specific FareHarbor base URL
			if ( strpos( $post->post_content, 'fareharbor.com/embeds/book/gulfislandsferry' ) !== false ) {
				// If the link is found, enqueue the FareHarbor script
				add_action( 'wp_enqueue_scripts', array( 'fareharbor', 'maybe_enqueue_fh_kit_styles' ) );
			} else {
				// If the link is not found, remove the script
				remove_action( 'wp_enqueue_scripts', array( 'fareharbor', 'maybe_enqueue_fh_kit_styles' ) );
			}
		}
	}

	public static function add_raf_trademark($content) {
		$raf_phrase = 'Real. Authentic. Florida.';
		$tm_markup = $raf_phrase . '<sup class="raf-tm">TM</sup> ';
	
		// Replace occurrences of the phrase with the trademarked version
		$content = str_replace($raf_phrase, $tm_markup, $content);
	
		return $content;
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
	 * Custom Role
	 */
	 public static function add_custom_roles() {

		remove_role('water_ferry');
		   // Get the editor role
		   $editor = get_role('editor');
    
		   // Define the custom capabilities
		//    $custom_capabilities = array(
		// 	   'read' => true,
		// 	   'edit_posts' => true,
		// 	   'delete_posts' => false,
		// 	   'publish_posts' => true,
		// 	   'upload_files' => true,
		// 	   'edit_others_posts' => false,
		// 	   'edit_published_posts' => true,
		// 	   'read_private_posts' => true,
		// 	   'delete_others_posts' => false,
		// 	   'delete_private_posts' => false,
		// 	   'delete_published_posts' => false,
		// //    );
		   
		   // Merge editor capabilities with custom capabilities
		   $editor_capabilities = $editor->capabilities;
	   
		   // Add a new role with the merged capabilities
		   add_role('water_ferry', 'Water Ferry', $editor_capabilities);

		   $role = get_role('water_ferry');
		   if ($role) {
				// Add custom capabilities
				$role->add_cap('read', true);
				$role->add_cap('edit_posts', true);
				$role->add_cap('publish_posts', true);
				$role->add_cap('edit_others_posts', false);
				$role->add_cap('delete_others_posts', false);
				$role->add_cap('delete_private_posts', false);
				$role->add_cap('delete_published_posts', false);
				$role->add_cap('edit_others_pages', false);
				$role->add_cap('delete_others_pages', false);
				$role->add_cap('delete_private_pages', false);
				$role->add_cap('delete_published_pages', false);
				$role->add_cap('upload_files', true);
				$role->add_cap('edit_published_posts', true);
				$role->add_cap('read_private_posts', true);
				
				// Remove specific capabilities
				// $role->remove_cap('delete_posts');
				// $role->remove_cap('edit_others_posts');
				// $role->remove_cap('delete_others_posts');
				// $role->remove_cap('delete_private_posts');
				// $role->remove_cap('delete_published_posts');
			}
	 
	}


	 public static function restrict_admin_menu() {
		// Get the current user
		$current_user = wp_get_current_user();
	
		// Water Ferry
		if (in_array('water_ferry', $current_user->roles)) {
	
			remove_menu_page('index.php');                  
			remove_menu_page('upload.php');                 
			remove_menu_page('edit-comments.php');       
			remove_menu_page('themes.php');               
			remove_menu_page('plugins.php');             
			remove_menu_page('users.php');             
			remove_menu_page('tools.php');              
			remove_menu_page('options-general.php');    
			remove_menu_page('admin.php');    
			remove_menu_page('edit.php?post_type=listing');       
			remove_menu_page('edit.php?post_type=event'); 
			remove_menu_page('edit.php?post_type=page'); 
			remove_menu_page('edit.php'); 

			// Remove Rank Math menu items
			remove_menu_page('rank-math'); 
			remove_submenu_page('rank-math', 'rank-math-dashboard');
			remove_submenu_page('rank-math', 'rank-math-general');
			remove_submenu_page('rank-math', 'rank-math-titles'); 
			remove_submenu_page('rank-math', 'rank-math-sitemap'); 
			remove_submenu_page('rank-math', 'rank-math-status'); 
			remove_submenu_page('rank-math', 'rank-math-help'); 

			// Remove MemberPress menu items
			remove_menu_page('memberpress'); 
			remove_submenu_page('memberpress', 'admin.php?page=memberpress-options'); 
			remove_submenu_page('memberpress', 'admin.php?page=memberpress-reports'); 
			remove_submenu_page('memberpress', 'admin.php?page=memberpress-trans'); 
			remove_submenu_page('memberpress', 'admin.php?page=memberpress-members');
	
			// Remove HubSpot menu items
			remove_menu_page('leadin');
        	remove_submenu_page('leadin', 'admin.php?page=leadin_user_guide'); 
		}
	}	

	public static function filter_posts_by_author($query) {
		// Check if the user has the 'water_ferry' role and if we're in the admin area
		if (in_array('water_ferry', wp_get_current_user()->roles)) {
			// Check if we're querying posts or pages
			if ($query->is_main_query() && ($query->is_post_type_archive('post') || $query->is_post_type_archive('page'))) {
				// Set the author parameter to the current user's ID
				$query->set('author', get_current_user_id());
			}
		}
	}

	/**
	 * Photo Credit for Core Block
	 */
	// Causing issues with certain elements like grid blocks - Aaron F. 
	// public static function add_photo_credit($block_content, $block) {
		
	// 	if (($block['blockName'] === 'core/image' || $block['blockName'] === 'core/cover' || $block['blockName'] === 'core/video') && isset($block['attrs']['photoCredit']) && $block['attrs']['photoCredit'] ) {
	// 		$imageId = $block['attrs']['id'];
	// 		$photoCredit = get_field('photo_credit', $imageId);
	// 		$position = $block['blockName'] === 'core/video' ? strpos($block_content, '<video ') : strpos($block_content, '<img ');
	// 		$photoIcon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 302.5 214.5" style="enable-background:new 0 0 302.5 214.5;" xml:space="preserve"> <style type="text/css"> .st0{fill:#FFFFFF;} </style> <path class="st0" d="M279.8,44.5h-50.4l-7.9-23.7c-3.2-9.8-12.4-16.3-22.6-16.3h-95.6c-10.3,0-19.4,6.6-22.6,16.3l-7.9,23.7H22.2 C9.7,44.5-0.5,54.7-0.5,67.2v124.6c0,12.6,10.2,22.7,22.7,22.7h257.6c12.5,0,22.7-10.2,22.7-22.7V67.2 C302.5,54.7,292.3,44.5,279.8,44.5z M151,191.7c-37.7,0-68.2-30.6-68.2-68.2s30.6-68.2,68.2-68.2s68.2,30.6,68.2,68.3 S188.7,191.7,151,191.7z"/> </svg>';

	// 		// FUTURE : This feels a little dicey, in that we have 2 known cases for captions - one is
	// 		//	when it has a figure/image in it (e.g. blogs) and one when it has a cover image in it - the
	// 		//	same CSS cannot work for both to ensure that the credit icon is in the upper left, so we
	// 		//	have logic for either
	// 		// KEYWORD: photoCredit2025
	// 		$photoCreditContent = (strpos($block_content, 'wp-block-cover__image-background') !== false)
	// 			? '<div class="photocreditWrap contentIsBackground"><div class="photocredit" data-photocredit="'. $photoCredit .'">'. $photoIcon .'</div></div>'
	// 			: '<div class="photocreditWrap"><div class="photocredit" data-photocredit="'. $photoCredit .'">'. $photoIcon . '</div>';

	// 		// add the svg and credit (either version)
	// 		$modifiedContent = $block['blockName'] === 'core/video' 
	// 			? substr_replace($block_content, $photoCreditContent . '<video ', $position, 0) 
	// 			: substr_replace($block_content, $photoCreditContent . '<img ', $position, 0);

	// 		// our non-cover version has other logic to close later
	// 		if (strpos($block_content, 'wp-block-cover__image-background') === false) {
	// 			// and close the div
	// 			$modifiedContent = trim($modifiedContent);
	// 			if (self::_endsWith($modifiedContent, '</figure>')) {
	// 				$modifiedContent = str_replace('</figure>', '</div></figure>', $modifiedContent);
	// 			} else if (self::_endsWith($modifiedContent, '</div>')) {
	// 				$pos = strrpos($modifiedContent, '</div>');
	// 				if ($pos !== false) {
	// 					$modifiedContent = substr_replace($modifiedContent, '</div></div>', $pos, strlen('</div>'));
	// 				}
	// 			} 
	// 		}
			
	// 		$block_content = $position !== false ? $modifiedContent : $block_content;
	// 	}

	// 	return $block_content;
	// }

	public static function add_photo_credit($block_content, $block) {

		if (($block['blockName'] === 'core/image' || $block['blockName'] === 'core/cover' || $block['blockName'] === 'core/video') && isset($block['attrs']['photoCredit']) && $block['attrs']['photoCredit'] ) {
			$imageId = $block['attrs']['id'];
			$photoCredit = get_field('photo_credit', $imageId);
			$position = $block['blockName'] === 'core/video' ? strpos($block_content, '<video ') : strpos($block_content, '<img ');
			$photoIcon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 302.5 214.5" style="enable-background:new 0 0 302.5 214.5;" xml:space="preserve"> <style type="text/css"> .st0{fill:#FFFFFF;} </style> <path class="st0" d="M279.8,44.5h-50.4l-7.9-23.7c-3.2-9.8-12.4-16.3-22.6-16.3h-95.6c-10.3,0-19.4,6.6-22.6,16.3l-7.9,23.7H22.2 C9.7,44.5-0.5,54.7-0.5,67.2v124.6c0,12.6,10.2,22.7,22.7,22.7h257.6c12.5,0,22.7-10.2,22.7-22.7V67.2 C302.5,54.7,292.3,44.5,279.8,44.5z M151,191.7c-37.7,0-68.2-30.6-68.2-68.2s30.6-68.2,68.2-68.2s68.2,30.6,68.2,68.3 S188.7,191.7,151,191.7z"/> </svg>';
			$photoCreditContent = '<div class="photocredit" data-photocredit="'. $photoCredit .'">'. $photoIcon .'</div>';
			$modifiedContent = $block['blockName'] === 'core/video' ? substr_replace($block_content, $photoCreditContent . '<video ', $position, 0) : substr_replace($block_content, $photoCreditContent . '<img ', $position, 0);
			$block_content = $position !== false ? $modifiedContent : $block_content;
		}

		return $block_content;
	}

	/**
	 * private helper function for photo credit
	 */
	private static function _endsWith($string, $ending) {
		$len = strlen($ending);
		if ($len == 0) {
			return true;
		}
		return substr($string, -$len) === $ending;
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
		if ( ! is_user_logged_in() && is_main_query() && is_single() && ( empty( get_post_type() ) || (get_post_type() === 'post') ) ) {
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
		$host = str_replace("www.", "", $urlparts['host']);
        return "nobody@{$host}";
	}

	/**
     * Change default email from name for all sends to the domain
     */
    public static function custom_wp_mail_from_name ($original_email_from) {
        return "Bradenton Gulf Islands";
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


		// Disable author archives
	public static function disable_author_archives() {
		if (is_author()) {
			global $wp_query;
			$wp_query->set_404();
			status_header(404);
			nocache_headers();
			include(get_query_template('404'));
			exit;
		}
	}

	// Disable custom post type "teams" archives
	public static function disable_team_archives() {
		if (is_post_type_archive('teams') || is_singular('teams')) {
			global $wp_query;
			$wp_query->set_404();
			status_header(404);
			nocache_headers();
			include(get_query_template('404'));
			exit;
		}
	}

	// Redirect non-logged-in users from account page to login page
	public static function redirect_non_logged_in_users() {
		if (!is_user_logged_in() && is_page('account')) {
			wp_redirect(home_url('/login/'));
			exit;
		}
	}

}
