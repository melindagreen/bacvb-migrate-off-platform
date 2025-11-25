# Kraken Events
## Requirements
- Advanced Custom Fields Pro

## FAQ
Plugin will create this ACF field group on activation:
- Kraken Events

### Updating from Eventastic
The meta keys have changed, see this doc for additional details: https://docs.google.com/document/d/1b-9vvCbQlXG_BMLRRz90ywe3NbXCEgP_m9bYaE-ptig/edit?tab=t.0

### Add Events Page
An add events page is optionally available that will use the ACF fields as an ACF form for users to submit events and admins to review and publish.

### Kraken CRM Integration
Installing both plugins will add new options to enable CRM features and restrict event management to only partners. This will also enable the edit event page.

### Custom Post Types & Taxonomies
The settings page to adjust the slug and add additional taxonomies can be found under Tools -> Kraken CRM. The args for these can be adjusted by adding filters to the custom-post-types.php and custom-taxonomies.php files such as these:
```
add_filter('register_post_type_args', __NAMESPACE__ . '\update_existing_post_types', 10, 2);
function update_existing_post_types($args, $post_type) {
	if ($post_type === 'event') {
		$args['menu_icon'] = 'dashicons-building';
	}
	return $args;
}
```
```
add_filter('register_taxonomy_args', __NAMESPACE__ . '\update_existing_taxonomies', 10, 2);
function update_existing_taxonomies($args, $taxonomy) {
	if ($taxonomy === 'event_category') {
		$args['rewrite']['slug'] = 'business-categories';
	}
	return $args;
}
```

### Styles & Customizations
A default stylesheet is included for basic layout and form styles and can be toggled on or off in the plugin settings.

## Blocks & Templates
All data can be pulled the standard ACF way using get_field. Some utility functions are available to help with date and time formatting. See inc/class-utilities.php for available parameters.

- get_event_recurrence_pattern
- get_events_between_dates
- formatEventDateRange
- formatSingleEventDate
- formatEventTimeRange
- recurringDatesList
- formatWeeklyRecurrence
- formatMonthlyDateRecurrence
- formatMonthlyDotwRecurrence

### List all future dates in a recurrence
This is intended to be used for events using weekly or monthly recurrence options.
```
$recurring_dates = E::recurringDatesList(get_field('event_repeat_dates', $id));
$recurring_output = '';
if (!empty($recurring_dates)) {
	foreach ($recurring_dates as $date) {
        $recurring_output .= '<!-- wp:paragraph --><p>';
		$date_string = E::formatSingleEventDate($date, 'M j, Y');
		$time_string = E::formatEventTimeRange($start_time, $end_time, $isAllDay);
		if ($time_string) {
			$time_string = ' ('.$time_string.')';
		}
		$recurring_output .= $date_string.$time_string;
        $recurring_output .= '</p><!-- /wp:paragraph -->';
	}
}
echo $recurring_output;
```
### Single Event Template
The "Kraken Events" block in the block library can be used to output event data on a single event template. https://github.com/maddenmedia/mm-block-library/tree/main/fse-blocks/kraken-events The King Theme has a starter template available that uses this block. https://github.com/maddenmedia/mm-king-theme/blob/main/templates/single-event.html

## Querying Events
Pass `kraken_event_start_date` as an argument to WP Query with a date formatted as Ymd and events will be returned ordered by the next occurrence after the specified date

To find events between two dates, use the utility function `get_events_between_dates`
```
use MaddenMedia\KrakenEvents\Utilities as E;
$event_ids = E::get_events_between_dates($start_date, $end_date);
$args['post__in'] = $event_ids;
```

To display event recurrence pattern, use the utility function `get_event_recurrence_pattern`
```
use MaddenMedia\KrakenEvents\Utilities as E;
$date_pattern = E::get_event_recurrence_pattern($id);
```