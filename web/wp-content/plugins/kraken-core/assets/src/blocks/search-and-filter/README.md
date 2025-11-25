# Search and Filter Block

This block can be used to display any post type available with optional taxonomy filters and an optional filter bar available for any registered taxonomies.

## Documentation

For complete documentation and advanced usage, see: [Search & Filter Documentation](../../../../docs/blocks/search-and-filter.md)

## Pending Features

- Ability to disable event assets
- Ability to enable/disable the Leaflet map assets. The block has a map view integration that is currently disabled and needs to be updated before being used with the Kraken Core plugin.

## Legacy Notes

### Leaflet Map Integration

This block has a setting to use list view, map view, or a list & map view toggle. To use Leaflet map view:

- Uncomment the Leaflet import at the top of assets/search-and-filter.js
- Uncomment the Leaflet import at the top of styles/style.scss
- Install Leaflet in the theme's package.json

```

yarn add leaflet

```

Data MUST have a latitute/longitude available to appear on the map. If data does not have this and it was not a requested feature, it may be best to disable this setting so no one asks why it's not working. By default the block will search for the "latitude" and "longtitude" meta keys, this will need to be updated if the keys are different. Keys can be updated in the inc/functions.php file - search for the $latitude and $longitude variables.

### Fix Leaflet Map Broken Images

Update webpack output publicPath to this:

```

publicPath: process.env.ASSET_PATH,

```
