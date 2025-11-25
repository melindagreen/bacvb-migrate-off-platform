<?php

namespace Eventastic\Admin;

/**
 * Layout for the admin area of the plugin
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Library\Utilities as Utilities;

class AdminLayout {
	
	/**
	 * If we are on the post admin screen, then we need to do stuff
	 */
	public static function admin_screen_listener () {

		$screen = get_current_screen();
		$postType = Utilities::getPluginPostType();
		if ( ($screen->post_type == $postType) && ($screen->id == $postType) ) {
			// PENDING unused
		}
	}
}

?>
