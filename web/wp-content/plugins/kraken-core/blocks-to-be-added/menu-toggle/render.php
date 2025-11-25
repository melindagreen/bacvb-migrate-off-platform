<?php
$targetEl = $attributes['menuTarget'];

$menuColor 			= $attributes['menuToggleColor'];
$menuColorActive 	= $attributes['menuToggleActiveColor'];

$styles = [
	$menuColor ? '--menu-color: var(--wp--preset--color--'.$menuColor.');' : '',
	$menuColorActive ? '--menu-color-active: var(--wp--preset--color--'.$menuColorActive.');' : ''
];

$classes = [
	'mobile-breakpoint-'.$attributes['mobileBreakpoint']
];

$wrapper_attributes = get_block_wrapper_attributes([
	'data-menutarget' 	=> $targetEl ? $targetEl : '',
	'class' => implode(' ', $classes),
	'style'	=> implode('', $styles)
]);
?>
<div <?php echo $wrapper_attributes; ?>>
	<button class="mobile-menu-toggle" aria-controls="<?php echo $targetEl; ?>" aria-label="<?php echo $attributes['menuLabel'] ? $attributes['menuLabel'] : 'Toggle navigation'; ?>" aria-expanded="false">
		<span class="mobile-menu-toggle__icon"></span>
	</button>
</div>
