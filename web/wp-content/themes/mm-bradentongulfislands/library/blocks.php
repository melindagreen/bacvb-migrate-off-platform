<?php
namespace MaddenNino\Library;
use MaddenNino\Library\Constants as C;

// Block types
add_action( 'init', __NAMESPACE__ . '\register_block_types' );

/**
 * Register all custom block types
 */
function register_block_types() {

  // Grab all existing block folders
  $directory = get_stylesheet_directory() . "/assets/build/scripts/gutenberg/blocks/*";
  $blocks = glob( $directory, GLOB_ONLYDIR );

  foreach ($blocks as $block) {
    $directoryName = basename($block);
    \register_block_type( get_stylesheet_directory() . "/assets/build/scripts/gutenberg/blocks/{$directoryName}" );
  }
}