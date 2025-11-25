<?php

namespace MaddenTheme\Blocks\SvgBorder;
use MaddenTheme\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content     
 */

$attrs = $attributes;

$anchor 			= $attrs['anchor'] ?? '';
$className 			= $attrs['className'] ?? '';
$primaryColor 		= $attrs['primaryColor'];
$secondaryColor 	= $attrs['secondaryColor'];
$tertiaryColor 		= $attrs['tertiaryColor'];

$classes 	= [];
$styles		= []; 

if ($attrs['flipY']) {
	$classes[] = 'flip-y';
}

if ($attrs['flipX']) {
	$classes[] = 'flip-x';
}

if ($attrs['positionZIndex']) {
	$styles[] 	= 'z-index: '.$attrs['positionZIndex'].';';
}

$wrapper_attributes = get_block_wrapper_attributes([
	'id'	=> $anchor,
	'class' => implode(' ', $classes),
	'style'	=> implode(';', $styles)
]);

//Add all svg colors here & use them as needed in the svg files
$svgColors = [
	'primary' 	=> $primaryColor ? 'var(--wp--preset--color--'.$primaryColor.')' : '',
	'secondary' => $secondaryColor ? 'var(--wp--preset--color--'.$secondaryColor.')' : '',
	'tertiary' => $tertiaryColor ? 'var(--wp--preset--color--'.$tertiaryColor.')' : ''
];
?>

<div <?php echo $wrapper_attributes; ?>>
	<?php 
	switch ($attrs['borderStyle']) {
		case "mountain-range":			
			include('svgs/mountain-range.php');
			break;
		case "small-waves":			
			include('svgs/small-waves.php');
			break;
	}
	?>
</div>
