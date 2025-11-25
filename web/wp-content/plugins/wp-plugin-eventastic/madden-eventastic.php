<?php

namespace Eventastic;

/**
 * Plugin Name: Eventastic
 * Version: 2.0.3
 * Description: A DMO-centric events plugin brought to you by Madden Media
 * Author: Madden Media
 * Author URI: https://maddenmedia.com
 * License: GNU GENERAL PUBLIC LICENSE
 *
 * Developer note: I am attempting to use single quotes and underscore function names here
 *    to play nicely with how WordPress does things. *breathes heavily into paper bag*
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */
// include library files this plugin requires
require_once(__DIR__.'/admin/MetaBoxAddress.php');
require_once(__DIR__.'/admin/MetaBoxContactInformation.php');
require_once(__DIR__.'/admin/MetaBoxDates.php');
require_once(__DIR__.'/admin/MetaBoxGallery.php');
require_once(__DIR__.'/admin/MetaBoxPrice.php');
require_once(__DIR__.'/admin/MetaBoxEventsPage.php');
require_once(__DIR__.'/admin/SettingsAdminLayout.php');
require_once(__DIR__.'/admin/TaxonomyMetaFieldsVenue.php');
require_once(__DIR__.'/admin/PageTemplates.php');
require_once(__DIR__.'/library/Constants.php');
require_once(__DIR__.'/library/Encoding.php');
require_once(__DIR__.'/library/Query.php');
require_once(__DIR__.'/library/Utilities.php');

// the front-facing render functions
require_once(__DIR__.'/functions/render.php');

use Eventastic\Admin\MetaBoxAddress as MetaBoxAddress;
use Eventastic\Admin\MetaBoxContactInformation as MetaBoxContactInformation;
use Eventastic\Admin\MetaBoxDates as MetaBoxDates;
use Eventastic\Admin\MetaBoxGallery as MetaBoxGallery;
use Eventastic\Admin\MetaBoxPrice as MetaBoxPrice;
use Eventastic\Admin\MetaBoxEventsPage as MetaBoxEventsPage;
use Eventastic\Admin\SettingsAdminLayout as SettingsAdminLayout;
use Eventastic\Admin\TaxonomyMetaFieldsVenue as TaxonomyMetaFieldsVenue;
use Eventastic\Admin\PageTemplates as PageTemplates;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\Encoding as Encoding;
use Eventastic\Library\Query as Query;
use Eventastic\Library\Utilities as Utilities;


/**
 * Main plugin class - will be instantiated after declaration within this file
 */
class Eventastic {

    /**
     * Constructor
     */
    function __construct () {

        $cpt = Utilities::getPluginPostType();

        // admin menu actions during wordpress initialization
        add_action('admin_menu', __NAMESPACE__.'\Eventastic::admin_actions');

        // register our custom post type and taxonomy to show the listings on the front end
        add_action('init', __NAMESPACE__.'\Eventastic::add_custom_post_type_taxonomies_and_templates');

        // register our custom post type and taxonomy to show the listings on the front end
        add_action('init', __NAMESPACE__.'\Eventastic::add_eventastic_blocks');

        // add our related scripts and styles
        add_action('admin_enqueue_scripts', __NAMESPACE__.'\Eventastic::enqueue_admin_scripts_and_styles');

        // add our related scripts and styles for the templates
        add_action('wp_enqueue_scripts', __NAMESPACE__.'\Eventastic::enqueue_template_scripts_and_styles');

        // the settings sections
        add_action('admin_init', __NAMESPACE__.'\Eventastic::setup_settings_and_edit_sections');

        add_action('admin_init', __NAMESPACE__.'\Eventastic::setup_settings_and_edit_sections');
//                    do_action( 'updated_option', string $option, mixed $old_value, mixed $value )

        // the table headers for our listings
        add_filter('manage_'.$cpt.'_posts_columns', __NAMESPACE__.'\Eventastic::add_table_headers');

        // make tables sortable
        add_filter('manage_edit-'.$cpt.'_sortable_columns', __NAMESPACE__.'\Eventastic::eventastic_sortable_columns');
        add_action( 'pre_get_posts', __NAMESPACE__.'\Eventastic::events_posts_orderby' );

        add_action('manage_'.$cpt.'_posts_custom_column', __NAMESPACE__.'\Eventastic::add_table_content', 10, 2);

        // do lat/lng lookups via ajax
        add_action('wp_ajax_eventastic_lookup_lat_lng',  __NAMESPACE__.'\Eventastic::eventastic_lookup_lat_lng');

        // resolves a query issue: set end date to start date if no end date exists
        add_action( 'wp_after_insert_post', __NAMESPACE__.'\Eventastic::update_end_date', 90, 4 );
        
        // meta fields for taxonomies
        foreach (Constants::PLUGIN_TAXONOMIES as $tax => $fields) {
            if ( (isset($fields["customFieldsClass"])) && (class_exists($fields["customFieldsClass"])) ) {
                $ptc = new $fields["customFieldsClass"]();
            }
        }

        add_action( 'wp_ajax_nopriv_get_events_date_ordered', [ new Query, 'get_events_date_ordered'] );
        add_action( 'wp_ajax_get_events_date_ordered',  [ new Query, 'get_events_date_ordered'] );        

        add_action( 'admin_init',  __NAMESPACE__.'\Eventastic::gravityFormCreate' );

        // set past events to noindex
        add_filter( 'wp_robots', __NAMESPACE__.'\Eventastic::past_events_noindex' );

    }


    public static function event_submission_cleanup ( $post_id, $feed, $entry, $form ) {    
        foreach ( $form['fields'] as &$field ) {
            if ( strpos( $field->cssClass, 'eventastic_recurring_days_v2' ) !== false || strpos( $field->cssClass, 'eventastic_recurring_weeks' ) !== false ) {
                $selected_choices = $field->get_value_export( $entry );
                if ( ! empty( $selected_choices ) && ! empty( $field->adminLabel ) ) {
                    $values = explode( ', ', $selected_choices );
                    update_post_meta( $post_id, $field->adminLabel, $values );
                    update_post_meta( $post_id, 'eventastic_repeat_type', 'month' );
                }
            }
        }
        /*
        if( $patternDates = (new Utilities)->convertRecurringEvents( $postData ) ){
            update_post_meta( $post_id, 'eventastic_pattern_dates',  $patternDates );
          $postData[MetaBoxDates::META_KEY_PATTERN_DATES["key"]] = $patternDates;
          $postData[MetaBoxDates::META_KEY_REPEAT_DATES["key"]] = null;
        } 
        */       
    }


    public static function gravityFormCreate(){
        if ( is_plugin_active( 'gravityforms/gravityforms.php' ) && is_plugin_active('gravityformsadvancedpostcreation/advancedpostcreation.php')) {
            add_action( 'gform_advancedpostcreation_post_after_creation', __NAMESPACE__.'\Eventastic::event_submission_cleanup', 10, 4 );
        }                
    }
    /**
     * Sets up the settings sections
     *
     * Danke https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/
     */
    public static function setup_settings_and_edit_sections () {

        if (! wp_doing_ajax()) {
            // settings
            $latyoutSettings = new SettingsAdminLayout();
            // meta boxes for event data
            $mbDates = new MetaBoxDates();
            $mbContact = new MetaBoxContactInformation();
            $mbPrice = new MetaBoxPrice();
            $mbAddress = new MetaBoxAddress();
            $mbGallery = new MetaBoxGallery();
            // meta box for template
            $mbGallery = new MetaBoxEventsPage();

        }
    }


    /**
     * Adds the custom taxonomies needed to categorize listing data
     */
    public static function add_custom_post_type_taxonomies_and_templates () {

        // before we do this, check if either post type or taxonomy already exists
        if (post_type_exists(Utilities::getPluginPostType())) {
            add_action('admin_notices', function() {
                $class = "notice notice-error is-dismissible";
                $message = __(Constants::PLUGIN_NAME_PLURAL." is trying to use a post type that is already in use (".Utilities::getPluginPostType()."). This will result in unpredictable behavior and should be resolved.");
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
            });
        }
        foreach (Constants::PLUGIN_TAXONOMIES as $taxonomy => $info) {
            if (taxonomy_exists($taxonomy)) {
                add_action('admin_notices', function() {
                    $class = "notice notice-error is-dismissible";
                    $message = __("{$info["plural"]} is trying to use a taxonomy that is already in use ({$taxonomy}). This will result in unpredictable behavior and should be resolved.");
                    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
                });
            }
        }

        $supports =  array('title', 'editor', 'template', 'excerpt');
        if( Utilities::getFeaturedMode() ){
            $supports[] = 'thumbnail';
        }

        // set up the custom post type
        $labels = array(
            'name' => _x(Constants::PLUGIN_NAME_PLURAL, 'post type general name'),
            'singular_name' => __(Constants::PLUGIN_NAME_SINGULAR, 'post type singular name'),
            'add_new' => __('Add New '.Constants::PLUGIN_NAME_SINGULAR),
            'add_new_item' => __('Add New '.Constants::PLUGIN_NAME_SINGULAR),
            'edit_item' => __('Edit '.Constants::PLUGIN_NAME_SINGULAR),
            'new_item' => __('New '.Constants::PLUGIN_NAME_SINGULAR),
            'view_item' => __('View '.Constants::PLUGIN_NAME_SINGULAR),
            'search_items' => __('Search '.Constants::PLUGIN_NAME_SINGULAR),
            'not_found' =>  __('No '.Constants::PLUGIN_NAME_PLURAL.' found'),
            'not_found_in_trash' => __('No '.Constants::PLUGIN_NAME_PLURAL.' found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'label'               => Constants::PLUGIN_NAME_PLURAL,
            'description'         => Constants::PLUGIN_NAME_PLURAL,
            'labels'              => $labels,
            'show_in_rest'        => true,
            'supports'            => $supports,
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => Constants::ADMIN_CUSTOM_POST_TYPE_MENU_POS,
            // 'menu_icon'           => plugins_url(Constants::MENU_ICON),
            'menu_icon'              => 'dashicons-calendar-alt',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'taxonomies'          => array(implode(array_keys(Constants::PLUGIN_TAXONOMIES)), 'post_tag'),
            'publicly_queryable'  => true,
            'map_meta_cap'           => true,
            'rewrite' => array('slug' => Utilities::getPluginPostType(), 'with_front' => false)            
        );
        register_post_type(Utilities::getPluginPostType(), $args);
        $pluginTaxonomies = Constants::PLUGIN_TAXONOMIES;
        if( !Utilities::getVenueMode() ){
            unset( $pluginTaxonomies['eventastic_venues'] );
        }
        if( !Utilities::getOrganizerMode() ){
            unset( $pluginTaxonomies['eventastic_organizers'] );
        }
        // now the taxonomies
        foreach ($pluginTaxonomies as $taxonomy => $info) {
            $labels = array(
                'name' => _x($info["plural"], 'post type general name'),
                'singular_name' => __($info["plural"], 'post type singular name'),
                'add_new' => __('Add New '.$info["single"]),
                'add_new_item' => __('Add New '.$info["single"]),
                'edit_item' => __('Edit '.$info["single"]),
                'new_item' => __('New '.$info["single"]),
                'view_item' => __('View '.$info["single"]),
                'search_items' => __('Search '.$info["single"]),
                'not_found' =>  __('No '.$info["plural"].' found'),
                'not_found_in_trash' => __('No '.$info["plural"].' found in Trash'),
                'parent_item_colon' => ''
            );
            $register_taxonomy_array = [
                'hierarchical' => true,
                'labels' => $labels,
                'show_in_rest' => true,
                'query_var' => true,
                'show_admin_column' => $info["showAdminColumn"],
                'capabilities' => array('manage_options', 'edit_posts'),
                'rewrite' => array(
                    'slug' => $taxonomy,
                    'with_front' => false,
                    'hierarchical' => true
                )
            ];

            if( !Utilities::getCategoryLocation() ){
                add_filter( 'rest_prepare_taxonomy', function( $response, $taxonomy, $request ){
                    $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
                    $data_response = $response->get_data();
                    if( "eventastic_categories" == $data_response['slug'] ){
                        $data_response['visibility']['show_ui'] = false;
                        $response->set_data( $data_response );
                            add_meta_box('eventastic_categoriesdiv', __('Categories'), 'post_categories_meta_box', 'event', 'normal', 'low', array( 'taxonomy' => 'eventastic_categories' ));
                    }
                    return $response;
                }, 10, 3 );
            }        

            // now add it
            register_taxonomy(
                $taxonomy,
                Utilities::getPluginPostType(),
                $register_taxonomy_array
            );
        }

        // initialize the PageTemplate class, this will do all the work to setup and render the templates
        $pageTemplates = new PageTemplates();
    }

    /**
     * Sets up the dedicated admin menu for this plugin    on the left side bar with sub links
     */
    public static function admin_actions () {

      $post_type = Utilities::getPluginPostType();

       // and our options
       add_submenu_page(
        "edit.php?post_type={$post_type}",
        Constants::PLUGIN_MENU_ADMIN_LABEL . 'Settings',
        'Settings',
        'manage_options',
        'eventastic-settings',
        __NAMESPACE__.'\Eventastic::show_settings' 
      );
    }
    
    /**
     * Resolves a query issue: set end date to start date if no end date exists
     */
    public static function update_end_date( $post_id, $post, $update, $post_before ) {
        if( 'event' == $post->post_type  ){
            $meta = get_post_meta($post_id);
            if( array_key_exists('eventastic_start_date', $meta) && (!array_key_exists('eventastic_end_date', $meta) || !$meta['eventastic_end_date'][0])){
                update_post_meta( $post_id, 'eventastic_end_date', $meta['eventastic_start_date'][0]);            
            }
        }
    }
    /**
     * Permalink adjustment for custom taxonomies
     */
    public static function filter_post_type_link () {

        if ($post->post_type != Utilities::getPluginPostType()) {
            // MAY EXIT THIS BLOCK
            return $link;
        }
        foreach (array_keys(Constants::PLUGIN_TAXONOMIES) as $taxonomy) {
            if ($cats = get_the_terms($post->ID, $taxonomy)) {
                $link = str_replace('%'.$taxonomy, array_pop($cats)->slug, $link);
            }
        }

        return $link;
    }

    /**
     * Enqueues necessary admin scripts and styles
     */
    public static function enqueue_admin_scripts_and_styles () {

        // media uploader
        wp_enqueue_media();

        // other ui management resources
        wp_enqueue_style(Utilities::getPluginPostType(), plugin_dir_url( __FILE__ ).'styles/eventastic-admin.css');
        wp_enqueue_style('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
        wp_enqueue_style('font-awesome-v5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');
        wp_enqueue_script('mm-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true);
        wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true);
        wp_enqueue_script('validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js', array(), '', true);

        wp_enqueue_script('fullcalendar_full', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js', array(), '', true);

        $jsAjaxFile = '/eventastic-theme-files/scripts/eventastic-ajax.js';
        $ajaxFilePath = ( Utilities::is_file_in_theme( $jsAjaxFile ) ) ? get_stylesheet_directory_uri().$jsAjaxFile : plugin_dir_url( __FILE__ ).'eventastic-theme-files/scripts/eventastic-ajax.js';
        wp_enqueue_script('eventastic-ajaxscript', $ajaxFilePath, ['jquery']);        
                $variable_to_js = [
            'ajax_url' => admin_url('admin-ajax.php')
        ];
        wp_localize_script('eventastic-ajaxscript', 'Eventastic_Variables', $variable_to_js);
      }

    /**
     * Enqueues necessary template scripts and styles
     * @return null
     */
    public static function enqueue_template_scripts_and_styles () {
        // for the main template
        if ( is_page_template( '../eventastic-theme-files/templates/eventastic-events.php' )  || is_post_type_archive( Utilities::getPluginPostType() ) ) {
            //check for the files in the theme before loading from the plugin
            $cssFile = '/eventastic-theme-files/styles/eventastic-events.css';
            $filePath = ( Utilities::is_file_in_theme( $cssFile ) ) ? get_stylesheet_directory_uri().$cssFile : plugin_dir_url( __FILE__ ).'eventastic-theme-files/styles/eventastic-events.css';
            wp_enqueue_style( 'eventastiv-events-css', $filePath );

            wp_enqueue_style( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
            wp_enqueue_script( 'mm-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true );
            wp_enqueue_script( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true );

            $jsFile = '/eventastic-theme-files/scripts/eventastic-events.js';
            $filePath = ( Utilities::is_file_in_theme( $jsFile ) ) ? get_stylesheet_directory_uri().$jsFile : plugin_dir_url( __FILE__ ).'eventastic-theme-files/scripts/eventastic-events.js';
            wp_enqueue_script( 'eventastic-events-js', $filePath, array( 'jquery', 'mm-moment', 'daterangepicker' ), '', true );
            wp_localize_script( 'eventastic-events-js', 'wp_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }

        // for the singular template
        if ( is_singular( Utilities::getPluginPostType() ) ) {
            wp_enqueue_style('font-awesome-v5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');

            //check for file in theme first
            $cssFile = '/eventastic-theme-files/styles/eventastic-single.css';
            $filePath = ( Utilities::is_file_in_theme( $cssFile ) ) ? get_stylesheet_directory_uri().$cssFile : plugin_dir_url( __FILE__ ).'eventastic-theme-files/styles/eventastic-single.css';
            wp_enqueue_style( 'eventastiv-single-css', $filePath );

            //include js
            $jsFile = '/eventastic-theme-files/scripts/eventastic-single.js';
            $filePath = ( Utilities::is_file_in_theme( $jsFile ) ) ? get_stylesheet_directory_uri().$jsFile : plugin_dir_url( __FILE__ ).'eventastic-theme-files/scripts/eventastic-single.js';
            wp_enqueue_script( 'eventastic-single-js', $filePath, array( 'jquery' ), '', true );
        }
        $jsAjaxFile = '/eventastic-theme-files/scripts/eventastic-ajax.js';
        $ajaxFilePath = ( Utilities::is_file_in_theme( $jsAjaxFile ) ) ? get_stylesheet_directory_uri().$jsAjaxFile : plugin_dir_url( __FILE__ ).'eventastic-theme-files/scripts/eventastic-ajax.js';
        wp_enqueue_script('eventastic-ajaxscript', $ajaxFilePath, ['jquery']);
        $variable_to_js = [
            'ajax_url' => admin_url('admin-ajax.php')
        ];
        wp_localize_script('eventastic-ajaxscript', 'Eventastic_Variables', $variable_to_js);
    }

    /**
     * The ajax listener for loading a lat/lng combo for an address
      */
    public static function eventastic_lookup_lat_lng () {

        global $wpdb;

        // what we will hand back to the item that called us
        $returnData = array(
            'status' => 'OK',
            'msg' => '',
            'data' => array()
        );

        // security
        $nonce = wp_verify_nonce($_POST["_wpnonce"], Constants::NONCE_ROOT.$_POST['eventasticid']);

        if (! $nonce) {
            $returnData['status'] = 'ERROR';
            $returnData['status'] = 'There was a security violation';
        } else {
            // data?
            $address = Utilities::getVar("address", "POST");
            if (! $address) {
                $returnData["status"] = "ERROR";
                $returnData["msg"] = "Missing address";
            } else {
                $latLng = Utilities::getLatLngFromAddress($address);
                $returnData["data"]["lat"] = $latLng["lat"];
                $returnData["data"]["lng"] = $latLng["lng"];
            }
        }

        // here ya go
        echo json_encode($returnData);

        // this is required to terminate immediately and return a proper response
        wp_die();
    }

    /**
     * Gets the current information for the plugin
     */
    public static function get_plugin_info () {

        return get_plugin_data(__FILE__);
    }

    /**
     * Show the settings
     */
    public static function show_settings () {

        include(plugin_dir_path( __FILE__ ) . 'templates/settings.php');
    }

    /**
     * The table headers for the main view
     *
     * @param array    $defaults The default table headers
     */
    public static function add_table_headers ($defaults) {

        $newHeaders = array();

        foreach($defaults as $key => $title) {
            // add our custom ones in before the date
            if ($key == 'date') {
                $newHeaders[Constants::TABLE_VIEW_START_DATE["key"]] = Constants::TABLE_VIEW_START_DATE["label"];
                $newHeaders[Constants::TABLE_VIEW_END_DATE["key"]] = Constants::TABLE_VIEW_END_DATE["label"];
                //$newHeaders[Constants::TABLE_VIEW_DATE_RANGE["key"]] = Constants::TABLE_VIEW_DATE_RANGE["label"];
                $newHeaders[Constants::TABLE_VIEW_RECURRENCE["key"]] = Constants::TABLE_VIEW_RECURRENCE["label"];
            }
            // add the default as well
            $newHeaders[$key] = $title;
        }

        return $newHeaders;
    }

    /**
     * Make columns sortable
     *
     * @param array    $defaults The default columns
     */
    public static function eventastic_sortable_columns( $columns ) {
        $columns[Constants::TABLE_VIEW_START_DATE["key"] ] = Constants::TABLE_VIEW_START_DATE["key"];
        $columns[Constants::TABLE_VIEW_END_DATE["key"] ] = Constants::TABLE_VIEW_END_DATE["key"];
        return $columns;
    }

    /**
     * Set Past events to noindex
     *
     * @param array    $robots default robots config
     */
    public static function past_events_noindex($robots){

        global $post;
        if ($post && 'event' == $post->post_type ){
            $meta = eventastic_get_event_meta($post->ID);
            $endDate = (isset($meta['end_date'])) ? strtotime($meta['end_date']) : false; //convert to time so we can use the date() function for rendering
            if( !$endDate ){
                $endDate = (isset($meta['start_date'])) ? strtotime($meta['start_date']) : '';
            }
            if( $endDate && $endDate < time() ){
                $robots['noindex'] = true;
                $robots['follow'] = true;                
            }
        }
        return $robots;
    }        

    /**
     * Update query based on sortable column selected
     */
    public static function events_posts_orderby( $query ) {
        if( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }
        if ( 'start_date' === $query->get( 'orderby') ) {
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_key', 'eventastic_start_date' );
            $query->set( 'meta_type', 'DATE' );
        }
        if ( 'end_date' === $query->get( 'orderby') ) {
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_key', 'eventastic_end_date' );
            $query->set( 'meta_type', 'DATE' );
        }        
    }

    /**
     * The table content for the custom headers in the main view
     *
     * @param string $column_name The custom column name
     * @param string $post_id The current post in the loop
     */
    public static function add_table_content ($column_name, $post_id) {

        // add the date range
        if ($column_name == Constants::TABLE_VIEW_DATE_RANGE["key"]) {
            $startDate = get_post_meta($post_id, MetaBoxDates::META_KEY_START_DATE["key"], true);
            $endDate = get_post_meta($post_id, MetaBoxDates::META_KEY_END_DATE["key"], true);
            // format 'em
            $outSD = (! empty($startDate))
                ? date(_x(Constants::DATE_FORMAT_PURDY, 'Event date format', 'textdomain'), strtotime($startDate))
                : "";
            $outED = (! empty($endDate))
                ? date(_x(Constants::DATE_FORMAT_PURDY, 'Event date format', 'textdomain'), strtotime($endDate))
                : "";
            // output logic
            if ( ($outSD == "") && ($outED == "") ) {
                echo "No specificed range";
            } else if ( ($outSD != "") && ($outED == "") ) {
                echo $outSD;
            } else if ( ($outSD != "") && ($outED != "") ) {
                echo ($outSD == $outED)
                    ? $outSD
                    : "{$outSD} - {$outED}";
            }
        }
        if ($column_name == Constants::TABLE_VIEW_START_DATE["key"]) {
            $startDate = get_post_meta($post_id, MetaBoxDates::META_KEY_START_DATE["key"], true);
            $outSD = (! empty($startDate))
                ? date(_x(Constants::DATE_FORMAT_PURDY, 'Event date format', 'textdomain'), strtotime($startDate))
                : "";
            echo $outSD;
        }
        if ($column_name == Constants::TABLE_VIEW_END_DATE["key"]) {
            $endDate = get_post_meta($post_id, MetaBoxDates::META_KEY_END_DATE["key"], true);
            $outED = (! empty($endDate))
                ? date(_x(Constants::DATE_FORMAT_PURDY, 'Event date format', 'textdomain'), strtotime($endDate))
                : "";
            echo $outED;
        }
        // and recurrence
        if ( Utilities::getRecurrenceVersion() ){
        }
        else{
            if ($column_name == Constants::TABLE_VIEW_RECURRENCE["key"]) {
                $doesRecur = get_post_meta($post_id, MetaBoxDates::META_KEY_END_DATE_LOGIC["key"], true);
                if ($doesRecur == MetaBoxDates::META_KEY_END_DATE_LOGIC["choices"][1]["key"]) {
                    $recurrence = get_post_meta($post_id, MetaBoxDates::META_KEY_WEEKDAYS["key"], true);
                    $frequency = get_post_meta($post_id, MetaBoxDates::META_KEY_FREQUENCY["key"], true);
                    echo implode(", ", $recurrence);
                    echo "<br/><i>".MetaBoxDates::META_KEY_FREQUENCY["options"][$frequency]."</i>";
                } else {
                    echo "Does not recur";
                }
            }
        }
    }

    public static function convert_recursion_update(  ){
        // This is checking if your form is submitted
        if ( (isset($_POST['submit']) && "ConvertRecurring" == $_POST['submit'] && $_POST['eventastic_recurrence_cleanup'] ) || isset($_GET['eventastic_recurrence_cleanup'] ) ){
            $delete = $_GET['eventastic_recurrence_delete_old'] ? $_GET['eventastic_recurrence_delete_old'] : true;
            $posts_per_page = $_GET['eventastic_recurrence_cleanup'] ? $_GET['eventastic_recurrence_cleanup'] : -1;
            $usingV2 = get_option(SettingsAdminLayout::SETTING_PLUGIN_RECURRENCE_V2["key"]);
            if( $usingV2 ){
                $query = new \WP_Query(array(
                    'post_type' => 'event',
                    'posts_per_page' => $posts_per_page,
                    'meta_query' => array(
                        array(
                             'key'     => 'eventastic_recurring_days',
                             'compare' => 'EXISTS'
                        ),
                    )
                ));
                $days = [
                    "Sunday" => 0,
                    "Monday" => 1,
                    "Tuesday" => 2,
                    "Wednesday" => 3,
                    "Thursday" => 4,
                    "Friday" => 5,
                    "Saturday" => 6
                ];

                while ($query->have_posts()) {
                    $query->the_post();
                    $post_id = get_the_ID();
                    $postData = get_postdata($post_id);
                    $meta = get_post_meta($post_id);
                    $eventastic_recurring_days_v2 = null;
                    $eventastic_recurring_weeks = null;
                    $recurWeek = $meta['eventastic_recurring_repeat'][0];
                    if( !$recurWeek ){
                        $recurDaysInput = unserialize( $meta['eventastic_recurring_days'][0] );
                        $recurDaysArr = [];
                        if( is_array($recurDaysInput ) ){
                            foreach( $recurDaysInput as $recurDay ){
                                $recurDaysArr[] = $days[$recurDay];
                            }
                        }
                        $recurDays = implode("," , $recurDaysArr );                        
                        $repeat_pattern = '{"freq":"weekly","days":[' . $recurDays . ']}';
                        $convertArgs = [
                            "eventastic_recurrence_options" => "pattern",
                            "eventastic_repeat_type" => "month",

                            "eventastic_recurring_weeks" => 'null',
                            "eventastic_repeat_pattern" => $repeat_pattern,

                            "eventastic_start_date" => $meta['eventastic_start_date'][0],
                            "eventastic_end_date" => $meta['eventastic_end_date'][0]
                        ];
                        update_post_meta($post_id, 'eventastic_recurrence_options', 'pattern');
                        update_post_meta($post_id, 'eventastic_repeat_type', 'month');
                        update_post_meta($post_id, 'eventastic_repeat_pattern', $repeat_pattern);
                        update_post_meta($post_id, 'eventastic_recurring_days_v2', $eventastic_recurring_days_v2);
                        update_post_meta($post_id, 'eventastic_recurring_weeks', 'null');                        
                    }
                    else{
                        $eventastic_recurring_weeks =  $recurWeek;
                        $eventastic_recurring_days_v2 = unserialize( $meta['eventastic_recurring_days'][0] );

                        $convertArgs = [
                            "eventastic_recurrence_options" => "pattern",
                            "eventastic_repeat_type" => "month",
                            "eventastic_repeat_pattern" => 'custom',

                            "eventastic_recurring_days_v2" => $eventastic_recurring_days_v2,
                            "eventastic_recurring_weeks" => $eventastic_recurring_weeks,

                            "eventastic_start_date" => $meta['eventastic_start_date'][0],
                            "eventastic_end_date" => $meta['eventastic_end_date'][0]
                        ];
                        update_post_meta($post_id, 'eventastic_recurrence_options', 'pattern');
                        update_post_meta($post_id, 'eventastic_repeat_type', 'month');
                        update_post_meta($post_id, 'eventastic_repeat_pattern', 'custom');
                        update_post_meta($post_id, 'eventastic_recurring_days_v2', $eventastic_recurring_days_v2);
                        update_post_meta($post_id, 'eventastic_recurring_weeks', $eventastic_recurring_weeks);                        
                    }

                    $patternDates = (new Utilities)->convertRecurringEvents( $convertArgs );
                    update_post_meta($post_id, 'eventastic_pattern_dates', $patternDates );

                    if( $delete ){
                        delete_post_meta( $post_id , 'eventastic_recurring_days');
                        delete_post_meta( $post_id , 'eventastic_recurring_repeat');
                    }
                    wp_reset_postdata();
                }
            }        
        }
    }

    public static function add_eventastic_blocks() {
/*        wp_register_script(
          'wp-plugin-eventastic-blocks',
            plugin_dir_url( __FILE__ ) . "/build.js",
            array( 'wp-blocks', 'wp-element', 'wp-data' )
        );
*/
        add_action('admin_init', __NAMESPACE__.'\Eventastic::convert_recursion_update');

        $json = __DIR__ . "/blocks/eventastic-calendar/build/block.json";
        $block =  register_block_type(  $json, array() );
    }
   
}
// off you go
new Eventastic();

?>
