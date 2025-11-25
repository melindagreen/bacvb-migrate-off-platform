# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0.2] - 11/20/2025

### Search & Filter Updates

- Stored the filtered args back into the variable to be available in API calls

## [1.1.0.1] - 11/20/2025

### Search & Filter Updates

- Added missing displayWebsiteLink attribute

## [1.1.0] - 11/20/2025

### New block ~ slider!

- Updated to Swiper 12+
- Removed Swiper navigation styles and added minimal styling
- Updated block color style output to follow same structure as Search & Filter block
- Added new setting & styles for "arrows outside slider" so they do not overlap the slides

### Content Card

- Added new filter 'kraken-core/content-card/event_date_format'
- Adjusted text-only default styling for icon alignment
- Added a span tag around event-end-date for styling

### Kraken ACF

- Added aria label filters for all relevant fields
- Added new filter 'kraken-core/kraken-acf-connector/event_date_format'
- Removed event title from directions link output
- Updated documentation

### Search & Filter

- Bug fix: added isSearching empty array so it's not missing on init
- Updated documentation
- Switched datepicker css import to fix annoying build warnings
- Updated default colors to use core WordPress color variables
- Added reset colors button to color controls
- Added missing "filter text hover color" option
- Minor default style fixes
- Switched to variable --filter-border-radius for setting default radius on filter bar inputs/buttons so it is easier to override

### Block Filters

- Added transform X and transform Y options to position absolute filter
- Added "Stack on Tablet" option for columns
- Fixed responsive display breakpoint values
- Updated responsive display filter text for cleaner editor UI
- Updated editor styles
- Fixed an issue where block filters did not consistently save to the block

## [1.0.9.1] - 10/30/2025

### All

- Added documentation for plugin and blocks

### Block Filter Updates

- Added new block filter for cover blocks to add the has-object-fit-contain class to be used for the background image

### Search & Filter Updates

- Fixed a bug with the search input sometimes causing duplicate results
- Added php and js orderby filters

## [1.0.9] - 10/24/2025

### Search & Filter Updates

- Fixed a bug in filter-bar.php overwriting the $args in render.php causing results to be out of order

## [1.0.8.1] - 10/22/2025

- Added the global $post variable to the content card. An error was being thrown when the content card source was set to queried post as $post was undefined.

## [1.0.8] - 10/17/2025

### Search & Filter Updates

- Updated scroll into view to only work when page numbers are active
- Removed focus on load more button to prevent unwanted page scrolling
- Updated search input to trigger a refresh when field is cleared (backspace/delete), previously only worked when field had content
- Updated applyFilters function to check if the search input actually changed before refreshing results
- Updated filters to clear search input when the page loads with a query string
- Added additional filter for dropdown icon

### Kraken ACF Updates

- Added additional args to filters
- Added customlinktext support to post object fields

## [1.0.7] - 10/13/2025

### Search & Filter - Taxonomy Query Term Selector Updates

- Added hierarchy to terms
- Added search box
- Added pills for currently selected items for easy removal
- Added clear all button

### Search & Filter Updates

- Fix to the search & filter block regarding `displayEventDates` for recurring events
- Fix to responsive display custom CSS properties that caused non-block element's display property to break if not configured for that breakpoint.
- Added proper support for responsive display for the native Single Column block.

### Other Updates

- Added Cursor rules for style guidelines

## [1.0.6] - 10/9/2025

### Content Card Updates

- Added tabindex="-1" to duplicate listing link when there are multiple links on the card

### Kraken Event Data -> Kraken ACF Connector block

- Renamed Kraken Event Data to Kraken ACF Connector
- Updated date outputs to use new recurrence data
- Added support for flexible custom fields based on field type, label customization, and link output
- Added accessibility attributes
- Added hooks for all data output options
- Moved block to main plugin blocks

### Search & Filter Updates

- Adjusted the focus style to focus-visible for the dropdown input fields
- Adjusted scrollIntoView on results refresh to occur after the block reloads
- Adjusted scrollIntoView target to use the outer block div

### Slider Updates

- Added "related" content type to the slider for single post templates. Needs some work to extend to other post types.

## [1.0.5.3] - 10/2/2025

- Added the following hooks to search & filter to allow greater control over how the filter bar labels are rendered
  - `kraken-core/search-and-filter/search_label`
  - `kraken-core/search-and-filter/start_date_label`
  - `kraken-core/search-and-filter/end_date_label`
  - `kraken-core/search-and-filter/filter_dropdown_label`
- Added a responsive display block filter to core blocks to provide display control across all breakpoints.

## [1.0.5.2] - 9/30/2025

- Added theme color picker support to custom attributes will control/type "color".
- Added event background color to event listing card.
- Added `kraken-core/search-and-filter/card-json` and `kraken-core/search-and-filter/color-options` hooks to search & filter that provides greater control over how to insert custom attributes.

### Block Filter Updates

- Added "alignfull-on-mobile" filter for columns and groups
- Updated button center on mobile to also center the text

### Content Card Updates

- Increased per page for post search in editor to 50
- Added Mindtrip support
- Added option to output Mindtrip prompt as secondary action (button) or icon only (can be positioned anywhere)
- Added option to customize the Mindtrip prompt using %postname% as the title placeholder
- Updated default card style to support secondary actions & Mindtrip
- Updated the secondary actions logic to always output the actions separately whenever adding a second link element to the card output and not relying on the read more to be enabled
- Added title attributes to the separate card actions output for accessibility
- Added additional card wrapper classes for separate card actions and Mindtrip
- Added filters for Mindtrip icon, cta text, and prompt
- Fixed a bug with the custom card image when using "custom" content type

### Search and Filter Updates

- Added kraken-core/search-and-filter/before_grid_wrapper & kraken-core/search-and-filter/after_grid_wrapper actions
- Added render_filter_dropdown( $attrs, $args ) helper function to create custom filter dropdowns
- Additional filters included in render_filter_dropdown() function
- Added the Mindtrip attributes for the content card
- Added apply_filters( 'kraken-core/search-and-filter/card_attrs', $cardAttrs, $args ) to modify attributes sent to cards
- Changed include_once() to include() for S&F render elements. Necessary for multiple grids on the same page.

## [1.0.5.1] - 09/23/2025

### Fixes

- Fixed dynamic block attributes for ServerSideRender blocks by registering the attributes with PHP

## [1.0.5] - 09/22/2025

### Fixes

- Fixed a block filter class in editor
- Fixed mismatched block attribute paginationArrowHoverColor in search and filter
- Removed pointer events from all links in search and filter block in the editor
- Fixed style specificity in search and filter block
- Fixed content card selectPost for page/post post types

## [1.0.4] - 09/18/2025

### Search and Filter Updates

- Added filtered_start_date and filtered_end_date attributes to be sent to the content card for events
- Switched the $cardAttrs to only use the $cardJson instead of the full attributes
- Updated the event querying logic to use new methods in Kraken Events to return only events occurring within the selected range
- Disabled past dates in the date picker
- Fixed a bug where the end date would not clear when resetting filters
- Fixed a bug where the start date would not match the query params

### Content Card Updates

- Checks for the new filtered_start_date and filtered_end_date values when displaying dates for Kraken Events
- Updated recurrence logic for Kraken Events date display; only the next date within the displayed date range will be displayed
- Added output for event occurrence count and event occurrence pattern

## [1.0.3.3] -

### Updates

- Added TEC event helper functions to Content Card Block
- Added filter bar hooks to S&F Block
- Added JS Datepicker to S&F Block
- Use filemtime for script versioning

## [1.0.3.2] - 09/11/2025

### Updates

- Updated version to load updated files

## [1.0.3.1] - 09/11/2025

### Added

- Added kraken-core/search-and-filter/query_args filter to Search and Filter block
- Added kraken-core.searchAndFilterPostTypeOptions js filter
- Added kraken-core.searchAndFilterContentTypes js filter

## [1.0.3] - 09/09/2025

### Added

- Added "media" type to the dynamic attributes for the content card
- Added "Custom Image" option to the content card when used on it's own to override the featured image with a selected image
- Added PHP hooks for search & filter block pagination icons
- Added get_option check to find the correct Kraken Events post type slug in the content card and search/filter blocks for sites that might not be using "event"
- Added event address keys filter so the field names can be changed

### Updates

- Moved theme-kraken.json to assets/src/kraken-core in the theme and updated the theme to kraken-core.json
- Moved dynamic attributes, example, and helpers to the filters folder
- Updated position-absolute block filter to use inline CSS variables and additional fields for tablet/mobile values

### Fixed

- Fixed a bug where the content card does not show the currently selected content
- Fixed the endpoint for page and post when querying content in the content card
- Fixed the dynamic asset enqueueing to only enqueue the asset if it exists. Previously if either css or js existed, it would try to enqueue both.

## [1.0.2] - 09/06/2025

### Added

- Dynamic attribute system for custom block controls
- `getDynamicAttributeSettings()` and `renderDynamicControls()` helper functions
- `cleanupDynamicAttributes()` function to remove old/unused dynamic values
- Block-specific filter system for custom additional content settings
- JavaScript-based cleanup during block editing/saving

### Changed

- Refactored custom additional content functionality into reusable helpers
- Moved cleanup from PHP render to JavaScript for better performance
- Updated content-card block to use new dynamic attribute system

### Fixed

- Prevented accumulation of old dynamic values in block attributes
- Improved block save performance by removing render-time cleanup

## [1.0.1] - 09/05/2025

- get_theme_settings() - Ensure we have data points from json so we don't get js errors

## [1.0.0] - 09/05/2025

Initial release

- Three blocks: Content Card, Search & Filter, & Responsive Spacer
- Ability to add custom block scripts & styles for all blocks
- PHP hooks/filters for content card block to modify HTML output (will be expanded to more blocks in the future)
- All block filters from the theme

## [0.0.1] - 07/21/2025

- Plugin structure/setup
