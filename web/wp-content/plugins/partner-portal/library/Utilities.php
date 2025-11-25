<?php

namespace PartnerPortal\Library;

/**
* Utilities
*
* GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
*
* Copyright (c) 2020 Madden Media
*/

class_exists('SettingsAdminLayout', false) or require_once __DIR__.'/../admin/SettingsAdminLayout.php';
class_exists('Constants', false) or require_once 'Constants.php';
class_exists('Encoding', false) or require_once 'Encoding.php';

// for geocoding
//require_once dirname(__DIR__).'/library/opencage/AbstractGeocoder.php';
//require_once dirname(__DIR__).'/library/opencage/Geocoder.php';

use \PartnerPortal\Admin\SettingsAdminLayout as SettingsAdminLayout;

/**
* Utility functions for the plugin to use throughout
*
* @todo There is redundancy across Madden plugins in terms of these functions. Consider a global
*    Madden plugin to handle utilities?
*/
class Utilities {

  /**
  * Are we on the command line? (danke https://www.binarytides.com/php-check-running-cli/)
  */
  public static function runningOnCLI () {

    if (defined('STDIN')) {
      // MAY EXIT THIS BLOCK
      return true;
    }

    if ( (empty($_SERVER['REMOTE_ADDR'])) &&
    (! isset($_SERVER['HTTP_USER_AGENT'])) && (count($_SERVER['argv']) > 0) ) {
      // MAY EXIT THIS BLOCK
      return true;
    }

    return false;
  }

  /**
  * Gets the post type for the plugin either from settings or a default
  *
  * @return string The plugin post type
  */
  public static function getPluginPostType () {

//    $cpt = get_option(SettingsAdminLayout::SETTING_PLUGIN_POST_TYPE["key"]);
    if( isset($cpt ) ){
      if( $cpt ){
        return $cpt;
      }
    }
    return Constants::PLUGIN_DEFAULT_CUSTOM_POST_TYPE;
  }

  /**
  * Looks up the taxonomies stored in the constants and finds the requested one for the passed class
  *
  * @param string $className The class name to look up
  * @return string The taxonomy name
  */
  public static function getTaxonomyForClass ($className) {

    foreach (Constants::PLUGIN_TAXONOMIES as $taxonomy => $data) {
      if (isset($data["customFieldsClass"])) {
        if ($data["customFieldsClass"] == "\\{$className}") {
          // MAY EXIT THIS BLOCK
          return $taxonomy;
        }
      }
    }

    return "";
  }

  /**
  * Prepends the meta storage snippet to the passed key for storage in the wp_postsmeta table
  *
  * @param string $key The key to prepend to
  * @return string The adjusted string
  */
  public static function prependMetaKey ($key) {

    return str_replace("[KEY]", $key, Constants::WP_POST_META_KEY_DETAILS);
  }

  /**
  * Gets a GET or POST variable if it's set or returns a default
  *
  * @param $name The variable
  * @param $method Variable in GET or POST? If "ALL", then check either.
  * @param $defNotExists What to return if the variable doesn't exist?
  * @param $defExists What to return if the variable does exist?
  * @return mixed
  */
  public static function getVar ($name, $method="GET", $defNotExists="", $defExists="") {

    $rhett = false;
    $formData = array();

    if ($method == "ALL") {
      $formData["GET"] = $_GET;
      $formData["POST"] = $_POST;
    } else {
      if ($method == "GET") {
        $formData["GET"] = $_GET;
      } else {
        $formData["POST"] = $_POST;
      }
    }
    foreach ($formData as $method => $data) {
      if (isset($data[$name])) {
        $rhett = ($defExists == "") ? $data[$name] : $defExists;
        break;
      } else {
        $rhett = $defNotExists;
      }
    }

    return $rhett;
  }

  /**
  * Looks up a lat/lng for an address
  *
  * @param $address: An address (e.g. "345 E. Toole Ave Tucson AZ 85701")
  * @return string
  */
  public static function getLatLngFromAddress ($address) {

    $apiKey = get_option(SettingsAdminLayout::SETTING_OPENCAGE_API_KEY["key"], "");
    $rhett = array("lat" => "", "lng" => "");

    if ($apiKey == "") {
      // MAY EXIT THIS BLOCK
      return $rhett;
    }

    $geocoder = new \OpenCage\Geocoder\Geocoder($apiKey);
    $data = $geocoder->geocode($address);
    if (count($data["results"]) > 0) {
      $rhett["lat"] = $data["results"][0]["geometry"]["lat"];
      $rhett["lng"] = $data["results"][0]["geometry"]["lng"];
    }

    return $rhett;
  }



  /**
  * Adds a protocol to a URL string if needed
  *
  * Regex courtesy of http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
  *
  * @param $url The URL to check
  * @param $makeSSL Make the protocol SSL?
  * @return string $url The updated URL
  */
  public static function addProtocolToAddress ($url, $makeSSL=false) {

    $protocol = "";

    // add a protocol?
    if (! preg_match("~^(?:f|ht)tps?://~i", $url)) {
      $protocol = ($makeSSL) ? "https://" : "http://";
    }

    // prepend the protocol string
    $url = ($protocol.$url);

    return $url;
  }

  /**
  * Cleans a string from odd characters for database entry
  *
  * @param string $string The string to clean
  * @return string The cleaned string
  */
  public static function cleanString ($string) {

    // encode to utf8 if it is not
    if (!preg_match('!!u', $string)) {
      $string = Encoding::fixUTF8($string);
    }

    $replacements = array(
      chr(130) => ',',    // baseline single quote
      chr(132) => '"',    // baseline double quote
      chr(133) => '...',  // ellipsis
      chr(145) => "'",    // left single quote
      chr(146) => "'",    // right single quote
      chr(147) => '"',    // left double quote
      chr(148) => '"',    // right double quote
      chr(150) => '-',    // long hyphen
      chr(151) => '-',    // long hyphen
      chr(25)  => '',        // end of medium
      '&#x91;' => "'",     // msword single quote
      '&#x92;' => "'",     // msword single quote
      '&#x93;' => '"',     // msword quote
      '&#x94;' => '"',     // msword quote
      '&#x96;' => '&',     // ampersand
      '&#x97;' => ' ',     // not defined
      '&#x99;' => '',     // not defined
      '&#x85;' => ' ',     // abnormal space
      '&#8211;' => '-',     // long hyphen
      '&#xe3;' => 'a',     // a with tilde
      '&#xe9;' => 'e',     // e with tilde
      // double encoded characters introduced in Tucson feed 2014-08-27
      '&amp;reg;' => '&reg;',
      '&amp;amp;' => '&amp;',
    );

    $string = trim(str_replace(array_keys($replacements), array_values($replacements), $string));

    // remove any weird double spaces
    while(strpos($string, '  ') !== false) {
      $string = str_replace('  ', ' ', $string);
    }

    return $string;
  }

  /**
  * Compares two images to see if they are the same (danke https://stackoverflow.com/a/30114215)
  *
  * @param string $firstPath The first local image path
  * @param string $secondPath The second local image path
  * @param int $chunkSize The chunk size to use for comparison
  * @return bool Are they equal or not
  */
  public static function imagesAreEqual ($firstPath, $secondPath, $chunkSize=500) {

    // First check if file are not the same size as the fastest method
    if (filesize($firstPath) !== filesize($secondPath)) {
      // MAY EXIT THIS BLOCK
      return false;
    }

    // Compare the first ${chunkSize} bytes
    // This is fast and binary files will most likely be different
    $fp1 = fopen($firstPath, 'r');
    $fp2 = fopen($secondPath, 'r');
    $chunksAreEqual = fread($fp1, $chunkSize) == fread($fp2, $chunkSize);
    fclose($fp1);
    fclose($fp2);

    if (! $chunksAreEqual) {
      // MAY EXIT THIS BLOCK
      return false;
    }

    // Compare hashes
    // SHA1 calculates a bit faster than MD5
    $firstChecksum = sha1_file($firstPath);
    $secondChecksum = sha1_file($secondPath);
    if ($firstChecksum != $secondChecksum) {
      // MAY EXIT THIS BLOCK
      return false;
    }

    return true;
  }

  /**
  * Turn all URLs in clickable links (danke https://gist.github.com/jasny/2000705)
  *
  * @param string $text The text to replace links in
  * @param array $protocols  HTTP/HTTPS, FTP, mail, twitter
  * @param array $attributes The optional tag attributes to add to each link
  * @return string Original text with links inserted
  */
  public static function linkify ($text, $protocols=array('http', 'mail'), $attributes=array()) {

    // link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
      $attr .= ' '.$key.'="'.htmlentities($val).'"';
    }

    $links = array();

    // extract existing links and tags
    $text = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
      return '<' . array_push($links, $match[1]) . '>';
    }, $text);

    // extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
      switch ($protocol) {
        case 'http':
        case 'https':
        $text = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $text); break;
          case 'mail':
          $text = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; }, $text); break;
          case 'twitter':
          $text = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; }, $text); break;
          default:
          $text = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $text); break;
        }
      }

      // insert all links and return
      return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $text);
    }

    /**
    * Check the current active theme for the file path provided
    *
    * @param string $file_path The relative file path to the root of the default theme
    * @return bool if the file exists or not
    */
    public static function is_file_in_theme( $file_path = __FILE__ ) {
      // Get the them root folder
      $root = get_theme_root().'/'.get_stylesheet();
      // Change all '\' to '/' to compensate for localhosts
      $root = str_replace( '\\', '/', $root ); // This will output E:\xampp\htdocs\wordpress\wp-content\themes
      // Make sure we do that to $file_path as well
      $file_path = str_replace( '\\', '/', $file_path );
      // Make entire path now
      $full_path = $root.$file_path;
      // We can now look for the file
      $bool = file_exists($full_path);
      if ( false === $bool )
      return false;

      return true;
    }
  }
  ?>
