<?php
namespace MaddenMedia\KrakenCore\Blocks\SearchResultsFilter;
use MaddenMedia\KrakenCore\Utilities as U;

use WP_Query;
include_once __DIR__ . '/inc/functions.php';

/**
 * Render function for the dynamic example block
 * @param array $attributes all block attributes
 * @param string $content
 * @param WP_Block $block The block instance.
 */

//IF there are not any results on page load WordPress will not enqueue the content card assets, this tells WordPress to do it anyways
if (\WP_Block_Type_Registry::get_instance()->is_registered('kraken-core/content-card')) {
  wp_enqueue_style('kraken-core-content-card-style');
  //wp_enqueue_script('kraken-core-content-card-view-script');
}

$in_editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST && !empty($_REQUEST['context']) && $_REQUEST['context'] === 'edit';
//Use a static variable to ensure the script is localized only once.
static $script_localized = false;
$attrs = $attributes;

// Generate a unique ID for this block instance
$unique_id = uniqid('kraken-search-filter-');

$content_type = isset( $attrs['contentType'] ) ? $attrs['contentType'] : 'automatic';
$manual_posts = isset( $attrs['manualPosts'] ) ? $attrs['manualPosts'] : [];
$post__in = ! empty( $manual_posts ) ? array_column( $manual_posts, 'id' ) : false;
$attrs['manualPosts'] = $post__in;

$paged          = get_query_var('paged') ? get_query_var('paged') : 1;
$queryParams    = isset($_GET) ? $_GET : false;
$activeFilters  = [];

$hideLoadMore   = false;
$gridView       = str_contains($attrs['enabledView'], 'grid');
$mapView        = str_contains($attrs['enabledView'], 'map');

$orderby        = isset($queryParams['orderby']) ? $queryParams['orderby'] : $attrs['orderBy'];

//Mobile device detection to set a different posts per page value
$is_mobile = preg_match('/(Mobi|Android|iPhone)/i', $_SERVER['HTTP_USER_AGENT']);
$posts_per_page = $is_mobile ? $attrs['perPageMobile'] : $attrs['perPage'];

$args = [
  'paged'               => $paged,
  'post_type'           => $attrs['postType'],
  'posts_per_page'      => $posts_per_page,
  'orderby'             => $orderby,
  'order'               => $attrs['order'],
  'post_status'         => 'publish',
  'ignore_sticky_posts' => true,
  'content_type'        => $content_type, // Added to use in filters
];

if ( 'manual' == $content_type && $post__in ) {
  $args['post__in'] = $post__in;
}

//If block is set to "related posts"
if ($content_type === "related") {
  $args = related_post_args($args, $attrs);
}

// Inject the event args
if (is_event($attrs['postType'])) {
  	$start_date = '';
  	$end_date   = '';

  	if ($queryParams && isset($queryParams['start_date']) && $queryParams['start_date'] !== "") {
    $start_date = validate_and_convert_datetime($queryParams['start_date']);
  	} else {
		//default to today's date if not set in query params
		$start_date =  validate_and_convert_datetime(date("Y/m/d"));
	}

	if ($queryParams && isset($queryParams['end_date']) && $queryParams['end_date'] !== "") {
		$end_date = validate_and_convert_datetime($queryParams['end_date']);
	}

	$attrs['start_date'] = $start_date;
	$attrs['end_date'] = $end_date;
	$args = add_event_args( $args, $attrs );
}

//build taxonomy query with prefiltered results from the editor & any query string parameters
$taxQuery     = [
	'relation' => 'AND'
];
$initTaxQuery = [];

//editor filters - these will be fixed and can't be cleared
if ($attrs['enableTaxonomyQuery']) {
  $initTaxQuery = [
    'taxonomy'      => $attrs['taxonomyQueryType'],
    'terms'         => $attrs['taxonomyQueryTerms']
  ];
  $taxQuery[] = $initTaxQuery;
}

//check for any query string parameters - these can be cleared by the user
$requireAllTerms = $attrs['requireAllTerms'] ?? false;
if ($queryParams) {
	$filterTaxQuery = [];
	foreach($queryParams as $key => $value) {
		//rewrite categories as category for php query
		if ($key === 'categories') $key = 'category';
		if (taxonomy_exists($key)) {
			$terms = is_array( $value ) ? $value : explode(',', $value);
			if ( count($terms) > 0 ) {
				foreach( $terms as $term ) {
					$filterTaxQuery[] = [
						'taxonomy'  => $key,
						'terms'     => $term,
					];
				}
			}
			if ( ! empty( $filterTaxQuery ) ) {
				$filterTaxQuery['relation'] = $requireAllTerms ? 'AND' : 'OR';
				$taxQuery[] = $filterTaxQuery;
			}
		}
	}
}

if (!empty($taxQuery)) {
  $args['tax_query'] = $taxQuery;
}

//Rewrite post to posts & page to pages for API queries
$postType = $attrs['postType'] ? $attrs['postType'] : null;
if ($postType === 'post') $postType = 'posts';
if ($postType === 'page') $postType = 'pages';

//Rewrite category to categories for API queries
$taxonomyQueryType = $attrs['taxonomyQueryType'] ? $attrs['taxonomyQueryType'] : null;
if ($taxonomyQueryType === 'category') $taxonomyQueryType = 'categories';

// Check if the block is set to display posts in random order
$cache_posts = isset( $attrs['cachePosts'] ) ? $attrs['cachePosts'] : false;
// Only apply random ordering if explicitly set to 'rand' AND caching is enabled
// Add explicit check to prevent random ordering when not intended
if ( 'rand' === $attrs['orderBy'] && $cache_posts === true ) {

  // Step 1: Prepare a unique cookie key and path for this page
  $cookie_name = 'filter_grid_posts_key';
  $current_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

  // Step 2: If the user doesn't already have a unique ID cookie, create and store one
  if ( empty( $_COOKIE[$cookie_name] ) ) {
    $unique_id = uniqid( 'filter_grid_posts_' . get_the_id() . '_', true ); // Generate a unique ID (can also use wp_generate_uuid4())
    setcookie( $cookie_name, $unique_id, time() + (10 * YEAR_IN_SECONDS), $current_path ); // Set the cookie with a long expiration
    $_COOKIE[$cookie_name] = $unique_id; // Ensure it's immediately available in the current request
  } else {
    $unique_id = $_COOKIE[$cookie_name]; // Retrieve existing unique ID from cookie
  }

  // Step 3: Attempt to get previously stored random post IDs for this user from the transient
  $random_post_data = get_transient( $unique_id );
  $force_refresh = true;
  if ( $random_post_data && is_array( $random_post_data ) && isset( $random_post_data['attrs'] ) ) {
    if ( serialize( $attrs ) === serialize( $random_post_data['attrs'] ) ) {
      $force_refresh = false;
    }
  }

  if ( ! $random_post_data || $force_refresh ) {

    // Step 4a: If no random IDs are cached, build a new random query
    $random_args = $args;
    $random_args['posts_per_page'] = -1; // Get all matching posts
    unset( $random_args['offset'] ); // Remove offset for full randomization
    $random_query = new WP_Query( $random_args );

    // Step 4b: Collect the post IDs from the results
    $random_post_ids = [];
    if ( $random_query->have_posts() ) {
      while ( $random_query->have_posts() ) {
        $random_query->the_post();
        $random_post_ids[] = get_the_id();
      }
      wp_reset_postdata();
    }

    $random_post_data = [
      'attrs' => $attrs,
      'post_ids' => $random_post_ids,
    ];

    // Step 4c: Save the randomized post IDs to a transient for this user's session on this page
    set_transient( $unique_id, $random_post_data, DAY_IN_SECONDS );
  }

  // Step 5: Update the query args to use the specific random post IDs in order
  $random_post_ids = isset( $random_post_data['post_ids'] ) ? $random_post_data['post_ids'] : [];

  // For random ordering with caching, we need to handle pagination manually
  if ( ! empty( $random_post_ids ) ) {
    $posts_per_page = $args['posts_per_page'];
    $paged = $args['paged'];
    $offset = ( $paged - 1 ) * $posts_per_page;

    // Get the posts for the current page
    $paged_post_ids = array_slice( $random_post_ids, $offset, $posts_per_page );

    $args['post__in'] = $paged_post_ids;
    $args['orderby'] = 'post__in'; // Preserves the random order
  }
}

if (!isset($args['order'])) {
  $args['order'] = 'asc';
}

//check for existing search term
if (isset($_GET['search'])) {
  $args['s'] = $_GET['search'];
  //orderby relevance if exists
  $args['orderby']  = 'relevance';
  $args['order']    = 'desc';
  //do not use this value for data-orderby="" so if the filters are cleared the results can be reset to the default ordering
}

// Query the initial set of posts
// Store the filtered args back into the variable
$args = apply_filters( 'kraken-core/search-and-filter/query_args', $args );
$query = new \WP_Query( $args );

// For random ordering with caching, we need to calculate total pages differently
if ( 'rand' === $attrs['orderBy'] && $cache_posts === true && isset( $random_post_ids ) && ! empty( $random_post_ids ) ) {
  $totalResults = count( $random_post_ids );
  $totalPages = ceil( $totalResults / $args['posts_per_page'] );
} else {
  $totalPages = $query->max_num_pages;
  $totalResults = $query->found_posts;
}

$classes = [
  $unique_id,
  $attrs['enableFilterBar'] ? $attrs['displayFilterSidebar'] ? 'filters-sidebar' : 'filters-above' : '',
  $attrs['className'] ?? ''
];

$wrapper_args = ['class' => implode(' ', $classes)];

//Generate inline styles
$color_styles = '';
$color_attributes = [
    'backgroundColor', 'textColor', 'filterBackgroundColor', 'filterBackgroundHoverColor',
    'filterBarBackgroundColor', 'filterBarTextColor', 'filterTextColor', 'filterTextHoverColor', 'activeFilterBackgroundColor', 'activeFilterTextColor',
    'activeFilterBackgroundHoverColor', 'activeFilterTextHoverColor', 'resetFilterBackgroundColor',
    'resetFilterTextColor', 'resetFilterBackgroundHoverColor', 'resetFilterTextHoverColor',
    'paginationBackgroundColor', 'paginationTextColor', 'paginationBackgroundHoverColor',
    'paginationTextHoverColor', 'paginationBackgroundActiveColor', 'paginationTextActiveColor',
    'paginationArrowBackgroundColor', 'paginationArrowColor', 'paginationArrowBackgroundHoverColor',
    'paginationArrowHoverColor', 'spinnerColor', 'viewToggleBackgroundColor', 'viewToggleTextColor',
    'viewToggleBackgroundHoverColor', 'viewToggleTextHoverColor', "resultsCountTextColor", "noResultsTextColor"
];

if ( is_event( $attrs['postType'] ) ) {
  $color_attributes[] = 'eventDateBackgroundColor';
  $color_attributes[] = 'eventDateTextColor';
}

$color_attributes = apply_filters( 'kraken-core/search-and-filter/color-options', $color_attributes, $attrs );

foreach ($color_attributes as $attr) {
  if (!empty($attrs[$attr])) {
      $css_var = '--' . U::to_kebab_case($attr);
      $color_styles .= "{$css_var}: var(--wp--preset--color--{$attrs[$attr]});";
  }
}

if (!empty($color_styles)) {
// For the editor (a REST request), apply styles directly to the wrapper's style attribute.
    if ($in_editor_preview) {
        $wrapper_args['style'] = $color_styles;
    }
    // For the frontend, enqueue the styles properly for better performance.
	// Attempting to make this more specific than any plugin or theme stylesheets since the editor settings should have top priority
    else {
        $style_rules = "body .{$unique_id}.wp-block-kraken-core-search-and-filter { {$color_styles} }";
        wp_add_inline_style('kraken-core-search-and-filter-style', $style_rules);
    }
}

//Data for inline script
$cardJson = [
  "backgroundColor"           => $attrs["backgroundColor"],
  "textColor"                 => $attrs["textColor"],
  "cardStyle"                 => $attrs["cardStyle"],
  "postType"                  => $attrs["postType"],
  "displayAdditionalContent"  => isset( $attrs["displayAdditionalContent"] ) ? $attrs["displayAdditionalContent"] : false,
  "displayEventDate"          => isset( $attrs["displayEventDate"] ) ? $attrs["displayEventDate"] : false,
  "displayEventTime"          => isset( $attrs["displayEventTime"] ) ? $attrs["displayEventTime"] : false,
  "displayAddress" 				=> isset( $attrs["displayAddress"] ) ? $attrs["displayAddress"] : false,
  "displayWebsiteLink" 			=> isset( $attrs["displayWebsiteLink"] ) ? $attrs["displayWebsiteLink"] : false,
  "displayMindtripCta" 			=> isset( $attrs["displayMindtripCta"] ) ? $attrs["displayMindtripCta"] : false,
  "displayExcerpt"            => isset( $attrs["displayExcerpt"] ) ? $attrs["displayExcerpt"] : false,
  "excerptLength"             => $attrs["excerptLength"],
  "displayReadMore"           => $attrs["displayReadMore"],
  "readMoreText"              => $attrs["readMoreText"],
  "mindtripCtaType" 			=> $attrs["mindtripCtaType"],
  "mindtripCtaText" 			=> $attrs["mindtripCtaText"],
  "mindtripPrompt" 			 	=> $attrs["mindtripPrompt"],
  "customAdditionalContent"   => $attrs["customAdditionalContent"],
];

//Pass the filtered start and/or end date to the card block
if (is_event($attrs['postType'])) {
	if (!empty($start_date)) {
    	$cardJson['customAdditionalContent']['filtered_start_date'] = $start_date;
	}
	if (!empty($end_date)) {
    	$cardJson['customAdditionalContent']['filtered_end_date'] = $end_date;
	}
  $cardJson['eventDateBackgroundColor'] = ( $attrs['eventDateBackgroundColor'] ) ? $attrs['eventDateBackgroundColor'] : '';
  $cardJson['eventDateTextColor'] = ( $attrs['eventDateTextColor'] ) ? $attrs['eventDateTextColor'] : '';
}



$cardJson = apply_filters( 'kraken-core/search-and-filter/card-json', $cardJson, $attrs );

// Only localize the script once per page load.
if (!$script_localized) {
    // Pass SVG icon content to the frontend script
    $svg_icons = [
        'prev'  => apply_filters( 'kraken-core/search-and-filter/pagination_prev_icon', file_get_contents( __DIR__ . '/icons/pagination-prev.php' ) ),
        'next'  => apply_filters( 'kraken-core/search-and-filter/pagination_next_icon', file_get_contents( __DIR__ . '/icons/pagination-next.php' ) ),
        'close' => apply_filters( 'kraken-core/search-and-filter/close_icon', file_get_contents( __DIR__ . '/icons/close.php' ) ),
    ];
    wp_localize_script('kraken-core-search-and-filter-view-script', 'krakenSearchFilter', [
        'svgs' => $svg_icons,
        'blocks' => [], // Initialize an empty object for block-specific data
    ]);
    $script_localized = true;
}

// Add an inline script to populate the data for this specific block instance.
$inline_script = sprintf(
    'window.krakenSearchFilter.blocks["%s"] = { cardJson: %s };',
    $unique_id,
    wp_json_encode($cardJson)
);
wp_add_inline_script('kraken-core-search-and-filter-view-script', $inline_script, 'after');

//$orderby is for initial WP Query, $data_orderby is for the data attribute
$data_orderby = is_array($args['orderby']) ? htmlentities(json_encode($args['orderby'])) : $orderby;

// Need to remove the url tax_query params and add the init tax query
$modified_query_args = $args;
$modified_query_args['tax_query'] = $initTaxQuery;

$wrapper_attributes = get_block_wrapper_attributes($wrapper_args);
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php
	if ($attrs['enableFilterBar']) {
		echo '<aside>';
		include_once('inc/filter-bar.php');
		include_once('inc/filter-actions.php');
		include_once('inc/view-toggle.php');
		echo '</aside>';
	}
	?>

	<?php do_action('kraken-core/search-and-filter/before_grid_wrapper', $attrs); ?>

	<div class="main-grid-wrapper">

		<?php
		$result_wrapper_attrs = [
			'uid'             => $unique_id,
			'page'            => $paged,
			'postin'          => isset($args['post__in']) ? implode(',', $args['post__in']) : false,
			'perpage'         => $attrs['perPage'],
			'perpagemobile'   => $attrs['perPageMobile'],
			'totalpages'      => $totalPages,
			'totalresults'    => $totalResults,
			'posttype'        => $postType,
			'taxquery'        => $attrs['enableTaxonomyQuery'],
			'taxonomy'        => $taxonomyQueryType,
			'terms'           => implode(', ', $attrs['taxonomyQueryTerms']),
			'orderby'         => $data_orderby,
			'order'           => $args['order'] ?? 'desc',
			'pagination'      => $attrs['paginationStyle'],
			'start_date'      => $start_date ?? false,
			'end_date'        => $end_date ?? false,
			'date_range'      => $attrs['selectedDateRange'],
			'views'           => $attrs['enabledView'],
			'requireallterms' => $requireAllTerms,
			'queryargs'       => htmlspecialchars(wp_json_encode($modified_query_args))
		];
		?>

		<div class="search-results" <?php foreach ($result_wrapper_attrs as $key => $value) : ?> data-<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>" <?php endforeach; ?>>

		<?php
		include('inc/grid-view.php');
		include('inc/map-view.php');
		?>
			<span class="loading-spinner" style="display: none;"><span></span></span>

		</div>

		<?php
		if ($attrs['paginationStyle'] !== 'none') {
			include('inc/pagination.php');
		}
		if ($attrs['displayResultsCount']) {
			include('inc/results-count.php');
		}
		?>
	</div>

	<?php do_action('kraken-core/search-and-filter/after_grid_wrapper', $attrs); ?>

</section>
