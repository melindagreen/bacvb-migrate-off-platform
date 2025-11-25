# Search & Filter Block

_Last Updated: October 2025_

## Overview

The Search & Filter block creates a powerful search engine for all post types and taxonomies. It features SEO-friendly pagination, query string parameter support, optional filter bars, and integration with the Content Card block.

## Key Features

- ✨ SEO-friendly pagination
- 🔍 Query string parameter support for prefiltering results
- 📊 Optional filter bar with search, taxonomy filters, sorting, and event dates
- 🎯 Filter actions (active filters, count, clear all)
- 📄 Pagination options (load more, page numbers, or none)
- 🎨 Integration with Content Card block
- 📅 Kraken Events, TEC, & Eventastic integration
- 🗺️ Optional Leaflet map view (disabled by default)

## Block Attributes

### Content Configuration

| Attribute       | Type    | Default     | Description                |
| --------------- | ------- | ----------- | -------------------------- |
| `contentType`   | string  | "automatic" | Content selection mode     |
| `postType`      | string  | "post"      | Post type to search        |
| `manualPosts`   | array   | []          | Manually selected posts    |
| `perPage`       | number  | 6           | Results per page (desktop) |
| `perPageMobile` | number  | 3           | Results per page (mobile)  |
| `orderBy`       | string  | "date"      | Sort field                 |
| `order`         | string  | "desc"      | Sort direction             |
| `cachePosts`    | boolean | false       | Enable post caching        |

### Date Query Options

| Attribute           | Type    | Default | Description           |
| ------------------- | ------- | ------- | --------------------- |
| `enableDateQuery`   | boolean | false   | Enable date filtering |
| `selectedDateRange` | number  | 0       | Date range selection  |

### Taxonomy Query Options

| Attribute             | Type    | Default    | Description               |
| --------------------- | ------- | ---------- | ------------------------- |
| `enableTaxonomyQuery` | boolean | false      | Enable taxonomy filtering |
| `taxonomyQueryType`   | string  | "category" | Taxonomy to filter by     |
| `taxonomyQueryTerms`  | array   | []         | Selected taxonomy terms   |

### Filter Bar Configuration

| Attribute                   | Type    | Default | Description                |
| --------------------------- | ------- | ------- | -------------------------- |
| `enableFilterBar`           | boolean | false   | Show filter bar            |
| `displayFilterSidebar`      | boolean | true    | Show filter sidebar        |
| `enableSearchInput`         | boolean | false   | Show search input          |
| `enableStartDateFilter`     | boolean | false   | Show start date filter     |
| `enableEndDateFilter`       | boolean | false   | Show end date filter       |
| `enableTaxonomyFilter`      | boolean | false   | Show taxonomy filters      |
| `taxonomyFilters`           | array   | []      | Available taxonomy filters |
| `enableSortingFilter`       | boolean | false   | Show sorting options       |
| `enableActiveFilterCount`   | boolean | false   | Show active filter count   |
| `enableActiveFilterDisplay` | boolean | false   | Show active filters        |
| `enableClearAllButton`      | boolean | true    | Show clear all button      |

### Display Options

| Attribute             | Type    | Default        | Description                  |
| --------------------- | ------- | -------------- | ---------------------------- |
| `enabledView`         | string  | "grid"         | Default view (grid/list/map) |
| `paginationStyle`     | string  | "page-numbers" | Pagination type              |
| `displayResultsCount` | boolean | true           | Show results count           |
| `cardStyle`           | string  | "default"      | Content card style           |

### Content Card Integration

| Attribute                  | Type    | Default     | Description             |
| -------------------------- | ------- | ----------- | ----------------------- |
| `displayAdditionalContent` | boolean | false       | Show additional content |
| `displayEventDate`         | boolean | false       | Show event dates        |
| `displayEventTime`         | boolean | false       | Show event times        |
| `displayAddress`           | boolean | false       | Show addresses          |
| `displayMindtripCta`       | boolean | false       | Show Mindtrip CTA       |
| `mindtripCtaType`          | string  | "button"    | CTA type                |
| `mindtripCtaText`          | string  | ""          | CTA text                |
| `mindtripPrompt`           | string  | ""          | Mindtrip prompt         |
| `displayExcerpt`           | boolean | false       | Show excerpts           |
| `excerptLength`            | number  | 12          | Excerpt length          |
| `displayReadMore`          | boolean | false       | Show read more links    |
| `readMoreText`             | string  | "Read More" | Read more text          |

### Color Customization

The block includes extensive color customization options for:

- Card colors
- Filter bar background and text
- Filter buttons (normal, hover, active states)
- Pagination (background, text, hover, active states)
- View toggle buttons
- Results count and no results text
- Event date styling

## Advanced Features

### Date Filtering

To enable date filtering:

1. Uncomment the JS Datepicker import in `assets/search-and-filter.js`
2. Uncomment the JS Datepicker import in `styles/style.scss`
3. Install the datepicker library:

```bash
yarn add js-datepicker
```

### Map Integration (Leaflet)

To enable map view:

1. Uncomment the Leaflet import in `assets/search-and-filter.js`
2. Uncomment the Leaflet import in `styles/style.scss`
3. Install Leaflet:

```bash
yarn add leaflet
```

**Important**: Data must have latitude/longitude meta keys to appear on the map. Update the keys in `inc/functions.php` if needed.

### Event Integration

If not using the standard "event" post type, add your event post type to the `$event_post_types` array in `inc/functions.php`.

## PHP Hooks

### Search & Filter Hooks

| Hook                                                | Parameters             | Description            |
| --------------------------------------------------- | ---------------------- | ---------------------- |
| `kraken-core/search-and-filter/query_args`          | `$args`, `$attributes` | Modify query arguments |
| `kraken-core/search-and-filter/card-json`           | `$cardAttrs`, `$args`  | Modify card attributes |
| `kraken-core/search-and-filter/card-attrs`          | `$cardAttrs`, `$args`  | Modify card attributes |
| `kraken-core/search-and-filter/before_grid_wrapper` | `$args`                | Before grid wrapper    |
| `kraken-core/search-and-filter/after_grid_wrapper`  | `$args`                | After grid wrapper     |

### Filter Bar Hooks

| Hook                                                  | Parameters | Description                  |
| ----------------------------------------------------- | ---------- | ---------------------------- |
| `kraken-core/search-and-filter/search_label`          | `$label`   | Modify search input label    |
| `kraken-core/search-and-filter/start_date_label`      | `$label`   | Modify start date label      |
| `kraken-core/search-and-filter/end_date_label`        | `$label`   | Modify end date label        |
| `kraken-core/search-and-filter/filter_dropdown_label` | `$label`   | Modify filter dropdown label |

### Icons

| Hook                                                 | Parameters        | Description |
| ---------------------------------------------------- | ----------------- | ----------- |
| `kraken-core/search-and-filter/pagination_prev_icon` | `$icon`           |             |
| `kraken-core/search-and-filter/pagination_next_icon` | `$icon`           |             |
| `kraken-core/search-and-filter/close_icon`           | `$icon`           |             |
| `kraken-core/search-and-filter/start_date_icon`      | `$icon`, `$attrs` |             |
| `kraken-core/search-and-filter/end_date_icon`        | `$icon`, `$attrs` |             |
| `kraken-core/search-and-filter/filter_dropdown_icon` | `$icon`, `$attrs` |             |

## JavaScript Filters

### Content Type Filters

| Filter                                       | Description                 |
| -------------------------------------------- | --------------------------- |
| `kraken-core.searchAndFilterPostTypeOptions` | Modify available post types |
| `kraken-core.searchAndFilterContentTypes`    | Modify content type options |

## Custom Filter Dropdowns

Use the `render_filter_dropdown()` helper function to create custom filter dropdowns:

```php
$custom_dropdown = render_filter_dropdown($attrs, $args);
```

This function includes additional filters for customization.

## Query String Parameters

The block supports URL parameters for pre-filtering:

- `search` - Search term
- `start_date` - Start date filter
- `end_date` - End date filter
- `{taxonomy_name}` - Taxonomy term filters
- `orderby` - Sort field
- `order` - Sort direction
- `page` - Page number

Example URL:

```
/search-results/?search=keyword&category=news&start_date=2025-01-01
```

## Troubleshooting

### Common Issues

**No results showing?**

- Check that the post type is accessible via REST API
- Verify the post type has Gutenberg editor support
- Ensure posts are published and not private

**Filters not working?**

- Confirm the API route is properly registered
- Check that taxonomy filters are correctly configured
- Verify JavaScript console for errors

**Map not displaying?**

- Ensure Leaflet is installed and imported
- Check that posts have latitude/longitude meta data
- Verify meta key names in functions.php

**Date picker not working?**

- Confirm js-datepicker is installed
- Check that imports are uncommented
- Verify date format compatibility

## Related Documentation

- [Content Card Block](content-card.md) - Card display options
- [Theme Customization](../theme-customization.md) - Extending from themes
