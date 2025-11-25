<?php
namespace MaddenMedia\KrakenCore;

class AdminSettings {

    public static $admin_menu_parent_slug = 'kraken-dashboard';

    public static function init() {
        add_action( 'mtphrSettings/init_settings', [__CLASS__, 'register_settings_page'], 1 );
        add_action( 'mtphrSettings/init_fields', [__CLASS__, 'initialize_fields'] );
    }

    public static function register_settings_page() {

        mtphr_settings_add_admin_page( [
            'page_title' => esc_html__( 'Kraken Portal', 'kraken-core' ),
            'menu_title' => esc_html__( 'Kraken Portal', 'kraken-core' ),
            'capability'    => 'manage_options',
            'menu_slug'     => self::$admin_menu_parent_slug,
            'icon'          => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8yIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA5MS43IDExMS43MiI+PGcgaWQ9IkxheWVyXzEtMiI+PHBhdGggZD0iTTkxLjcsMjAuNjV2LS4xMWMtLjAyLS4yLS4wNS0uMzktLjEyLS41N3YtLjAyYy0uMDctLjE5LS4xOC0uMzctLjMtLjUzLS4wMi0uMDMtLjA0LS4wNi0uMDYtLjA5LS4xMy0uMTYtLjI3LS4yOS0uNDQtLjQxLS4wMS0uMDEtLjAyLS4wMy0uMDMtLjAzLS4wMi0uMDEtLjA0LS4wMS0uMDYtLjAzLS4xNy0uMTEtLjM1LS4yLS41NS0uMjctLjAyLDAtLjA0LDAtLjA2LS4wMS0uMTEtLjAzLS4yMS0uMDYtLjMyLS4wN2gtLjM1Yy0uMiwwLS4zOS4wNC0uNTguMDktLjA0LjAxLS4wNy4wMi0uMTEuMDQtLjE5LjA3LS4zOC4xNi0uNTUuMjdoLS4wMWwtMzMuNDMsMjMuNzdMMy42OC41cy0uMDMtLjAyLS4wNS0uMDNjLS4wNS0uMDQtLjEtLjA3LS4xNS0uMTEtLjA1LS4wNC0uMTEtLjA3LS4xNi0uMS0uMDUtLjAzLS4xMS0uMDUtLjE3LS4wOC0uMDYtLjAyLS4xMi0uMDUtLjE4LS4wNy0uMDYtLjAyLS4xMi0uMDQtLjE4LS4wNS0uMDYsMC0uMTItLjAzLS4xOC0uMDQtLjA2LDAtLjEzLDAtLjE5LS4wMmgtLjE4Yy0uMDYsMC0uMTIsMC0uMTgsMC0uMDcsMC0uMTQsMC0uMi4wMmgtLjA3cy0uMDcuMDItLjEuMDNjLS4wNy4wMi0uMTMuMDQtLjIuMDYtLjA2LjAyLS4xMS4wNC0uMTcuMDdzLS4xMi4wNS0uMTcuMDljLS4wNi4wMy0uMTEuMDctLjE3LjEtLjAzLjAyLS4wNi4wMy0uMDkuMDUtLjAyLjAyLS4wNC4wNC0uMDYuMDUtLjA1LjA0LS4xLjA5LS4xNS4xM2wtLjEzLjEzYy0uMDQuMDUtLjA4LjA5LS4xMS4xNC0uMDQuMDUtLjA4LjEtLjExLjE2LS4wMy4wNS0uMDYuMS0uMDkuMTZzLS4wNi4xMi0uMDguMThjLS4wMi4wNS0uMDQuMTEtLjA2LjE3LS4wMi4wNi0uMDQuMTMtLjA1LjE5cy0uMDIuMTEtLjAzLjE3YzAsLjA3LDAsLjE0LS4wMi4yMXY4MC44MmMwLC45NS42LDEuOCwxLjUsMi4xMi4yNS4wOS41LjEzLjc1LjEzLjY3LDAsMS4zMS0uMywxLjc1LS44M2wyMS4xMy0yNi4wNywzOS42LDUyLjU0LjAzLjAzYy4xLjEyLjIuMjQuMzIuMzQuMDIuMDIuMDUuMDMuMDcuMDUuMS4wOC4yMS4xNS4zMi4yMS4wNC4wMi4wOC4wNC4xMi4wNi4xMi4wNS4yNC4xLjM2LjEzLjAzLDAsLjA1LjAyLjA4LjAyLjEzLjAzLjI3LjA1LjQxLjA1aC4wOGMuMTQsMCwuMjgtLjAyLjQyLS4wNC4wNCwwLC4wOC0uMDIuMTItLjAzLjEtLjAzLjItLjA2LjMtLjEuMDItLjAxLjA0LS4wMS4wNi0uMDIuMDMtLjAxLjA1LS4wMy4wNy0uMDQuMDYtLjAzLjEyLS4wNi4xOC0uMS4wNS0uMDMuMS0uMDYuMTUtLjFzLjEtLjA4LjE1LS4xMmMuMDQtLjA0LjA5LS4wOC4xMy0uMTIuMDQtLjA1LjA4LS4wOS4xMy0uMTQuMDQtLjA0LjA4LS4wOS4xMS0uMTQuMDQtLjA1LjA3LS4xLjEtLjE2LjAzLS4wNS4wNi0uMS4wOS0uMTYuMDMtLjA2LjA1LS4xMS4wNy0uMTdzLjA1LS4xMi4wNi0uMThjMC0uMDIuMDItLjA1LjAzLS4wN2w4LjQ0LTMyLjY1LDExLjMsNS43OGMuMzIuMTYuNjcuMjUsMS4wMi4yNS40MSwwLC44Mi0uMTEsMS4xNy0uMzMuNjctLjQxLDEuMDgtMS4xNCwxLjA4LTEuOTJWMjAuNjVNODUuNzQsMjYuMTNsLTE4Ljg2LDcyLjk5LTkuNzgtNTIuNjNzMjguNjQtMjAuMzYsMjguNjQtMjAuMzZaTTguMSw5Ljk4bDQxLjg5LDM0LjYtMjMuNTQsNy4zM0w4LjEsOS45OFpNNC41LDEyLjk5bDE4LjA4LDQxLjMyLTE4LjA4LDIyLjMxVjEyLjk5Wk0yOC45MSw1NS44NmwyMy45Ni03LjQ2LDkuNjksNTIuMTFMMjguOTEsNTUuODZaTTg3LjIxLDc3LjUxbC04LjkxLTQuNTYsOC45MS0zNC41MnYzOS4wN2gwWiIgc3R5bGU9ImZpbGw6I2E3YWFhZCIvPjwvZz48L3N2Zz4=',
            // 'parent_slug' => 'options-general.php', // Use to create a submenu page
        ]);

        mtphr_settings_add_section( [
            'id'            => 'kraken_core_blocks',
            'slug'          => 'blocks',
            'label'         => __('Blocks', 'kraken-core'),
            'option'        => 'kraken_core_settings',
            'menu_slug'     => self::$admin_menu_parent_slug,
        ]);

		mtphr_settings_add_section( [
            'id'            => 'kraken_core_filters',
            'slug'          => 'filters',
            'label'         => __('Filters', 'kraken-core'),
            'option'        => 'kraken_core_settings',
            'menu_slug'     => self::$admin_menu_parent_slug,
        ]);

        mtphr_settings_add_default_values( 'kraken_core_settings', [
            'disabled_blocks' => [],
			'filter_presets' => [],
        ]);
    }

    /**
     * Initialize setting fields
     */
    public static function initialize_fields() {

        $block_names = [];
        $blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
        if ( is_array( $blocks ) && ! empty( $blocks ) ) {
            foreach ( $blocks as $block ) {
            	$block_names[$block->name] = $block->name;
            }
        }

        // Add block settings
        mtphr_settings_add_fields( [
            'section' => 'kraken_core_blocks',
            'fields' => [
                [
                    'type'    => 'heading',
                    'label'   => esc_html__( 'Block Management', 'kraken-core' ),
                ],
                [
                    'type'      => 'picklist',
                    'id'        => 'disabled_blocks',
                    'help'      => __( 'Check blocks you want to disable on the site.', 'kraken-core' ),
                    'choices'   => $block_names,
                ],
            ],
        ]);

		// Add filter settings
		$block_filter_json = KRAKEN_CORE_PLUGIN_DIR . 'inc/admin/block-filter-settings.json';
		$block_filter_fields = [];
		if (file_exists($block_filter_json)) {
			$json_data = file_get_contents($block_filter_json);
			$presets_data = json_decode($json_data, true);
			if ( is_array($presets_data)) {
				foreach ($presets_data as $id => $choices) {
					$block_filter_fields[] = [
						'type'    => 'checkboxes',
						'id'      => $id,
						'label'   => $id,
						'choices' => $choices,
					];
				}
			}
		}

        mtphr_settings_add_fields( [
            'section' => 'kraken_core_filters',
            'fields' => [
                [
                    'type'    => 'heading',
                    'label'   => esc_html__( 'Filter Presets', 'kraken-core' ),
                ],
				[
					'type'      => 'group',
					'alignment' => 'stretch',
					'wrap'      => true,
					'id'        => 'filter_presets',
					'fields'    => $block_filter_fields
				],
            ],
        ]);
    }

    /**
     * Return settings
     */
    public static function get_option_value( $key = false, $option = false ) {
        $option = $option ? $option : 'kraken_core_settings';
        if ( function_exists( 'mtphr_settings_ready' ) && mtphr_settings_ready() ) {
            return mtphr_settings_get_option_value( $option, $key );
        } else {
            $settings = get_option( $option );
            if ( $key ) {
            if ( isset( $settings[$key] ) ) {
                return $settings[$key];
            }
            } else {
            return $settings;
            }
        }
    }

    /**
     * Update settings
     */
    public static function set_option_value( $key, $value = false, $option = false ) {
        $option = $option ? $option : 'kraken_core_settings';
        if ( function_exists( 'mtphr_settings_set_option_value' ) ) {
            return mtphr_settings_set_option_value( $option, $key, $value );
        }
    }
}
