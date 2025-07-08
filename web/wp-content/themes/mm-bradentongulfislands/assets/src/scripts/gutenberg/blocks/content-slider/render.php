<?php

namespace MaddenNino\Blocks\ContentSlider;
use MaddenNino\Library\Constants as Constants;
  
$attrs = $attributes;

$html = "<section class='mm-content-slider'>
  <div class='swiper-wrapper'>".$content."</div>
  <div class='temp-holder-pagination'></div>
  <div class='content-slider-controls'>
  <div class='content-slider-swiper-button-prev'><img src='".get_stylesheet_directory_uri()."/assets/images/prev.png' /></div>
  <div class='content-sliderswiper-pagination'></div>
  <div class='content-slider-swiper-button-next'><img src='".get_stylesheet_directory_uri()."/assets/images/next.png' /></div>
  </div>
</section>";
echo $html;