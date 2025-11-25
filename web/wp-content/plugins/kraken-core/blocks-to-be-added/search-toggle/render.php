<?php
$searchEl = $attributes['searchTarget'];

$searchColor 		= $attributes['searchIconColor'];
$searchColorActive 	= $attributes['searchIconActiveColor'];

$styles = [
	$searchColor ? '--search-color: var(--wp--preset--color--'.$searchColor.');' : '',
	$searchColorActive ? '--search-color-active: var(--wp--preset--color--'.$searchColorActive.');' : ''
];

$classes = [
	'mobile-breakpoint-'.$attributes['mobileBreakpoint']
];

$wrapper_attributes = get_block_wrapper_attributes([
	'data-searchtarget' => $searchEl ? $searchEl : '',
	'class' => implode(' ', $classes),
	'style'	=> implode('', $styles)
]);
?>

<div <?php echo $wrapper_attributes; ?>>
	<button class="search-toggle" aria-controls="<?php echo $searchEl; ?>" aria-label="<?php echo $attributes['searchLabel'] ? $attributes['searchLabel'] : 'Toggle site search'; ?>" aria-expanded="false">
		<svg aria-hidden="true" focusable="false" class="search-icon" viewBox="0 0 24 24" width="24" height="24">
			<path d="M13 5c-3.3 0-6 2.7-6 6 0 1.4.5 2.7 1.3 3.7l-3.8 3.8 1.1 1.1 3.8-3.8c1 .8 2.3 1.3 3.7 1.3 3.3 0 6-2.7 6-6S16.3 5 13 5zm0 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z"></path>
		</svg>
	</button>
</div>
