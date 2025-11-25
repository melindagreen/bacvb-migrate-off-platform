# Plugin Architecture

_Last Updated: October 2025_

## Overview

Kraken Core follows a modular architecture designed for extensibility and maintainability. The plugin is organized into distinct layers handling different aspects of functionality.

## Directory Structure

```
kraken-core/
├── assets/                    # Built assets and source files
│   ├── build/                # Compiled assets
│   └── src/                  # Source files
│       ├── blocks/           # Individual block implementations
│       ├── filters/          # Block filters and presets
│       └── styles/           # Global styles
├── docs/                     # Documentation
├── inc/                      # Core PHP classes and functionality
│   ├── admin/               # Admin interface classes
│   ├── core/                # Core functionality classes
│   └── mtphr-settings/      # Settings framework
├── kraken-core.php          # Main plugin file
├── package.json             # Node.js dependencies
└── composer.json            # PHP dependencies
```

## Core Classes

### Main Plugin File (`kraken-core.php`)

The main plugin file handles:

- Plugin header information
- Namespace definition
- Constant definitions
- File includes
- Plugin initialization

**Key Constants:**

- `KRAKEN_CORE_PLUGIN_VERSION` - Current plugin version
- `KRAKEN_CORE_PLUGIN_DIR` - Plugin directory path
- `KRAKEN_CORE_PLUGIN_URL` - Plugin URL

### Core Classes

#### `Helpers` (`inc/class-helpers.php`)

Utility functions for plugin management:

- `check_required_plugins()` - Validates required dependencies
- `check_kraken_crm_status()` - Checks if Kraken CRM is active
- `check_kraken_events_status()` - Checks if Kraken Events is active
- `get_events_plugin()` - Returns active events plugin
- `get_events_slug()` - Returns events post type slug
- `log_error()` - Error logging functionality

#### `Utilities` (`inc/class-utilities.php`)

Frontend utility functions:

- `to_kebab_case()` - String conversion utility

#### `Assets` (`inc/core/class-assets.php`)

Asset management:

- `enqueue_front_scripts()` - Frontend script enqueueing
- `enqueue_front_styles()` - Frontend style enqueueing

#### `Blocks` (`inc/core/class-blocks.php`)

Block registration and management:

- `register_block_types()` - Registers all available blocks
- `register_dynamic_attributes()` - Adds custom attributes to blocks
- `add_block_categories()` - Creates custom block categories
- `allowed_blocks()` - Controls which blocks are available
- `enqueue_theme_block_assets()` - Loads theme customizations

### Admin Classes

#### `AdminSetup` (`inc/admin/class-admin-setup.php`)

Plugin setup and configuration:

- Settings initialization
- Theme integration
- Default configuration

#### `AdminSettings` (`inc/admin/class-admin-settings.php`)

Settings page management:

- Block enable/disable controls
- Filter management
- Configuration options

#### `AdminDashboard` (`inc/admin/class-admin-dashboard.php`)

Dashboard widgets and admin interface

## Initialization Flow

1. **Plugin Activation**

   - Constants are defined
   - Required files are included
   - Dependencies are checked

2. **WordPress `plugins_loaded` Action**

   - Required plugins are validated
   - Core classes are initialized
   - Admin classes are set up
   - Block system is registered

3. **Block Registration**

   - Dynamic attributes are applied
   - Block types are registered
   - Theme customizations are loaded

4. **Asset Enqueueing**
   - Frontend scripts and styles are loaded
   - Theme-specific assets are included

## Build Commands

```bash
# Install dependencies
yarn install

# Development (watch mode)
yarn start

# Production build
yarn build
```

### Asset Structure

- **Source files** are in `assets/src/`
- **Built files** are in `assets/build/`
- **Asset manifests** (`.asset.php`) contain dependency information

## Block System Architecture

### Block Registration

Blocks are automatically discovered and registered from the `assets/build/blocks/` directory. Each block follows WordPress block standards:

- `block.json` - Block configuration
- `render.php` - Server-side rendering
- `index.js` - Editor functionality
- `style.scss` - Block styles
- `editor.scss` - Editor-specific styles

### Dynamic Attributes

The plugin supports dynamic attributes through:

- Theme JSON configuration (`kraken-core.json`)
- JavaScript filter system
- PHP attribute merging

### Filter System

Block filters are organized into presets:

- `apply.js` - General block modifications
- `attributes.js` - Attribute management
- `block-edit.js` - Editor modifications
- `block-list-block.js` - Block list customizations
- `extra-props.js` - Additional properties

## Theme Integration

### File Structure

Themes can extend blocks by creating:

```
theme/assets/src/kraken-core/
├── {block-name}/
│   ├── common.js
│   ├── common.scss
│   ├── editor.js
│   ├── editor.scss
│   ├── view.js
│   ├── view.scss
│   └── hooks.php
└── kraken-core.json
```

### Integration Points

- **Asset Loading**: Theme assets are automatically detected and loaded
- **Hook System**: PHP hooks allow theme customization
- **JSON Configuration**: Theme settings control block behavior
- **Filter System**: JavaScript filters modify block functionality

## Namespace and Autoloading

The plugin uses the `MaddenMedia\KrakenCore` namespace and follows PSR-4 autoloading standards. All classes are properly namespaced to avoid conflicts.

## Optional Integrations

- Kraken CRM
- Kraken Events
- Eventastic
- The Events Calendar (TEC)

## Extension Points

The architecture provides several extension points:

1. **Block Filters** - Modify existing blocks
2. **Custom Blocks** - Add new block types
3. **Theme Integration** - Customize from themes
4. **Hook System** - PHP and JavaScript hooks
5. **Settings API** - Configuration management

## Related Documentation

- [Getting Started](getting-started.md) - Installation and setup
- [Block Documentation](blocks/) - Individual block guides
- [Theme Customization](theme-customization.md) - Extending from themes
