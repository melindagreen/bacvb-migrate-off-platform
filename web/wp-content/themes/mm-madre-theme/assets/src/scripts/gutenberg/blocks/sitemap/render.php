<?php
namespace MaddenNino\Blocks\Sitemap;
use MaddenMadre\Library\Utilities as U;;

?>
<section class="madden-block-sitemap">
  <?php
  $mapData = array();
  foreach( $attributes['sitemapTypes'] as $post_type_slug => $post_type_label ) {
    if( !empty( $post_type_label ) ) {
      $mapData[$post_type_slug] = U::the_child_tree_list( $post_type_slug, $post_type_label );
    }
  }
  
  foreach ($mapData as $key => $val) {
    if (empty(strip_tags(trim($val)))) {
      unset($mapData[$key]);
    } else {
      $mapData[$key] = '<div class="sitemap-wrapper">' . $val . '</div>';
    }
  }

  ksort($mapData);
  echo  implode("", array_values($mapData));
  ?>
</section>