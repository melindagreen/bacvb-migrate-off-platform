<?php

namespace MaddenNino\Blocks\BradensotaMap;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  $ferryStops = ['ami'=>'Anna Maria City Pier','bridgeStreet'=>'Historic Bridge Street Pier','bradentonRiverwalk'=>'Bradenton Riverwalk Pier'];
  $stylesheet_directory_uri = get_stylesheet_directory_uri();

  $html = "<div class='" . Constants::BLOCK_CLASS . "-bradensota-map'>";

    $html .= '<div class="mapContainer">';

    $html .= '<div class="zoomInfo">Click Images To Zoom</div><button id="resetZoom">Reset Map</buton></div>';

    $html .= '<div class="bradensota-map-content">';
      $html .= '<h2 class="bradensota-map-content__title"></h2>';
      $html .= '<hr>';
      $html .= '<h3 class="bradensota-map-content__location"></h3>';
      $html .= '<p class="bradensota-map-content__description"></p>';
    $html .= '</div>';
  $html .= "</div>";

  return $html;
}