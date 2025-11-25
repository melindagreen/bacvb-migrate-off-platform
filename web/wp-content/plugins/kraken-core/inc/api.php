<?php
namespace MaddenMedia\KrakenCore;

add_action( 'rest_api_init',  __NAMESPACE__ . '\register_api_routes' );

// Register API routes
function register_api_routes() {
  require_once KRAKEN_CORE_PLUGIN_DIR . '/assets/build/blocks/search-and-filter/inc/functions.php';
  register_rest_route( "kraken-core/v1", 'searchFilterResults', array(
    'methods' 	=> 'GET',
    'callback' 	=> '\MaddenMedia\KrakenCore\Blocks\SearchResultsFilter\search_filter_results',
    'permission_callback' => '__return_true',
  ) );
}