# Block Filters Reference

_Last Updated: October 2025_

## Overview

Block filters are JavaScript-based modifications that extend WordPress blocks with additional functionality. Kraken Core includes a comprehensive set of preset filters that can be enabled/disabled per block type through the plugin settings.

## Available Preset Filters

### Layout & Positioning

#### Content Width Settings

- **Filter**: `content-width-settings`
- **Description**: Adds controls to restrict block width with custom or default max-width
- **Attributes Added**:
  - `enableMaxWidth` (boolean) - Enable width restriction
  - `defaultMaxWidth` (boolean) - Use default max width (80rem)
  - `customMaxWidth` (number) - Custom max width in rem units
- **CSS Classes**: `main-column-auto`
- **Use Case**: Control content width for better readability

#### Position Absolute

- **Filter**: `position-absolute`
- **Description**: Enables absolute positioning with responsive controls
- **Attributes Added**:
  - `positionAbsolute` (boolean) - Enable absolute positioning
  - `positionTop`, `positionBottom`, `positionLeft`, `positionRight` (string) - Desktop positions
  - `positionTopTablet`, `positionBottomTablet`, `positionLeftTablet`, `positionRightTablet` (string) - Tablet positions
  - `positionTopMobile`, `positionBottomMobile`, `positionLeftMobile`, `positionRightMobile` (string) - Mobile positions
- **CSS Classes**: `has-position-absolute`
- **Use Case**: Overlay elements, floating content

#### Z-Index Control

- **Filter**: `z-index`
- **Description**: Adds z-index control for layering elements
- **Attributes Added**:
  - `zIndex` (number) - Z-index value (-100 to 100)
- **CSS Classes**: `has-z-index`
- **CSS Variables**: `--z-index`
- **Use Case**: Control element stacking order

### Responsive Display

#### Responsive Display

- **Filter**: `responsive-display`
- **Description**: Hide/show elements at different breakpoints
- **Attributes Added**:
  - `responsiveDisplay` (boolean) - Enable responsive display options
  - `hideOnMobile` (boolean) - Hide on mobile devices
  - `hideOnTablet` (boolean) - Hide on tablet devices
  - `hideOnDesktop` (boolean) - Hide on desktop devices
- **CSS Classes**: `responsive-display`, `hide-on-mobile`, `hide-on-tablet`, `hide-on-desktop`
- **Use Case**: Show/hide content based on screen size

#### Align Full on Mobile

- **Filter**: `alignfull-on-mobile`
- **Description**: Makes blocks full-width on mobile devices
- **Attributes Added**:
  - `alignfullOnMobile` (boolean) - Enable full width on mobile
- **CSS Classes**: `alignfull-on-mobile`
- **Use Case**: Mobile-optimized layouts

#### Center on Mobile

- **Filter**: `center-on-mobile`
- **Description**: Centers content on mobile devices
- **Attributes Added**:
  - `centerOnMobile` (boolean) - Center content on mobile
- **CSS Classes**: `center-on-mobile`
- **Use Case**: Mobile text alignment

#### Reverse Order

- **Filter**: `reverse-order`
- **Description**: Reverses element order on mobile
- **Attributes Added**:
  - `reverseOrder` (boolean) - Reverse order on mobile
- **CSS Classes**: `reversed-on-mobile`
- **Use Case**: Mobile layout adjustments

### Grid & Layout

#### Responsive Grid Columns

- **Filter**: `responsive-grid-columns`
- **Description**: Controls grid column count across breakpoints
- **Attributes Added**:
  - `enableResponsiveGridCols` (boolean) - Enable responsive grid
  - `tabletGridCols` (number) - Tablet column count
  - `mobileGridCols` (number) - Mobile column count
- **CSS Classes**: `responsive-grid-columns`
- **CSS Variables**: `--tablet-grid-cols`, `--mobile-grid-cols`
- **Use Case**: Responsive grid layouts

#### Responsive Sizes

- **Filter**: `responsive-sizes`
- **Description**: Sets different sizes for different breakpoints
- **Attributes Added**:
  - `enableResponsiveSizes` (boolean) - Enable responsive sizing
  - `tabletWidth` (string) - Tablet width
  - `mobileWidth` (string) - Mobile width
- **CSS Classes**: `responsive-sizes`
- **CSS Variables**: `--tablet-width`, `--mobile-width`
- **Use Case**: Responsive element sizing

### Visual Effects

#### Rotate Element

- **Filter**: `rotate-element`
- **Description**: Rotates elements by specified degrees
- **Attributes Added**:
  - `rotateElement` (number) - Rotation angle in degrees
- **CSS Classes**: `has-rotate-element`
- **CSS Variables**: `--rotate-element`
- **Use Case**: Creative layouts, decorative elements

#### Object Fit Contain

- **Filter**: `object-fit-contain`
- **Description**: Applies object-fit: contain to images
- **Attributes Added**:
  - `objectFitContain` (boolean) - Enable object-fit contain
- **CSS Classes**: `has-object-fit-contain`
- **Use Case**: Image aspect ratio control

#### Image Transparency

- **Filter**: `image-has-transparency`
- **Description**: Treats background images as transparent textures
- **Attributes Added**:
  - `enableImageTransparency` (boolean) - Enable transparency mode
- **CSS Classes**: `image-has-transparency`
- **Use Case**: Overlay effects, texture backgrounds

### Spacing & Layout

#### Mobile Padding

- **Filter**: `mobile-padding`
- **Description**: Adds custom padding for mobile devices
- **Attributes Added**:
  - `enableMobilePadding` (boolean) - Enable mobile padding
  - `mobilePadding` (object) - Padding values object
- **CSS Classes**: `has-mobile-padding`, `has-mobile-padding-top`, etc.
- **CSS Variables**: `--mobile-padding-top`, `--mobile-padding-right`, etc.
- **Use Case**: Mobile-specific spacing adjustments

#### Overflow Visible

- **Filter**: `overflow-visible`
- **Description**: Sets overflow to visible
- **Attributes Added**:
  - `overflowVisible` (boolean) - Enable visible overflow
- **CSS Classes**: `overflow-visible`
- **Use Case**: Content that extends beyond container

### Interaction

#### Disable Pointer Events

- **Filter**: `disable-pointer-events`
- **Description**: Disables pointer events on elements
- **Attributes Added**:
  - `disablePointerEvents` (boolean) - Disable pointer events
- **CSS Classes**: `disable-pointer-events`
- **Use Case**: Overlay elements, decorative content

## Enabling/Disabling Filters

1. Navigate to **Settings > Kraken Portal**
2. Go to the **Block Filters** tab
3. Select which filters to enable for each block type
4. Save settings

## Custom Filter Development

### Creating Custom Filters

**Add Dynamic Block Filters**:
Filters will be detected and built automatically by the plugin.

```javascript
// In your theme's kraken-core.json
{
  "blockFilters": {
    "core/group": {
      "customSpacerText": {
        "type": "string",
        "label": "Custom Text",
        "default": "Hello World"
      },
      "customSpacerToggle": {
        "type": "boolean",
        "label": "Enable Feature",
        "default": true
      },
      "customSpacerSize": {
        "type": "number",
        "label": "Spacer Size",
        "default": 100
      },
      "customSpacerColor": {
        "type": "string",
        "label": "Color",
        "control": "color",
        "default": "#ff0000"
      }
    }
  }
}
```

## Filter Hook Reference

| Hook                               | File                  | Purpose                      |
| ---------------------------------- | --------------------- | ---------------------------- |
| `blocks.registerBlockType`         | `attributes.js`       | Add new attributes to blocks |
| `editor.BlockEdit`                 | `block-edit.js`       | Add editor controls          |
| `blocks.getSaveElement`            | `apply.js`            | Apply frontend modifications |
| `blocks.getBlockList`              | `block-list-block.js` | Modify block list behavior   |
| `blocks.getSaveContent.extraProps` | `extra-props.js`      | Add extra properties         |

## CSS Classes Generated

### Responsive Classes

- `.hide-on-mobile` - Hidden on mobile devices
- `.hide-on-tablet` - Hidden on tablet devices
- `.hide-on-desktop` - Hidden on desktop devices
- `.center-on-mobile` - Centered on mobile
- `.alignfull-on-mobile` - Full width on mobile
- `.reversed-on-mobile` - Reversed order on mobile

### Layout Classes

- `.main-column-auto` - Auto-width content
- `.has-position-absolute` - Absolutely positioned
- `.has-z-index` - Has z-index control
- `.responsive-display` - Responsive display enabled
- `.responsive-grid-columns` - Responsive grid enabled
- `.responsive-sizes` - Responsive sizing enabled

### Visual Classes

- `.has-rotate-element` - Rotated element
- `.has-object-fit-contain` - Object-fit contain
- `.image-has-transparency` - Transparent image mode
- `.overflow-visible` - Visible overflow
- `.disable-pointer-events` - Disabled pointer events

### Spacing Classes

- `.has-mobile-padding` - Mobile padding enabled
- `.has-mobile-padding-top` - Mobile padding top
- `.has-mobile-padding-right` - Mobile padding right
- `.has-mobile-padding-bottom` - Mobile padding bottom
- `.has-mobile-padding-left` - Mobile padding left

## CSS Variables

### Position Variables

- `--z-index` - Z-index value
- `--rotate-element` - Rotation angle in degrees

### Grid Variables

- `--tablet-grid-cols` - Tablet grid columns
- `--mobile-grid-cols` - Mobile grid columns

### Size Variables

- `--tablet-width` - Tablet width
- `--mobile-width` - Mobile width

### Spacing Variables

- `--mobile-padding-top` - Mobile padding top
- `--mobile-padding-right` - Mobile padding right
- `--mobile-padding-bottom` - Mobile padding bottom
- `--mobile-padding-left` - Mobile padding left

## Best Practices

- Only enable filters that are actually used
- Use CSS variables for dynamic values
- Ensure hidden elements don't break screen readers
- Test with keyboard navigation
- Maintain proper focus management
- Test all breakpoints

## Troubleshooting

### Common Issues

**Filter not applying?**

- Check that the filter is enabled in plugin settings
- Check for JavaScript errors in console

**Editor controls not showing?**

- Confirm block is selected
- Check for JavaScript errors
- Verify filter configuration

## Related Documentation

- [Custom Attributes](custom-attributes.md) - Dynamic attribute system
- [Theme Customization](theme-customization.md) - Extending from themes
