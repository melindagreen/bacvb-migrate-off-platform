# Custom Card Attributes System

_Last Updated: October 2025_

## Overview

The Custom Attributes System allows you to dynamically add new controls and attributes to the content card without modifying the block's core code. This system is powered by theme JSON configuration and JavaScript filters, making it highly flexible and maintainable.

Any attributes added to the content card with this method will also be loaded in the Search and Filter block.

## How It Works

### 1. Theme JSON Configuration

Custom attributes are defined in `theme/assets/src/kraken-core/kraken-core.json`

### 2. JavaScript Filter System

The system uses WordPress hooks to dynamically add attributes and controls

### 3. PHP Integration

Custom attribute values are stored in the customAdditionalContent object and must be accessed through that. Output can be managed with hooks.

## Configuration

### Basic Structure

```json
{
  "blockData": {
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
    }
  }
}
```

### Supported Control Types

- Boolean
- String
- Select
- Number
- Color

## Output Example

```php
//Place in: theme/assets/src/kraken-core/content-card/hooks.php
add_filter('kraken-core/content-card/link', function($link, $id, $attrs) {
	if (isset($attrs['customAdditionalContent']['linkQueryString'])) {
		$link = $link . $attrs['customAdditionalContent']['linkQueryString'];
	}
	return $link;
}, 10, 3);
```

## Best Practices

- Use descriptive, camelCase names for attributes
- Always provide sensible defaults
- Use empty strings for optional text fields
- Use `false` for boolean toggles
- Only add attributes that are actually used

## Troubleshooting

### Common Issues

**Attribute not appearing in editor?**

- Check JSON syntax in `kraken-core.json`
- Verify block name matches exactly
- Clear browser cache and rebuild assets

**Attribute not saving?**

- Ensure attribute is properly defined
- Check for JavaScript errors in console
- Verify attribute type is supported

**CSS not applying?**

- Check CSS class names match attribute values
- Verify CSS specificity
- Ensure styles are loading after block styles

**PHP not receiving attribute?**

- Check attribute name spelling
- Verify attribute is saved in editor
- Use `var_dump($attributes)` to debug

## Related Documentation

- [Theme Customization](theme-customization.md) - Extending blocks from themes
- [Block Documentation](blocks/) - Individual block guides
- [Examples](examples.md) - Common customization patterns
