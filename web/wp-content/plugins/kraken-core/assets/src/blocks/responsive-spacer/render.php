<?php
$desktop_height = ( isset( $attributes['heightDesktop'] ) && '' != $attributes['heightDesktop'] ) ? $attributes['heightDesktop'] : 0;
$tablet_height = ( isset( $attributes['heightTablet'] ) && '' != $attributes['heightTablet'] ) ? $attributes['heightTablet'] : 0;
$mobile_height = ( isset( $attributes['heightMobile'] ) && '' != $attributes['heightMobile'] ) ? $attributes['heightMobile'] : 0;

$block_attributes = get_block_wrapper_attributes([
  'style' => "--responsive-spacer--desktop:{$desktop_height};--responsive-spacer--tablet:{$tablet_height};--responsive-spacer--mobile:{$mobile_height};"
]);

echo '<div ' . $block_attributes . '></div>';