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
  $site_host    = 'mm-sandbox.local';

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

/** SETS UP WORDPRESS VARS AND INCLUDED FILES. */
require_once(ABSPATH . 'wp-settings.php');