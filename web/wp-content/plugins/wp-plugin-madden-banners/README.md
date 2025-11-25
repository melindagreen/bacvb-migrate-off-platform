# Madden Banners

This plugin allows the conditional render of banners and fly-ins. It registers an options page where you can add any number of flyins and banners and set conditions based on current page, repeat views, device size, and more.

## Table of Contents

* `admin`
    * `AdminConstants.php`: Registers reusable admin-only consants, including the options fields. **Add a new banner field here!**
    * `SettingsLayout.php`: Renders the settings page HTML.
* `assets`: Front- and admin-end images, JavaScript, and styles.
* `library`
    * `Constants.php`: Global constants
    * `Utilities.php`: Reusable utility functions
* `templates`:
    * `settings.php`: Template for the options page, calls the render functions defined in `admin/SettingsLayout.php`.
* `madden-banners.php`: Core plugin registration

## Building

To modify and build the assets folder, first run `npm install` to install necessary packages, then run `npm start` for dev mode and `npm run build` for production mode.