<?php

namespace MaddenNino\Blocks\QuickLinks;
use MaddenNino\Library\Constants as Constants;
use MaddenNino\Library\Utilities as Utilities;
  
$attrs = $attributes ?? [];
?>
<section class="<?php echo Constants::BLOCK_CLASS ?>-quick-links <?php if(isset($attrs['className'])) { echo $attrs['className']; } ?> <?php if($attrs['hideOnMobile']) { echo Constants::BLOCK_CLASS . '-quick-links--hideonmobile'; } ?>">
<?php if($attrs['blockTitle'] !== '') { ?>
<h2 class="wp-block-heading has-text-align-center quick-links-heading"><?php echo $attrs['blockTitle']; ?></h2>
<?php } ?>
  <div class="quick-link-items">
    <?php foreach( $attrs['quickLinks'] as $i => $link ) { ?>
      <?php if( isset($link['linkObj']['url']) ) { ?><a class="quick-link-a" href="<?php echo $link['linkObj']['url'] ?>"><?php } ?>
      <article style="background-color:<?php echo $attrs['color']; ?>" class="quick-link-item quick-link-item--<?php echo $i + 1 ?>"> <?php
                if( isset( $link['customTitle'] ) && !empty( $link['customTitle'] ) ) {
              echo $link['customTitle'];
            }
            else if( isset( $link['linkObj']['id'] ) ) {
              echo get_the_title( $link['linkObj']['id'] );
            }
      ?> </article> <?php
      if( isset($link['linkObj']['url']) ) { ?></a><?php } ?>
    <?php } ?>
  </div>
</section>