<?php

namespace MaddenTheme\Blocks\SliderPagination;
  
/**
 * Render function for the slider controls block
 * @param array $attributes all block attributes
 * @param string $content     
 */

$attrs = $attributes;

$anchor = $attrs['anchor'] ?? '';  
$className = $attrs['className'] ?? '';
$blockId = $attrs['blockId'] ?? '';
$selectedSlider = $attrs['selectedSlider'] ?? 0;
$enablePagination = $attrs['enablePagination'] ?? true;
$enableArrows = $attrs['enableArrows'] ?? true;
$dotColor = $attrs['dotColor'] ?? '';
$dotColorActive = $attrs['dotColorActive'] ?? '';
$dotBorderColor = $attrs['dotBorderColor'] ?? '';
$arrowColor = $attrs['arrowColor'] ?? '';
$arrowBackgroundColor = $attrs['arrowBackgroundColor'] ?? '';
$arrowBorderColor = $attrs['arrowBorderColor'] ?? '';
$arrowStyle = $attrs['arrowStyle'] ?? 'default';

$classes = [
  'slider-controls-block',
  $className,
];

$wrapper_attributes = get_block_wrapper_attributes([
  'class' => implode(' ', $classes),
  'data-slider-number' => $selectedSlider,
  'data-block-id' => $blockId,
  'data-arrow-style' => $arrowStyle
]);

// Generate unique ID for CSS
$element_id = $anchor ? $anchor : 'slider-controls-' . $blockId;
?>

<div id="<?php echo $element_id; ?>" <?php echo $wrapper_attributes; ?>>
    <div class="external-swiper-controls">
        <?php if ($enableArrows) : ?>
          <div class="external-swiper-button-prev swiper-button-prev" data-color="<?php echo $arrowColor; ?>" data-color-background="<?php echo $arrowBackgroundColor; ?>" data-border-color="<?php echo $arrowBorderColor; ?>">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.18 28.91"><path data-name="Path 2" d="m14.45 0 3.73 3.73L7.46 14.45l10.72 10.72-3.73 3.73L0 14.45 14.45 0Z" fill="currenColor" /></svg>
          </div>
        <?php endif; ?>
        <?php if ($enablePagination) : ?>
        <div class="external-swiper-pagination swiper-pagination" data-color="<?php echo $dotColor; ?>" data-color-active="<?php echo $dotColorActive; ?>" data-border-color="<?php echo $dotBorderColor; ?>"></div>
        <?php endif; ?>
        <?php if ($enableArrows) : ?>
          <div class="external-swiper-button-next swiper-button-next" data-color="<?php echo $arrowColor; ?>" data-color-background="<?php echo $arrowBackgroundColor; ?>" data-border-color="<?php echo $arrowBorderColor; ?>">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.18 28.91"><path data-name="Path 2" d="M3.73 28.91 0 25.18l10.72-10.72L0 3.73 3.73 0l14.45 14.45L3.73 28.91Z" fill="currenColor" /></svg>
          </div>
        <?php endif; ?>
    </div>
    
    <?php if ($enablePagination) : ?>
    <style>
        #<?php echo $element_id; ?> .external-swiper-pagination .swiper-pagination-bullet {
            <?php if ($dotColor) : ?>
            background-color: <?php echo $dotColor; ?>;
            <?php endif; ?>
            <?php if ($dotBorderColor) : ?>
            border: 2px solid <?php echo $dotBorderColor; ?>;
            <?php endif; ?>
        }
        
        #<?php echo $element_id; ?> .external-swiper-pagination .swiper-pagination-bullet-active {
            <?php if ($dotColorActive) : ?>
            background-color: <?php echo $dotColorActive; ?>;
            <?php endif; ?>
        }
    </style>
    <?php endif; ?>
    
    <?php if ($enableArrows) : ?>
    <style>
        #<?php echo $element_id; ?> .external-swiper-button-prev,
        #<?php echo $element_id; ?> .external-swiper-button-next {
            <?php if ($arrowColor) : ?>
            color: <?php echo $arrowColor; ?>;
            <?php endif; ?>
            
            <?php if ($arrowBackgroundColor) : ?>
            background-color: <?php echo $arrowBackgroundColor; ?>;
            <?php endif; ?>
            
            <?php if ($arrowBorderColor) : ?>
            border: 2px solid <?php echo $arrowBorderColor; ?>;
            <?php endif; ?>
        }
        
        <?php if ($arrowStyle === 'modern') : ?>
        #<?php echo $element_id; ?> .external-swiper-button-prev::after,
        #<?php echo $element_id; ?> .external-swiper-button-next::after {
            content: none;
        }
        
        #<?php echo $element_id; ?> .arrow-svg {
            display: block;
        }
        <?php endif; ?>
    </style>
    <?php endif; ?>
</div>