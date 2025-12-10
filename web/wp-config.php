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

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Content directory variable
$content_dir = 'wp-content';

// URL Configuration
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

define('WP_HOME', $site_scheme . '://' . $site_host);
define('WP_SITEURL', WP_HOME);

// Database Configuration
define('DB_NAME', $_ENV['DB_NAME'] ?? 'wordpress');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Database table prefix
$table_prefix = $_ENV['DB_PREFIX'] ?? 'wp_';

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
    define('WP_DEBUG', filter_var($_ENV['WP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
    define('WP_DEBUG_LOG', filter_var($_ENV['WP_DEBUG_LOG'] ?? true, FILTER_VALIDATE_BOOLEAN));
    define('WP_DEBUG_DISPLAY', filter_var($_ENV['WP_DEBUG_DISPLAY'] ?? false, FILTER_VALIDATE_BOOLEAN));
    define('SCRIPT_DEBUG', filter_var($_ENV['SCRIPT_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
    @ini_set('display_errors', 0);
}

// Multisite Configuration
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', $_ENV['DOMAIN_CURRENT_SITE'] ?? $site_host);
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

// Duplicator Plugin
define('DUPLICATOR_AUTH_KEY', '{q@P]LzQl?YEJP&ajwjxVtPTt:&5LRr4$MChq_~,ne#DJ/z,P3WK5*XZhuEk,)Y[');

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
