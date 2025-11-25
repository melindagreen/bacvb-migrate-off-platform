<?php
namespace MaddenMedia\KrakenCore;

class AdminSetup {

    public static function init() {
    	add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
    	add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueue_admin_editor_assets']);
		add_filter( 'mtphrSettings/enqueue_fields', [__CLASS__, 'enqueue_custom_fields'] );
    }

    public static function get_theme_settings() {
		// Prefer get_theme_file_path() to allow parent-theme fallback if needed.
		$theme_blocks_path = get_theme_file_path( 'assets/src/kraken-core/kraken-core.json' );

		// Decode as an associative array.
		$block_customization = [];

		if (file_exists($theme_blocks_path) && is_readable($theme_blocks_path)) {
			$decoded = wp_json_file_decode(
				$theme_blocks_path,
				[ 'associative' => true, 'depth' => 512 ]
			);

			if (is_array($decoded)) {
				$block_customization = $decoded;
			} else {
				error_log( 'kraken-core.json: JSON decode failed or did not return an array.' );
			}
		} else {
			//error_log( 'kraken-core.json: file missing or unreadable.' );
		}

		// Ensure we have data points from json so we don't get js errors
		$block_customization['blockData'] = $block_customization['blockData'] ?? [];
		$block_customization['blockData']['cardStyles'] = $block_customization['blockData']['cardStyles'] ?? [];
		$block_customization['blockData']['cardAttributes'] = $block_customization['blockData']['cardAttributes'] ?? [];
		$block_customization['blockData']['ignoredPostTypes'] = $block_customization['blockData']['ignoredPostTypes'] ?? [];
		$block_customization['blockFilters'] = $block_customization['blockFilters'] ?? [];

		// Add block filter presets
		$block_customization['blockFilterPresets'] = AdminSettings::get_option_value( 'filter_presets' );

  		return $block_customization;
    }

    public static function enqueue_admin_assets() {
        wp_enqueue_style(
			'kraken-core-admin-style',
			KRAKEN_CORE_PLUGIN_URL . 'assets/build/admin.css',
			[],
			filemtime( KRAKEN_CORE_PLUGIN_DIR . 'assets/build/admin.css' ),
        );

        wp_enqueue_script(
            'kraken-core-admin-script',
            KRAKEN_CORE_PLUGIN_URL . 'assets/build/admin.js',
            ['wp-hooks'],
            filemtime( KRAKEN_CORE_PLUGIN_DIR . 'assets/build/admin.js' ),
            true
        );
	}

    public static function enqueue_admin_editor_assets() {

        // Register a dummy handle to localize global data
        wp_register_script( 'kraken-theme-settings', false, [], null );
        wp_localize_script(
			'kraken-theme-settings',
			'KrakenThemeSettings',
			self::get_theme_settings()
        );

		// Enqueue filter scripts
        wp_enqueue_script(
			'kraken-core-block-filters',
			KRAKEN_CORE_PLUGIN_URL . 'assets/build/filters.js',
			['kraken-theme-settings', 'kraken-core-admin-script'],
			filemtime(KRAKEN_CORE_PLUGIN_DIR . 'assets/build/filters.js'),
			false //must be loaded in head or filters will be broken
        );
    }

	/**
	 * Enqueue custom mtphr settings fields
	 */
	public static function enqueue_custom_fields( $registry ) {
		$asset_file = include( KRAKEN_CORE_PLUGIN_DIR . 'assets/build/customFields.asset.php' );
		wp_enqueue_style(
			'kraken-core-custom-fields',
			KRAKEN_CORE_PLUGIN_URL . 'assets/build/customFields.css',
			[],
			filemtime( KRAKEN_CORE_PLUGIN_DIR . 'assets/build/customFields.js' )
        );
		wp_enqueue_script(
			'kraken-core-custom-fields',
			KRAKEN_CORE_PLUGIN_URL . 'assets/build/customFields.js',
			array_unique( array_merge( $asset_file['dependencies'], [$registry] ) ),
			filemtime( KRAKEN_CORE_PLUGIN_DIR . 'assets/build/filters.js' ),
			true
        );
    }
}
