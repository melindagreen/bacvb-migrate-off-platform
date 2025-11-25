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

namespace MaddenMadre\Assets;

use MaddenMadre\Library\Constants as Constants;

class AssetHandler {
    function __construct () {
        // Admin enqueues
        add_action( "admin_enqueue_scripts", array( \get_called_class(), "enqueue_admin_scripts_and_styles" ) );

        // Front-end enqueues
        add_action( "wp_enqueue_scripts", array( \get_called_class(), "enqueue_front_scripts" ) );
        add_action( "wp_enqueue_scripts", array( \get_called_class(), "enqueue_front_styles" ) );

        // Block types
        add_action( "init", array( \get_called_class(), "register_block_types" ) );

        // Make sure blocks only load on pages they are added on
        add_filter( 'should_load_separate_core_block_assets', '__return_true' ); 
	}

    /**
     * Glob all block.json files and parse
     */
    private static function _get_block_data() {
        // Grab all existing block JSON files
        $all_files = glob( __DIR__ . "/src/scripts/gutenberg/blocks/*/block.json" );

        // Map all blocks to JSON decoded content and preserve the original directory
        $all_blocks = array_map( function( $file ) {
            $file_str = \file_get_contents( $file );
            $json = \json_decode( $file_str, true );
            $json["directory"] = \str_replace( __DIR__ . "/", "", dirname( $file ) );
            return $json;
        }, $all_files );

        return $all_blocks;
    }
    
    /**
     * Enqueue admin-only scripts and styles, including block styles
     */
   public static function enqueue_admin_scripts_and_styles() {
        $assets_file = include( __DIR__ . "/build/admin.asset.php" );
        array_push( $assets_file["dependencies"], "jquery" );

       // Admin styles
        wp_enqueue_style(
            Constants::THEME_PREFIX . "-admin-css", // handle
            get_template_directory_uri()."/assets/build/admin.css", // src
            [], // dependencies
            $assets_file["version"] // version
        );

        // Admin block styles
        wp_enqueue_style( 
            Constants::THEME_PREFIX . "-blocks-admin-css", // hanlde
            get_template_directory_uri()."/assets/build/gutenberg.css", // src
            [], // dependencies
            $assets_file["version"] // version
        );
        
        // Admin script
        wp_enqueue_script(
            Constants::THEME_PREFIX . "-admin-js", // handle
            get_template_directory_uri()."/assets/build/admin.js", // src
            $assets_file["dependencies"], // dependencies
            $assets_file["version"], // version
            true // in footer?
        );
    }

    /**
     * Enqueue front-end scripts. Include template- and block-specific files if available.
     */
    public static function enqueue_front_scripts() {
        // Enqueue front global styles & scripts
        $assets_file_front = include( __DIR__ . "/build/app.asset.php" );
        array_push( $assets_file_front["dependencies"], "jquery" );

        global $template; 
        $filename = str_replace( ".php", "", basename( $template ) );

        $scripts = array(
          "app" => "/assets/build/app.js",
        );
        $dependencies = array( "jquery" );
        $has_ajax = false;

        $js_path =  __DIR__ .  "/build/" . $filename . ".js";
        if( file_exists( $js_path ) ) {
          $scripts[$filename] = "/assets/build/" .  $filename.".js";
        }
 
        // Dedupe scripts
        $scripts = array_unique( $scripts );

        foreach ($scripts as $k => $v) {
          wp_enqueue_script(
            Constants::THEME_PREFIX . "-" .$k, // handle
            get_template_directory_uri() . $v, // src
            $dependencies, // dependencies
            filemtime( get_template_directory() . $v ),
            array(
              'strategy'  => 'defer',
              'footer'    => true
            )
          );
        }

        // Localize AJAX data if neeeded
        if( $has_ajax ) {
            $ajax_data = array(
                "nonce" => wp_create_nonce( Constants::THEME_PREFIX . "_block_ajax_nonce" ), // The nonce action string *must* remain consistant!
                "url" => admin_url( "admin-ajax.php" ),
            );
            
            wp_localize_script( Constants::THEME_PREFIX . "front-js", "ajaxData", $ajax_data );
        }
    }

    /**
     * Enqueue front-end stylesheets
     */
    public static function enqueue_front_styles() {
        // Enqueue front global styles & scripts
        $assets_file_front = include( __DIR__ . "/build/app.asset.php" );

        global $template; 
        $filename = str_replace( ".php", "", basename( $template ) );

        $styles = array(
          "app" => "/assets/build/style-app.css",
          //"gutenberg" => "/assets/build/style-gutenberg.css",
        );

         foreach ($styles as $k => $v) {        
          wp_enqueue_style(
            Constants::THEME_PREFIX . "-" .$k, // handle
            get_template_directory_uri() . $v, // src
            [], // dependencies
            filemtime( get_template_directory() . $v ),
          );
        }
    }

    /**
     * Register all custom block types and enqueue their assets.
     * If a block is dynamic, find it's renderer function.
     * If a block has front-end JS, find it and associate it.
     */
   public static function register_block_types() {
      // Grab all existing block folders
      $directory = __DIR__ . "/build/scripts/gutenberg/blocks/*";
      $blocks = glob($directory, GLOB_ONLYDIR);
      
      foreach ($blocks as $block) {
          $directoryName = basename($block);
          \register_block_type(__DIR__ . "/build/scripts/gutenberg/blocks/{$directoryName}");

          //check for possible child blocks
          $subdirectories = __DIR__ . "/build/scripts/gutenberg/blocks/{$directoryName}/*";
          $childBlocks = glob($subdirectories, GLOB_ONLYDIR);

          foreach ($childBlocks as $child) {
              $subdirectoryName = basename($child);
              if (file_exists(__DIR__ . "/build/scripts/gutenberg/blocks/{$directoryName}/{$subdirectoryName}/block.json")) {
                  \register_block_type(__DIR__ . "/build/scripts/gutenberg/blocks/{$directoryName}/{$subdirectoryName}");
              }
          }
      }
    }

    public static function register_block_patterns() {
        // register_block_pattern(
        //     'my-plugin/my-awesome-pattern',
        //     array(
        //         'title'       => __( 'Two buttons', 'my-plugin' ),
        //         'description' => _x( 'Two horizontal buttons, the left button is filled in, and the right button is outlined.', 'Block pattern description', 'my-plugin' ),
        //         'content'     => "<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"backgroundColor\":\"very-dark-gray\",\"borderRadius\":0} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-background has-very-dark-gray-background-color no-border-radius\">" . esc_html__( 'Button One', 'my-plugin' ) . "</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"textColor\":\"very-dark-gray\",\"borderRadius\":0,\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color has-very-dark-gray-color no-border-radius\">" . esc_html__( 'Button Two', 'my-plugin' ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
        //     )
        // );
    }
}