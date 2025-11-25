# Eventastic
Madden's event calendar!
## Version Two Docs
If you're installing Eventastic on a site for the first time, it's likely best to start with the Version Two docs here: http://demo.maddenmedia.com/2024/content/eventastic/deployment/install.html

## Install
The plugin will create the custom post type and taxonomy registration needed to create the section in the WordPress admin to manage events.

## Configuration
In the plugin settings, there are a few options. Additional configuration may be done in the `library/Constants.php` file, however please always consider architecting in any change to this plugin so that other sites may benefit from the change.
#### Event post slug
**Post Slug:** The slug to use for all events.
#### Map options
**Google API Key:** Use this to set a map key for the callable map render function per listing. If a Google key is provided, a Google iFrame map will be used.
**Leaflet Tile Library:**  If using Leaflet for the map, you may provide an optional tile library string. See [here](http://leaflet-extras.github.io/leaflet-providers/preview/) for examples.
#### Geocode options
**OpenCageData API Key:** API key from [OpenCageData](https://opencagedata.com/api) for looking up lat/lng from a street address. **This is not a a free API!** Please be aware of costs associated with the key.
#### Layout options
**Category Colors:** The colors to use for categories in layouts such as filters for events. Provide as many colors as you desire. Category counts exceeding this set will loop back to the start.

## Page Templates
The plugin will automatically generate a page template for an event calendar page listing all the events. It also generates a basic layout for the single event pages. In order to modify these templates you simply copy the `eventastic-theme-files` folder into your themes root directory, DO NOT modify the actual plugin files themselves. Once moved over any changes made to the files in the theme will be reflected on the site.

## Callable functions
An example of how to pass in a custom Leaflet map icon can be seen [here](https://jsfiddle.net/sitruc/gajvy10e/).
```
/**
 * Get all current events.
 *
 * All standard WP query args are accepted, plus the following:
 *
 * @param array $args {
 *        @type string $start_date        Minimum start date for matching events
 *        @type string $end_date            Maximum end date for matching events
 *        @type string $exact_start_date    A specific start date
 *        @type string $category_slug        A specific event category slug
 * }
 * @param bool  $full If the full query object is required or just an array of event posts
 * @return array List of event posts
 *
 */
function eventastic_get_events ($args=array(), $full=false) { }

/**
 * Returns all meta data about a event - usually used on a event template page
 *
 * @param int $postId The post id
 * @return array The matching meta for the passed post
 */
function eventastic_get_event_meta ($postId) { }

/**
 * Returns a rendered event location map for the passed post id
 *
 * @param int $postId The post id
 * @param string $mapCSSId The CSS Id to use for the map div
 * @param string $mapIconLeafletJSON The options for a custom Leaflet L.divIcon map icon - build these
 *                                     as a PHP array, and this function will json_encode those automatically
 * @return array A rendered map using either Google or Leaflet (depending on plugin preference settings)
 */
function eventastic_render_event_map ($postId, $mapCSSId="map", $mapIconLeafletJSON=null) { }

/**
 * Returns all event venues
 *
 * @param boolean $hideEmpty Hide empty terms?
 * @return array List of event venues
 */
function eventastic_get_venues ($hideEmpty=false) { }

/**
 * Returns all event organizers
 *
 * @param boolean $hideEmpty Hide empty terms?
 * @return array List of event organizers
 */
function eventastic_get_organizers ($hideEmpty=false) { }

/**
 * Returns all event categories
 *
 * @param boolean $hideEmpty Hide empty terms?
 * @param boolean  $assignColors Assign a hex color from settings to each color?
 * @return array List of event categories
 */
function eventastic_get_categories ($hideEmpty=false, $assignColors=true) { }
```

## Ajax functions
```
/**
 * Returns all events across the queried date range
 *
 * @type string $start_date          Minimum start date for matching events
 * @type string $end_date            Maximum end date for matching events
 * @type string $exact_start_date    A specific start date
 * @return array List of events keyed by date
 */
function get_events_date_ordered() {}

//Use eventastic-theme-files/scripts/eventastic-ajax.js as example:
var data = {
	'action': 'get_events_date_ordered',
	'start_date' : '2023-06-01'
	'exact_start_date' : '2023-06-01'
}
$.post(Eventastic_Variables.ajax_url, data, function(response) {
	var days = JSON.parse( response );
	console.log('response', days);
});
 
```


