<?php
/**
 * WordPress Configuration File
 *
 * This file contains the following configurations:
 * - Database settings
 * - Secret keys
 * - Database table prefix
 * - Environment-specific settings
 * - Multisite configuration
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Content directory variable (used later)
$content_dir = 'wp-content';

// Load local configuration overrides first (if exists)
$local_config = __DIR__ . '/wp-config-local.php';
if (file_exists($local_config)) {
    require_once $local_config;
}

// If no local config, load environment variables from .env file
if (!file_exists($local_config) && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// URL Configuration (only if not set by local config)
$site_scheme = 'http';
if (
    !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
    $site_scheme = 'https';
}

if (isset($_SERVER['HTTP_HOST'])) {
    $site_host = $_SERVER['HTTP_HOST'];
    $site_scheme = !empty($_SERVER['HTTPS']) ? 'https' : $site_scheme;
}

if (!defined('WP_HOME')) {
    define('WP_HOME', $site_scheme . '://' . $site_host);
}
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', WP_HOME);
}

// Database Configuration (only if not set by local config)
if (!defined('DB_NAME')) {
    define('DB_NAME', $_ENV['DB_NAME'] ?? 'wordpress');
}
if (!defined('DB_USER')) {
    define('DB_USER', $_ENV['DB_USER'] ?? 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
}
if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8');
}
if (!defined('DB_COLLATE')) {
    define('DB_COLLATE', '');
}

// Database table prefix (only if not set by local config)
if (!isset($table_prefix)) {
    $table_prefix = $_ENV['DB_PREFIX'] ?? 'wp_';
}

// Authentication Unique Keys and Salts
define('AUTH_KEY',         'ZtV%9pO`*MV*H@qkB$?PLeez]T1v;,_9/a|xm=~S]HUw4~8|84`%IG1Z>1]9ty
g|');
define('SECURE_AUTH_KEY',  'V4T?gLj6ja/H |>kPd=%y+n1W[[9]QyZ$guqN_Uzkdox|QO|p77}NyJl>ysj+a
N(');
define('LOGGED_IN_KEY',    'k<EQp)L.!y4K1ZIpT @R5PXW56CLHEP!%U%7:,0TR.Q|q+CF{Q/U>!@ OJG5mp
Kh');
define('NONCE_KEY',        '2CKOG8zG{Abb9v+?8x|hV]$I[hkV0ZYpSz7_ITv;F{ob!a9C,pL-Cw+ivhQ9WE
+`');
define('AUTH_SALT',        '#-xE|d;!}zx,U3:|G~{HN!2t;EZ@$MZA%.FlyLdc!5P f>cEo%+tog>$V-u[PV
YT');
define('SECURE_AUTH_SALT', 'o}d`?|Y03C.BM@0vW`Jf82nB+62f.3Z7*MI&k8wVI:&LlHP:joc3Zp+OUL:,9Q
!p');
define('LOGGED_IN_SALT',   '[5k}H,/s|gE7pP}=Me4KP2_ELS$9c.*e@(zCY&g{pf(-6ioz^+R*7g+ W|wVGO
K~');
define('NONCE_SALT',       'V4taBW5+;(}G{[h3J+?&o(Xo!`R})B;rP,fJ`dMq3H.m%Cy.s4g{ryPQ705{qE
vZ');

// Environment-based settings
$wp_environment = $_ENV['WP_ENV'] ?? 'production';

if ($wp_environment === 'production') {
    define('WP_DEBUG', false);
    define('WP_DEBUG_LOG', false);
    define('WP_DEBUG_DISPLAY', false);
    define('DISALLOW_FILE_MODS', true);
    define('DISABLE_WP_CRON', true);
} else {
    define('WP_DEBUG', $_ENV['WP_DEBUG'] ?? true);
    define('WP_DEBUG_LOG', $_ENV['WP_DEBUG_LOG'] ?? true);
    define('WP_DEBUG_DISPLAY', $_ENV['WP_DEBUG_DISPLAY'] ?? false);
    define('SCRIPT_DEBUG', $_ENV['SCRIPT_DEBUG'] ?? true);
    @ini_set('display_errors', 0);
}

// Content Directory
define('WP_CONTENT_DIR', __DIR__ . "/{$content_dir}");
define('WP_CONTENT_URL', WP_HOME . "/{$content_dir}");

// PHP Settings
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);
ini_set('pcre.backtrack_limit', 200000);
ini_set('pcre.recursion_limit', 200000);

// For Breeze and file operations
define('FS_METHOD', 'direct');

// Absolute path to WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Sets up WordPress vars and included files
require_once ABSPATH . 'wp-settings.php';
