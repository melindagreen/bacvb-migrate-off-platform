# Getting Started with Kraken Core

_Last Updated: October 2025_

## Overview

Kraken Core is a WordPress plugin that provides essential Gutenberg functionality for Madden Media websites. It includes custom blocks, block filters, and extensive customization capabilities.

## Initial Configuration

### Plugin Settings

Navigate to **Kraken Portal** in your WordPress admin to configure:

- **Block Management**: Enable/disable specific blocks
- **Filter Management**: Enable/disable block filters

### Build Process

The plugin uses a build system for assets. To work with the source files:

```bash
# Install dependencies
yarn install

# Start development mode (watch for changes)
yarn start

# Build for production
yarn build
```

## Quick Start

### Basic Customization

To customize blocks from your theme:

1. Create the directory: `theme/assets/src/kraken-core/`
2. Add block-specific folders (e.g., `content-card/`)
3. Include your custom files:
   - `common.scss` - Styles for both editor and frontend
   - `common.js` - Scripts for both editor and frontend
   - `editor.scss` - Editor-only styles
   - `editor.js` - Editor-only scripts
   - `view.scss` - Frontend-only styles
   - `view.js` - Frontend-only scripts

## Next Steps

- [Architecture Overview](architecture.md) - Understanding the plugin structure
- [Block Documentation](blocks/) - Detailed guides for each block
- [Theme Customization](theme-customization.md) - Extending blocks from themes

### Getting Help

- Check the [Examples](examples.md) for common patterns
- Review [Block Filters](block-filters.md) for available customization options
- Consult individual block documentation for specific issues
