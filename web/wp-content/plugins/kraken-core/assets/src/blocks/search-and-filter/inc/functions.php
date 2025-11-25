<?php
namespace MaddenMedia\KrakenCore\Blocks\SearchResultsFilter;
use MaddenMedia\KrakenEvents\Utilities as E;

function validate_and_convert_datetime($date_string, $format = 'Y/m/d') {
    $date = \DateTime::createFromFormat($format, $date_string);
    // Check if parsing succeeded AND no warnings/errors
    $errors = \DateTime::getLastErrors();
    if ($date && !$errors) {
        return $date->format($format); // Return in MySQL-friendly format
    }
    return false; // Invalid date
}

/**
 * Check if an event post type
 */
function is_event( $post_type ) {
	$event_post_types = [
		'event',
		'events',
		'tribe_events'
	];

	$kraken_event_slug = get_option('kraken_events_event_slug', 'event');
	if (!in_array($kraken_event_slug, $event_post_types)) {
		$event_post_types[] = $kraken_event_slug;
	}

	if ( in_array( $post_type, $event_post_types ) ) {
		return true;
	}
}

//If block is set to "related posts":
//This will set $args['post__in'] to only fetch the NLP post ids if possible
//Fallback to tags/categories if NLP is not installed or not enough posts are found
function related_post_args($args, $attrs = []) {

	//be sure to not display current post
	$args['post__not_in'] = array(get_the_ID());

	//Try to use NLP first
	if (class_exists('\MaddenNLP\\Library\\Query')) {
		$nlp_querier = new \MaddenNLP\Library\Query;

		//grab more than the perpage number as this could fetch posts that are not published
		//also filter results to the selected post type
		$related = $nlp_querier->get_posts_by_vector(get_the_ID(), null, ($attrs['perPage'] * 3), true, [
			'post_types' => [$attrs['postType']],
			'post__not_in' => get_the_ID()
		]);

		$nlp_post_ids = [];
		$i = 0;

		foreach ($related->data as $data) {
			//only save up to the perPage limit
			if ($i === $attrs['perPage']) { break; }
			//make sure the post status is publish
			$id = $data->post->ID;
			$status = get_post_status($id);
			if ($status && $status === 'publish') {
				$nlp_post_ids[] = $id;
				$i++;
			}
		}

		//if we end up with less than the perPage amount, fallback to tags/categories
		if (count($nlp_post_ids) < $attrs['perPage']) {
			$related_args = [
					"post_type"       => $attrs['postType'],
					"posts_per_page"  => $attrs['perPage'] - count($nlp_post_ids),
					"orderby"         => "rand",
					"status"          => "publish",
					"post__not_in"    => array_merge($nlp_post_ids, [get_the_ID()]),
					"fields"          => "ids"
			];

			//try tags, then categories
			$tags = wp_get_post_tags(get_the_ID());
			if ($tags) {
				$tag_ids = array();
				foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
				$related_args['tag__in'] = $tag_ids;
			} else {
				$categories = get_the_category(get_the_ID());
				if ($categories) {
						$category_ids = array_map(function($cat) { return $cat->term_id; }, $categories);
						$related_args['category__in'] = $category_ids;
				}
			}

			$more_posts = new WP_Query($related_args);
			$nlp_post_ids = array_merge($nlp_post_ids, $more_posts->posts);
		}

		//set the query to only include these ids
		$args['post__in'] = $nlp_post_ids;
	} else {
		//if nlp does not exist, try to use tags first
		$tags = wp_get_post_tags(get_the_ID());
		if ($tags) {
			$tag_ids = array();
			foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
			$args['tag__in'] = $tag_ids;
		} else {
			//fallback to categories if no tags are found
			$categories = get_the_category(get_the_ID());
			if ($categories) {
					$category_ids = array_map(function($cat) { return $cat->term_id; }, $categories);
					$args['category__in'] = $category_ids;
			}
		}
	}

	return $args;
}

/**
 * Inject the event args
 */
function add_event_args($args, $attrs = []) {
	if (!function_exists( 'is_plugin_active')) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$event_plugin = false;
	$start_date_meta_key = 'event_start_date';
	$end_date_meta_key = 'event_end_date';
	$orderby_date_key = 'event_next_occurrence';
	$orderby_time_key = 'event_next_occurrence_start_time';

	// Kraken
	if ( is_plugin_active( 'kraken-events/kraken-events.php' ) ) {
		$event_plugin = 'kraken-events';
		$start_date_meta_key = 'event_start_date';
		$end_date_meta_key = 'event_end_date';
		$orderby_date_key = 'event_next_occurrence';
		$orderby_time_key = 'event_next_occurrence_start_time';

	// Eventastic
	} elseif( is_plugin_active( 'wp-plugin-eventastic/madden-eventastic.php' ) ) {
		$event_plugin = 'eventastic';
		$start_date_meta_key = 'eventastic_start_date';
		$end_date_meta_key = 'eventastic_end_date';
		$orderby_date_key = 'eventastic_start_date';

	// TEC
	} elseif( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {
		$event_plugin = 'the-events-calendar';
		$start_date_meta_key = '_EventStartDate';
		$end_date_meta_key = '_EventEndDate';
		$orderby_date_key = '_EventStartDate';
		$args['eventDisplay'] = 'custom';
	}

	$start_date = $attrs['start_date'] ? $attrs['start_date'] : "";
	$end_date = $attrs['end_date'] ? $attrs['end_date'] : "";

	/*
	This meta query will
	- Find events that START or END after today's date.
	- If the date range query is enabled, it will only find events that START or END before the future date (such as today + 60 days)
	*/
	$start_date_formatted = "";
	$end_date_formatted = "";
	$meta_query = [];

	if ($start_date !== "" && !($start_date instanceof DateTime)) {
		$start_date = new \DateTime($start_date);
		$start_date_formatted = $start_date->format('Ymd');
	} elseif ($start_date === "") {
		$start_date = new \DateTime();
		$start_date_formatted = $start_date->format('Ymd');
	}

	if ($end_date !== "" && !($end_date instanceof DateTime)) {
		$end_date = new \DateTime($end_date);
		$end_date_formatted = $end_date->format('Ymd');
	} elseif ($attrs['enableDateQuery'] && $attrs['selectedDateRange'] > 0) {
		$end_date = clone $start_date;
		$end_date->modify('+'.$attrs['selectedDateRange'].' days');
		$end_date_formatted = $end_date->format('Ymd');
	}

	if ($event_plugin === "kraken-events" && $start_date && $end_date) {
		// KRAKEN EVENTS ONLY - if both a start & end date are set
		// - Use an extra query to find events within the date range
		// - This query will find all event ids of events that actually occur within the selected date range and add a post__in arg to our query
		// - This guarantees the events shown actually occur within the range vs only starting/ending within the range (an issue with recurrence)
		$event_ids = E::get_events_between_dates($start_date, $end_date);
		$args['post__in'] = $event_ids;
	} else {
		//If the initial query used our Kraken Events $event_ids above, but the user removes the end date we need to remove the $args['post__in'] from our search
		//Removing it via PHP instead of JS as it is easier to control here
		if ($event_plugin === "kraken-events" && isset($args['post__in']) && isset($args['content_type']) && !in_array($args['content_type'], ['manual', 'related'])) {
			unset($args['post__in']);
		}

		if ($start_date_formatted === $end_date_formatted) {
			// OTHER EVENT PLUGINS ONLY - if the start & end date match
			// - Try to find dates that start or end on the selected date
			// - Kraken Events will use the query above instead for better results
			array_push($meta_query, [
				'relation' => 'OR',
				array(
					'key'     => $start_date_meta_key,
					'value'   => $start_date_formatted,
					'compare' => '=',
					'type'    => 'DATE',
				),
				array(
					'key'     => $end_date_meta_key,
					'value'   => $start_date_formatted,
					'compare' => '=',
					'type'    => 'DATE',
				),
				//array(
				//	'key'     => "event_next_occurrence",
				//	'value'   => $start_date_formatted,
				//	'compare' => '=',
				//	'type'    => 'DATE',
				//)
			]);
		} else {
			// ALL EVENT PLUGINS
			// - Return events with an end date after the selected start date
			$date_meta_query = [
				'relation'	=> 'AND',
				[
					'key'     => $end_date_meta_key,
					'value'   => $start_date_formatted,
					'compare' => '>=',
					'type'    => 'DATE',
				]
			];

			// ALL EVENT PLUGINS
			// If the end date is also set:
			if ($end_date_formatted !== "") {
				if ($event_plugin === "kraken-events") {
					// KRAKEN EVENTS ONLY
					// - Returns events with a next_occurrence before the selected end date
					$date_meta_query[] = [
						'key'     => "event_next_occurrence",
						'value'   => $end_date_formatted,
						'compare' => '<=',
						'type'    => 'DATE',
					];
				} else {
					// OTHER EVENT PLUGINS ONLY
					// - Returns events with a start date before the selected end date
					$date_meta_query[] = [
						'key'     => $start_date_meta_key,
						'value'   => $end_date_formatted,
						'compare' => '<=',
						'type'    => 'DATE',
					];
				}
			}

			array_push($meta_query, $date_meta_query);
		}
	}

	//Add appropriate orderby parameters depending on plugin
	if ($event_plugin === 'kraken-events') {
		//Kraken Events will perform a custom database query for event ordering
		$args['kraken_event_start_date'] = $start_date_formatted;
	} else {
		// Order by event date followed by event start time
		if ($args['orderby'] === 'date') {
			$args['orderby']  = [
				'orderby_date_clause' => 'ASC',
				'orderby_time_clause' => 'ASC',
			];
		}
		// Order by date clause
		array_push($meta_query, [
			'relation' => 'AND',
			'orderby_date_clause' => [
				'key' => $orderby_date_key,
				'type' => 'DATE',
			],
		]);
	}

	$args['meta_query'] = $meta_query;

	return $args;
}

/**
 * Return the api results
 */
function search_filter_results( $request ) {
	$params = $request->get_params();
	$args   = $params['queryArgs'];

	// Grab and remove card attributes
	$card_atts = $args['cardjson'] ?? false;
	unset( $args['cardjson'] );

	if ( isset( $args['tax_query'] ) ) {
		$args['tax_query']['relation'] = 'AND';
	}
	if ( isset( $args['meta_query'] ) ) {
		$args['meta_query']['relation'] = 'AND';
	}
	if ( isset( $params['dateFilters'] ) && is_array( $params['dateFilters'] ) ) {
		$args = add_event_args( $args, $params['dateFilters'] );
	}

	// Create a flap to let dev know this is an api call
	$args['is_api'] = true;

	$filter_query = new \WP_Query( apply_filters( 'kraken-core/search-and-filter/query_args', $args ) );
	$total_pages  = $filter_query->max_num_pages;
	$total_posts  = $filter_query->found_posts;
	$post_data    = [];

	if ( $filter_query->have_posts() ) {
		$request_obj = new \WP_REST_Request( 'GET', '' );

		while ( $filter_query->have_posts() ) {
			global $post;
			$filter_query->the_post();

			// Render content card
			$content_card = '';
			if ( $card_atts ) {
				ob_start();
				$cardAttrs = array_merge( $card_atts, [ 'contentId' => $post->ID ] );

				//Pass the filtered start date to the card block
				if (is_event($args['post_type'])) {
					if (!isset($cardAttrs['customAdditionalContent'])) {
						$cardAttrs['customAdditionalContent'] = [];
					}
					if (isset($params['dateFilters']['start_date'])) {
						$cardAttrs['customAdditionalContent']['filtered_start_date'] = $params['dateFilters']['start_date'];
					}
					if (isset($params['dateFilters']['end_date'])) {
						$cardAttrs['customAdditionalContent']['filtered_end_date'] = $params['dateFilters']['end_date'];
					}
				}

				$cardAttrs = array_filter( $cardAttrs, fn( $v ) => $v !== false && $v !== 'false' );
				$cardAttrs = apply_filters( 'kraken-core/search-and-filter/card_attrs', $cardAttrs, $args );

				$cardAttrs['blockParent'] = 'kraken-core/search-and-filter';
				echo do_blocks( '<!-- wp:kraken-core/content-card ' . json_encode( $cardAttrs ) . ' /-->' );
				$content_card = ob_get_clean();
			}

			// Get geo + image
			$latitude  = get_post_meta( $post->ID, 'latitude', true );
			$longitude = get_post_meta( $post->ID, 'longitude', true );
			$featured_media_id = get_post_thumbnail_id( $post->ID );

			// REST-style formatting
			$controller = new \WP_REST_Posts_Controller( $post->post_type );
			$response   = $controller->prepare_item_for_response( $post, $request_obj );
			$data       = $controller->prepare_response_for_collection( $response );

			// Add extras
			$data['content_card']       = $content_card;
			$data['latitude']           = $latitude;
			$data['longitude']          = $longitude;

			if ( $featured_media_id ) {
				$media = get_post( $featured_media_id );

				if ( $media && $media->post_type === 'attachment' ) {
					$source_url = wp_get_attachment_image_url( $featured_media_id, 'large' );

					$data['_embedded'] = [
						'wp:featuredmedia' => [
							[
								'source_url' => $source_url,
								'id'         => $featured_media_id,
								'type'       => 'image',
							],
						],
					];
				}
			}

			$post_data[] = $data;
		}
		wp_reset_postdata();
	}

	return rest_ensure_response( [
		'post_data'    => $post_data,
		'total_pages'  => $total_pages,
		'total_posts'  => $total_posts,
	] );
}

/**
 * Render a filter dropdown
 */
function render_filter_dropdown( $attrs, $atts ) {
	$defaults = [
		'type' => '',
		'filter' => '',
		'name' => 'Filter',
		'options' => [],
		'selected' => [],
	];
	$args = shortcode_atts( $defaults, $atts );
	?>
	<div class="filter-wrapper filter-<?php echo esc_attr( $args['type'] ); ?>" data-filtertype="<?php echo esc_attr( $args['type'] ); ?>" data-type="<?php echo esc_attr( $args['filter'] ); ?>">
		<?php
			$filter_dropdown_label_el = '';
			$filter_dropdown_label_el = apply_filters( 'kraken-core/search-and-filter/filter_dropdown_label', $filter_dropdown_label_el, $args );
			if (! empty( $filter_dropdown_label_el ) ) :
				echo $filter_dropdown_label_el;
			endif;
		?>
		<button class="filter-toggle-btn" aria-haspopup="true" aria-expanded="false">
			<span class="filter-toggle-btn-label">
				<?php
				echo apply_filters( 'kraken-core/search-and-filter/filter_dropdown_name', $args['name'], $attrs );

				// Active filter count
				if ($attrs['enableActiveFilterCount']) { ?>
					<span class="filter-active-count" style="display: none;"></span>
				<?php } ?>

			</span>
			<?php
			$icon = '<svg aria-hidden="true" focusable="false" role="presentation" xmlns="http://www.w3.org/2000/svg" width="17.533" height="10.024" viewBox="0 0 17.533 10.024" class="filter-arrow">
	<path d="M14.956,18.253l6.629-6.634a1.253,1.253,0,0,1,1.769,1.775l-7.511,7.516a1.251,1.251,0,0,1-1.728.037L6.553,13.4a1.253,1.253,0,0,1,1.769-1.775Z" transform="translate(-6.188 -11.251)" fill="inherit"></path>
</svg>';
			echo apply_filters( 'kraken-core/search-and-filter/filter_dropdown_icon', $icon, $attrs );
			?>
		</button>

		<div class="filter-dropdown filter-<?php echo esc_attr( $args['filter'] ); ?>" tabindex="-1">
			<ul>

				<?php
				// Filter terms
				$options = apply_filters( 'kraken-core/search-and-filter/filter_dropdown_options', $args['options'], $args['filter'], $attrs );
				foreach ($options as $option_id => $option__name) {
					$isSelected = $args['selected'] && is_array($args['selected']) && in_array($option_id, $args['selected']) ? 'checked="checked"': null;
					?>

					<li>
						<label for="<?php echo $option_id; ?>">
							<input type="checkbox" id="<?php echo $option_id; ?>" value="<?php echo $option_id; ?>" data-filtertype="<?php echo esc_attr( $args['type'] ); ?>" data-type="<?php echo esc_attr( $args['filter'] ); ?>" <?php echo $isSelected; ?>>
							<span class="label">
								<?php echo $option__name; ?>
							</span>
						</label>
					</li>

				<?php } ?>
			</ul>
		</div>
	</div>
	<?php
}
