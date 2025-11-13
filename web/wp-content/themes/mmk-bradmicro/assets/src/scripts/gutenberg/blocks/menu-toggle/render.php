<?php
$hamburger_type = $attributes['hamburgerType'] ?? 'hamburger--collapse';
$label = $attributes['label'] ?? '';
$target = $attributes['target'] ?? '';

$wrapper_atts = [];
if ( '' != $target ) {
  $wrapper_atts['data-target'] = $target;
}
?>
<div <?php echo get_block_wrapper_attributes( $wrapper_atts ); ?>>
	<a href="#" class="hamburger <?php echo esc_attr( $hamburger_type ); ?> hamburger--accessible js-hamburger">
		<span class="hamburger-box">
			<span class="hamburger-inner"></span>
		</span>
    <?php if ( '' != $label ) { ?>
		  <span class="hamburger-label"><?php echo esc_html( $label ); ?></span>
    <?php } ?>
  </a>
</div>
