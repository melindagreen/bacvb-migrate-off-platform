<?php
namespace MaddenMedia\KrakenCore;

class Assets {
	public static function init() {
    	add_action( "wp_enqueue_scripts", [__CLASS__, 'enqueue_front_scripts']);
    	add_action( "wp_enqueue_scripts", [__CLASS__, 'enqueue_front_styles']);
	}

  public static function enqueue_front_scripts() {
	/*
	Leaving disabled unless we add anything to app.js
    $assets_file_front = include(KRAKEN_CORE_PLUGIN_DIR. 'assets/build/app.asset.php');
	wp_enqueue_script(
		'kraken-core-script',
		KRAKEN_CORE_PLUGIN_URL . 'assets/build/app.js',
		[],
		$assets_file_front["version"],
		true
	);
	*/
  }

  public static function enqueue_front_styles() {
    $assets_file_front = include(KRAKEN_CORE_PLUGIN_DIR. 'assets/build/app.asset.php');
	wp_enqueue_style(
		'kraken-core-style',
		KRAKEN_CORE_PLUGIN_URL . 'assets/build/style-app.css',
		[],
		filemtime( KRAKEN_CORE_PLUGIN_DIR . 'assets/build/style-app.css' ),
	);
  }
}
