# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.5.0] - 2022-12-08
### Security
- Updated composer.json and installed packages
### Fixed
- Removed reference to WOT Industry site in service worker
### Added
- Redis support by default

## [2.4.2] - 2022-07-19
### Changed
- Put revision control count (WP_POST_REVISIONS) out in main config instead of just docksal, and set it to 10

### Fixed
- Updated docksal files to use main branch

## [2.4.0] - 2022-07-10
### Fixed
- Switched Cron runs from legacy master branch to new main branch
- Reversed this change log commit order to most recent first

## [2.3.3] - 2022-05-24
### Fixed
- Updated composer.json to use install-wp-local-config instead of copy-config
- Removed copy-config
- Renamed get-theme-dir-path to theme-dir
- Updated composer build script DIR to use the theme:dir file which fixes the hang that happens on composer build / yarn install

## [2.3.2] - 2022-05-03
### Security
- Updated composer.json and installed packages

## [2.3.1] - 2022-03-11
### Fixed
- Update misnamed theme dir command

## [2.3.1] - 2022-03-01
### Security
- Updated composer.json and installed packages

## [2.3.0] - 2022-03-01
- Removed Tonik CLI dependency
- Updated WP-CLI to 2.5 and fixed configuration to correctly install it
- Removed requirement of twentytwentyone theme in composer
- Removed translationpress plugin dependency in composer
- Removed really-simple-csv-importer plugin dependency in composer
- Removed cache-control plugin dependency in composer
- Removed wp-media-library-categories plugin dependency in composer
- Set some wpackagist plugins to always pull the latest version
- Defined a default state for $site_scheme in wp-config-platform.php
- Ran dos2unix to remove Win line endings in composer config files
- Removed ACF from git repo
- Removed accidentally added Niño theme from git repo

## [2.2.1] - 2022-02-07
### Added
- Update routes.yaml to use current caching config

## [2.2.0] - 2022-02-03
### Added

## [2.1.1] - 2022-01-10
### Added
- Added some icon and related bitmap file cache rules back to the "/" location for images in our theme folder.
- Add service worker JS file in web root

## [2.1.0] - 2022-01-10
### Added
- Config for better caching assets in wp-content/uploads
- Disk space adjustment (512 for database vs 2048, with the rest put back to the main project)
- Enabling PHP Gzip compression by default

## [2.0.0] - 2022-01-10
### Security
- Composer updates
