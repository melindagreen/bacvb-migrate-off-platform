<?php

namespace MaddenNino\Blocks\BeachesMap;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  $args = array(
      'post_type' => 'listing',
      'posts_per_page' => -1,
      'tax_query' => array(
          array(
              'taxonomy' => 'listing_categories',
              'field'    => 'slug',
              'terms'    => 'beaches',
          ),
      ),
  );
  $posts = get_posts($args);


  $html = "<div class='" . Constants::BLOCK_CLASS . "-beaches-map'>";

    // Map Illustrations
    $html .= '<div class="beaches-map">';

    // loop through all the beaches and display marker on map
    if (!empty($posts)) {
      foreach ($posts as $post) {
        // Remove content inside parentheses from the post title
        $shortenedTitle = preg_replace('/\s?\([^)]+\)/', '', $post->post_title);

        $html .= '<div class="location_pin" id="'.$post->post_name.'" data-id="'.$post->ID.'">';
        $html .= '<div class="beachName">'.$shortenedTitle.'</div>';
        $html .= '<img src="' . get_stylesheet_directory_uri() . '/assets/images/map-POI-marker.png" class="marker" alt="Marker icon">';
        $html .= '</div>';
      }
    }

    $html .= '</div>'; // .beaches-map

    $html .= '<div class="beach_content_overlay">';

      $html .= '<div class="beach_content">';

        $html .= '<div class="close">x</div>';

        if (!empty($posts)) {
          foreach ($posts as $post) {
          setup_postdata($post);

          $postMeta = get_post_meta($post->ID);

          // formatting beach name to pull in logo file name
          $shortenedTitle = preg_replace('/\s?\([^)]+\)/', '', $post->post_title);
          
          // to pull beach logo from assets folder
          $postSlug = str_replace(' ', '-', $shortenedTitle);
          
          // modifying slug to match the variable in inspector.js to pull beach content
          $beachContent = strtolower(str_replace(' ', '', $shortenedTitle));

          $html .= '<div class="selectBeach '.$post->post_name.'">';

            // featured image
            $featImg = get_stylesheet_directory_uri() . '/assets/images/20210520102150-anna-maria-island-beach-access.jpeg';
            if (!empty($attrs[$beachContent.'Image'])) {
              $featImg = $attrs[$beachContent.'Image'];
            }
            else if (has_post_thumbnail($post->ID)) {
              $featImg = get_the_post_thumbnail_url($post->ID, 'large');
            }

            $html .= '<div class="beachImg">';

            if($postMeta['partnerportal_beach-amenities']) {              
              $html .= '<div class="features">';
                $html .= '<div class="featuresTItle">Amenities</div>';

                foreach($postMeta['partnerportal_beach-amenities'] as $amenitySerialized) {
                    $amenityArray = unserialize($amenitySerialized);
                    foreach ($amenityArray as $singleAmenity) {
                      $html .= '<div class="tooltip" data-tooltip="'.ucwords(str_replace('-', ' ', $singleAmenity)).'"><img class="icon" src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/amenities/'.$singleAmenity.'.png" alt="'.$singleAmenity.' icon" /></div>';
                    }
                }

              $html .= '</div>'; // .features
            }

              $html .= '<img alt="beach image" class="img" src="'.$featImg.'">';

              $html .= '<div class="beachLogo"><img src="' . get_stylesheet_directory_uri() . '/assets/images/' . $postSlug . '_logo.png" alt="beach logo"></div>';

            $html .= '</div>'; // .beachImg

            $html .= '<div class="content">';

              $html .= '<h3>' . $post->post_title . '</h3>';

              if(!empty($attrs[$beachContent])) {
                $html .= '<p>'.$attrs[$beachContent].'</p>';
              } else {
                $html .= '<p>'.$postMeta['partnerportal_description'][0].'</p>';
              }

              // $html .= '<a href="'.get_permalink($post->ID).'" class="postLink">Dive In</a>';

            $html .= '</div>'; // .content

          $html .= '</div>'; // .selectBeach

          }

          // Restore original post data
          wp_reset_postdata();
      }

      $html .= '</div>';

    $html .= '</div>'; // .beach_content_overlay

  // $html .= "<pre>" . print_r( $attrs, true ) . "</pre>";

  $html .= "</div>";

  return $html;
}