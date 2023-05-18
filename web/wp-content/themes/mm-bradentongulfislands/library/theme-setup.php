<?php /**
 * This code adds base theme options
 */

namespace MaddenNino\Library;

class ThemeSetup {
	function __construct () {
		add_action( 'after_setup_theme', array( get_called_class(), 'madden_theme_support' ) );
		
		// Add 'partner' role with contributor capabilities
add_role('partner', 'Partner', get_role('contributor')->capabilities);

// Allow partners to edit their own posts
add_action('init', 'allow_partner_edit_posts');
function allow_partner_edit_posts() {
    $partner_role = get_role('partner');
    $partner_role->add_cap('edit_posts');
    $partner_role->add_cap('edit_published_posts');
}

// Set partner posts to pending review after edit
add_action('pre_post_update', 'set_partner_posts_pending_review', 10, 2);
function set_partner_posts_pending_review($new_post, $old_post) {
    if ($new_post->post_type === 'post' && current_user_can('partner') && $new_post->post_status === 'publish') {
        $new_post->post_status = 'pending';
        $new_post->post_date = $old_post->post_date;
        $new_post->post_date_gmt = $old_post->post_date_gmt;
        $new_post->post_modified = $old_post->post_modified;
        $new_post->post_modified_gmt = $old_post->post_modified_gmt;
        $new_post->post_name = $old_post->post_name;
        $new_post->post_title = $old_post->post_title;
        $new_post->post_content = $old_post->post_content;
        $new_post->post_excerpt = $old_post->post_excerpt;
        $new_post->post_password = $old_post->post_password;
        $new_post->post_parent = $old_post->post_parent;
        $new_post->post_mime_type = $old_post->post_mime_type;
        $new_post->guid = $old_post->guid;
        $new_post->menu_order = $old_post->menu_order;
        $new_post->post_category = $old_post->post_category;
    }
}

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
}
