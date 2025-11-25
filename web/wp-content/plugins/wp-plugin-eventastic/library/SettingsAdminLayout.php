<?php

namespace Eventastic\Admin;

/**
 * Layout for the admin area of the plugin
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Library\Constants as Constants;
use Eventastic\Library\Utilities as Utilities;

class SettingsAdminLayout {

	public const SETTING_KEY_GOOGLE_API = array("key" => "eventastic_google_maps_api_key", "label" => "Google API Key");
	public const SETTING_PLUGIN_POST_TYPE = array("key" => "eventastic_post_slug", "label" => "Post Slug");
	public const SETTING_LEAFLET_TILE_LIBRARY = array("key" => "eventastic_leaflet_tile_library", "label" => "Leaflet Tile Library");
	public const SETTING_CATEGORY_COLORS = array("key" => "eventastic_category_colors", "label" => "Category Colors");
	public const SETTING_OPENCAGE_API_KEY = array("key" => "eventastic_opencagedata_api_key", "label" => "OpenCageData API Key");
	public const SETTING_PLUGIN_DEBUG = array("key" => "eventastic_debug", "label" => "Debug mode?", "choices" => array("true" => "Yes", "false" => "No"));
	public const SETTING_PLUGIN_VENUE = array("key" => "eventastic_use_venues", "label" => "Use Venues?", "choices" => array("true" => "Yes", "false" => "No"));
	public const SETTING_PLUGIN_ORGANIZER = array("key" => "eventastic_use_organizers", "label" => "Use Organizers?", "choices" => array("true" => "Yes", "false" => "No"));	
	public const SETTING_PLUGIN_FEATURED = array("key" => "eventastic_use_featured", "label" => "Use Featured Image?", "choices" => array("true" => "Yes", "false" => "No"));	
	public const SETTING_PLUGIN_CATEGORY_SIDEBAR = array("key" => "eventastic_category_location", "label" => "Place Category in Sidebar?", "choices" => array("true" => "Yes", "false" => "No"));	
	public const SETTING_PLUGIN_RECURRENCE_V2 = array("key" => "eventastic_use_recurrence_v2", "label" => "Use Version 2 of Recurrence Options", "choices" => array("true" => "Yes", "false" => "No"));
	public const SETTING_PLUGIN_RECURRENCE_CLEANUP = array("key" => "eventastic_recurrence_cleanup", "label" => "Convert Version 1 Recurrence Options to Version 2", "choices" => array("true" => "Yes", "false" => "No"));	

	public function __construct () {
		// field sets
		add_settings_section('section_uno', 'Event post slug', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);

		add_settings_section('section_v2', 'Recurrence (Version Two Upgrade)', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);

		add_settings_section('section_dos', 'Map options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
		add_settings_section('section_tres', 'Geocode options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
		add_settings_section('section_quatro', 'Layout options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
		add_settings_section('section_cinco', 'Other options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
	
		// and fields for settings
		$fields = array(
			array(
				'uid' => self::SETTING_PLUGIN_POST_TYPE["key"],
				'label' => self::SETTING_PLUGIN_POST_TYPE["label"],
				'section' => 'section_uno',
				'type' => 'text',
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('Changing this after events have been created will hide events from your main listing!'),
				'default' => Utilities::getPluginPostType()
			),
			array(
				'uid' => self::SETTING_KEY_GOOGLE_API["key"],
				'label' => self::SETTING_KEY_GOOGLE_API["label"],
				'section' => 'section_dos',
				'type' => 'text',
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('If using Google Maps, please provide an API key here; otherwise Leaflet.js will be used for event maps.'),
				'default' => ''
			),
			array(
				'uid' => self::SETTING_LEAFLET_TILE_LIBRARY["key"],
				'label' => self::SETTING_LEAFLET_TILE_LIBRARY["label"],
				'section' => 'section_dos',
				'type' => 'textarea',
				'cssclass' => 'large-text code',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('You must assign your layer to the map object via .addTo(map) to have it tied to the map. Some nice samples are available at the <a href="http://leaflet-extras.github.io/leaflet-providers/preview/">Leaflet Providers library</a>.'),
				'default' => Constants::PLUGIN_SETTING_LEAFLET_TILE_LIBRARY
			),
			array(
				'uid' => self::SETTING_OPENCAGE_API_KEY["key"],
				'label' => self::SETTING_OPENCAGE_API_KEY["label"],
				'section' => 'section_tres',
				'type' => 'text',
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => '',
				'default' => ''
			),
			array(
				'uid' => self::SETTING_CATEGORY_COLORS["key"],
				'label' => self::SETTING_CATEGORY_COLORS["label"],
				'section' => 'section_quatro',
				'type' => 'textarea',
				'cssclass' => 'large-text code',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('Provide as many colors as you desire. Category counts exceeding this set will loop back to the start.'),
				'default' => json_encode(Constants::PLUGIN_SETTING_CATEGORY_COLORS)
			),
			array(
				'uid' => self::SETTING_PLUGIN_DEBUG["key"],
				'label' => self::SETTING_PLUGIN_DEBUG["label"],
				'section' => 'section_cinco',
				'type' => 'radio',
				'cssclass' => '',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('If "yes", data will be sent to the WordPress do_error function (i.e. enable logging)'),
				'choices' => self::SETTING_PLUGIN_DEBUG["choices"],
				'default' => Constants::DEBUG_MODE
			),
			array(
				'uid' => self::SETTING_PLUGIN_VENUE["key"],
				'label' => self::SETTING_PLUGIN_VENUE["label"],
				'section' => 'section_cinco',
				'type' => 'radio',
				'cssclass' => '',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('Set to no, the Venues will be hidden'),
				'choices' => self::SETTING_PLUGIN_VENUE["choices"],
				'default' => Constants::VENUE_MODE
			),
			array(
				'uid' => self::SETTING_PLUGIN_ORGANIZER["key"],
				'label' => self::SETTING_PLUGIN_ORGANIZER["label"],
				'section' => 'section_cinco',
				'type' => 'radio',
				'cssclass' => '',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('Set to no, the Organizers will be hidden'),
				'choices' => self::SETTING_PLUGIN_ORGANIZER["choices"],
				'default' => Constants::ORGANIZER_MODE
			),			
			array(
				'uid' => self::SETTING_PLUGIN_FEATURED["key"],
				'label' => self::SETTING_PLUGIN_FEATURED["label"],
				'section' => 'section_cinco',
				'type' => 'radio',
				'cssclass' => '',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('Set to no, the Featured Image will be hidden'),
				'choices' => self::SETTING_PLUGIN_FEATURED["choices"],
				'default' => Constants::FEATURED_MODE
			),						
			array(
				'uid' => self::SETTING_PLUGIN_CATEGORY_SIDEBAR["key"],
				'label' => self::SETTING_PLUGIN_CATEGORY_SIDEBAR["label"],
				'section' => 'section_cinco',
				'type' => 'radio',
				'cssclass' => '',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('Set to no, the Categories for Events will be shown in the main section'),
				'choices' => self::SETTING_PLUGIN_CATEGORY_SIDEBAR["choices"],
				'default' => Constants::CATEGORY_SIDEBAR
			)
		);
		// Version 2 conditionals::

		$usingV2 = get_option(self::SETTING_PLUGIN_RECURRENCE_V2["key"]);
		$recurrenceConversion = get_option(self::SETTING_PLUGIN_RECURRENCE_CLEANUP["key"]);
		if( 'true' == $usingV2 ){
			$meta_query = array(
			    array(
			         'key'     => 'eventastic_recurring_days',
			         'compare' => 'EXISTS',
			    ),
			);
			$query = new \WP_Query(array(
			    'post_type' => 'event',
			    'posts_per_page' => 1,
			    'meta_query' => $meta_query
			));
			if ($query->found_posts) {
				$supplemental = __('<p id="crwrapper"><button type="submit" name="submit" id="submit" class="button button-primary" value="ConvertRecurring">Convert Recurring</button</p><script>
				 const btn = document.getElementById("submitvoid");
				 btn.addEventListener("click", (e) => {
				 	e.preventDefault();
				 	btn.innerHTML = "Converting..."
				});
				</script>');
				$new_field = array(
					'uid' => self::SETTING_PLUGIN_RECURRENCE_CLEANUP["key"],
					'label' => self::SETTING_PLUGIN_RECURRENCE_CLEANUP["label"],
					'section' => 'section_v2',
					'type' => 'radio',
					'cssclass' => '',
					'options' => false,
					'placeholder' => '',
					'helper' => '',
					'supplemental' => $supplemental,
					'choices' => self::SETTING_PLUGIN_RECURRENCE_CLEANUP["choices"],
					'default' => Constants::RECURRENCE_CLEANUP
				);
				$main_supplemental = __("You are currently using Version 2 of Eventastic. <br><br><b>Status:</b><i> Some Eventastic Events might need converting from Version 1 to Version 2.<i>");				
			}
			else{
				$main_supplemental = __("You are currently using Version 2 of Eventastic. <br><br><b>Status:</b><i> All Eventastic Events appear to be ready for using the Version 2 Calendar Block.</i>");			
			}
		}
		else{
			$main_supplemental = __("Set to yes if this is a new project. Version 2 provides PHP8 support, extensive recurrence options and out-of-the-box templates.");
		}
		$fields[] = array(
			'uid' => self::SETTING_PLUGIN_RECURRENCE_V2["key"],
			'label' => self::SETTING_PLUGIN_RECURRENCE_V2["label"],
			'section' => 'section_v2',
			'type' => 'radio',
			'cssclass' => '',
			'options' => false,
			'placeholder' => '',
			'helper' => '',
			'supplemental' => $main_supplemental,
			'choices' => self::SETTING_PLUGIN_RECURRENCE_V2["choices"],
			'default' => Constants::RECURRENCE_V2
		);

		if( isset ($new_field)){
			$fields[] = $new_field;
		}
		wp_reset_postdata();
		foreach ($fields as $field) {
			add_settings_field($field['uid'], $field['label'], 
				array($this, 'callbackSectionFields'), 
				Constants::PLUGIN_NAME_PLURAL, $field['section'], $field);
			register_setting(Constants::PLUGIN_NAME_PLURAL, $field['uid']);
		}	
	}

	/**
	 * Settings section callback for rendering text
	 *
	 * @param array $arguments Data from WordPress related to the caller of the callback
	 */
	public function callbackAdminSettings ($arguments) {
		
		switch( $arguments['id']) {
			case 'section_uno':
				_e('The slug that all events will be shown under.');
				break;
			case 'section_dos':
				_e('Provide any API keys here needed for event display.');
				break;
			case 'section_tres':
				_e('This requires an API key from <a href="https://opencagedata.com/api">OpenCageData</a>. Please work with Madden directly for options here.');
				break;
			case 'section_v2':
				_e(`<script>				</script>`);
				break;
			case 'section_quatro':
				_e('Adjust layout styles for Eventastic displays.');
				break;
			case 'section_cinco':
				break;
			}
	}
	
	/**
	 * Section field callback for rendering fields
	 *
	 * @param array $arguments Data from WordPress related to the caller of the callback
	 */
	public function callbackSectionFields ($arguments) {
				
		$currentValue = get_option($arguments['uid']);

		if (! $currentValue) {
			// except for checkboxes
			if ($arguments['type'] != 'checkbox') {
				$currentValue = $arguments['default'];
			} else {
				$currentValue = array();
			}
		}

		// Check which type of field we want
		switch($arguments['type']) {
			case 'text':
				printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" class="%4$s" value="%5$s" />', 
					$arguments['uid'], $arguments['type'], $arguments['placeholder'], $arguments['cssclass'], $currentValue);
				break;
			case 'textarea':
				printf('<textarea name="%1$s" id="%1$s" rows="8" placeholder="%2$s" class="%3$s">%4$s</textarea>', 
					$arguments['uid'], $arguments['placeholder'], $arguments['cssclass'], $currentValue);
				break;
			case 'radio':
				foreach ($arguments['choices'] as $key => $value) {
					$checked = ($key == $currentValue) ? 'checked="true"' : '';
					printf('<label for="%1$s_%2$s"><input type="radio" name="%1$s" id="%1$s_%2$s" value="%2$s" %3$s> %4$s</label><br/>', 
						$arguments['uid'], $key, $checked, $value);
				}
				break;
			case 'checkbox':
				foreach ($arguments['choices'] as $key => $value) {
					$checked = (in_array($key, $currentValue)) ? 'checked="true"' : '';
					printf('<label for="%1$s_%2$s"><input type="checkbox" name="%1$s[]" id="%1$s_%2$s" value="%2$s" %3$s> %4$s</label><br/>', 
						$arguments['uid'], $key, $checked, $value);
				}
				break;
			}

		// help text?
		if ($helper = $arguments['helper']) {
			printf('<span class="helper"> %s</span>', $helper);
		}

		// If there is supplemental text
		if ($supplimental = $arguments['supplemental']) {
			printf('<p class="description">%s</p>', $supplimental);
		}
	}
}

?>
