# Content Card

This is a customizable card block that can be used to display any type of content.

## Card Styles
Each card style should be added in a separate stylesheet in the styles folder.

## Post Types
This block will pull in all available post types.

## Using with Slider
If using the "slider" block in automatic or manual post mode, this block including content options and styles will be imported there.

## Using with Search & Filter 
If using the "Search & Filter" block; this card block will be used for the grid items.

## Block Supports
Background and text color block supports are available. Background image support can be used as well, but needs to be added to the render file.

## Kraken Events
Uncomment line 7:
```include_once('inc/kraken-events.php';``` in render.php to use the Kraken Events functions for date & time. Supports recurring event dates. Markup can be customized however needed.