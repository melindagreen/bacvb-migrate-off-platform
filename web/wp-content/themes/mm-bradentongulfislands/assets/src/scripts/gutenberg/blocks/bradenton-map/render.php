<?php

namespace MaddenNino\Blocks\BradentonMap;
use MaddenNino\Library\Constants as Constants;

/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  $ferryStops = [
    'PINE_AVENUE'=> 'Pine Avenue',
    'BRIDGE_STREET_FERRY_STOP' => 'Bridge Street Ferry Stop',
    'LAKEWOOD_RANCH' => 'Lakewood Ranch',
    'LAKE_MANATEE_STATE_PARK'=> 'Lake Manatee State Park',
    'ANNA_MARIE_ISLAND'=> 'Anna Maria Island',
    'LONGBOAT_KEY'=> 'Longboat Key',
    'ROBINSON_PRESERVE' => 'Robinson Preserve',
    'CORTEZ'=> 'Cortez',
    'DESOTO' => 'DeSoto National Memorial',
    'BISHOP_MUSEUM'=> 'Bishop Museum',
    'BRADENTON_RIVER_WALK' => 'Bradenton Riverwalk',
    'ANNA_MARIE_CITY_PIER'=> 'Anna Maria City Pier',
    'GULF_ISLANDS_FERRY'=> 'Gulf Islands Ferry',
    'HERRIG_CENTER'=> 'Herrig Center',
    'MANATEE_PERFORMING_ARTS'=> 'Manatee Performing Arts',
    'VILLAGE_OF_THE_ARTS'=> 'Village of the Arts'
  ];
  $stylesheet_directory_uri = get_stylesheet_directory_uri();

  $html = "<div class='" . Constants::BLOCK_CLASS . "-bradenton-map is-style-collage-square'>";

  $html .= '<div class="mapContainer">';
  $html .= '<div class="mapViewArea">';
  $html .= '<img src="' . $stylesheet_directory_uri . '/assets/images/bradenton-map.png" alt="" />';
  $html .= file_get_contents(get_stylesheet_directory() . '/assets/images/bradenton-map.svg');
  $html .= '</div>'; // .mapViewArea
  $html .= '</div>'; // .mapContainer

  $html .= '<div class="bradenton-lightbox"><div class="close"><img height="25px" width="25px" src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/close-x.png" alt="close"></div>';

  foreach($ferryStops as $stop => $stop_name){
    $cityTitle = $stop_name;
    $cityDescription = $attrs[$stop.'Description'];
    $cityUrl = $attrs[$stop.'Url'];
    $ferryCardCta = isset($cityUrl) && $cityUrl !== '' ? '<div class="bradenton-card-cta"><a href="'. $cityUrl .'">Learn More About '. $stop_name .'</a></div>' : '';

    $html .= <<<HTML
    <div class="bradenton-card {$stop}" data-city="{$stop}">
        <div class="bradenton-card__content">
          <h3>{$cityTitle}</h3>
          <p>{$cityDescription}</p>
          $ferryCardCta
        </div>
    </div>
    HTML;
  }
  $html .= '</div>'; // .bradenton-lightbox

  $html .= "</div>"; // main wrapper
  return $html;
}