<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'bacvb-migrate' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Melgr33n&' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ',RerNfR1H6S8ZG0Z}1n-e=lQ@?He@ay{9LK-M_6a`3!M-?3.NN+Fqm|X cyY2y(:');
define('SECURE_AUTH_KEY',  'CkH|E31nDPIjw|HjdT,7ji|zOlm)HK;cxao2+z1N%9n+(/=!d-TQ;m%r+c3mk?CL');
define('LOGGED_IN_KEY',    'oaAY*Q|xoc:K|usHN_@HyyPO|JVGxrRrlxESEr-+%N!hxQ7TD+}%Q.;xi?_PX4J3');
define('NONCE_KEY',        'L9A:^J6;;m;j;-}L+xtoRWs/yZ7O[xp5Uy-``QjXTH{o:`:Yu?XS5]0pu!^Q(8Do');
define('AUTH_SALT',        '9uZ1RRqLbUuz)l]Z!<cdq@jLmK~aHx&<]B(g+D79;B)$`VmG^aWQ?M-+nx]$;([,');
define('SECURE_AUTH_SALT', 'I^bVE?6D<-yJY0g0`]My 7Yo![WEC#cRmS?w-NE=)&D.8:$+6x$7]0d,fZSp!C)P');
define('LOGGED_IN_SALT',   'b}eDyo]j*9K*D5EVVD[wBUHDu |Cs-{r^>s|7/A$-0>^KyWgt!U>C[cc%Lf5 ]-n');
define('NONCE_SALT',       'M9Z=y;w<#Ln0^>%&L`Ev-&BO`$.>]a2x.oZQ0)nUUUh{(ih7Z!L99~pV:DSUgr=D');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
@ini_set('display_errors', 0);

/** ABSOLUTE PATH TO THE WORDPRESS DIRECTORY. **/
if (!defined('ABSPATH')) {
  define('ABSPATH', dirname(__FILE__) . '/');
}

/** MULTISITE DEFINITIONS **/
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
if (isset($site_host)) {
  define( 'DOMAIN_CURRENT_SITE', $site_host );
} else {
  // Fallback for CLI/non-browser requests
  define( 'DOMAIN_CURRENT_SITE', 'bacvb-migrate-site.test' );
}
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/** SETS UP WORDPRESS VARS AND INCLUDED FILES. */
define( 'DUPLICATOR_AUTH_KEY', '{q@P]LzQl?YEJP&ajwjxVtPTt:&5LRr4$MChq_~,ne#DJ/z,P3WK5*XZhuEk,)Y[' );



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
