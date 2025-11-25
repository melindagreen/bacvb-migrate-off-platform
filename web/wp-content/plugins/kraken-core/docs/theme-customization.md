# Theme Customization Guide

_Last Updated: October 2025_

## Overview

Kraken Core is designed to be highly customizable from themes. This guide covers how to extend blocks, add custom styles, create custom attributes, and integrate with your theme's design system.

## File Structure

### Example Directory Structure

```
theme/
├── assets/
│   └── src/
│       └── kraken-core/
│           ├── content-card/
│           │   ├── common.scss
│           │   ├── common.js
│           │   ├── editor.scss
│           │   ├── editor.js
│           │   ├── view.scss
│           │   ├── view.js
│           │   ├── hooks.php
│           │   └── styles/
│           │       ├── default.scss
│           │       ├── event.scss
│           │       └── custom.scss
│           ├── search-and-filter/
│           │   ├── common.scss
│           │   ├── common.js
│           │   ├── editor.scss
│           │   ├── editor.js
│           │   ├── view.scss
│           │   ├── view.js
│           │   └── hooks.php
│           ├── responsive-spacer/
│           │   ├── common.scss
│           │   └── common.js
│           ├── kraken-acf-connector/
│           │   ├── common.scss
│           │   ├── common.js
│           │   ├── editor.scss
│           │   ├── editor.js
│           │   ├── view.scss
│           │   ├── view.js
│           │   └── hooks.php
│           ├── block-hooks.php
│           └── kraken-core.json
```

## File Types and Usage

### SCSS Files

#### `common.scss`

- Styles applied to both editor and frontend
- Most commonly used file.

#### `editor.scss`

- Styles only applied in the WordPress editor

#### `view.scss`

- Styles only applied on the frontend
- Try to use common.scss over this to maintain frontend & editor consistency unless there is a specific use-case

### JavaScript Files

#### `common.js`

- Scripts applied to both editor and frontend

#### `editor.js`

- Scripts only applied in the WordPress editor

#### `view.js`

- Scripts only applied on the frontend

### PHP Files

#### `hooks.php`

- PHP hooks and filters for block customization
- Must be included in `block-hooks.php`

#### `block-hooks.php`

- Main file that includes all block hooks
- Required for PHP hooks to work

## Custom Attributes System

### Example Configuration File

Example configuration for `kraken-core.json`:

```json
{
  "blockData": {
    "cardStyles": {
      "add": [
        {
          "label": "Event",
          "value": "event"
        }
      ],
      "remove": ["overlay-partial"]
    },
    "cardAttributes": {
      "displayRegion": {
        "type": "boolean",
        "default": false,
        "label": "Display Region"
      },
      "displayMilePosts": {
        "type": "boolean",
        "default": false,
        "label": "Display Mile Posts"
      }
    },
    "ignoredPostTypes": {
      "add": [],
      "remove": []
    }
  },
  "blockFilters": {
    "kraken-core/responsive-spacer": {
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

## Related Documentation

- [Custom Attributes](custom-attributes.md) - Dynamic attribute system
- [Block Documentation](blocks/) - Individual block guides
- [Examples](examples.md) - Common customization patterns
