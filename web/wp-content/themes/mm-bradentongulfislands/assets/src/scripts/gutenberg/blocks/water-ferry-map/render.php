<?php

namespace MaddenNino\Blocks\WaterFerryMap;
use MaddenNino\Library\Constants as Constants;
  
$attrs = $attributes;

$ferryStops = ['ami'=>'Anna Maria City Pier','bridgeStreet'=>'Historic Bridge Street Pier','bradentonRiverwalk'=>'Bradenton Riverwalk Pier'];
$stylesheet_directory_uri = get_stylesheet_directory_uri();

$html = "<div class='" . Constants::BLOCK_CLASS . "-water-ferry-map is-style-collage-square'>";

  $html .= '<div class="mapContainer">';

    ob_start();
    include get_stylesheet_directory() . '/assets/images/water-ferry-map.svg';
    $svg_content = ob_get_clean();
    $html .= $svg_content;

  $html .= '</div>';
  $html .= '<div class="ferry-map-help"><h4>Interactive Map</h4><p>Click Location to Book Your Trip</p></div>'; 
$html .= "</div>";

$html .= '<div class="ferry-stop-lightbox"><div class="close"><img height="25px" width="25px" src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/close-x.png" alt="close" loading="lazy"></div>';
    
      foreach($ferryStops as $stop => $stop_name){

        $cityImage = $attrs[$stop . 'MediaId'] ? wp_get_attachment_image_src($attrs[$stop . 'MediaId'], 'large')[0] : $stylesheet_directory_uri . '/assets/images/placeholder.jpg';
        $cityImageAlt = $attrs[$stop . 'MediaId'] ? get_post_meta($attrs[$stop . 'MediaId'], '_wp_attachment_image_alt', true) : 'Placeholder';
        $cityTitle = $stop_name;
        $cityDescription = $attrs[$stop.'Description'];
        $cityUrl = $attrs[$stop.'Url'];
        $ferryCardCta = isset($cityUrl) && $cityUrl !== '' ? '<div class="ferry-card-cta"><a href="'. $cityUrl .'">Book Now at '. $stop_name .'</a></div>' : '';

        $html .= <<<HTML
        <div class="ferry-stop-card is-style-collage-square {$stop}" data-city="{$stop}">
            <div class="ferry-stop-card__content">
              <h3>{$cityTitle}</h3>
              <p>{$cityDescription}</p>
              $ferryCardCta
            </div>
            <img 
                data-load-type="img" 
                data-load-onload="true" 
                data-load-alt="{$cityImageAlt}" 
                data-load-all="{$cityImage}" 
                src="{$stylesheet_directory_uri}/assets/images/pixel.png" 
                alt="placeholder pixel" 
                loading="lazy" 
                width="400" 
                height="300" 
            />
        </div>
        HTML;
      }

    $html .= '</div>';
echo $html;