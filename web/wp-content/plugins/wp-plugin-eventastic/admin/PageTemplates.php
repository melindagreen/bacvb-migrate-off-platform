<?php

namespace Eventastic\Admin;

/**
 * Page Template Class
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/../library/Utilities.php');
use Eventastic\Library\Utilities as Utilities;

class PageTemplates {
    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    public function __construct() {

        $this->templates = array();


        // Add a filter to the attributes metabox to inject template into the cache.
        if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
            // 4.6 and older
            add_filter(
                'page_attributes_dropdown_pages_args',
                array( $this, 'register_project_templates' )
            );

        } else {
            // Add a filter to the wp 4.7 version attributes metabox
            add_filter(
                'theme_page_templates', array( $this, 'add_new_template' )
            );
        }

        // Add a filter to the save post to inject out template into the page cache
        add_filter(
            'wp_insert_post_data',
            array( $this, 'register_project_templates' )
        );


        // Add a filter to the template include to determine if the page has our
        // template assigned and return it's path
        add_filter(
            'template_include',
            array( $this, 'view_project_template')
        );


        // Add your templates to this array.
        $this->templates = array(
            '../eventastic-theme-files/templates/eventastic-events.php' => 'Eventastic Events Page',
        );
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     *
     */
    public function add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_project_templates( $atts ) {
        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, $this->templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }

    /**
     * Checks if the template is assigned to the page
     * @param string $template              The original template
     * @return string                       The modified template
     */
    public function view_project_template( $template ) {

        // Get global post
        global $post;

        // Return template if post is empty
        if ( !$post || is_search() || 
            // or if we don't have a custom one defined
            ( !isset( $this->templates[get_post_meta(
                $post->ID, '_wp_page_template', true
            )] ) ) && $post->post_type != Utilities::getPluginPostType()
        ) {
            return $template;
        }

        //check if this is a single event
        if ( $post->post_type == Utilities::getPluginPostType() && is_singular() ) {
            $template = locate_template( array( 'eventastic-theme-files/templates/eventastic-single.php' ) );
            return $template;
            if ( !$template) {
                $file = plugin_dir_path( __FILE__ ).'../eventastic-theme-files/templates/eventastic-single.php';
                // Just to be safe, we check if the file exist first
                if ( file_exists( $file ) ) {
                    return $file;
                } else {
                    echo $file;
                }
            }
        }

        // Check theme for a template, use that instead of the plugin template if it exists
        $template = locate_template( array( 'eventastic-theme-files/templates/eventastic-events.php' ) );

        // If we didn't find a template, load the plugin template
        if ( !$template) {
            $file = plugin_dir_path( __FILE__ ). get_post_meta(
                $post->ID, '_wp_page_template', true
            );

            // Just to be safe, we check if the file exist first
            if ( file_exists( $file ) ) {
                return $file;
            } else {
                echo $file;
            }
        }
	    
        // Return template
        return $template;
    }
}
