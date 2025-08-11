<?php

namespace MaddenNino\Library\Hooks;

add_action( 'wp_head', __NAMESPACE__ . '\add_global_css_vars' );
add_action( 'template_redirect', __NAMESPACE__ . '\redirect_search_results' );
add_filter( 'fareharbor/disabled', __NAMESPACE__ . '\disable_fareharbor' );
// add_action( 'init', __NAMESPACE__ . '\remove_fareharbor_script', 11 );
// add_action( 'wp_footer', __NAMESPACE__ . '\add_fareharbor_script' );
//add_action( 'wp', __NAMESPACE__ . '\testing' );

function testing() {
  $custom = get_post_custom( get_the_id() );
  //echo '<pre>';print_r( $custom );echo '</pre>';
}

/**
 * Add global css vars
 */
function add_global_css_vars() {
  $images_path = get_stylesheet_directory_uri() . '/assets/images/';
  echo "
  <style>:root {
    --img-hero-bottom: url('{$images_path}hero-bottom.png');
    --img-pixel: url('{$images_path}pixel.png');
    --img-slideshow-bottom: url('{$images_path}slideshow-bottom.png');
    --img-weatherbug-background: url('{$images_path}weatherbug-background.svg');
    --img-ui-white-right-arrow-default: url('{$images_path}UI-white-right-arrow-default.svg');
    --img-rt-arrow: url('{$images_path}icons/rt-arrow.svg');
    --img-portraitmask1: url('{$images_path}portraitmask1.svg');
    --img-portraitmask2: url('{$images_path}portraitmask2.svg');
    --img-portraitmask3: url('{$images_path}portraitmask3.svg');
    --img-irregular-mask: url('{$images_path}irregular-mask.svg');
    --img-large-image-mask: url('{$images_path}large-image-mask.svg');
    --img-section-top-rust: url('{$images_path}section-top-rust.png');
    --img-double-arrow: url('{$images_path}double-arrow.png');
    --img-ui-link-arrow: url('{$images_path}ui-link-arrow.png');
    --img-curve-arrow-big: url('{$images_path}curve-arrow-big.png');
    --img-ui-next-on: url('{$images_path}ui-next-on.png');
    --img-ui-next-off: url('{$images_path}ui-next-off.png');
    --img-ui-prev-on: url('{$images_path}ui-prev-on.png');
    --img-ui-prev-off: url('{$images_path}ui-prev-off.png');
    --img-section-top-lt-blue: url('{$images_path}section-top-lt-blue.png');
    --img-curve-arrow-lt-blue: url('{$images_path}curve-arrow-lt-blue.png');
    --img-section-top-turquoise: url('{$images_path}section-top-turquoise.png');
    --img-mobile-arrow-left: url('{$images_path}mobile-arrow-left.png');
    --img-mobile-arrow-right: url('{$images_path}mobile-arrow-right.png');
    --img-section-top-light-blue: url('{$images_path}section-top-light-blue.png');
    --img-beach-map: url('{$images_path}beach-map.png');
    --img-oedmap: url('{$images_path}oedmap.svg');
    --img-blue-arrow: url('{$images_path}blue-arrow.png');
    --img-arrow-teal: url('{$images_path}arrow-teal.png');
    --img-small-circ-down: url('{$images_path}small-circ-down.png');
    --img-small-arrow-down: url('{$images_path}small-arrow-down.png');
    --img-rigged-bottom: url('{$images_path}rigged-bottom.svg');
  }</style>";
}

/**
 * Force WP to use search.php for search results
 */
function redirect_search_results() {
  if ( is_search() ) {
    include( get_stylesheet_directory() . '/search.php' );
    exit;
  }
}

/**
 * Disable Fareharbor from loading on all pages except necessary pages
 */
function disable_fareharbor( $disable ) {
  if ( ! is_admin() && isset( $_SERVER['REQUEST_URI'] ) ) {
		$post_id = url_to_postid( esc_url_raw( home_url( $_SERVER['REQUEST_URI'] ) ) );
    if ( 7157 == $post_id ) {
      return false;
    }
	}
  return true;
}

function remove_fareharbor_script() {
  remove_action( 'wp_footer', array( 'fareharbor', 'lightframe_api_footer' ) );
}

function add_fareharbor_script() {
  $fareharbor = \fareharbor::get_instance();
  $test = $fareharbor::get_option( 'fh_auto_lightframe_enabled' );
  echo '<pre>';print_r( $test );echo '</pre>';
  // $src = 'https://' . self::url() . '/embeds/api/v1/';

  // if ( fareharbor::get_option( 'fh_auto_lightframe_enabled' ) )
  //   $src .= '?autolightframe=yes';

  // echo "<!-- FareHarbor plugin activated --><script src=\"$src\" defer></script>";
}
