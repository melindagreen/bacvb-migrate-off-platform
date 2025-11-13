<?php

/**
 * Madden King Theme - functions.php
 * 
 * All WordPress actions and filters should be placed within an appropriate library class file. The only
 * code placed directly within functions.php should be include/require statements and calls to class
 * constructors.
 * 
 * If you would like to create an action/filter but don't feel it belongs in any existing library class files,
 * first search to see if an appropriate boilerplate file exists. If it doesn't, consider creating a new class
 * file. See library/README.md for more information about the library directory and its structure.
 * 
 * @author      Madden Media
 * @version     1.0.7
 * @link        https://github.com/maddenmedia/madden-theme
 * @since       1.0.0
 */

namespace MaddenTheme;

// Include theme function files
include_once('library/constants.php');
include_once('library/hooks.php');
include_once('library/theme-setup.php');
include_once('library/admin/admin-menus.php');
include_once('assets/assets.php');

include_once('library/utilities.php');
include_once('library/blocks.php');
include_once('library/custom-post-types.php');
include_once('library/custom-taxonomies.php');

// Initialize classes with contructors
new Library\ThemeSetup;
new \MaddenTheme\Library\Admin\AdminMenus;
new Assets\AssetHandler;