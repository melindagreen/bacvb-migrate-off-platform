<?php

/***
 *    ╦ ╦╔═╗   ╔═╗┌─┐┌┐┌┌─┐┬┌─┐  ┌─┐┌─┐┬─┐
 *    ║║║╠═╝───║  │ ││││├┤ ││ ┬  ├┤ │ │├┬┘
 *    ╚╩╝╩     ╚═╝└─┘┘└┘└  ┴└─┘  └  └─┘┴└─
 *    ╔═╗┬  ┌─┐┌┬┐┌─┐┌─┐┬─┐┌┬┐ ┌─┐┬ ┬     
 *    ╠═╝│  ├─┤ │ ├┤ │ │├┬┘│││ └─┐├─┤     
 *    ╩  ┴─┘┴ ┴ ┴ └  └─┘┴└─┴ ┴o└─┘┴ ┴     
 */

/*********
 * WP_ROCKET
 *********/
// Your license KEY.
if ( ! defined( 'WP_ROCKET_KEY' ) ) {
  define( 'WP_ROCKET_KEY', 'ea71e7e9');
}

// Your email, the one you used for the purchase.
if ( ! defined( 'WP_ROCKET_EMAIL' ) ) {
  define( 'WP_ROCKET_EMAIL', 'mmserv@maddenmedia.com' );
}
define( 'WP_CACHE', true ); // Added by WP Rocket

// -- PLATFORM.SH CONFIG READER -- 
// Create a new config object to ease reading the Platform.sh environment variables.
// You can alternatively use getenv() yourself.
use Platformsh\ConfigReader\Config;

$config = new Config();
$site_scheme = 'http';

// Update scheme and hostname for the requested page.
if (isset($_SERVER['HTTP_HOST'])) {
  $site_host = $_SERVER['HTTP_HOST'];
  $site_scheme = !empty($_SERVER['HTTPS']) ? 'https' : $site_scheme;
}
define('WP_HOME', $site_scheme . '://' . $site_host);
define('WP_SITEURL', WP_HOME);

if ($config->isValidPlatform()) {
  // Running on platform
  // IS PRODUCTION
  define('IS_PRODUCTION', getenv('PRODUCTION'));

  // FORCE SSL
  define('FORCE_SSL_ADMIN', true);

  // DISABLE PLUGIN UPDATES
  define('DISALLOW_FILE_MODS', true);

  // DISABLE WP CRON
  define('DISABLE_WP_CRON', true);

  // Avoid PHP notices on CLI requests.
  if (php_sapi_name() === 'cli') {
    session_save_path("/tmp");
  }

  // Get the database credentials
  $credentials = $config->credentials('database');

  // We are using the first relationship called "database" found in your
  // relationships. Note that you can call this relationship as you wish
  // in your `.platform.app.yaml` file, but 'database' is a good name.
  define('DB_NAME', $credentials['path']);
  define('DB_USER', $credentials['username']);
  define('DB_PASSWORD', $credentials['password']);
  define('DB_HOST', $credentials['host']);
  define('DB_CHARSET', 'utf8');
  define('DB_COLLATE', '');

  // Get the credentials to connect to the Redis service.
  if ($config->hasRelationship('rediscache')) {
    $credentials_redis = $config->credentials('rediscache');
    try {
        define( 'WP_REDIS_HOST', $credentials_redis['host'] );
        define( 'WP_REDIS_PORT', $credentials_redis['port'] );
    } catch (Exception $e) {
        print $e->getMessage();
    }
  }

  // Check whether a route is defined for this application in the Platform.sh
  // routes. Use it as the site hostname if so (it is not ideal to trust HTTP_HOST).
  if ($config->routes()) {

    $routes = $config->routes();

    foreach ($routes as $url => $route) {
      if ($route['type'] === 'upstream' && $route['upstream'] === $config->applicationName) {

        // Pick the first hostname, or the first HTTPS hostname if one exists.
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($host !== false && (!isset($site_host) || ($site_scheme === 'http' && $scheme === 'https'))) {
          $site_host = $host;
          $site_scheme = $scheme ?: 'http';
        }
      }
    }
  }

   // Debug mode should be disabled on Platform.sh. Set this constant to true
    // in a wp-config-local.php file to skip this setting on local development.

    if (!defined( 'WP_DEBUG' )) {
      define( 'WP_DEBUG', false );
    }

  // NEEDED FOR DC FRAMEWORK PLUGIN
  $_SERVER['APP_ENV'] = IS_PRODUCTION ? 'prod' : 'dev';
  define('DC_LOG_FOLDER', __DIR__ . '/logs');

  // Set all of the necessary keys to unique values, based on the Platform.sh
  // entropy value.
  if ($config->projectEntropy) {
    $keys = [
      'AUTH_KEY',
      'SECURE_AUTH_KEY',
      'LOGGED_IN_KEY',
      'NONCE_KEY',
      'AUTH_SALT',
      'SECURE_AUTH_SALT',
      'LOGGED_IN_SALT',
      'NONCE_SALT',
    ];
    $entropy = $config->projectEntropy;
    foreach ($keys as $key) {
      if (!defined($key)) {
        define($key, $entropy . $key);
      }
    }
  }
} else {

  $local = dirname(__FILE__) . '/wp-config-local.php';
  $isDocksal = getenv('DOCKSAL_STACK') ?? false;
  $docksal = dirname(__FILE__) . '/wp-config-docksal.php';

  if ($isDocksal && file_exists($docksal)) {
    include($docksal);
  } else if (file_exists($local)) {
    include($local);
  }
}

// CONTENT DIRECTORY
define('WP_CONTENT_DIR', dirname(__FILE__) . "/{$content_dir}");
define('WP_CONTENT_URL', WP_HOME . "/{$content_dir}");

// DEFAULT PHP SETTINGS.
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);
ini_set('pcre.backtrack_limit', 200000);
ini_set('pcre.recursion_limit', 200000);
