<?php
namespace MaddenMedia\KrakenCore;

class Blocks {
  public static function init() {
	add_filter( 'register_block_type_args', [__CLASS__, 'register_dynamic_attributes'], 10, 2 );
    add_action( 'init', [__CLASS__, 'register_block_types']);
    add_filter( 'block_categories_all', [__CLASS__, 'add_block_categories'], 10, 2);
    add_filter( 'allowed_block_types_all',  [__CLASS__, 'allowed_blocks'], 99, 2 );
	add_action( 'enqueue_block_assets', [__CLASS__, 'enqueue_theme_block_assets']);
	add_action( 'after_setup_theme', [__CLASS__, 'include_theme_hooks']);
  }

	public static function register_dynamic_attributes($args, $name) {
		$theme_json = AdminSetup::get_theme_settings();

		$attributes_config = $theme_json['blockFilters'] ?? [];

		if (empty($attributes_config) || !isset($attributes_config[$name])) {
			return $args;
		}

		// Merge dynamic attributes with the block's existing attributes.
		$extra_attributes = $attributes_config[$name];
		$args['attributes'] = array_merge(
			isset( $args['attributes'] ) ? $args['attributes'] : [],
			$extra_attributes
		);

		return $args;
	}

  public static function register_block_types() {
    // Grab all existing block folders
    $directory = KRAKEN_CORE_PLUGIN_DIR . "assets/build/blocks/*";
    $blocks = glob($directory, GLOB_ONLYDIR);

    foreach ($blocks as $block) {
    	$block_name 	= basename($block);
		$block_folder 	= KRAKEN_CORE_PLUGIN_DIR . "assets/build/blocks/{$block_name}";

    	\register_block_type($block_folder);

		//check for possible child blocks
		$subdirectories = $block_folder."/*";
		$child_blocks 	= glob($subdirectories, GLOB_ONLYDIR);

		if ($child_blocks) {
			foreach ($child_blocks as $child) {
				$subdirectory_name 	= basename($child);
				$child_block_json 	= $block_folder . "/{$subdirectory_name}/block.json";
				if (file_exists($child_block_json)) {
					\register_block_type($block_folder. "/{$subdirectory_name}");
				}
			}
		}
    }
  }

	/**
	 * Adds custom "Kraken Core" block category
	 */
	public static function add_block_categories($categories, $context) {
		$categories[] = [
		'slug' => 'kraken-core',
		'title' => __( 'Kraken Core', 'kraken-core' ),
		];
		return $categories;
	}

  /**
   * Disable user selected blocks
   */
  public static function allowed_blocks( $allowed_blocks, $context ) {

    // If $allowed_blocks is false, return false
    if ( ! $allowed_blocks ) {
      return $allowed_blocks;
    }

    // Check if there are any disabled blocks
    $disabled_blocks = AdminSettings::get_option_value('disabled_blocks');
    if ( ! is_array( $disabled_blocks ) || empty( $disabled_blocks ) ) {
      return $allowed_blocks;
    }

    // Get all registered blocks
    $registry = \WP_Block_Type_Registry::get_instance();
    $blocks = $registry->get_all_registered();

    // Create a new array of non-disabled blocks to return
    $updated_allowed_blocks = [];
    if ( is_array( $blocks ) && ! empty( $blocks ) ) {
      foreach ( $blocks as $block_name => $block ) {
        if ( ! in_array( $block_name, $disabled_blocks ) ) {
          $updated_allowed_blocks[] = $block_name;
        }
      }
    }

    return $updated_allowed_blocks;
  }

	public static function enqueue_theme_block_assets() {

		$block_folders = glob(KRAKEN_CORE_PLUGIN_DIR . "assets/build/blocks/*", GLOB_ONLYDIR);

		foreach ( $block_folders as $block_folder ) {
			$block_json_path = $block_folder . "/block.json";

			if (!file_exists($block_json_path)) {
				continue;
			}

			$block_data = json_decode(file_get_contents($block_json_path), true);
			$full_block_name = $block_data['name'] ?? null;

			if (!$full_block_name) {
				continue;
			}

			$block_name 		= basename($block_folder);
			$theme_build_dir 	= get_stylesheet_directory() . '/assets/build/src/kraken-core/' . $block_name;
			$theme_build_url 	= get_stylesheet_directory_uri() . '/assets/build/src/kraken-core/' . $block_name;

			if ( ! is_dir( $theme_build_dir ) ) {
				continue;
			}

			$asset_files = ['editor', 'view', 'common'];

			foreach ($asset_files as $file_base_name) {
				$asset_path = $theme_build_dir . '/' . $file_base_name . '.asset.php';
				$js_path    = $theme_build_dir . '/' . $file_base_name . '.js';
				$css_path   = $theme_build_dir . '/' . $file_base_name . '.css';

				$scripts 	= false;
				$styles 	= false;

				if (!file_exists($asset_path)) {
					continue;
				} else {
					//Double check that the JS/CSS files exist before enqueueing them
					if (file_exists($js_path)) {
						$scripts = true;
						$js_url = $theme_build_url . '/' . $file_base_name . '.js';
					}
					if (file_exists($css_path)) {
						$styles = true;
						$css_url = $theme_build_url . '/' . $file_base_name . '.css';
					}
				}

				$asset = require $asset_path;

				// --- Enqueue Scripts ---
				if ($scripts) {
					$original_script_handle = '';
					if ((is_admin() && in_array($file_base_name, ['editor', 'common'], true ))) {
						$original_script_handle = str_replace('/', '-', $full_block_name) . '-editor-script';
					} elseif (!is_admin() && in_array($file_base_name, ['view', 'common'], true)) {
						$original_script_handle = str_replace('/', '-', $full_block_name) . '-view-script';
					}

					if (!empty($original_script_handle) && wp_script_is($original_script_handle, 'enqueued')) {
						$handle       = "kraken-core-theme-{$block_name}-{$file_base_name}-script";
						$dependencies = array_merge($asset['dependencies'], [$original_script_handle]);
						wp_enqueue_script($handle, $js_url, $dependencies, $asset['version'], true);
					}
				}

				// --- Enqueue Styles ---
				if ($styles) {
					$original_style_handle = '';
					if (is_admin() && in_array($file_base_name, ['editor', 'common'], true)) {
						$original_style_handle = str_replace( '/', '-', $full_block_name ) . '-editor-style';
					}
					if (!is_admin() && in_array($file_base_name, ['view', 'common'], true)) {
						// The 'style' key from block.json maps to the main style handle.
						$original_style_handle = str_replace('/', '-', $full_block_name) . '-style';
					}

					if (!empty($original_style_handle) && wp_style_is($original_style_handle, 'enqueued')) {
						$handle = "kraken-core-theme-{$block_name}-{$file_base_name}-style";
						wp_enqueue_style($handle, $css_url, [$original_style_handle], $asset['version']);
					}
				}
			}
		}
	}

	public static function include_theme_hooks() {
		$child_theme_hooks_file = get_stylesheet_directory() . '/assets/src/kraken-core/block-hooks.php';
		if (file_exists($child_theme_hooks_file)) {
			require_once $child_theme_hooks_file;
		}
	}
}
