# Kraken Core

Core Gutenberg functionality including blocks, filters, and extensive customization capabilities for Madden Media websites.

## Quick Start

1. **Install** the plugin in composer.json
2. **Configure** blocks and filters in Settings > Kraken Portal
3. **Customize** from your theme using the file structure in `theme/assets/src/kraken-core/`

## Available Blocks

- **[Content Card](docs/blocks/content-card.md)** - Display posts and custom content in customizable card layouts
- **[Search & Filter](docs/blocks/search-and-filter.md)** - Powerful search engine with filtering and pagination
- **[Responsive Spacer](docs/blocks/responsive-spacer.md)** - Responsive spacing control across breakpoints
- **[Kraken ACF Connector](docs/blocks/kraken-acf-connector.md)** - Display ACF field data with custom formatting

## Key Features

- ✨ **Block Filters** - 15+ preset filters for layout, positioning, and responsive behavior
- 🎨 **Theme Customization** - Extend blocks with custom styles, scripts, and attributes
- 🔧 **Dynamic Attributes** - Add custom controls to any block via JSON configuration
- 🎯 **Extensive Hooks** - 50+ PHP and JavaScript hooks for customization
- 📱 **Responsive Design** - Mobile-first approach with breakpoint-specific controls
- ⚡ **Performance Optimized** - Caching, lazy loading, and efficient queries

## Documentation

### Getting Started

- [Installation & Setup](docs/getting-started.md) - Complete setup guide
- [Architecture Overview](docs/architecture.md) - Plugin structure and organization

### Block Documentation

- [Content Card](docs/blocks/content-card.md) - Card layouts and customization
- [Search & Filter](docs/blocks/search-and-filter.md) - Search functionality and filtering
- [Search & Filter Advanced](docs/search-and-filter-advanced.md) - API integration, maps, date filtering
- [Responsive Spacer](docs/blocks/responsive-spacer.md) - Responsive spacing control
- [Kraken ACF Connector](docs/blocks/kraken-acf-connector.md) - ACF field display

### Customization Guides

- [Theme Customization](docs/theme-customization.md) - Extending blocks from themes
- [Custom Attributes](docs/custom-attributes.md) - Dynamic attribute system
- [Block Filters](docs/block-filters.md) - Available preset filters
- [Hooks & Filters](docs/hooks-and-filters.md) - Complete API reference

### Reference

- [Helpers & Utilities](docs/helpers-and-utilities.md) - Helper classes and utility functions
- [Examples & Recipes](docs/examples.md) - Common patterns and real-world implementations

## Quick Customization

### Theme File Structure

```
theme/assets/src/kraken-core/
├── content-card/
│   ├── common.scss
│   ├── common.js
│   └── hooks.php
├── search-and-filter/
│   ├── common.scss
│   └── common.js
└── kraken-core.json
```

### Custom Attributes Example

```json
{
  "blockFilters": {
    "kraken-core/content-card": {
      "customAnimation": {
        "type": "select",
        "label": "Animation",
        "default": "none",
        "options": [
          { "label": "Fade In", "value": "fade-in" },
          { "label": "Slide Up", "value": "slide-up" }
        ]
      }
    }
  }
}
```

### PHP Hook Example

```php
// Customize card title
add_filter('kraken-core/content-card/title', function($title, $id, $attrs) {
    return 'Custom: ' . $title;
}, 10, 3);
```

## Support

For detailed documentation, examples, and customization guides, see the [complete documentation](docs/).
