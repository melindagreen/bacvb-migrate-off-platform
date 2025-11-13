# Gutenberg Documentation

## Block Overrides
Override a core block render callback function to modify the block's html. The PHP for this is in library/blocks.php

## Block Styles
This folder is for adding, removing, and modifying block styles. This will add a single class to the block for styling; only one block style can be selected at a time. [Block Styles](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/)

## Blocks
Add your custom blocks here.

## Components
Reusable React components that can be used in custom blocks.

## Filters
Modify core blocks with custom functionality. This can be used to add multiple classes, attributes, and styles at the same time. [Block Filters](https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/)

## Formats
Formats allow you to add custom inline styles. [Block Formats](https://developer.wordpress.org/block-editor/how-to-guides/format-api/)

## Variations
Block variations can be used to create a new variant of an existing block with different attributes or inner blocks applied by default. [Block Variations](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/)

## Dispatches
Core editor dispatches can be used to modify the block editor. [Core Editor Actions](https://developer.wordpress.org/block-editor/reference-guides/data/data-core-editor/#actions)