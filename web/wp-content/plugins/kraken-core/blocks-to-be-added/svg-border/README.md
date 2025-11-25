# SVG Border Block

This block is for SVG borders with color and margin support.

## Features
- Add multiple SVGs
- Flip the SVG by either axis or both
- Multi-color SVG support
- Background color support
- Margin support because you probably need negative margins
- Z Index support

## Set-up Instructions
May need to prep your SVG to crop it correctly and remove any styles or colors in the SVG itself so the block colors can be used as the fill.

Add your svgs to the svgs folder, update render.php with your svg names, and update inspector.js borderStyle field with the new svg options.

Assuming your SVG is purely decorative, please add aria-hidden="true" and focusable="false" to your SVG for accessibility purposes