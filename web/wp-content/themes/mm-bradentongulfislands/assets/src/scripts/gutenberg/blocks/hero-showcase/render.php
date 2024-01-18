<?php

namespace MaddenNino\Blocks\HeroShowcase;
use MaddenNino\Library\Constants as Constants;
use MaddenNino\Library\Utilities as Utilities;
  
/**
 * Render function for the Portrait Grid
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {
  $posts = array();

  // clean up posts attr
  if( $attrs['queryMode'] === 'manual' ) {
    $posts = array_map( function( $post ) {
      $clean_post = array();


      // post id
      $clean_post['id'] = $post['postObj']['id'];
  
      // title
      $clean_post['title'] = '';
      if( isset( $post['customTitle'] ) && !empty( $post['customTitle'] ) ) {
        $clean_post['title'] = $post['customTitle'];
      }
      else if( isset( $post['postObj']['id'] ) ) {
        $clean_post['title'] = get_the_title( $post['postObj']['id'] );
      }
  
      // excerpt
      $clean_post['excerpt'] = '';
      if( isset( $post['customExcerpt'] ) && !empty( $post['customExcerpt'] ) ) {
        $clean_post['excerpt'] = $post['customExcerpt'];
      }
      else if( isset( $post['postObj']['id'] ) ) {
        $clean_post['excerpt'] = Utilities::excerpt_by_sentences( $post['postObj']['id'], 1, 50, 100 );
      }

      // excerpt
      $clean_post['ctaText'] = '';
      if( isset( $post['customCTAText'] ) && !empty( $post['customCTAText'] ) ) {
        $clean_post['ctaText'] = $post['customCTAText'];
      }
      else if( isset( $post['postObj']['id'] ) ) {
        $clean_post['ctaText'] = 'Explore';
      }
  
      // thumbnail
      $clean_post['thumb'] = false;
      if( isset( $post['customThumb']['url'] ) && !empty( $post['customThumb']['url'] ) ) {
        $clean_post['thumb'] = $post['customThumb']['url'];
      }
      else if( isset( $post['postObj']['id'] ) && has_post_thumbnail( $post['postObj']['id'] ) ) {
        $clean_post['thumb'] = get_the_post_thumbnail_url( $post['postObj']['id'], 'full' );
      }
  
      // link
      $post['link'] = false;
      if( isset( $post['postObj']['url'] ) ) $clean_post['link'] = $post['postObj']['url'];

      // Factoid
      $clean_post['factThumb'] = $post['factThumb']['url'];
      $clean_post['factTitle'] = $post['factTitle'];
      $clean_post['factDescription'] = $post['factDescription'];
  
      return $clean_post;
    }, $attrs['posts'] );
  } 

  ob_start();
  ?>
  <section class="<?php echo Constants::BLOCK_CLASS ?>-hero-showcase is-length-<?php echo count( $posts ); ?> <?php if($attrs['className']) { echo $attrs['className']; } ?>">
  <div class="hero-showcase-legend">
    <h2 class="legendTitle">Beach Amenities Legend</h2>
  <div class="check-controls">
    <div class="control control--categories">
    <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/fishing.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Fishing</span>
        </label>
    <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/sightseeing.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Sightseeing</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/sunbathing.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Sunbathing</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/shelling.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Shelling</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/swimming.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Swimming</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/playground.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Playground</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/bathrooms.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Bathrooms</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/changing-station.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Changing Station</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/lifeguard.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Lifeguard</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/wheelchair-accessible.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Wheelchair Accessible</span>
        </label>
        <label class='control__label control__label--categories control__label--accomodations'>
        <input
          class='control__input control__input--categories control__input--checkbox'
           type='checkbox'
             name="accomodations-facility-amenities"
           />
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/amenities/cafe-food.png" class="icon" alt="Fishing icon">
          <span class='control__text'>Cafe/Food</span>
        </label>
    </div>
    
  </div>
</div>
    <div class="hero-showcases">
      <?php foreach( $posts as $i => $post ) { 
          $postMeta = get_post_meta($post['id']);
            // formatting beach name to pull in logo file name
            $shortenedTitle = preg_replace('/\s?\([^)]+\)/', '', $post['title']);
            $postSlug = str_replace(' ', '-', $shortenedTitle);
            // Bean point beach uses Bradenton-Beach logo
            $postSlug = str_replace('Bean-Point', 'Bradenton-Beach', $postSlug);
        ?>
        
        <article class="hero-showcase hero-showcase--<?php echo $i + 1 ?>">
            <div class="beachLogo"><img src="/wp-content/uploads/<?php echo $postSlug; ?>_logo.png" alt="beach logo"></div>
            <div class="hero-showcase__background is-style-collage-square">
              <?php if( $post['thumb'] ) { ?>
                <img class="hero-showcase__image" src="<?php echo $post['thumb'] ?>" alt="">
              <?php } ?>
            </div>
            <?php if($postMeta['partnerportal_beach-amenities']) {  ?>         
              <div class="hero-showcase-amenities <?php echo $i % 2 === 0 ?  'hero-showcase--topright' : 'hero-showcase--bottomleft'; ?>">
                <div class="amenitiesTitle">Amenities</div>

               <?php foreach($postMeta['partnerportal_beach-amenities'] as $amenitySerialized) {
                    $amenityArray = unserialize($amenitySerialized);
                    foreach ($amenityArray as $singleAmenity) { ?>
                      <div class="tooltip" data-tooltip="<?php echo ucwords(str_replace('-', ' ', $singleAmenity)); ?>"><img class="icon" src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/amenities/<?php echo $singleAmenity; ?>.png" alt="<?php echo $singleAmenity; ?> icon" /></div>
                 <?php   }
                } ?>

              </div>
          <?php  } ?>
            <div class="hero-showcase-body hero-showcase-body--<?php echo $i + 1 ?> <?php echo $i % 2 === 0 ? 'hero-showcase--bottomleft' : 'hero-showcase--topright'; ?> is-style-collage-square">
            <h3 class="hero-showcase-body__title"><?php echo $post['title']; ?></h3>
            <p class="hero-showcase-body__excerpt"><?php echo $post['excerpt']; ?></p>
            <?php if( $post['link'] ) { ?><a class="hero-showcase-body__link" href="<?php echo $post['link']; ?>"><?php echo $post['ctaText']; ?></a> <?php } ?>
          </div>
          <?php if($post['factThumb']) { ?>
          <div class="hero-showcase-factoid">
            <div class="factoid-img"><img src="<?php echo $post['factThumb']; ?>" alt=""></div>
            <div class="factoid-content is-style-irregular-shape">
              <h2><?php echo $post['factTitle']; ?></h2>
              <p><?php echo $post['factDescription']; ?></p>
            </div>
          </div>
          <?php } ?>
        </article>
      <?php } ?>
    </div>
  </section>
  <?php 

  return ob_get_clean();
}