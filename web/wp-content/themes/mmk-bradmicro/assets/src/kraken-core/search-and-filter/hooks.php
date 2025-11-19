<?php
namespace MaddenTheme\KrakenCore\SearchAndFilter;

add_filter( 'kraken-core/search-and-filter/filter_elements', __NAMESPACE__ . '\filter_elements', 10, 2 );
add_action( 'kraken-core/search-and-filter/action_filter_label', __NAMESPACE__ . '\action_filter_label' );
add_action( 'kraken-core/search-and-filter/action_search_label', __NAMESPACE__ . '\action_search_label' );

/**
 * Add filter elements
 */
function filter_elements( $elements, $attributes ) {
	return [
		'action_filter_label',
		'event_start',
		'event_end',
		//'action_search_label',
		'search',
		'taxonomies',
		'sort',
	];
}

function action_filter_label() {
	echo '<h3 class="filters-label">' . __( 'Filter by date', 'madden-theme' ) . '</h3>';
}

function action_search_label() {
	echo '<h3 class="search-label">' . __( 'Event Name', 'madden-theme' ) . '</h3>';
}
