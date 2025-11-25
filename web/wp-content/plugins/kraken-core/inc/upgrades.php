<?php
namespace MaddenMedia\KrakenCore;
use function MaddenMedia\KrakenCore\set_option_value;

add_action( 'admin_init', __NAMESPACE__ . '\run_updates' );

/**
 * Run updates
 *
 * @since  1.0.0
 * @return void
 */
function run_updates() {
	$current_version = get_option( 'kraken_core_version', '0' );
  if ( ! $current_version ) {
    $current_version = '0';
  }
	if ( version_compare( $current_version, '0.0.1', '<' ) ) {
    update_v0_0_1();
	}

	if ( KRAKEN_CORE_PLUGIN_VERSION != $current_version ) {
		update_option( 'kraken_core_version_upgraded_from', $current_version );
		update_option( 'kraken_core_version', KRAKEN_CORE_PLUGIN_VERSION );
	}
}

/**
 * Migrate settings from gforms plugins
 */
function update_v0_0_1() {
  // $old_setting_1 = get_option( 'option_name_here' );
  // $old_setting_2 = get_option( 'option_name_here' );
  // $update_array = [
  //   'old_setting_1' => $old_setting_1,
  //   'old_setting_2' => $old_setting_2,
  // ];
  // AdminSettings::set_option_value( $update_array );
}