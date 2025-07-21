<?php

/** DDEV MySQL hostname */
define( 'DB_HOST', 'db' );

/** The name of the database for WordPress */
define( 'DB_NAME', 'db' );

/** MySQL database username */
define( 'DB_USER', 'db' );

/** MySQL database password */
define( 'DB_PASSWORD', 'db' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'T6=P.xIAY~/nQGFQfqo,Kwl|u$S(G2cl>5)$3N^%c9lI)3ZQ~#yWl.}hWJL|qxd4' );
define( 'SECURE_AUTH_KEY',   '*.hw=Nt,uY%mbL6K: wL3b!4xPhPMOZYFn%>!cw^^Bo@=E,TesE*_JFdl(RX*~?m' );
define( 'LOGGED_IN_KEY',     'Q:6#qF>I2DtwjK3A1)-g{Yd?@UJL*.alTSR+:`2ksP5xCgA>{zF(UvNNQ $*I4?h' );
define( 'NONCE_KEY',         'zJ_M+~?rK=k]`Gf66}EfOk1BJ&(Vpg_??,)51t ByP{PmY`L2uAo|w{J?<5`<aPC' );
define( 'AUTH_SALT',         'MkK=ukfm2)4T6oi4#eK!)>O1v@iI]4M49Za2K>nRybGN-yXNeLi6u2V7BbA32[^y' );
define( 'SECURE_AUTH_SALT',  'wQ9`UYu$2)||&KZ,F5]cn5,ra)YEs:ZCVB2+4&n#[|%/Sj}@X6Uy(O^f<>iVkEPz' );
define( 'LOGGED_IN_SALT',    '-~b&lweAB0)[:HHf~o|M.d}]rl+IRq7-+#Q9a[vrCyJYhqP%RD<EUO(FQtRMM6%f' );
define( 'NONCE_SALT',        'w~*+y2<GrA-41Hk~:4*W[h9/k2D?J)GviCLL(<kvM1pNrBv)P6X!j#(=/IhI~Fe4' );
define( 'WP_CACHE_KEY_SALT', '^j~3?O!+[E{W3>z`t4W/u?|L(rE0gHwS4nuP$^Yd|_YLwT24LE/NrK>T%$#r()xh' );


/** FORCE DEBUGGING!!! **/
define( 'IS_PRODUCTION', false );
define( 'WP_DEVELOPMENT_MODE', 'plugin' );
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
@ini_set('display_errors', 0);

// DISABLE THEME AND PLUGIN EDITOR
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_MEMORY_LIMIT', '512M' );

/* That's all, stop editing! Happy publishing. */

// Avoid "no cookies" warning.
define('ADMIN_COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIEPATH', '');
define('SITECOOKIEPATH', ''); 

//site url needs to be set to the ddev url
define('WP_HOME', 'https://bradenton.ddev.site');
define('WP_SITEURL', 'https://bradenton.ddev.site');