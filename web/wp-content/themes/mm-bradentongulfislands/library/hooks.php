<?php

namespace MaddenNino\Library\Hooks;

add_action( 'wp_head', __NAMESPACE__ . '\add_global_css_vars' );

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
