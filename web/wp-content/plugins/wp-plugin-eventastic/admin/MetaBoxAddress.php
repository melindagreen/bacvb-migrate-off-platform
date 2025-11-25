<?php

namespace Eventastic\Admin;

/**
 * Meta box for event address information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */
 
require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');
require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Admin\AbstractMetaBox as AbstractMetaBox;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\FormControlLayout as FormControlLayout;
use Eventastic\Library\Utilities as Utilities;

/**
 * Meta box for address fields
 */
class MetaBoxAddress extends AbstractMetaBox {
 
	public const ID = "event_address_box";
	public const TITLE = "Event Address Information";

	public const META_KEY_ADDRRESS_MULTI =	array("key" => Constants::WP_POST_META_KEY_PREPEND."addr_multi", "label" => "Location(s)", "icon" => "fas fa-map-pin");
	public const META_KEY_ADDRRESS_ONE =	array("key" => Constants::WP_POST_META_KEY_PREPEND."addr1", "label" => "Address 1", "icon" => "far fa-address-card");
	public const META_KEY_ADDRRESS_TWO =	array("key" => Constants::WP_POST_META_KEY_PREPEND."addr2", "label" => "Address 2");
	public const META_KEY_CITY = 			array("key" => Constants::WP_POST_META_KEY_PREPEND."city", "label" => "City");
	public const META_KEY_STATE = 			array("key" => Constants::WP_POST_META_KEY_PREPEND."state", "label" => "State", "options" => Constants::SELECT_STATES);
	public const META_KEY_ZIP = 			array("key" => Constants::WP_POST_META_KEY_PREPEND."zip", "label" => "Zip");
	public const META_KEY_LAT = 			array("key" => Constants::WP_POST_META_KEY_PREPEND."lat", "label" => "Latitude", "icon" => "fas fa-map-marker-alt");
	public const META_KEY_LNG = 			array("key" => Constants::WP_POST_META_KEY_PREPEND."lng", "label" => "Longitude", "icon" => "fas fa-map-marker-alt");
	
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
		echo "<p>".__("If your event is held at multiple locations, describe that here.")."</p>";

		// multiple locations
		echo FormControlLayout::renderTextFontAwesomeInput($postMeta, self::META_KEY_ADDRRESS_MULTI, 
				"regular-text", self::META_KEY_ADDRRESS_MULTI["icon"], true);

		// instructions
		if( Utilities::getVenueMode() ){
			echo "<p>".__('If your event is hosted at a specific location that might be hosting other events, '
				.'consider adding a <a href="').get_site_url().__('/wp-admin/edit-tags.php?taxonomy=eventastic_venues&post_type=eventasticvenue">Venue</a> '
				.'instead and tying the event to that location')."</p>";
		}
		else{
			echo "<p>".__('Single Location Address:</br></br>');
		}
		
		// address
		echo FormControlLayout::renderTextFontAwesomeInput($postMeta, self::META_KEY_ADDRRESS_ONE, 
				"regular-text", self::META_KEY_ADDRRESS_ONE["icon"], true);
		echo FormControlLayout::renderTextInput($postMeta, self::META_KEY_ADDRRESS_TWO, "regular-text", true);
		echo FormControlLayout::renderTextInput($postMeta, self::META_KEY_CITY, "", false);
		echo FormControlLayout::renderSelectInput($postMeta, self::META_KEY_STATE, "", false, true, self::META_KEY_STATE["options"]);
		echo FormControlLayout::renderTextInput($postMeta, self::META_KEY_ZIP, "zip", true, true);
			
		// lat/lng fields
		echo FormControlLayout::renderTextFontAwesomeInput($postMeta, self::META_KEY_LAT, 
				"regular-text", self::META_KEY_LAT["icon"], true);
		echo FormControlLayout::renderTextFontAwesomeInput($postMeta, self::META_KEY_LNG, 
				"regular-text", self::META_KEY_LAT["icon"]);
		if (get_option(SettingsAdminLayout::SETTING_OPENCAGE_API_KEY["key"], "") != "") {
			echo '<a href="javascript:lookupLatLng()">'.__("Lookup from address").'</a>';
		}
		?>
		<script type="text/javascript">
		// lat/lng update function
		function lookupLatLng () {
			var address = jQuery('#<?php echo self::META_KEY_ADDRRESS_ONE["key"] ?>').val()
				+ ' ' + jQuery('#<?php echo self::META_KEY_ADDRRESS_TWO["key"] ?>').val()
				+ ' ' + jQuery('#<?php echo self::META_KEY_CITY["key"] ?>').val()
				+ ' ' + jQuery('#<?php echo self::META_KEY_STATE["key"] ?>').val()
				+ ' ' + jQuery('#<?php echo self::META_KEY_ZIP["key"] ?>').val();
			// if there's an address, run it
			if ( (address.trim() == "") || (address.trim() == jQuery('#<?php echo self::META_KEY_STATE["key"] ?>').val()) ) {
				// clear 'em
				jQuery('#<?php echo self::META_KEY_LAT["key"] ?>').val("");
				jQuery('#<?php echo self::META_KEY_LNG["key"] ?>').val("");
				// and we tested to see if they cleared it all but state - clear that too now
				jQuery('#<?php echo self::META_KEY_STATE["key"] ?>').val("");
			} else {
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						'action': 'eventastic_lookup_lat_lng',
						'eventasticid': '<?php echo $post->ID ?>',
						'address': address,
						'_wpnonce': '<?php echo wp_create_nonce(Constants::NONCE_ROOT.$post->ID) ?>'
					},
					success: function(data) {
						try {
							var json = JSON.parse(data);
							if (json.status == 'OK') {
								// set the lat/lng!
								jQuery('#<?php echo self::META_KEY_LAT["key"] ?>').val(json.data.lat);
								jQuery('#<?php echo self::META_KEY_LNG["key"] ?>').val(json.data.lng);
							} else {
								console.log(json.msg);
								alert(json.msg);
							}
						} catch (error) {
							console.log(error);
							alert(error);
						}
					},
					error: function(errorThrown) {
						alert(errorThrown);
					}
				});
			}
		}
		</script>
		<?php
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
				self::META_KEY_ADDRRESS_MULTI["key"],
				self::META_KEY_ADDRRESS_ONE["key"],
				self::META_KEY_ADDRRESS_TWO["key"],
				self::META_KEY_CITY["key"],
				self::META_KEY_STATE["key"],
				self::META_KEY_ZIP["key"],
				self::META_KEY_LAT["key"],
				self::META_KEY_LNG["key"]
			),
			$postId
		);		
	}
}
