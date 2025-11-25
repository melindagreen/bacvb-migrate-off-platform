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
        <?php if ($enablePagination) : ?>
        <div class="external-swiper-pagination swiper-pagination" data-color="<?php echo $dotColor; ?>" data-color-active="<?php echo $dotColorActive; ?>" data-border-color="<?php echo $dotBorderColor; ?>"></div>
        <?php endif; ?>
        
        <?php if ($enableArrows) : ?>
        <div class="external-swiper-nav-wrapper">
            <div class="external-swiper-button-prev swiper-button-prev" data-color="<?php echo $arrowColor; ?>" data-color-background="<?php echo $arrowBackgroundColor; ?>" data-border-color="<?php echo $arrowBorderColor; ?>">
                <?php if ($arrowStyle === 'modern') : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none" class="arrow-svg arrow-prev">
                        <path d="M10.553 3.05273L3.50226 10.1035L10.553 17.1542" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.50226 10.1035L18.5022 10.1035" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?php endif; ?>
            </div>
            <div class="external-swiper-button-next swiper-button-next" data-color="<?php echo $arrowColor; ?>" data-color-background="<?php echo $arrowBackgroundColor; ?>" data-border-color="<?php echo $arrowBorderColor; ?>">
                <?php if ($arrowStyle === 'modern') : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none" class="arrow-svg arrow-next">
                        <path d="M10.553 3.05273L17.6037 10.1035L10.553 17.1542" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.6037 10.1035L2.60376 10.1035" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?php endif; ?>
            </div>
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
            border: 1px solid <?php echo $dotBorderColor; ?>;
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
            border: 1px solid <?php echo $arrowBorderColor; ?>;
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