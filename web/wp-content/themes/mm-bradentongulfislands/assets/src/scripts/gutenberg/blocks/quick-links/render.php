<?php

namespace MaddenNino\Blocks\PortraitGrid;
use MaddenNino\Library\Constants as Constants;
use MaddenNino\Library\Utilities as Utilities;
  
/**
 * Render function for the Quick Links
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {
  $posts = array();

  ob_start();
  ?>
  <section class="<?php echo Constants::BLOCK_CLASS ?>-quick-links <?php if($attrs['className']) { echo $attrs['className']; } ?>">
  <?php if($attr['blockTitle'] !== '') { ?>
  <h2 class="wp-block-heading has-text-align-center"><?php echo $attrs['blockTitle']; ?></h2>
  <?php } ?>
    <div class="grid-items">
      <?php foreach( $posts as $i => $post ) { ?>
        <div class="grid-item-body grid-item-body--<?php echo $i + 1 ?>">
            <h3 class="grid-item-body__title"><?php echo $post['title']; ?></h3>
            <p class="grid-item-body__excerpt"><?php echo $post['excerpt']; ?></p>
            <?php if( $post['link'] ) { ?><a class="grid-item-body__link" href="<?php echo $post['link'] ?>"><?php echo $post['ctaText'] ?></a> <?php } ?>
            <div class="grid-item-body__arrow"></div>
        </div>
        <article class="grid-item grid-item--<?php echo $i + 1 ?>">
          <?php if( $post['link'] ) { ?><a href="<?php echo $post['link'] ?>"><?php } ?>
            <div class="grid-item__background">
              <?php if( $post['thumb'] ) { ?>
                <img class="grid-item__image" src="<?php echo $post['thumb'] ?>" alt="">
              <?php } ?>
            </div>
          <?php if( $post['link'] ) { ?></a><?php } ?>
        </article>
      <?php } ?>
    </div>
  </section>
  <?php 

  return ob_get_clean();
}