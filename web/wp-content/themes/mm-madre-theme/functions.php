<?php

namespace MaddenMadre;

// Include theme function files
include_once( 'library/theme-setup.php' );
include_once( 'assets/assets.php' );
include_once( 'library/admin/admin-menus.php' );
include_once( 'library/constants.php' );
include_once( 'library/custom-post-types.php' );
include_once( 'library/rest-api.php' );
include_once( 'library/utilities.php' );

// Initialize classes with contructors
new Library\MaddenTheme;
new Assets\AssetHandler;
new Library\Admin\AdminMenus;
new Library\RestApi;
