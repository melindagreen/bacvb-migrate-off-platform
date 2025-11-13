<?php
$menus = isset( $attributes['menus'] ) ? $attributes['menus'] : [];

// Don't display the mega menu link if there is no label or no menu slug.
if ( empty( $menus ) ) {
	return null;	
}
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
  <?php
  if ( is_array( $menus ) && count( $menus ) > 0 ) {
    foreach ( $menus as $menu_slug ) {
      echo "<div class='wp-block-madden-theme-mega-menu wp-block-madden-theme-mega-menu--{$menu_slug}' data-id='{$menu_slug}'>";
        echo block_template_part( $menu_slug );
      echo '</div>';
    }
  }
  ?>
</div>
