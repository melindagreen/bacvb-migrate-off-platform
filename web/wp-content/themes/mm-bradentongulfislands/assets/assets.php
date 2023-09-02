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
        add_action( "admin_enqueue_scripts", array( \get_called_class(), "enqueue_admin_scripts_and_styles" ) );

        // Front-end enqueues
        add_action( "wp_enqueue_scripts", array( \get_called_class(), "enqueue_front_scripts" ) );
        add_action( "wp_enqueue_scripts", array( \get_called_class(), "enqueue_front_styles" ) );

        // Block types
        add_action( "init", array( \get_called_class(), "register_block_types" ) );
        add_action( "acf/init", array( \get_called_class(), "register_acf_block_types" ) );

        // Block patterns
        add_action( 'init', array( \get_called_class(), 'register_block_patterns' ) );
	}

    /**
     * Glob all block.json files and parse
     * @param boolean $acf          Do we want ACF blocks?
     * @return array                All block data
     */
    private static function _get_block_data( $acf = false ) {
        // Grab all existing block JSON files
        $all_files = glob( __DIR__ . "/src/scripts/gutenberg/blocks/*/block.json" );

        // Map all blocks to JSON decoded content and preserve the original directory
        $all_blocks = array_map( function( $file ) {
            $file_str = \file_get_contents( $file );
            $json = \json_decode( $file_str, true );
            $json["directory"] = \str_replace( __DIR__ . "/", "", dirname( $file ) );
            return $json;
        }, $all_files );

        // Filter for ACF only blocks depending on which hook we're using
        $filtered_blocks = array_filter( $all_blocks, function( $block ) use( $acf ) {
            return ( $acf && isset( $block['acf'] ) && $block['acf'] )
                || ( !$acf && ( !isset( $block['acf'] ) || !$block['acf'] ) ) ;
        } );

        return $filtered_blocks;
    }

    /**
     * Locate a script as external, block-specific, or library, and return a path for enqueueing
     * @param string $script                        The script's name
     * @param string $block_dir                     The current block's directory
     * @param boolean $is_style                      Look in the stylesheet directory instead?
     * @return string|boolean                       The path, or false if not found
     */
    private static function _locate_block_script( $script, $block_dir, $is_style = false ) {
        $lib_dir = $is_style ? 'styles' : 'scripts';

        // Is it an external URL? Push it directly
        if( filter_var( $script, FILTER_VALIDATE_URL ) ) {
            return $script;
        // Is it in the block directory? Push from there.
        } else if( \file_exists( get_stylesheet_directory() ."/assets/$block_dir/$script" ) ) {
            return "$block_dir/$script";
        // Is it in the global library? Push from there.
        } else if( \file_exists( 
            get_stylesheet_directory() ."/assets/src/$lib_dir/library/$script"
        ) ) {
            return "src/$lib_dir/library/$script";
        }

        return false;
    }

    /**
     * Enqueue admin-only scripts and styles, including block styles
     */
    public static function enqueue_admin_scripts_and_styles() {
        $assets_file = include( __DIR__ . "/build/admin.asset.php" );
        array_push( $assets_file["dependencies"], "jquery" );

        // Admin styles
        wp_enqueue_style(
            C::THEME_PREFIX . "-admin-css", // handle
            get_stylesheet_directory_uri()."/assets/build/admin.css", // src
            [], // dependencies
            $assets_file["version"] // version
        );

        // Admin block styles
        wp_enqueue_style( 
            C::THEME_PREFIX . "-blocks-admin-css", // hanlde
            get_stylesheet_directory_uri()."/assets/build/gutenberg.css", // src
            [], // dependencies
            $assets_file["version"] // version
        );
        
        // Admin script
        wp_enqueue_script(
            C::THEME_PREFIX . "-admin-js", // handle
            get_stylesheet_directory_uri()."/assets/build/admin.js", // src
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

        //Swiper 
        wp_enqueue_style(
            C::THEME_PREFIX . "-swiper-styles", // handle,
            "//unpkg.com/swiper@8.4.5/swiper-bundle.min.css", // src
            NULL, // dependencies
            NULL // version
        );
        
        //Swiper Enqueue CDN
        wp_enqueue_script(
            C::THEME_PREFIX . "-swiper-js", // handle
            "//cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js", // src
            NULL, // dependencies
            NULL, // version
            true // in footer?
        );

        global $template; 
        $filename = str_replace( ".php", "", basename( $template ) );

        $scripts = array(
            "build/app.js",
        );

        $dependencies = array( "jquery" );
        
        $has_ajax = false;

        $all_blocks = array_merge( self::_get_block_data(), self::_get_block_data( true ) );
 
        foreach( $all_blocks as $block ) {
            $prefix = isset( $block['acf'] ) && $block['acf'] ? 'acf' : C::THEME_PREFIX;

            // Enqueue front-end scripts for the block if it exists and the block is present on the page
            if(
                (
                    has_block( $prefix . "/" . $block["name"] )
                    // Some templates have hardcoded static blocks, check for those
                    || (
                        isset( C::TEMPLATE_STATIC_BLOCKS[$filename] )
                        && in_array( $block["name"], C::TEMPLATE_STATIC_BLOCKS[$filename] )
                    ) 
                    || U::enhanced_has_block( $prefix . "/" . $block["name"])
                )
                && isset( $block["scripts"] )
                && !is_admin()
                && is_array( $block["scripts"] )
            ) {
                foreach( $block["scripts"] as $script ) {
                   $path = self::_locate_block_script( $script, $block["directory"] );
                   if( $path ) array_push( $scripts, $path );
                }

                if( isset( $block["hasAjax"] ) && $block["hasAjax"] ) $has_ajax = true;
            }
        }

        $js_path =  __DIR__ .  "/build/" . $filename . ".js";
        if( file_exists( $js_path ) ) {
            array_push( $scripts,  "/build/" .  $filename.".js" );
        }
 
        // Dedupe scripts
        $scripts = array_unique( $scripts );
 
        wp_enqueue_script(
            C::THEME_PREFIX . "front-js", // handle
            get_stylesheet_directory_uri() . "/assets/code/script-loader.php?t=text/javascript&s=" . implode( "%7C", $scripts ), // src
            $dependencies, // dependencies
            $assets_file_front["version"], // version
            true // in footer?
        );

        // Localize AJAX data if neeeded
        if( $has_ajax ) {
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
        $assets_file_front = include( __DIR__ . "/build/app.asset.php" );

        $styles = array(
            "build/style-app.css",
            "build/style-gutenberg.css",
        );

        global $template; 
        $filename = str_replace( ".php", "", basename( $template ) );

        $css_path =  __DIR__ .  "/build/" . $filename . ".css";
        if( file_exists( $css_path ) ) {
            array_push( $styles,  "/build/" .  $filename.".css" );
        }
        
        // show as tags
        wp_enqueue_style(
            C::THEME_PREFIX . "-front", // handle,
            get_stylesheet_directory_uri() . "/assets/code/script-loader.php?t=text/css&s=" . implode( "%7C", $styles ), // src
            [], // dependencies
            $assets_file_front["version"] // version
        );
    }

    /**
     * Register all custom block types and enqueue their assets.
     * If a block is dynamic, find it's renderer function.
     * If a block has front-end JS, find it and associate it.
     */
    public static function register_block_types() {
        global $pagenow;
        if( is_admin() && $pagenow !== "post.php" && $pagenow !== "post-new.php" && $pagenow !== "widgets.php") return;

        $assets_file = include( __DIR__ . "/build/gutenberg.asset.php" );

        // Enqueue block editor script
        if( is_admin() )
            wp_enqueue_script(
                C::THEME_PREFIX . "-blocks-admin-js", // handle
                get_stylesheet_directory_uri()."/assets/build/gutenberg.js", // src
                $assets_file["dependencies"], // dependencies
                $assets_file["version"], // version
                false // in footer?
            );

        // Map all blocks to JSON decoded content and preserve the original directory
        $all_blocks = self::_get_block_data();
       
        foreach( $all_blocks as $block ) {
            $args = [
                "attributes"      => $block["settings"]["attributes"],
                "render_callback" => self::_get_block_render_callback( $block ),
            ];

            \register_block_type( C::THEME_PREFIX . "/" . $block["name"], $args );
        }
    }

    /**
     * Register block types for ACF blocks
     */
    public static function register_acf_block_types() {
        $all_blocks = self::_get_block_data( true );

        foreach( $all_blocks as $block ) {
            if(  function_exists( 'acf_register_block_type' )  ) {
                $addl_args = array(
                    'name' => $block['name'],
                    'render_template' => get_stylesheet_directory() . '/assets/' . $block['directory'] . '/template.php',
                );

                // register block
                acf_register_block_type( array_merge( $addl_args, $block["settings"] ) );
            }
        }
    }
    
    /**
     * _get_block_render_callback
     * ---------------------------------------------------------------------------------
     * by default will include render.php file - match function name to render_callback in block.json
     * 2nd option is to use block.mustache file - $attrs and $content are accessible via {{ attrs }} && {{ content }}
     * 3rd option use both mustache and block.php to create a php class to help handle logic for your template - match namespace\class to render_callback in block.json 
     *
     * @param  mixed $block
     * @return void
     */
    public static function _get_block_render_callback($block){
        $renderFile = "render.php";
        $classFile  = "block.php";
        $tplFile    = "block.mustache";

        // If the block is dynamic 
        if( isset( $block["dynamic"] ) && $block["dynamic"] ){
          // Block's Directory
          $blockDir      = get_stylesheet_directory() . '/assets/' . $block["directory"];

          // Classic Render File
          $renderFile    = "{$blockDir}/{$renderFile}";

          // Mustache TPL File
          $tplFile       = "{$blockDir}/{$tplFile}";

          $hasRenderFile = \file_exists( $renderFile );
          $hasTplFile    = \file_exists( $tplFile );

          // TPL will take priority over render file if found
          if( $hasTplFile ) {

            // we"ve found its tpl file, use mustache and pass its attrs 
            return function( array $attrs, string $content ) use ($tplFile, $block, $blockDir, $classFile) {
              $mustache = new Mustache_Engine([
                'entity_flags' => ENT_QUOTES,
                'loader'       => new Mustache_Loader_FilesystemLoader( dirname(__FILE__)."/".$block['directory'] ),
              ]);
              
              $tpl = $mustache->loadTemplate("block");

              $classFile    = "{$blockDir}/{$classFile}";
              $hasClassFile = \file_exists( $classFile );

              // Class file will be passed to template if found 
              if( $hasClassFile ){ 
                require_once($classFile);
                $block = new $block["render_callback"]( $attrs, $content );
                return $tpl->render( $block );
              }else{
                // parse the data through mustache
                return $tpl->render( compact("attrs", "content") );
              }

            };
          }else if( $hasRenderFile ) {
            // we"ve found its renderer, include it and add a render cb:
            include_once( $renderFile );
            return $block["render_callback"];
          }
        }
    }

    /**
     * Register block patterns & their categories from JSON config files
     */
    public static function register_block_patterns() {
        // register cats first incase patters depend on them
        if( file_exists( __DIR__ . '/src/scripts/gutenberg/block-patterns/block-pattern-categories.json' ) ) {
            $block_pattern_categories = json_decode( file_get_contents( __DIR__ . '/src/scripts/gutenberg/block-patterns/block-pattern-categories.json' ), true );

            foreach( $block_pattern_categories as $category ) {
                register_block_pattern_category(  C::THEME_PREFIX . '/' .  $category['title'],  $category['properties'] );
            }
        }

        // register patterns
        if( file_exists(  __DIR__ . '/src/scripts/gutenberg/block-patterns/block-patterns.json' ) ) {
            $block_patterns = json_decode( file_get_contents( __DIR__ . '/src/scripts/gutenberg/block-patterns/block-patterns.json' ), true );
        
            foreach( $block_patterns as $block_pattern ) {
                $block_pattern['properties']['title'] = C::BLOCK_NAME_PREFIX . $block_pattern['properties']['title'];
                register_block_pattern(  C::THEME_PREFIX . '/' . $block_pattern['title'], $block_pattern['properties'] );
            }
        }
    }
}