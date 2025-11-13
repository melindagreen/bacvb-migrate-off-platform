# Madden Media King - Library

This directory contains namespaced PHP class files that seperate WordPress hooks, actions, and other useful reusable functionality into semantic groups. The exact contents of this directory will be customized to meet the individual sites needs, but some common structure is recommended.

## Common Files

If you chose to add or remove files, please update this table of contents.

- `admin` contains classes pertaining to extending or modifying the WordPress admin dashboard
- `plugins` contains classes that hook into specific WordPress plugins e.g. `gravity-forms.php` for Gravity Forms hooks. Loading these classes should be conditional on the presence of the plugin they modify.
- `constants.php` contains constants relevant across the child theme such as the THEME_PREFIX.
- `theme-setup.php` contains code that sets up some basic theme presets such as image sizes and menu names.
- `utilities.php` contains reusable utility functions.
