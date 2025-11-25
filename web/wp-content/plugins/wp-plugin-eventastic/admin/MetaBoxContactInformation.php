<?php

namespace Eventastic\Admin;

/**
 * Meta box for event contact information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */
 
require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');

use Eventastic\Admin\AbstractMetaBox as AbstractMetaBox;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\FormControlLayout as FormControlLayout;

/**
 * Meta box for contact information
 */

class MetaBoxContactInformation extends AbstractMetaBox {
 
	public const ID = "event_contact_info_box";
	public const TITLE = "Event Contact Information";

	public const META_KEY_URL =				array("key" => Constants::WP_POST_META_KEY_PREPEND."url", "label" => "Event URL", "icon" => "fas fa-link");
	public const META_KEY_FACEBOOK =		array("key" => Constants::WP_POST_META_KEY_PREPEND."facebook", "label" => "Facebook", "icon" => "fab fa-facebook");
	public const META_KEY_TWITTER =			array("key" => Constants::WP_POST_META_KEY_PREPEND."twitter", "label" => "Twitter", "icon" => "fab fa-twitter");
	public const META_KEY_INSTAGRAM =		array("key" => Constants::WP_POST_META_KEY_PREPEND."instagram", "label" => "Instagram", "icon" => "fab fa-instagram");	
	public const META_KEY_EMAIL =			array("key" => Constants::WP_POST_META_KEY_PREPEND."email", "label" => "Email", "icon" => "fas fa-at");
	public const META_KEY_PHONE =			array("key" => Constants::WP_POST_META_KEY_PREPEND."phone", "label" => "Phone", "icon" => "fas fa-phone");
	
	/**
	 * Constructor
	 */
    public function __construct () {

		parent::__construct(self::ID, self::TITLE);

		// add our save action
		add_action('save_post', array($this, 'saveMetaBoxData'));
	}
 
    /**
     * Renders the content of the meta box
	 *
	 * @param object $post The parent post data
	 * @return void
     */
    public function displayMetaBox ($post) {

		// get the post meta
		$postMeta = get_post_meta($post->ID);

		// security
		wp_nonce_field(self::$NONCE_BASE, Constants::NONCE_ROOT.self::ID);
				
		// instructions
		echo "<p>".__("Provide the primary URL that has details for your event here.")."</p>";
	
		// url and contact points
		echo FormControlLayout::renderUrlFontAwesomeInput($postMeta, self::META_KEY_URL, "regular-text", self::META_KEY_URL["icon"], true);
		echo FormControlLayout::renderTextFontAwesomeInput($postMeta, self::META_KEY_EMAIL, "regular-text", self::META_KEY_EMAIL["icon"], false);
		echo FormControlLayout::renderTextFontAwesomeInput($postMeta, self::META_KEY_PHONE, "regular-text", self::META_KEY_PHONE["icon"], true);

		// instructions
		echo "<p>".__("If your event has other specific social sites, provide them here. Use full URLs for social media addresses.")."</p>";

		echo FormControlLayout::renderUrlFontAwesomeInput($postMeta, self::META_KEY_FACEBOOK, "regular-text", self::META_KEY_FACEBOOK["icon"], false);
		echo FormControlLayout::renderUrlFontAwesomeInput($postMeta, self::META_KEY_TWITTER, "regular-text", self::META_KEY_TWITTER["icon"], true);
		echo FormControlLayout::renderUrlFontAwesomeInput($postMeta, self::META_KEY_INSTAGRAM, "regular-text", self::META_KEY_INSTAGRAM["icon"], true);	    
    }
 
	/**
	 * Event meta box content save upon submission
	 *
	 * @param int $postId The related post id
	 * @return void
	 */
	public static function saveMetaBoxData ($postId) {
		
		self::_savePassedData(
			self::ID,
			array(
				self::META_KEY_URL["key"],
				self::META_KEY_FACEBOOK["key"],
				self::META_KEY_TWITTER["key"],
				self::META_KEY_INSTAGRAM["key"],				
				self::META_KEY_EMAIL["key"],
				self::META_KEY_PHONE["key"]
			),
			$postId
		);		
	}
}
