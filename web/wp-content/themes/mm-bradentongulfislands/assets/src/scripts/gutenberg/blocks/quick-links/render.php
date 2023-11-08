<?php

namespace MaddenNino\Blocks\QuickLinks;
use MaddenNino\Library\Constants as Constants;
use MaddenNino\Library\Utilities as Utilities;
  
/**
 * Render function for the Quick Links
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  ob_start();
  ?>
  <section class="<?php echo Constants::BLOCK_CLASS ?>-quick-links <?php if($attrs['className']) { echo $attrs['className']; } ?>">
  <?php if($attr['blockTitle'] !== '') { ?>
  <h2 class="wp-block-heading has-text-align-center quick-links-heading"><?php echo $attrs['blockTitle']; ?></h2>
  <?php } ?>
    <div class="quick-link-items">
      <?php foreach( $attrs['quickLinks'] as $i => $link ) { ?>
        <?php if( $link['linkObj']['url'] ) { ?><a href="<?php echo $link['linkObj']['url'] ?>"><?php } ?>
        <article class="quick-link-item quick-link-item--<?php echo $i + 1 ?>"> <?php
                  if( isset( $link['customTitle'] ) && !empty( $link['customTitle'] ) ) {
                echo $link['customTitle'];
              }
              else if( isset( $link['linkObj']['id'] ) ) {
                echo get_the_title( $link['linkObj']['id'] );
              }
       ?> </article> <?php
        if( $link['link'] ) { ?></a><?php } ?>
      <?php } ?>
    </div>
  </section>
  <?php 

  return ob_get_clean();
}