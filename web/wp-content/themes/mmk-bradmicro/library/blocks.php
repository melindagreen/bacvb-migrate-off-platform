<?php
namespace MaddenTheme\Library;
use MaddenTheme\Library\Constants as C;

// Block types
add_action( 'init', __NAMESPACE__ . '\register_block_types' );
add_action( 'wp', __NAMESPACE__ . '\override_block_render_callback', 99 );
add_action( 'after_setup_theme', __NAMESPACE__ . '\remove_block_patterns' );
add_filter( 'should_load_remote_block_patterns', '__return_false' );
add_filter( 'default_wp_template_part_areas', __NAMESPACE__ . '\template_part_areas' );
add_filter('allowed_block_types_all', __NAMESPACE__ . '\remove_core_blocks');

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

/**
 * Set a custom render callback for blocks
 */
function override_block_render_callback() {
  if ( is_admin() || ! function_exists('register_block_type') ) {
    return false;
  }

  $base_dir = get_stylesheet_directory() . "/assets/src/scripts/gutenberg/block-overrides/";
  $directories = scandir( $base_dir );

  // Initialize an empty array to store our results
  $result = array();

  // Loop through the directory listing
  foreach ( $directories as $directory ) {
      
    // Construct the full path to the directory
    $full_dir_path = $base_dir . $directory;

    // Check if it's a directory and not '.' or '..'
    if ( $directory != '.' && $directory != '..' && is_dir( $full_dir_path ) ) {
        
      // Loop through subdirectories and build relative paths
      $subdirectories = scandir( $full_dir_path );
      foreach ( $subdirectories as $subdir ) {
        $subdir_path = $full_dir_path . '/' . $subdir;
        if ( $subdir != '.' && $subdir != '..' && is_dir( $subdir_path ) ) {
          
          // Make sure render.php exists
          if ( ! file_exists( $subdir_path . '/render.php' ) ) {
            continue;
          }

          // Create the block name and get the block
          $block_name = $directory . '/' . $subdir;
          $block = \WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
          if ( ! is_null( $block ) ) {

            $original_callback = ! empty( $block->render_callback ) ? $block->render_callback : false;

            // Override the render_callback with our file contents
            $template_path = $subdir_path . '/render.php';
            $block->render_callback = static function ( $attributes, $content, $block ) use ( $template_path, $original_callback ) {

              // Call the original render callback
              $original_output = false;
              if ( $original_callback ) {
                $render_content = call_user_func( $original_callback, $attributes, $content, $block );
              }

              ob_start();
              require $template_path;
              return ob_get_clean();
            };
          }
        }
      }
    }
  }
}

/**
 * Remove core block patterns, disables the "pattern directory"
 */
function remove_block_patterns() {
  remove_theme_support( 'core-block-patterns' );
}

/**
 * Adds a custom template part area for mega menus to the list of template part areas.
 *
 * @param array $areas Existing array of template part areas.
 * @return array Modified array of template part areas including the new "Menu" area.
 */
function template_part_areas( array $areas ) {
	$areas[] = array(
		'area'        => 'menu',
		'area_tag'    => 'div',
		'description' => __( 'Menu templates are used to create sections of a mega menu.', C::THEME_PREFIX ),
		'icon'        => '',
		'label'       => __( 'Menu', C::THEME_PREFIX ),
	);

	return $areas;
}

/*
Remove specified core blocks
*/
function remove_core_blocks($allowed_blocks) {
    // Get all registered blocks
    $blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();

    // Disable two specific blocks
    unset($blocks['core/spacer']);

    return array_keys($blocks);
}