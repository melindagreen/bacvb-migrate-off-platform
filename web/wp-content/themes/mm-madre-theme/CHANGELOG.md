# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.6] - 2025-1-2
### Update
- Updated a madre npm package for @wordpress/scripts

## [1.4.5] - 2023-10-12
### Update
- fixed default sitemap block

## [1.4.4] - 2023-10-12
### Update
- This incorporates mini-update 1.4.3 as well.
- endpointsToSave would ignore what Madre has so essentially we block all and use endpointsToSave to exclude

## [1.4.2] - 2023-10-12
### Update
- added support for endpoints to save in theme-nino.json so we can easily stop Madre from blocking ALL default API routes.

## [1.4.1] - 2023-02-22
### Update
- Updated caniuse-lite

## [1.4.0] - 2022-12-13
### Added
- Added filter to add ID/anchor editor support to all M/N blocks

## [1.3.0] - 2022-11-4
### Added
- Add sitemap block and clean GB directory to more closely match current niño setup
### Removed
- Removed [mm] hero block, doesn't appear to be in wide use as cover block suffices

## [1.2.2] - 2022-08-08
### Added
- Added specific theme support for menus - this is required as of WordPress 6.0.

## [1.2.1] - 2022-06-17
### Added
- Added "browserslist" config option to package.json to help ensure more local setups can successfully build ES6 locally (see https://bit.ly/3HzD98X)

## [1.2.0] - 2022-05-04
### Added
- Added theme-madre.json file for configuring  various theme properties
- Limited encorporation into thumbnail sizes and REST endpoints, but should be extensable

## [1.1.3] - 2022-05-04
### Security
- Updated two packages

## [1.1.2] - 2022-03-14
### Fixed
- Call REST de-register function statically
### Cleaned
- Fidgeting with paren WS
### Added
- Migrate fancy new addr util from Niño

## [1.1.1] - 2022-02-07
### Fixed
- Load REST API file w/ endpoint de-registration

## [1.1.0] - 2021-12-23
### Added
- Support for featured images in CPTs by default
- Support for default categories & tags in Page post type by default
- 
## [1.0.1] - 2021-12-10
### Fixed
- Stray comma throwing a critical error
- 
## [1.0.0] - 2021-10-29
### Added
- Initial release with all theme files
