# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.7.3] - 2025-06-06

### Added
- Added hook in blocks.php to disable/remove core blocks from the editor
- Added missing AOS files

### Updated
- Updated responsive spacer with changes from block library
- Updated theme.json spacing scale names

## [1.7.2] - 2025-05-08

### Updated
- Added in the hooks.php file a body opening function to put the noscript gtm tag snippet just after the opening body. The current version has it for the head but not the noscript part.


## [1.7.1] - 2025-05-06

### Updated
- Additional CSS cleanup & theme.json fixes
- Consistency update for SCSS to always use kebab-case in core files
- Switched SCSS variables to CSS variables wherever possible
- Block filter cleanup & removal of no longer needed filters
- Updated constants.js
- Fixed a bug with admin_enqueue_scripts in the block editor
- Fixed responsive spacer tablet/mobile views in editor

## [1.7.0] - 2025-04-24

### Updated

- Removed CSS layout and spacing updates. Moved to theme.json
- Other front and back end CSS updates
- Added additional CSS variables from webpack

## [1.6.0] - 2025-03-11

### Updated

- Webpack and stylesheets to use @use instead of @import

## [1.5.0] - 2025-01-02

### Added

- Best practice compliance headers
- All the other stuff between now and last October

## [1.0.7] - 2024-10-02

### Added

- Added default header / main navigation styles and mobile menu toggle function.
- Added support for custom social icons in the core social links block. If a matching icon exists in the assets/images/icons/social folder, it will be used in place of the default WordPress icons.
- Added default constants for block_class and social_links in library/constants.php.
- Added new block filter for z-index that can be used with negative margins which are now a core feature.
- Added page-templates folder and example.js to conditionally load scripts and styles for specific pages and templates that are not block related.
- Added \_find_assets function from Nino theme to support the above ^ item.
- Added support for GTM and other snippets in the header and footer.

### Changed

- Updated editor styles for more consistency with the front-end.
- Updated theme.json with more base styles and removed duplicate styles from various stylesheets.
- Removed custom post types and taxonomies and replaced them with a commented out example post type and taxonomy.
- Updated 404 default template.
- Updated slug on default patterns from mm-king-theme to madden-theme for consistency.
- Updated footer.php to footer-social-media.php to loop in default social media links set in the constants file.
- Updated webpack config with custom page template support.

### Removed

- Removed script loader; assets will be enqueued separately for better caching support.
- Removed breakpoints SCSS files as this code is not used in this theme.
- Removed custom fonts and only included Open Sans so it is easier to start from scratch.
- Removed helpers.php; this provides the same function as utilities.php.
- Removed listing directory slug function
- Removed editor folder for styles; we can load the front-end stylesheets directly in style-admin.scss if needed or editor ui styles can be added in /src/scripts/gtunberg/block-styles/styles/index.scss
- Removed build folder from repo
- Removed post meta template part and pattern and added the block markup directly to the archive and search templates

### Fixed

- Updated hook for removing core block patterns from init to after_setup_theme.

## [1.0.6] - 2024-09-04

### Added

- Added $render_content to block override filter
- Added core/button and core/read-more block override examples

## [1.0.5] - 2024-08-01

### Added

- Added phone, tablet, desktop, and wide media query mixins

### Updated

- CSS breakpoint variable formats

## [1.0.4] - 2024-07-31

### Added

- Added dispatches file for core editor
- Added readme to Gutenberg folder for documentation on different block editor functions

### Removed

- Removed Owl Carousel from packages; slider packages should be added in package.json.
- Disabled group grid variation since this feature is now core; left the code as an example for future variations.
- Removed random map function in utilities.js

### Updated

- Moved block-overrides folder to the Gutenberg folder to keep all block modifications in the same folder. Updated path in blocks.php.
- Updated example blocks to madden-media category for consistency.
- Updated ACF example with [MM] prefix; this is not added automatically to ACF blocks.
- Updated block styles unregister function to use setTimeout due to a bug in Gutenberg.
- Updated justify content block filter to fix console warnings
- Fixed style variables for media queries

## [1.0.3] - 2024-07-24

### Added

- Added missing media query (screen-sm-min).
- Added new custom block filters for center and hide on mobile.
- Added styles for core blocks using new filters.

### Updated

- Updated block filter functionality to use BlockListBlock to apply custom classes & styles in the Gutenberg editor. This removes the need for a custom wrapper.
- Updated block-edit function to only add custom controls when the block is selected to improve performance.
- Renamed block filter controls to block-edit.

## [1.0.2] - 2024-06-26

### Added

- Added colorPalette scss variable to webpack.config.js
- Updated theme.json and files in parts & templates to be be closer to the Nino theme
- Updated custom-post-types to show an example CPT

## [1.0.1] - 2024-06-05

### Added

- Added upload_mimes hooks to allow svg uploads

## [1.0.0] - 2024-02-12

### Added

- Initial release with all theme files
