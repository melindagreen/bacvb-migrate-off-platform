<?php
namespace MaddenMedia\KrakenCore;

class AdminDashboard {
    public static function init() {
		/* Remove Site Health capability filter */
		remove_filter('user_has_cap', 'wp_maybe_grant_site_health_caps', 1, 4);

		/* Remove Site Health from admin menu */
		add_action('admin_menu', function() {
			remove_submenu_page('tools.php', 'site-health.php');
		}, 999);

		/* Remove WordPress news widget completely */
		add_action('wp_user_dashboard_setup', function() {
			remove_meta_box('dashboard_primary', 'dashboard-user', 'side');
		});

		/* Disable all unwanted dashboard widgets */
		/* Disable Site Health, WordPress Events and News dashboard widgets */
		add_action('wp_dashboard_setup', function() {
			global $wp_meta_boxes;
			remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
			remove_meta_box('dashboard_primary', 'dashboard', 'side');
			unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
			unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		});
	}
}
