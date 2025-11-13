<?php

namespace MaddenTheme\Blocks\BradentonMap;
use MaddenTheme\Library\Constants as Constants;
$attrs = $attributes;

$ferryStops = [
	'annaMariaPier',
	'bishopMuseum',
	'bradentonConventionCenter',
	'bradentonRiverwalk',
	'bridgeStFerryStop',
	'coquinaBeach',
	'cortez',
	'deSotoMemorial',
	'egmontKey',
	'ellentonOutlets',
	'gulfIslandsFerry',
	'herrigCenterArts',
	'imgAcademy',
	'lakeManateeStatePark',
	'lakewoodRanchMainSt',
	'lecomPark',
	'manateeBeach',
	'manateePerformingArtsCenter',
	'pineAvenue',
	'powelCrosleyEstate',
	'robinsonPreserve',
	'shoppesUniversityCenter',
	'srqAirport',
	'sunshineSkywayBridge',
	'tampaInternationalAirport',
	'villageOfArts',
];
$stylesheet_directory_uri = get_stylesheet_directory_uri();

$html = "<div class='" . Constants::BLOCK_CLASS . "-bradenton-meetings-map is-style-collage-square'>";

$html .= '<div class="mapContainer">';
$html .= '<div class="bradenton-meetings-map-help"><span class="bradenton-meetings-map-help__arrow"><</span> <p>Scroll Left & Right</p> <span class="bradenton-meetings-map-help__arrow">></span></div>';
$html .= '<div class="mapViewArea">';
$html .= '<img src="' . $stylesheet_directory_uri . '/assets/images/bradenton-meetings-map.png" alt="" />';
$html .= file_get_contents(get_stylesheet_directory() . '/assets/images/bradenton-meetings-map.svg');
$html .= '</div>'; // .mapViewArea
$html .= '</div>'; // .mapContainer

$html .= '<div class="bradenton-lightbox"><div class="close"><img height="25px" width="25px" src="' . get_stylesheet_directory_uri() . '/assets/images/icons/close-x.png" alt="close"></div>';

foreach($ferryStops as $stop){
  $cityTitle = $attrs["{$stop}Title"] ?? $stop;
  $cityDescription = $attrs["{$stop}Description"] ?? '';
  $cityUrl = $attrs["{$stop}Url"] ?? false;
  $ferryCardCta = isset($cityUrl) && $cityUrl !== '' ? '<div class="bradenton-card-cta"><a href="'. $cityUrl .'">Learn More About '. $cityTitle .'</a></div>' : '';

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
echo $html;
