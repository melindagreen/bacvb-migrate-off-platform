<?php

namespace PartnerPortal\Admin;

/**
 * Layout for the admin area of the plugin
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */
require_once(__DIR__.'../../partner-portal.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');

use PartnerPortal\PartnerPortal as PartnerPortal;
use partnerportal\Library\Constants as Constants;
use partnerportal\Library\Utilities as Utilities;

class SettingsAdminLayout {
	public const SETTING_KEY_GOOGLE_API = array("key" => "partnerportal_google_maps_api_key", "label" => "Google API Key");
	public const SETTING_PLUGIN_POST_TYPE = array("key" => "partnerportal_post_slug", "label" => "Post Slug");
	//public const SETTING_LEAFLET_TILE_LIBRARY = array("key" => "partnerportal_leaflet_tile_library", "label" => "Leaflet Tile Library");
	public const SETTING_CATEGORY_COLORS = array("key" => "partnerportal_category_colors", "label" => "Category Colors");
	public const SETTING_OPENCAGE_API_KEY = array("key" => "partnerportal_opencagedata_api_key", "label" => "OpenCageData API Key");
	
	public function __construct ( $variables ) {

        $default_variables = array(
            'cpt_plural' => '',
            'cpt_singular' => '',
            'import_csv' => '',
            'admin_custom_post_type_menu_pos' => '',
        );
        foreach( $default_variables as $variable_key => $variable_value ){
            if( !(array_key_exists($variable_key, $variables) ) ){
                $variables[$variable_key] = $variable_value;
            }
        }

		// field sets
		add_settings_section('section_one', 'Import Listings CSV', 
			array($this, 'callbackAdminSettings'), $variables['cpt_plural']);
		/*
		add_settings_section('section_two', 'Map options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
		add_settings_section('section_tres', 'Geocode options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
		add_settings_section('section_quatro', 'Layout options', 
			array($this, 'callbackAdminSettings'), Constants::PLUGIN_NAME_PLURAL);
		*/

        $config_file = PartnerPortal::get_plugin_file('partner.json' );

        $strJsonFileContents = file_get_contents( $config_file );
        $partnerportalObject = json_decode($strJsonFileContents, true);
        $partnerportalConfigurations = null;
		$partnerImportConfigurations = null;

        if( array_key_exists('configurations', $partnerportalObject) ){
        	$partnerportalConfigurations = $partnerportalObject['configurations'];
	        if( array_key_exists('import', $partnerportalConfigurations ) ){
		    	$partnerImportConfigurations = $partnerportalConfigurations['import'];
		    }
        }
   		// split off general config from metabox config - so need to look twice:
        // check if there is a new separate file 
        if( !$partnerportalConfigurations ){
	        $plugin_config_file = PartnerPortal::get_plugin_file('partner_plugin_config.json' );
	        $strJsonConfigFileContents = file_get_contents( $plugin_config_file );
	        $partnerportalConfigurationObject = json_decode($strJsonConfigFileContents, true);
	        if( array_key_exists('configurations', $partnerportalConfigurationObject) ){
	        	$partnerportalConfigurations = $partnerportalConfigurationObject['configurations'];
		        if( array_key_exists('import', $partnerportalConfigurations ) ){
			    	$partnerImportConfigurations = $partnerportalConfigurations['import'];
			    }
	        }
	        if( array_key_exists('import', $partnerportalConfigurations ) ){
		    	$partnerImportConfigurations = $partnerportalConfigurations['import'];
		    }
        }
        // should setup valid defaults here (we dont yet)

        // check if there is a custom name for the import file; if not use 'partners' as default
        $partnerFilename = (array_key_exists('import_filename', $partnerImportConfigurations) && $partnerImportConfigurations['import_filename'] ) ? $partnerImportConfigurations['import_filename'] : 'partners';

       // check if the import file exists in theme; if not use default import file (for testing) in plugin
        $importFile = get_stylesheet_directory() . "/" . Constants::PLUGIN_THEME_DIR_SLUG . "/" . $partnerFilename . ".csv";

        if( !file_exists( $importFile ) ){
            $importFile = plugin_dir_url( __FILE__ ) . $partnerFilename . ".csv";            
        }

		// and fields for settings

		$fields = array(
			array(
				'uid' => self::SETTING_PLUGIN_POST_TYPE["key"],
				'label' => '',//self::SETTING_PLUGIN_POST_TYPE["label"],
				'section' => 'section_one',
				'type' => 'message',
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'default' => Utilities::getPluginPostType()
			),
			array(
				'uid' => 'import-csv',
				'label' => 'Import CSV',//self::SETTING_PLUGIN_POST_TYPE["label"],
				'section' => 'section_one',
				'type' => 'checkbox',
				'choices' => ['import-csv' => 'Import CSV'],
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('This will import the following csv file: <b>' . $importFile . '</b>'),
				'default' => Utilities::getPluginPostType()
			),
			
			array(
				'uid' => 'import-type',
				'label' => 'Import Type',//self::SETTING_PLUGIN_POST_TYPE["label"],
				'section' => 'section_one',
				'type' => 'select',
				'choices' => ['csv-import' => 'Manual CSV Import', 'simpleview-cron' => 'SimpleView Cron', 'simpleview-manual' => 'SimpleView Manual'],
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __(''),
				'default' => Utilities::getPluginPostType()
			),

			/*
			array(
				'uid' => self::SETTING_KEY_GOOGLE_API["key"],
				'label' => self::SETTING_KEY_GOOGLE_API["label"],
				'section' => 'section_two',
				'type' => 'text',
				'cssclass' => 'regular-text',
				'options' => false,
				'placeholder' => '',
				'helper' => '',
				'supplemental' => __('If using Google Maps, please provide an API key here; otherwise Leaflet.js will be used for maps.'),
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
			*/
		);
		foreach ($fields as $field) {
			add_settings_field($field['uid'], $field['label'], 
				array($this, 'callbackSectionFields'), 
				$variables['cpt_plural'], $field['section'], $field);
			register_setting($variables['cpt_plural'], $field['uid']);
		}	
	}


	/**
	 * Settings section callback for rendering text
	 *
	 * @param array $arguments Data from WordPress related to the caller of the callback
	 */
	public function callbackAdminSettings ($arguments) {
		
		switch( $arguments['id']) {
			case 'section_one':
				_e('');
				break;
			case 'section_two':
				_e('Provide any API keys here needed for display.');
				break;
			case 'section_tres':
				_e('This requires an API key from <a href="https://opencagedata.com/api">OpenCageData</a>. Please work with Madden directly for options here.');
				break;
			case 'section_quatro':
				_e('Adjust layout styles for partnerportal displays.');
				break;
		}
	}
	
	/**
	 * Section field callback for rendering fields
	 *
	 * @param array $arguments Data from WordPress related to the caller of the callback
	 */
	public function callbackSectionFields ($arguments) {
		
		$value = get_option($arguments['uid']);
		if (! $value) {
			// except for checkboxes
			if ($arguments['type'] != 'checkbox') {
				$value = $arguments['default'];
			} else {
				$value = array();
			}
		}

		// Check which type of field we want
		switch($arguments['type']) {
			case 'text':
				printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" class="%4$s" value="%5$s" />', 
					$arguments['uid'], $arguments['type'], $arguments['placeholder'], $arguments['cssclass'], $value);
				break;
			case 'textarea':
				printf('<textarea name="%1$s" id="%1$s" rows="8" placeholder="%2$s" class="%3$s">%4$s</textarea>', 
					$arguments['uid'], $arguments['placeholder'], $arguments['cssclass'], $value);
				break;
			case 'checkbox':
				foreach ($arguments['choices'] as $key => $arrValue) {
					$checked = (in_array($key, $value)) ? 'checked="true"' : '';
					printf('<label for="%1$s_%2$s"><input type="checkbox" name="%1$s[]" id="%1$s_%2$s" value="%2$s" %3$s> %4$s</label><br/>', 
						$arguments['uid'], $key, $checked, $arrValue);
				}
				break;
			case 'select':
				printf('<label for="%1$s_%2$s">%4$s</label><select name="%1$s" id="%1$s_%2$s">', 
					$arguments['uid'], "", "", "");				
				foreach ($arguments['choices'] as $key => $arrValue) {
					$selected = ( $key == $value) ? 'selected' : '';
					printf('<option value="%2$s" %3$s> %4$s</option>', 
						$arguments['uid'], $key, $selected, $arrValue);
				}
				echo "</select>";
				break;												
		}

		// help text?
		if ($helper = $arguments['helper']) {
			printf('<span class="helper"> %s</span>', $helper);
		}

		// If there is supplemental text
		if ($supplimental = ( isset($arguments['supplemental']) ? $arguments['supplemental'] : "" )) {
			printf('<p class="description">%s</p>', $supplimental);
		}
	}
}

?>
