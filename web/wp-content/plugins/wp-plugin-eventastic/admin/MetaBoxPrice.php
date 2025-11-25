<?php

namespace Eventastic\Admin;

/**
 * Meta box for event price information
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

class MetaBoxPrice extends AbstractMetaBox {
 
	public const ID = "event_price_box";
	public const TITLE = "Event Price";

	public const META_KEY_PRICE = 			array("key" => Constants::WP_POST_META_KEY_PREPEND."price", "label" => "Price", "icon" => "fas fa-dollar-sign");
	public const META_KEY_PRICE_VARIES =	array("key" => Constants::WP_POST_META_KEY_PREPEND."price_varies", "label" => "Price Varies", "choices" => 
												array(
													array("key" => "varies", "label" => "Price varies")
												)
											);
	public const META_KEY_PRICE_LINK = 		array("key" => Constants::WP_POST_META_KEY_PREPEND."ticket_link", "label" => "Tickets URL", "icon" => "fas fa-link");
	
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
		echo "<p>".__("If your event is free, leave the price blank.")."</p>";

		// price and ticket link
		echo FormControlLayout::renderNumberInput($postMeta, self::META_KEY_PRICE,
				"price", false, false, false, self::META_KEY_PRICE["icon"]);
		echo FormControlLayout::renderCheckboxInput(
			$postMeta, self::META_KEY_PRICE_VARIES, "", true, false, true, true);
		echo FormControlLayout::renderUrlFontAwesomeInput($postMeta, self::META_KEY_PRICE_LINK,
				"regular-text", self::META_KEY_PRICE_LINK["icon"]);
	}
 
	/**
	 * Event meta box content save upon submission
	 *
	 * @param int $postId The related post id
	 * @return void
	 */
	public static function saveMetaBoxData ($postId) {
		
		parent::_savePassedData(
			self::ID,
			array(
				self::META_KEY_PRICE["key"],
				self::META_KEY_PRICE_VARIES["key"],
				self::META_KEY_PRICE_LINK["key"]
			),
			$postId
		);
	}
}
