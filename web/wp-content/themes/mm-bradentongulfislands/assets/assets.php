<?php
/**
 * This file handles theme assets: JavaScript, styles, and Gutenberg blocks.
 * 
 * NPM is required. Install Node.js to access NPM: https://nodejs.org/en/download/
 * 
 * If it's your first time, run `npm install` to install necessary local dependencies.
 * 
 * To build assets, open the assets directory in the command line and run `npm start` ( for dev mode )
 * or `npm run build` ( for production mode ).
 *
 */

namespace MaddenNino\Assets;

use MaddenNino\Library\Constants as C;
use MaddenNino\Library\Utilities as U;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class AssetHandler {
  function __construct () {
    // Admin enqueues
    add_action( "enqueue_block_assets", array( \get_called_class(), "enqueue_admin_scripts_and_styles" ) );

    // Front-end enqueues
    add_action( "wp_enqueue_scripts", array( \get_called_class(), "enqueue_front_scripts" ) );
    add_action( "wp_enqueue_scripts", array( \get_called_class(), "enqueue_front_styles" ) );    
  }

  /**
   * Enqueue admin-only scripts and styles, including block styles
   */
  public static function enqueue_admin_scripts_and_styles() {
    if (is_admin()) {

      $assets_file = include( get_stylesheet_directory() . "/assets/build/admin.asset.php" );
      array_push( $assets_file["dependencies"], "jquery" );

      wp_enqueue_style(
        C::THEME_PREFIX . "-admin-css", // handle
        get_stylesheet_directory_uri()."/assets/build/admin.css", // src
        [], // dependencies
        $assets_file["version"] // version
      );

      wp_enqueue_script(
        C::THEME_PREFIX . "-admin-js", // handle
        get_stylesheet_directory_uri()."/assets/build/admin.js", // src
        $assets_file["dependencies"], // dependencies
        $assets_file["version"], // version
        true // in footer?
      );

      $assets_file = include( get_stylesheet_directory() . "/assets/build/gutenberg.asset.php" );
      wp_enqueue_style( 
        C::THEME_PREFIX . "-blocks-admin-css", // hanlde
        get_stylesheet_directory_uri()."/assets/build/gutenberg.css", // src
        [], // dependencies
        $assets_file["version"] // version
      );

      wp_enqueue_script(
        C::THEME_PREFIX . "-blocks-admin-js", // handle
        get_stylesheet_directory_uri() . "/assets/build/gutenberg.js", // src
        $assets_file["dependencies"], // dependencies
        $assets_file["version"], // version
        false // in footer?
      );
      
    }
  }

  /**
   * Finds front-end styles & scripts based on post type, slug, & custom body class.
   * This helps us avoid creating unnecessary php templates when we only need custom css/js.
   */
  private static function _find_assets() {
    global $template; 
    global $post;

    $assets = array(
      str_replace(".php", "", basename($template)),
    );

    if (is_tax()) {
      //taxonomy-specific styles
      array_push($assets, 'tax-'.get_queried_object()->taxonomy);
      //array_push($assets, 'term-'.get_queried_object()->slug);
    } elseif (is_archive()) {
      array_push($assets, 'archive');
      array_push($assets, 'post-type-archive-'.$post->post_type);
    } elseif (is_single()) {
      //single-cpt or single-post & the post slug
      array_push($assets, 'single-'.$post->post_type);
      array_push($assets, $post->post_name);
    } elseif (is_page()) {
      //page slug and parent page slug
      array_push($assets, $post->post_name);
      if ($post->post_parent) {
        array_push($assets, get_page($post->post_parent)->post_name);
      }
    } elseif (is_search()) {
      array_push($assets, 'search-results');
    }

    //print_r($assets);

    return array_unique($assets);
  }

  /**
   * Enqueue front-end scripts. Include template- and block-specific files if available.
   */
  public static function enqueue_front_scripts() {
    
    // Enqueue front global styles & scripts
    $assets_file_front = include(get_stylesheet_directory() . "/assets/build/app.asset.php");

    $scripts = array(
      "app" => "/assets/build/app.js",
    );

    $dependencies = array("jquery");
    
    $has_ajax = false;

    $loadJS = self::_find_assets();

    foreach ($loadJS as $jsFile) {
      $js_path =  __DIR__ .  "/build/" . $jsFile . ".js";
      if (file_exists($js_path)) {
          $scripts[$jsFile] = "/assets/build/" . $jsFile .".js";
      }
    }

    // Dedupe scripts
    $scripts = array_unique($scripts);

    foreach ($scripts as $k => $v) {
      wp_enqueue_script(
        C::THEME_PREFIX . "-" .$k, // handle
        get_stylesheet_directory_uri() . $v, // src
        $dependencies, // dependencies
        $assets_file_front["version"], // version
        array(
            'strategy'  => 'defer',
            'footer'    => true
        )
      );
    }

    // Localize AJAX data if neeeded
    $has_ajax = false;
    if ($has_ajax) {
      $ajax_data = array(
        "nonce" => wp_create_nonce( C::THEME_PREFIX . "_block_ajax_nonce" ), // The nonce action string *must* remain consistant!
        "url" => admin_url( "admin-ajax.php" ),
      );
      
      wp_localize_script( C::THEME_PREFIX . "front-js", "ajaxData", $ajax_data );
    }
  }

  /**
   * Enqueue front-end stylesheets
   */
  public static function enqueue_front_styles() {
      // Enqueue front global styles & scripts
      $assets_file_front = include( get_stylesheet_directory() . "/assets/build/app.asset.php" );

      $styles = array(
        "app" => "/assets/build/style-app.css",
        "gutenberg" => "/assets/build/style-gutenberg.css",
      );

      $loadCSS = self::_find_assets();

      foreach ($loadCSS as $cssFile) {
        $css_path =  __DIR__ .  "/build/" . $cssFile . ".css";
        if (file_exists($css_path)) {
            $styles[$cssFile] = "/assets/build/" . $cssFile .".css";
        }
      }

      foreach ($styles as $k => $v) {        
        wp_enqueue_style(
          C::THEME_PREFIX . "-" .$k, // handle
          get_stylesheet_directory_uri() . $v, // src
          [], // dependencies
          $assets_file_front["version"] // version
        );
      }
  }
}