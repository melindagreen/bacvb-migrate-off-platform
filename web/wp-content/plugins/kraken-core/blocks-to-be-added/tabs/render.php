<?php
namespace MaddenTheme\Blocks\Tabs;
  
/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content     
 */

$attrs = $attributes;
$anchor = $attrs['anchor'] ?? uniqid('madden-theme-tabs-');  
$className = $attrs['className'] ?? '';

$classes = [
  $className
];

$wrapper_args = [
    'id'	=> $anchor,
    'class' => implode(' ', $classes)
];

//Generate inline styles
$color_styles = '';
$color_attributes = ["tabBackgroundColor", "tabTextColor", "tabBackgroundHoverColor", "tabBackgroundActiveColor", "tabTextHoverColor", "tabTextActiveColor"];

foreach ($color_attributes as $attr) {
  if (!empty($attrs[$attr])) {
      $css_var = '--' . strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $attr));
      $color_styles .= "{$css_var}: var(--wp--preset--color--{$attrs[$attr]});";
  }
}

if (!empty($color_styles)) {
    $style_rules = "body #{$anchor} { {$color_styles} }";
    wp_add_inline_style('madden-theme-tabs-style', $style_rules);
}

$wrapper_attributes = get_block_wrapper_attributes($wrapper_args);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="tabs-nav-placeholder"></div>
	<div class="tabs-content">
        <?php echo $content; ?>
    </div>
</section>