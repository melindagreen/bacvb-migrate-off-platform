# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

# [2.4.0] - 2022-07-22
### Changed
- Set theme to use v1.5 of lazy load library and also set theme to use minified version of v1.3 of the parallax library 

# [2.3.6] - 2022-07-22
### Added
- Added BLOCK_NAME_PREFIX to both JS and PHP to auto-prefix blocks and patterns with a '[MM] ' pattern to make them easily searchable on the front end. This pattern can be changed to the site's initials as well.

# [2.3.5] - 2022-07-21
### Changed
- Updated outdated wiki links
- Removed outdated scss modules directory, use block-styles or base sheets as appropriate now
### Added
- Added `images/` alias to webpack.config
- Added custom logo support and replaced hard-coded logo with core the_custom_logo func
- Tweaked core block-styles for better default styling
#### Fixed
- Fixed MMLazyLoad import error in app.js

## [2.3.4] - 2022-07-05
### Added
- Added default breakpoint variables for media queries

## [2.3.3] - 2022-06-17
### Added
- Added "browserslist" config option to package.json to help ensure more local setups can successfully build ES6 locally (see https://bit.ly/3HzD98X)

## [2.3.2] - 2022-06-03
### Fixed
- Code cleaning
- Remove custom gradients
### Added
- Addl. webpack config to handle loading webfonts with script-loader
- Front JS boilerplate in block examples
- Style boilerplate for example-acf and boilerplate cleaning
- Add social links template part

## [2.3.1] - 2022-05-10
### Added
- Removing '/build' from the $scriptDirRoot string in script-loader.php
    - It was causing a conflict with nested scripts & scripts not in the build folder.
- Adding '/build' to the array of scripts/styles files that need it in assets.php

## [2.3.0] - 2022-05-04
### Added
- Added new theme-nino.json configuration file with example values, these will overwrite theme-madre.json recursively

## [2.2.2] - 2022-05-03
### Security
- Updated dependencies

## [2.2.1] - 2022-04-20
### Fixed
- Error in new TaxonomyControl example in ExampleDynamic due to missing block attr in block.json + missing destructured setAttributes
- Wrong value of THEME_PREFIX in JS const, was also duped in two const files, removed dupe and set to match PHP const
- Sass in new repeater component broke on build due to oudated import style + color references

## [2.2.0] - 2022-04-14
### Added
- Improved GB components library
    - Replaced `reorderable-list` with `repeater` component
    - Replaced `category-control` with more generic `taxonomy-control` featuring built-in withSelect
    - Removed `post-picker-query` - going forward, can be handled via grouping more specific components like tax control
- Replaced example code with links to wiki documentation for:
    - Block patterns
    - Block styles
    - Custom post types and custom taxonomies

## [2.1.0] - 2022-04-07
### Added
- Improved default styling of page block content
- Moved colors & fonts to reference theme.json rather than hard-code seperately
- Call true JS parallax for default parallaxing covers
### Fixed
- No the_title in default template, we expect post-title block usage instead

## [2.0.0] - 2022-04-05
### Added
- Starter header/footer styles per M/N roadmap - Thanks, Jenna!
- Added a few more utility Sass mixins
### Fixed
- Remnant of old prefix in constants.php

## [1.6.1] - 2022-03-17
### Fixed
- Add semicolons for minification fix in block examples  (doesn't actually fix but oh well)
- Swap URI for filepath in header logo grab
### Changed
- Remove some commented code from assets.php
- Don't register ACF example in gutenberg/blocks/index.js (this reg's it twice)

## [1.6.0] - 2022-03-10
### Added
- Improvements to assets has_block detection to account for static blocks used within templates
- Fancier logic for generate_address utility
- Updated ACF example block to enclude scripts & style enqueues parallel to standard blocks
### Fixed
- Fixed bug in mobile menu trigger
### Changed
- Minor code cleaning (shortening Utilities and Constants, WS tweaks)

## [1.5.3] - 2022-03-01
### Changed
- Minor edit to static readme.md file to update link to wiki and remove tonik reference

## [1.5.2] - 2022-03-01
### Changed
- Removing tonik require from example static block
- 
## [1.5.1] - 2022-02-07
### Fixed
- Move `wp_head` lower in `header.php` to pass SEO audit in lighthouse.

## [1.5.0] - 2022-02-03
### Added
- Example code for ACF block (created example block and modified assets.php)
- Service worker registration in app.js (depends on service worker file at root, found in shell)

## [1.4.0] - 2022-01-25
### Added
- Example code for gutenberg filters
- Additional documentation in gutenberg directories

## [1.3.0] - 2022-01-25
### Added
- `theme.json` file
- GTM placeholder code in `header.php`
### Changed
- Modified style paths in @import statements to directly reference `style` alias
### Fixed
- Removed file extension in `app.js` import that caused JS bug

## [1.2.1] - 2022-01-20
### Fixed
- Bug fix in example-static, wrong style paths

## [1.2.0] - 2022-01-20
### Changed
- Updated code in example-dynamic and example-static Gutenberg Block
    - Simplify file structure
    - Use render func rather than mustache renderer in dynamic

## [1.1.0] - 2022-01-14

Migration of features developed for industry.travelwyoming.com

### Added
- Structure and boilterplate for additional Gutenberg functionality:
    - Block patterns & block pattern categories
    - Block class registeration and styles
    - Inline code formats
    - Added filter for justify controls on core/columns block
- Improved code-commenting and function comments

## [1.0.1] - 2022-01-10
### Fixed
- Moved requirement of theme files out of hook in functions.php to fix missing theme options

## [1.0.0] - 2021-10-29
### Added
- Initial release with all theme files
