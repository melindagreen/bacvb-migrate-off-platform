<?php

namespace Eventastic\Library;

/**
 * Constants for the plugin
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

class Constants {

	// Display string roots
	public const PLUGIN_MENU_ADMIN_LABEL = "Eventastic";
	public const PLUGIN_NAME_SINGULAR = "Event";
	public const PLUGIN_NAME_PLURAL = "Eventastic";

	// If in debug mode, some output can be logged or additional information returned
	public const DEBUG_MODE = "false";

	// If venue mode is true, will use venues
	public const VENUE_MODE = "true";

	// If organizer mode is true, will use organizers
	public const ORGANIZER_MODE = "true";

	// If featured mode is true, will use Featured Image
	public const FEATURED_MODE = "true";

	// If  recurrence_v2 is true, admin will display newer recurrence options (the data structure of v1 and v2 are incompatible)
	public const RECURRENCE_V2 = "false";

	// If  recurrence_cleanup is true, will run the v1 to v2 recurrence conversion
	public const RECURRENCE_CLEANUP = "false";

	// If true, category will show in sidebar, if false in main
	public const CATEGORY_SIDEBAR = "true";
		
	// CPT and taxonomies
	//
	// FUTURE Having a custom field class specified here is not a lot of help,
	//	because you still have to go make that class if you add a taxonomy here. That said,
	//	we aren't likely to need to add random taxonomies, either. We also have to refer
	//	to them by their key in code, meaning they aren't a true constant.
	public const PLUGIN_DEFAULT_CUSTOM_POST_TYPE = "event";
	public const PLUGIN_TAXONOMIES = array(
		"eventastic_categories"	=> array(
			"single" => "Category", 
			"plural" => "Categories", 
			"showAdminColumn" => true
		),
		"eventastic_venues" 	=> array(
			"single" => "Venue", 
			"plural" => "Venues", 
			"customFieldsClass" => "Eventastic\Admin\TaxonomyMetaFieldsVenue", 
			"showAdminColumn" => false
		),
		"eventastic_organizers"	=> array(
			"single" => "Organizer", 
			"plural" => "Organizers", 
			"showAdminColumn" => false
		)
	);
		
	// Custom post type placement in admin menu and icon
	public const ADMIN_CUSTOM_POST_TYPE_MENU_POS = 27;
	public const MENU_ICON = "eventastic/images/eventastic_icon.png";

	// Default plugin settings
	public const PLUGIN_SETTING_LEAFLET_TILE_LIBRARY = 
		"L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {\n"
			."  maxZoom: 19,\n"
			."  attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'\n"
			."}).addTo(map);";
	public const PLUGIN_SETTING_CATEGORY_COLORS = array(
		"#eee", "#ddd", "#ccc", "#bbb", "#aaa", "#999",
		"#888", "#777", "#666", "#555", "#444"
	);

	// Security keys
	public const NONCE_ROOT = "nonce-eventastic-";

	// wp_postmeta key values
	public const WP_POST_META_KEY_DETAILS = "eventastic_[KEY]"; // replace [KEY]!
	public const WP_POST_META_KEY_PREPEND = "eventastic_";
	
	// Main listing table custom columns
	public const TABLE_VIEW_DATE_RANGE = array("key" => "date_range", "label" => "Date(s)");
	public const TABLE_VIEW_RECURRENCE = array("key" => "recurrence", "label" => "Recurrence");
	public const TABLE_VIEW_START_DATE = array("key" => "start_date", "label" => "Start Date");
	public const TABLE_VIEW_END_DATE = array("key" => "end_date", "label" => "End Date");

	// OpenCage geocode API URL
	public const URL_OPENCAGE_API = "https://api.opencagedata.com/geocode/v1/json?key=[KEY]&q=[QUERY]"; // [KEY] is in plugin settings provided by user
	
	// Dates. Who doesn't love dates?
	public const DATETIME_DASH_FORMAT_IN = "Y-m-d H:i:s";
	public const DATE_DASH_FORMAT_IN = "m-d-Y";
	public const DATETIME_SLASH_FORMAT_IN = "m/d/Y H:i:s";
	public const DATETIME_SLASH_AMPM_FORMAT_IN = "m/d/Y H:i:s A";
	public const DATE_SLASH_FORMAT_IN = "m/d/Y";
	public const DATE_FORMAT_MYSQL = "Y-m-d";
	public const DATETIME_FORMAT_MYSQL = "Y-m-d H:i:s";
	public const DATE_FORMAT_PURDY = "F d, Y";
	public const DATE_FORMAT_PURDY_NO_YEAR = "F j";
	public const DATE_FORMAT_JS_DATEPICKER = "M/D/YYYY";
	public const DATE_FORMAT_JS_MYSQL = "YYYY-MM-DD";
	public const TIME_FORMAT_JS_PRETTY = "h:mm a";
	public const TIME_FORMAT_JS_MYSQL = "HH:mm:ss";
	
	// for the calendar view of events, what is the earliest time per day that we show?
	public const CALENDAR_VIEW_MIN_TIME_VIEW = "07:00:00";
	
	// danke https://gist.github.com/maxrice/2776900
	public const SELECT_STATES = array(
		'' => '',
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
	);
	
}

?>
