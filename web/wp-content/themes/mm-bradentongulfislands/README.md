# Madden Media Nino Child Theme

This child theme expands upon the Madre parent theme. This child theme should be the backbone of a web project and contain all assets and functionality specific to the site. Nino contains some useful scaffholding and boilerplate code for constructing a theme code, but is intended to be extendable to suit the needs of a specific site.
## Useful links
* [Child and Parent Themes - WP Codex](https://developer.wordpress.org/themes/advanced-topics/child-themes/)
* [Template Hierarchy - WP Codex](https://developer.wordpress.org/themes/basics/template-hierarchy/)
* [How to update this repo](https://wiki.maddenmedia.com/Updating_Madre/Ni%C3%B1o)

## Structure
* `functions.php` loads our library files and initializes the theme. This will *not* be overwritten by the child theme.
* `style.css` provides information to WP on our theme including the text domain for i18n and the parent name to reference in the child theme's root `style.css`.
* Some basic default template files are provided to expand upon in the child theme.
* `assets` contains all buildable files and configuration for building theme.
    * `build` contains built files.
    * `code` contains PHP code useful for loading and enqueueing built files.
    * `images` holds static theme images such as UI icons, theme logos, et c.
    * `src` contains source SCSS and JS files.
    * `assets.php` contains the logic for serving static assets.
    * `package-lock.json`, `package.json`, and `wepback.config.js` configure the build process and provide the build commands.
* `library` contains PHP functions that load and customize the theme and provide expanded functionality to the chlid theme.
## Building this theme
This theme's `composer.json` file contains several useful commands for buliding out the theme's assets. These commands can also be run directly from within the assets directory.

* `composer install-assets` will install all required node_modules through Yarn.
* `composer upgrade-assets` will run the interactive Yarn module upgrader.
* `composer start` will begin watching the `assets/src` directory for changes.
* `composer build` will build production code from the `assets/src` directory.
