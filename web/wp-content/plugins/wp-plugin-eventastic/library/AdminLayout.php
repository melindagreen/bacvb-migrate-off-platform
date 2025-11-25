<?php

namespace Eventastic\Library;

/**
 * Layout for the admin area of the plugin
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

class_exists('Constants', false) or require_once 'Constants.php';

class AdminLayout {
	
	/**
	 * If we are on the post admin screen, then we need to do stuff
	 */
	public static function admin_screen_listener () {

		$screen = get_current_screen();
		if ( ($screen->post_type == Constants::PLUGIN_CUSTOM_POST_TYPE) 
				&& ($screen->id == Constants::PLUGIN_CUSTOM_POST_TYPE) ) {
			// PENDING - not currently needed
		}
	}
}

?>
