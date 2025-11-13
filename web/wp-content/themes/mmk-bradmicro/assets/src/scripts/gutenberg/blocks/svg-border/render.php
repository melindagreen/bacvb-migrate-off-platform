<?php

namespace MaddenTheme\Blocks\SvgBorder;
use MaddenTheme\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content     
 */

$attrs = $attributes;

$secondary_color = isset( $attributes['secondaryColor'] ) ? $attributes['secondaryColor'] : false;
$min_width = isset( $attributes['minWidth'] ) ? $attributes['minWidth'] : false;

$classes = [];
if ($attrs['flipped']) {
	$classes[] = 'flip-svg';
}
if ($attrs['flippedVertical']) {
	$classes[] = 'flip-vertical-svg';
}

$custom_styles = '';
$custom_styles .= "--svg-border--color_secondary:var(--wp--preset--color--{$secondary_color});";
if ( $min_width ) {
  $custom_styles .= "--svg-border--min_width:{$min_width};";
}

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => implode(' ', $classes),
  'style' => $custom_styles
]);

if ( isset( $attrs['borderStyle'] ) && '' != $attrs['borderStyle'] ) {
  ?>
  <div <?php echo $wrapper_attributes; ?>>
    <?php 
    include('svgs/' . $attrs['borderStyle'] . '.php');
    ?>
  </div>
  <?php
}
