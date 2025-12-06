<?php
/********************************************
╦ ╦┌─┐┬─┐┌┬┐╔═╗┬─┐┌─┐┌─┐┌─┐  ╔═╗┌─┐┌┐┌┌─┐┬┌─┐
║║║│ │├┬┘ ││╠═╝├┬┘├┤ └─┐└─┐  ║  │ ││││├┤ ││ ┬
╚╩╝└─┘┴└──┴┘╩  ┴└─└─┘└─┘└─┘  ╚═╝└─┘┘└┘└  ┴└─┘
 *********************************************/

//! if using docksal/fin - configure the variables in  .docksal/docksal.env
$site_host       = getenv('VIRTUAL_HOST');
$content_dir     = getenv('WP_CONTENT');
$table_prefix    = getenv('MYSQL_PREFIX');

//! else - configure the static strings below
if(!$site_host)
  $site_host    = 'bacvb-local.test';

if(!$content_dir)
  $content_dir  = 'wp-content';

if(!$table_prefix)
  $table_prefix = 'wp_';

/********************************************
╔═╗╔╦╗╔═╗╔═╗  ╔═╗┌─┐┌┐┌┌─┐┬┌─┐┬ ┬┬─┐┬┌┐┌┌─┐┬
╚═╗ ║ ║ ║╠═╝  ║  │ ││││├┤ ││ ┬│ │├┬┘│││││ ┬│
╚═╝ ╩ ╚═╝╩    ╚═╝└─┘┘└┘└  ┴└─┘└─┘┴└─┴┘└┘└─┘o
 *********************************************/

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/wp-config-platform.php';

/** ABSOLUTE PATH TO THE WORDPRESS DIRECTORY. */
if (!defined('ABSPATH')) {
  define('ABSPATH', dirname(__FILE__) . '/');
}

// **MOVE ALL MULTISITE DEFINITIONS HERE**
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
if (isset($site_host)) {
  define( 'DOMAIN_CURRENT_SITE', $site_host );
} else {
  // Fallback for CLI/non-browser requests
  //define( 'DOMAIN_CURRENT_SITE', 'multisite-test-ofxilii-bxdsevwcta6ce.us-4.platformsh.site' );
}
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/** SETS UP WORDPRESS VARS AND INCLUDED FILES. */
define( 'DUPLICATOR_AUTH_KEY', '{q@P]LzQl?YEJP&ajwjxVtPTt:&5LRr4$MChq_~,ne#DJ/z,P3WK5*XZhuEk,)Y[' );
require_once(ABSPATH . 'wp-settings.php');
