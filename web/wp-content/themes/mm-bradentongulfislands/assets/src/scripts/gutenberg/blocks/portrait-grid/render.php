<?php

namespace MaddenNino\Blocks\PortraitGrid;
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
  
      return $clean_post;
    }, $attrs['posts'] );
  } else if( $attrs['queryMode'] === 'persona' ) {
    $post_ids = Utilities::query_by_persona( $attrs['persona'] );
    
    $posts = array_map( function( $post_id ) {
      $post = array();

      // title
      $post['title'] = get_the_title( $post_id );
   
      // excerpt
      $post['excerpt'] = Utilities::excerpt_by_sentences( $post_id, 1, 50, 100 );
   
      // thumbnail
      $post['thumb'] = get_the_post_thumbnail_url( $post_id, 'full' );
      if( !$post['thumb'] ) $post['thumb'] = 'https://www.thepalmbeaches.com/wp-content/uploads/bg-book-your-stay-1.jpg';
   
      // link
      $post['link'] = get_the_permalink( $post_id );

      return $post;
    }, $post_ids );
  }

  ob_start();
  ?>
  <section class="<?php echo Constants::BLOCK_CLASS ?>-portrait-grid is-length-<?php echo count( $posts ); ?> <?php if($attrs['className']) { echo $attrs['className']; } ?>">
  <?php if($attr['blockTitle'] !== '') { ?>
  <h2 class="wp-block-heading has-text-align-center"><?php echo $attrs['blockTitle']; ?></h2>
  <?php } ?>
    <div class="grid-items">
      <?php foreach( $posts as $i => $post ) { ?>
        <article class="grid-item grid-item--<?php echo $i + 1 ?>">
          <?php if( $post['link'] ) { ?><a href="<?php echo $post['link'] ?>"><?php } ?>
            <div class="grid-item__background">
              <?php if( $post['thumb'] ) { ?>
                <img class="grid-item__image" src="<?php echo $post['thumb'] ?>" alt="">
              <?php } ?>
            </div>
          <?php if( $post['link'] ) { ?></a><?php } ?>
        </article>
        <div class="grid-item-body grid-item-body--<?php echo $i + 1 ?>">
            <h3 class="grid-item-body__title"><?php echo $post['title']; ?></h3>
            <p class="grid-item-body__excerpt"><?php echo $post['excerpt']; ?></p>
            <?php if( $post['link'] ) { ?><a class="grid-item-body__link" href="<?php echo $post['link'] ?>"><?php echo $post['ctaText'] ?></a> <?php } ?>
        </div>
      <?php } ?>
    </div>
  </section>
  <?php 

  return ob_get_clean();
}