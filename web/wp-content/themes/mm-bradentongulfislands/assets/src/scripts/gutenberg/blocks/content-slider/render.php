<?php

namespace MaddenNino\Blocks\ContentSlider;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {
  $html = "<section class='mm-content-slider'>
    <div class='swiper-wrapper'>".$content."
        <div class='content-slider-controls'>
    <div class='content-slider-swiper-button-prev'><img src='".get_stylesheet_directory_uri()."/assets/images/prev.png' /></div>
    <div class='content-sliderswiper-pagination'></div>
    <div class='content-slider-swiper-button-next'><img src='".get_stylesheet_directory_uri()."/assets/images/next.png' /></div>
    </div>
    </div>
    <div class='temp-holder-pagination'></div>
  </section>";
  return $html;
}