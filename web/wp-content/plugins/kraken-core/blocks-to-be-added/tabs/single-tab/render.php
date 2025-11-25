<?php
namespace MaddenTheme\Blocks\SingleTab;
  
/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content     
 */

$attrs      = $attributes;
$className  = $attrs['className'] ?? '';

$classes = [
  $className
];

$wrapper_args = [
    'data-title' => $attributes['title'] ?? '',
    'class' => implode(' ', $classes)
];

// Generate inline styles for colors
$color_styles = '';
$color_attributes = [
    "tabBackgroundColor", "tabTextColor", "tabBackgroundHoverColor",
    "tabTextHoverColor", "tabBackgroundActiveColor", "tabTextActiveColor"
];

foreach ($color_attributes as $attr) {
    if (!empty($attrs[$attr])) {
        // Convert camelCase to kebab-case for CSS custom property
        $css_var = '--' . strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $attr));
        $color_styles .= "{$css_var}: var(--wp--preset--color--{$attrs[$attr]});";
    }
}

if (!empty($color_styles)) {
    $wrapper_args['data-custom-styles'] = $color_styles;
}

$wrapper_attributes = get_block_wrapper_attributes($wrapper_args);
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>