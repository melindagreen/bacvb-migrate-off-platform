<?php

namespace MaddenNino\Blocks\PortraitGrid;
use MaddenNino\Library\Constants as Constants;
use MaddenNino\Library\Utilities as Utilities;
  
$attrs = $attributes;

$posts = array();

// clean up posts attr
if( $attrs['queryMode'] === 'manual' ) {
  $posts = array_map( function( $post ) {
    $clean_post = array();

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
      $clean_post['thumb'] = get_the_post_thumbnail_url( $post['postObj']['id'], 'madden_hero_md' );
    }

    // link
    $clean_post['link'] = false;
    if( isset( $post['postObj']['url'] ) ) $clean_post['link'] = $post['postObj']['url'];

    return $clean_post;
  }, $attrs['posts'] );
} 
?>
<section class="<?php echo Constants::BLOCK_CLASS ?>-portrait-grid is-length-<?php echo count( $posts ); ?> <?php if($attrs['className']) { echo $attrs['className']; } ?>">
    <img class="nautical-illustration nautical-illustration--oyster" src="<?php echo get_theme_file_uri()?>/assets/images/oyster.png" alt="Oyster Illustration">
    <img class="nautical-illustration nautical-illustration--crab" src="<?php echo get_theme_file_uri()?>/assets/images/crab.png" alt="Crab Illustration">
    <img class="nautical-illustration nautical-illustration--starfish" src="<?php echo get_theme_file_uri()?>/assets/images/starfish.png" alt="Star Fish Illustration">
  <div class="grid-items">
    <?php foreach( $posts as $i => $post ) { ?>
      <article class="grid-item grid-item--<?php echo $i + 1 ?>">
        <?php if( $post['link'] ) { ?><a href="<?php echo $post['link'] ?>"><?php } ?>
          <div class="grid-item__background">
            <?php if( $post['thumb'] ) { ?>
              <img class="grid-item__image" src="<?php echo $post['thumb'] ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/pixel.png"
              data-load-type="img"
              data-load-offset="lg"
              data-load-lg="<?php echo $post['thumb'] ?>" alt="">
            <?php } ?>
          </div>
        <?php if( $post['link'] ) { ?></a><?php } ?>
      </article>
      <div class="grid-item-body grid-item-body--<?php echo $i + 1 ?>">
          <h3 class="grid-item-body__title"><?php echo $post['title']; ?></h3>
          <p class="grid-item-body__excerpt"><?php echo $post['excerpt']; ?></p>
          <?php if( $post['link'] ) { ?><a class="grid-item-body__link" href="<?php echo $post['link'] ?>"><?php echo $post['ctaText'] ?></a> <?php } ?>
          <div class="grid-item-body__arrow"></div>
      </div>
    <?php } ?>
  </div>
</section>