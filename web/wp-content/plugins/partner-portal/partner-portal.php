<?php /**
 * Plugin Name: Partner Portal
 * Version: 1.2.4
 * Description: Listings
 * Author: Madden Media
 * Author URI: https://maddenmedia.com
 * License: Attribution-NonCommercial-NoDerivs 3.0 Unported ( CC BY-NC-ND 3.0)
 *
 * Copyright (c) 2025 Madden Media
 */

namespace PartnerPortal;

require_once( __DIR__.'/admin/AdminConstants.php' );
require_once( __DIR__.'/admin/SettingsLayout.php' );
require_once(__DIR__.'/admin/SettingsAdminLayout.php');
require_once( __DIR__.'/assets/AssetHandler.php' );
require_once( __DIR__.'/library/Constants.php' );
require_once(__DIR__.'/library/Utilities.php');
require_once(__DIR__.'/admin/PageTemplates.php');
require_once(__DIR__.'/admin/ImportPartners.php');
require_once(__DIR__.'/admin/MetaBox.php');

use PartnerPortal\Admin\SettingsLayout as SettingsLayout;
use PartnerPortal\Assets\AssetHandler as AssetHandler;
use PartnerPortal\Library\Constants as Constants;
use PartnerPortal\Library\Utilities as Utilities;
use PartnerPortal\Admin\SettingsAdminLayout as SettingsAdminLayout;
use PartnerPortal\Admin\PageTemplates as PageTemplates;
use PartnerPortal\Admin\ImportPartners as ImportPartners;
use PartnerPortal\Admin\MetaBox as MetaBox;


class PartnerPortal {
    
    private $config_file;
    private $variables;

    function __construct() {
        
        // Init assets handler
        new AssetHandler();

        $this->config_file = $this->get_plugin_file('partner.json');

        // set global vars:
        $strJsonFileContents = file_get_contents( $this->config_file );
        $partnerportalObject = json_decode($strJsonFileContents, true);

        $this->variables = (isset($partnerportalObject['configurations']) && isset($partnerportalObject['configurations']['variables']) && $partnerportalObject['configurations']['variables']) ? $partnerportalObject['configurations']['variables'] : [];

        $default_variables = array(
            'cpt_plural' => '',
            'cpt_singular' => '',
            'import_csv' => '',
            'admin_custom_post_type_menu_pos' => '',
        );
        foreach( $default_variables as $variable_key => $variable_value ){
            if( !(array_key_exists($variable_key, $this->variables) ) ){
                $this->variables[$variable_key] = $variable_value;
            }
        }
 
        if( !$this->variables['cpt_plural'] ){
            $this->variables['cpt_plural'] = Constants::CPT_NAME_PLURAL;
        }
        if( !$this->variables['cpt_singular'] ){
            $this->variables['cpt_singular'] = Constants::CPT_NAME_SINGULAR;
        }
        if( isset($this->variables['admin_custom_post_type_menu_pos']) && !$this->variables['admin_custom_post_type_menu_pos'] || isset($this->variables['admin_custom_post_type_menu_pos'])){
            $this->variables['admin_custom_post_type_menu_pos'] = Constants::ADMIN_CUSTOM_POST_TYPE_MENU_POS;
        }
        if( isset($this->variables['cpt_taxonomies']) && !$this->variables['cpt_taxonomies'] || !isset($this->variables['cpt_taxonomies']) ){
            $this->variables['cpt_taxonomies'] = Constants::CPT_TAXONOMIES;
        }

        // Setup settings page
        add_action( 'admin_init', array( $this, 'setup_settings' ) );
        add_action( 'admin_menu', array( get_called_class(), 'add_settings_page' ) );

        // register our custom post type and taxonomy to show the listings on the front ende
        add_action('init', [ $this, __NAMESPACE__.'\PartnerPortal::add_custom_post_type_taxonomies_and_templates' ] );
        add_action('init', __NAMESPACE__.'\PartnerPortal::add_partner_role' );

        add_filter('theme_page_templates', __NAMESPACE__.'\PartnerPortal::register_plugin_templates');
        add_filter( 'template_include', __NAMESPACE__.'\PartnerPortal::listings_management_template', 99 );

        add_action( 'wp_ajax_nopriv_partnerportal_pagination', __NAMESPACE__.'\PartnerPortal::partnerportal_pagination' );
        add_action( 'wp_ajax_partnerportal_pagination', __NAMESPACE__.'\PartnerPortal::partnerportal_pagination' );            

        // add our related scripts and styles for the templates
        add_action('wp_enqueue_scripts', __NAMESPACE__.'\PartnerPortal::enqueue_template_scripts_and_styles');

        // the settings sections
        add_action('admin_init', [$this,  __NAMESPACE__.'\PartnerPortal::setup_settings_and_edit_sections' ] );  

        add_filter( 'pre_update_option_partnerportal_post_slug', __NAMESPACE__.'\PartnerPortal::myplugin_update_field_foo', 10, 2 );

        add_action( 'admin_init',  __NAMESPACE__.'\PartnerPortal::partner_remove_menu_pages' );

        add_action( 'update_postmeta',  __NAMESPACE__.'\PartnerPortal::partner_updated_postmeta', 10, 4 );
       // add_action( 'wp_insert_post',  __NAMESPACE__.'\PartnerPortal::partner_save_post_function', 10, 3 );

        add_action(  'transition_post_status',   __NAMESPACE__.'\PartnerPortal::partner_status_transitions', 10, 3 );
        add_action(  'delete_post',   __NAMESPACE__.'\PartnerPortal::partner_delete_post', 10, 3 );

        add_filter('gettext', __NAMESPACE__.'\PartnerPortal::change_add_title_text');

    }

    public static function register_plugin_templates ( $theme_templates ) {
        $theme_templates[ 'listings_management_template.php'] = 'Listings Management';        
        return $theme_templates;
    }
    public static function listings_management_template( $template ) {
        $file_name = 'listings-management-template.php';
        if (is_page_template('listings_management_template.php')) {
            if ( locate_template( $file_name ) ) {
                $template = locate_template( $file_name );
            } else {
                // Template not found in theme's folder, use plugin's template as a fallback
                $template = plugin_dir_path( __FILE__ ) . "/partnerportal-theme-files/" . $file_name;
            }
        }
        return $template;
    }        

    public static function get_plugin_file( $fileName ){
        $file = get_stylesheet_directory() . "/" . Constants::PLUGIN_THEME_DIR_SLUG . "/" . $fileName;
        if( !file_exists( $file ) ){
            $file = plugin_dir_path( __FILE__ ) . "/partnerportal-theme-files/" . $fileName;            
        }
        if( file_exists( $file ) ){
          return $file;
        }
        return false;
    }

    public static function partner_updated_postmeta($meta_id, $object_id, $meta_key, $meta_value) {
        $post_type = get_post_type($object_id);
        $is_new = 0;
        if( 'listing' == $post_type || 'event' == $post_type){
            $user = wp_get_current_user(); 
            $midnight = strtotime("today 00:00");
            if ( !in_array( 'administrator', (array) $user->roles ) ) {
            // check if post is published - if so log changea;
                $post_history = json_decode( get_post_meta($object_id, 'partner_changes', true) );
                if( !$post_history ){
                    $post_history = new \stdClass();
                    $post_history->is_new = time();
                    $is_new = 1;
                }
                if( !property_exists($post_history, 'changes')){
                    $post_history->changes = new \stdClass();
                }
                if( !property_exists($post_history->changes, $midnight)){
                    $post_history->changes->$midnight = new \stdClass();
                }
                if($is_new){
                    $post_history->changes->$midnight->is_new_post = 1;
                    update_post_meta($object_id, 'partner_changes', json_encode($post_history));
                }
                $old_value = get_post_meta($object_id, $meta_key, true);
                if( ( $old_value != $meta_value || $is_new ) && "_edit_lock" != $meta_key  && "partner_changes" != $meta_key){
                    if( !property_exists($post_history->changes->$midnight, $meta_key)){
                        $post_history->changes->$midnight->$meta_key = [];
                    }

                    $post_history->changes->$midnight->$meta_key = ['old' => $old_value, 'new' => $meta_value];
                    update_post_meta($object_id, 'partner_changes', json_encode($post_history));
                }
            }
            else{
                //delete_post_meta($object_id, 'partner_changes');
            }
        }
    }
    public static function partner_delete_post( $post_id ) {
        delete_post_meta($post_id, 'partner_changes');
    }

   public static function partner_status_transitions( $new_status, $old_status, $post ) {
        if ( $new_status != $old_status ) {

            if( 'draft' != $new_status ){
                delete_post_meta($post->ID, 'partner_changes');
            }
        }
    }

    public static function partner_save_post_function( $post_ID, $post, $updated ) {
        if( $_POST ){
            $meta = get_post_meta($post_ID);
            foreach( $meta as $key => $existing_arr ){
                $existing_value = $existing_arr[0];
                if( $existing_value != $_POST[$key] ){
                    $match = preg_match( '#partnerportal#' , $key);
                }
            }
        }
    }

    /**
     * Enqueues necessary template scripts and styles
     */
    public static function enqueue_template_scripts_and_styles () {
        // for the main template
        $dir = plugin_dir_url( __FILE__ );
        wp_enqueue_script( 'ajax_pagination',  $dir . 'assets/build/ajax.js', array( 'jquery' ), '1.0', true );
        wp_localize_script( 'ajax_pagination', 'ajax_pagination', array('ajaxurl' => admin_url( 'admin-ajax.php' )));
        if (is_page_template('../partnerportal-theme-files/templates/partnerportal-listings.php')) {
            //check for the files in the theme before loading from the plugin
            $cssFile = '/partnerportal-theme-files/styles/partnerportal-listings.css';
            $filePath = (Utilities::is_file_in_theme($cssFile)) ? get_template_directory_uri().$cssFile : plugin_dir_url( __FILE__ ).'partnerportal-theme-files/styles/partnerportal-listings.css';
            wp_enqueue_style('partnerportal-listings-css', $filePath);

            wp_enqueue_style('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
            wp_enqueue_script('moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true);
            wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true);

            $jsFile = '/partnerportal-theme-files/scripts/partnerportal-listings.js';
            $filePath = (Utilities::is_file_in_theme($jsFile)) ? get_template_directory_uri().$jsFile : plugin_dir_url( __FILE__ ).'partnerportal-theme-files/scripts/partnerportal-listings.js';
            wp_enqueue_script('partnerportal-listings-js', $filePath, array('jquery', 'moment', 'daterangepicker'), '', true);
        }

        if (is_page_template('listings_management_template.php')) {
                        $mgmcssFile = '/partnerportal-theme-files/styles/partnerportal-listings-management.css';            
            $mgmfilePath = (Utilities::is_file_in_theme($mgmcssFile)) ? get_template_directory_uri() . $mgmcssFile : plugin_dir_url( __FILE__ ).'partnerportal-theme-files/styles/partnerportal-listings-management.css';            
            wp_enqueue_style('partnerportal-listings-management-css', $mgmfilePath);

            $jsFile = '/partnerportal-theme-files/scripts/partnerportal-listings.js';
            $filePath = (Utilities::is_file_in_theme($jsFile)) ? get_template_directory_uri().$jsFile : plugin_dir_url( __FILE__ ).'partnerportal-theme-files/scripts/partnerportal-listings.js';
            wp_enqueue_script('partnerportal-mgmt-js', $filePath, array('jquery', 'moment', 'daterangepicker'), '', true);
        }

        //for the singular template
        if (is_singular(Utilities::getPluginPostType())) {
            wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/61fcc94f36.js');

            //check for file in theme first
            $cssFile = '/partnerportal-theme-files/styles/partnerportal-single.css';
            $filePath = (Utilities::is_file_in_theme($cssFile)) ? get_template_directory_uri().$cssFile : plugin_dir_url( __FILE__ ).'partnerportal-theme-files/styles/partnerportal-single.css';
            wp_enqueue_style('partnerportal-single-css', $filePath);

            //include js
            $jsFile = '/partnerportal-theme-files/scripts/partnerportal-single.js';
            $filePath = (Utilities::is_file_in_theme($jsFile)) ? get_stylesheet_directory_uri().$jsFile : plugin_dir_url( __FILE__ ).'partnerportal-theme-files/scripts/partnerportal-single.js';
            wp_enqueue_script('partnerportal-single-js', $filePath, array('jquery'), '', true);
        }
    }
    public static function wpcf_create_temp_column($fields) {
        global $wpdb;
        $matches = 'The';
        $has_the = " CASE 
            WHEN $wpdb->posts.post_title regexp( '^($matches)[[:space:]]' )
            THEN trim(substr($wpdb->posts.post_title from 4)) 
            ELSE $wpdb->posts.post_title 
            END AS title2";
        if ($has_the) {
            $fields .= ( preg_match( '/^(\s+)?,/', $has_the ) ) ? $has_the : ", $has_the";
        }
        return $fields;
    }

    public static function wpcf_sort_by_temp_column ($orderby) {
        $custom_orderby = " UPPER(title2) ASC";
        if ($custom_orderby) {
            $orderby = $custom_orderby;
        }
        return $orderby;
    }    

    public static function get_listings( $params = [] ) {
        if( array_key_exists('params', $_REQUEST ) ){
            $params = $_REQUEST['params'];
        }
        $posts_per_page = -1;
        if( array_key_exists('posts_per_page',$params) ){
            $posts_per_page = $params['posts_per_page'];
        }
        $category_id = "";
        if( array_key_exists('category_id', $params)){
            $category_id = $params['category_id'];
        }
        $date = "";
        if( array_key_exists('date', $params)){
            $date = $params['date'];
        }
        $keyword = "";
        if( array_key_exists('keyword', $params)){
            $keyword = $params['keyword'];
        }
        $orderby = "";
        if( array_key_exists('orderby', $params)){
            $orderby = $params['orderby'];
        }

        $order = "";
        if( array_key_exists('order', $params)){
            $order = $params['order'];
        }

        $post_types = "post";
        if( array_key_exists('post_types', $params)){
            $post_types = $params['post_types'][0];
        }
        $param = array(
            'post_type' => "listing", 
            'posts_per_page' => $posts_per_page, 
            'paged' => $params['paged'], 
            'category_id' => $category_id,
            'date' => $date,
            'orderby'   => $orderby,
            'order' => $order
        );
        if( $keyword ){
            $param['s'] = $keyword;
        }

        if( array_key_exists('listing_categories', $params) && is_array($params['listing_categories']) && count($params['listing_categories'])>0  ){
            $tax_queries = ['relation' => 'OR'];
            foreach( $params['listing_categories'] as $cat ){
                if( !is_numeric($cat)){
                    $categoryObject = get_term_by('slug', strtolower($cat), 'listing_categories'); 
                    //error_log(print_r($categoryObject,true));
                    $cat = $categoryObject->term_id;
                }
                $tax_queries[] = [
                    'taxonomy' => 'listing_categories',
                    'field'    => 'id',
                    'terms'    => $cat
                ];
            }
            $param['tax_query'] = $tax_queries;
        }
        add_filter('posts_fields', __NAMESPACE__.'\PartnerPortal::wpcf_create_temp_column'); // Add the temporary column filter
        add_filter('posts_orderby', __NAMESPACE__.'\PartnerPortal::wpcf_sort_by_temp_column'); // Add the custom order filter


        $query = new \WP_Query( $param );
        remove_filter('posts_fields',__NAMESPACE__.'\PartnerPortal::wpcf_create_temp_column'); // Remove the temporary column filter
        remove_filter('posts_orderby',__NAMESPACE__.'\PartnerPortal::wpcf_sort_by_temp_column'); // Remove the temporary order filter         

        if( count( $query->posts ) > 0 ){
            foreach( $query->posts as $key => $post ){
                $url = get_the_post_thumbnail_url( $post );
                $attachment_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );
                $query->posts[$key]->thumbnail = $attachment_img[0];
                $query->posts[$key]->square_featured = "";
                $meta = get_post_meta($post->ID);
                $square_featured_id = json_decode($meta['partnerportal_gallery_square_featured_image'][0]);
                $square_featured_img = wp_get_attachment_image_src( $square_featured_id[0], 'full' );
                if( $square_featured_img ){
                    $query->posts[$key]->square_featured = $square_featured_img[0];
                }
                $query->posts[$key]->meta = $meta;
                $query->posts[$key]->prettylink = get_permalink($post);
                $query->posts[$key]->object_position = get_field('listings_image_position', $post);
            }
             return $query->posts;
         }
         else{
            return $query;
         }
    }
    public static function partnerportal_pagination( $params = [] ) {
        $query = PartnerPortal::get_listings( $params ); 

        $result = json_decode(json_encode($query), true);
        $response['posts'] = $result;
        echo json_encode( $response );
        exit;
    }   

    public static function add_partner_role(){
        //add_role( 'partner', 'Partner', get_role( 'contributor' )->capabilities );
    }

    public static function myplugin_update_field_foo( $new_value, $old_value ) {
        if( 'simpleview-manual' == $_POST['import-type'] ){
            PartnerPortal::import_from_simpleview();
        die;
        }
        if( 'import-csv' == $_POST['import-csv'][0] ){
            PartnerPortal::import_partners();
        }
        else{

        }
        return;
    }

    public function setup_settings() {
        $variables = $this->variables;

        if(!wp_doing_ajax()) new SettingsAdminLayout( $variables );
    }

    public static function partner_remove_menu_pages() {
        global $user_ID;

        if ( current_user_can( 'partner' ) ) {
            remove_menu_page( 'edit.php' );
        }
    }

    public static function add_settings_page() {
        add_options_page(Constants::PLUGIN_MENU_ADMIN_LABEL,          // page title
                      Constants::PLUGIN_MENU_ADMIN_LABEL,             // menu title
                      'manage_options',                                // capability required by user
                      __NAMESPACE__,                  // slug
                      __NAMESPACE__.'\PartnerPortal::show_settings'    // function
        );

    }

    public static function show_settings() {
        include(plugin_dir_path( __FILE__ ) . 'templates/settings.php');    
    }

    // Clean up label width after output
    public static function custom_admin_js() {
        echo "<script type='text/javascript'>jQuery(document).ready(function($) {
            setTimeout(
                function(){
                    $.each( $('.postbox-container .postbox'), function(i,obj){
                        var objLabelWidth = 0;
                        $.each( $(obj).find('label') ,function(k,iLabel){
                            if( $(iLabel).width() > objLabelWidth){
                                objLabelWidth = $(iLabel).width();
                            }
                        })
                        $(obj).find('label').css('min-width', objLabelWidth).css('text-align','right').css('padding-right','5px');
                    })
            }, 100);
        })</script>";
    } 


    /**
     * Sets up the settings sections
     *
     * Danke https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/
     */
    public function setup_settings_and_edit_sections () {
        $variables = $this->variables;

        if (! wp_doing_ajax()) {
            $latyoutSettings = new SettingsAdminLayout( $variables );
            // meta boxes for listing data
            $config_file = PartnerPortal::get_plugin_file('partner.json' );
            $strJsonFileContents = file_get_contents( $config_file );
            $partnerportalObject = json_decode($strJsonFileContents, true);
            $partnerFormArray = $partnerportalObject['metaBoxes'];

            if( is_array($partnerFormArray) ){
                foreach( $partnerFormArray as $partnerMetaBox ){
                    $partner_input = new MetaBox( $partnerMetaBox );
                }
            }
            add_action('admin_footer', array( get_called_class(), 'custom_admin_js'), 1000);            
        }
    }

    /**
     * Adds the custom taxonomies needed to categorize listing data
     */
    public static function saveAllMetaBox () {
        $strJsonFileContents = file_get_contents( $this->config_file );
        $partnerportalObject = json_decode($strJsonFileContents, true);
        $partnerFormArray = $partnerportalObject['metaBoxes'];
        if( is_array($partnerFormArray) ){
            foreach( $partnerFormArray as $partnerMetaBox ){

            }
        }        
    }

    /**
     * 
     * the following script was pulled from the wp-plugin-simpleview-importer-listings
     */
    public static function import_from_simpleview() {

        global $wpdb;        
        $fieldMappings = get_option(Constants::SV_PLUGIN_SETTING_KEY_MAPPINGS["key"]);
        $fieldMappings = ($fieldMappings) ? json_decode($fieldMappings, JSON_OBJECT_AS_ARRAY) : [];

        // load our data from the cache table
        $query = ($listingId != "")
            ? "SELECT * FROM ".Constants::SV_TABLE_LISTING." WHERE listingid = '{$listingId}'"
            : "SELECT * FROM ".Constants::SV_TABLE_LISTING." WHERE unprocessed = true";
        $listingsRaw = $wpdb->get_results($query, ARRAY_A);  

        foreach ($listingsRaw as $listing) {
            $wpPostInsertData = array();
            $dbListingId = strtolower($fieldMappings["listingid"]);
            $wpPostMetaMapKey = str_replace("[ID]", $listing[$dbListingId], Constants::SV_WP_POST_META_KEY_ID_MAP_META);

            // we can update any existing post with the post id - to get that, we can 
            //  query the post meta table to find that id by our id
            $wpPostId = $wpdb->get_var($wpdb->prepare(
                "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s LIMIT 1", 
                $wpPostMetaMapKey 
            ));
            if ($wpPostId != "") {
                $wpPostInsertData["ID"] = $wpPostId;
            }

            // now build up the rest of the post data
            $postDate = gmdate(Constants::SV_DATETIME_DASH_FORMAT_IN);
            $wpPostInsertData["post_author"] = get_current_user_id();
            $wpPostInsertData["post_date"] = $postDate;
            $wpPostInsertData["post_date_gmt"] = $postDate;
            $wpPostInsertData["post_content"] = $listing[strtolower($fieldMappings["wp_posts"]["post_content"])];
            $wpPostInsertData["post_title"] = $listing[strtolower($fieldMappings["wp_posts"]["post_title"])];
            $wpPostInsertData["post_status"] = "publish";
            $wpPostInsertData["comment_status"] = "closed";
            $wpPostInsertData["ping_status"] = "closed";
            $wpPostInsertData["post_name"] = sanitize_title($listing[strtolower($fieldMappings["wp_posts"]["post_title"])]);
            $wpPostInsertData["post_modified"] = $postDate;
            $wpPostInsertData["post_modified_gmt"] = $postDate;
            $wpPostInsertData["post_parent"] = "0";
            $wpPostInsertData["menu_order"] = "0";
            $wpPostInsertData["post_type"] = Constants::SV_PLUGIN_CUSTOM_POST_TYPE;
            $wpPostInsertData["comment_count"] = "0";

            //
            // now do the post meta
            //
            $query = "SELECT datakey, datavalue FROM ".Constants::SV_TABLE_LISTING_DATA
                ." WHERE listingid = '{$listing[$dbListingId]}'";
            $listingData = $wpdb->get_results($query, ARRAY_A);
            $postMeta = array();

            // add the listing id
            $postMeta[str_replace("[KEY]", $fieldMappings["listingid"], Constants::SV_WP_POST_META_KEY_DETAILS)] = 
                $listing[$dbListingId];

            // and the rest of the data
            foreach ($listingData as $data) {
                $mKey = str_replace("[KEY]", $data["datakey"], Constants::SV_WP_POST_META_KEY_DETAILS);
                $postMeta[$mKey] = Utilities::unserialized_unicode_cleanup($data["datavalue"]);
                $fieldAdjustments = get_option(Constants::SV_PLUGIN_SETTING_FIELD_ADJUSTMENTS["key"]);
                $fieldAdjustments = ($fieldAdjustments)
                    ? json_decode($fieldAdjustments, JSON_OBJECT_AS_ARRAY)
                    : Constants::SV_META_FIELD_ADJUSTMENT_DEFAULT;

                // check for cleanup rules
                if ( (is_array($postMeta[$mKey])) &&
                        (is_array($fieldAdjustments)) &&
                        (isset($fieldAdjustments[$data["datakey"]])) ) {
                    // yup
                    $subwrap = "";
                    foreach ($fieldAdjustments[$data["datakey"]] as $rule) {
                        $arrEl = (isset($rule["subwrap"])) ? $postMeta[$mKey][$rule["subwrap"]] : $postMeta[$mKey];
                        $subwrap = $rule["subwrap"];
                        if ($rule["rule"] == "concatenate") {
                            if (! is_array($arrEl[0])) {
                                $arrEl = array($arrEl);
                            }
                            for ($n=0; $n < count($arrEl); $n++) {
                                foreach ($arrEl[$n] as $ak => $av) {
                                    $newField = "";
                                    foreach ($rule["fields"] as $field) {
                                        $newField .= $arrEl[$n][$field];
                                    }
                                }
                                $arrEl[$n][$rule["rename"]] = $newField;
                            }
                        } else if ($rule["rule"] == "remove") {
                            if (! is_array($arrEl[0])) {
                                $arrEl = array($arrEl);
                            }
                            for ($n=0; $n < count($arrEl); $n++) {
                                foreach ($arrEl[$n] as $ak => $av) {
                                    if (in_array($ak, $rule["fields"])) {
                                        unset($arrEl[$n][$ak]);
                                    }
                                }
                            }
                        }
                        // put it back
                        if (isset($rule["subwrap"])) {
                            $fieldData[$rule["subwrap"]] = $arrEl;
                        } else {
                            $fieldData = $arrEl;
                        }
                        $postMeta[$mKey] = $fieldData;
                    }
                    // get rid of the subwrap now that we are completely done
                    if ($subwrap != "") {
                        $postMeta[$mKey] = $postMeta[$mKey][$subwrap];
                    }
                }
            }

            $wpPostInsertData["meta_input"] = $postMeta;
            $log[] = " - Listing details processed";

            // and insert the post
            $pId = wp_insert_post($wpPostInsertData);
            $log[] = "Listing processed: {$listing[strtolower($fieldMappings["wp_posts"]["post_title"])]}";
            $counter += (! is_wp_error($pId)) ? 1 : 0;

            // now update the guid if we were inserting
            if ($wpPostId == "") {
                $update = wp_update_post(array(
                    "ID" => $pId,
                    "guid" => get_option("siteurl")."/?post_type=".Constants::SV_PLUGIN_CUSTOM_POST_TYPE."&p={$pId}&instance_id="
                ));
            }

            // taxonomy maps
            $listingCats = $wpdb->get_results($wpdb->prepare(
                'SELECT DISTINCT category, taxonomy FROM '.Constants::SV_TABLE_LISTING_TAXONOMY.' WHERE listingid = %s', 
                $listing[$dbListingId]
            ), ARRAY_A);

            // get all the ids for the matching categories
            $taxIds = array();
            foreach ($listingCats as $cat) {
                $termData = get_term_by("name", $cat["category"], $cat["taxonomy"], ARRAY_A);
                $taxIds[$cat["taxonomy"]][] = $termData["term_id"];             
            }
            $log[] = " - Listing taxonomy processed: ".implode(', ', array_keys($taxIds));

            // update the taxonomy map
            foreach ($taxIds as $tax => $ids) {
                wp_set_post_terms($pId, implode(",", $ids), $tax);
            }

            // featured image?
            if (InjectListings::DOWNLOAD_IMAGES) {
                if ($listing["feaduredimageurl"] != null) {
                    InjectListings::setFeaturedImageForPost($pId, $listing["feaduredimageurl"]);
                    $log[] = " - Listing image processed: {$listing["feaduredimageurl"]}";
                }
            }

            // the post id to the simpleview account id, and vice-versa
            update_post_meta($pId, Constants::SV_WP_POST_META_KEY_ACCOUNT_ID, $listing[$dbListingId]);
            update_post_meta($pId, $wpPostMetaMapKey, $pId);        
        }

        // set all to processed
        if ($listingId == "") {
            $wpdb->query("UPDATE ".Constants::SV_TABLE_LISTING." SET unprocessed = false WHERE unprocessed = true");
            $wpdb->query("UPDATE ".Constants::SV_TABLE_LISTING_DATA." SET unprocessed = false WHERE unprocessed = true");
        }


    }

    /**
     * Adds the custom taxonomies needed to categorize listing data
     */
    public static function import_partners() {
        $config_file = PartnerPortal::get_plugin_file('partner.json' );
        $strJsonFileContents = file_get_contents( $config_file );
        $partnerportalObject = json_decode($strJsonFileContents, true);
        $partnerImportConfigurations = $partnerportalObject['configurations']['import'];

        // check if there is a custom name for the import file; if not use 'partners' as default
        $partnerFilename = (array_key_exists('import_filename', $partnerImportConfigurations) && $partnerImportConfigurations['import_filename'] ) ? $partnerImportConfigurations['import_filename'] : 'partners';

       // check if the import file exists in theme; if not use default import file (for testing) in plugin
        $importFile = get_stylesheet_directory() . "/" . Constants::PLUGIN_THEME_DIR_SLUG . "/" . $partnerFilename . ".csv";
        if( !file_exists( $importFile ) ){
            $importFile = plugin_dir_url( __FILE__ ) . $partnerFilename . ".csv";            
        }

        // get array of all meta fields
        $partnerFormArray = $partnerportalObject['metaBoxes'];

        $row = 1;

        // $uniqueKeys is an array of column header titles (from the import file); these values will define a unique Listing record 
        $uniqueKeys = $partnerImportConfigurations['unique_keys'];
        
        $post_title_key = $partnerImportConfigurations['post_title_key'];
        $post_title_column = get_column_by_key( $post_title_key, $partnerFormArray );

        // create array of the unique columns :: ['column title' =>  'column #']
        $uniqueColumns = [];
        foreach( $partnerFormArray as $metaBox ){
            foreach( $metaBox['inputs'] as $inputElement ){
                if( in_array( $inputElement['key'], $uniqueKeys) ){
                    $uniqueColumns[$inputElement['key']] = $inputElement['column'];
                }
            }
        }


        $user_email_column = "";
        if( $partnerImportConfigurations['author']['assign'] ){
            $user_email_key = $partnerImportConfigurations['author']['email_key'];
            $user_email_column = get_column_by_key( $user_email_key, $partnerFormArray );
        }

        // TAXONOMIES
        $taxonomies = [];
        $variables = $partnerportalObject['configurations']['variables'];
 
        if( !$variables['cpt_taxonomies'] ){
            $variables['cpt_taxonomies'] = Constants::CPT_TAXONOMIES;
        }
        foreach( $variables['cpt_taxonomies'] as $tax_name => $cpt_taxonomy ){
            $cats = [];
            $cat_args = array(
                'hide_empty' => FALSE,
                'taxonomy' => $tax_name,
                'orderby' => 'name',
                'order'   => 'ASC'
            );
            $cat_objects = get_categories($cat_args);
            foreach($cat_objects as $cat_object){
                $cats[$cat_object->name] = $cat_object;
            }
            // first condition is for backwards comaptibility where there was a single constant TAXONOMY
            if( 'listing_categories' == $tax_name && $partnerImportConfigurations['category_columns'] ){
                $category_columns_array =  $partnerImportConfigurations['category_columns'];
            }
            else{
                $category_columns_array = $cpt_taxonomy['columns']; 
            }
            $taxonomies[$tax_name] = [
                "categories" => $cats,
                "category_columns_array" => $category_columns_array
            ];
        }

        if (($handle = fopen( $importFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                if( $row > 1 ){
                    $num = count($data);

                    $params = [];
                    $update_if_exists =  $partnerImportConfigurations['update_if_exists'];
                    if( $update_if_exists ){

                        foreach($uniqueColumns as $key => $uniqueColumn){
                            $params[] = [
                                'key' => Constants::WP_POST_META_KEY_PREPEND . $key,
                                'value' => $data[$uniqueColumn - 1]
                            ];
                        }
                        $listing = get_listing( $params );
                        $postID = $listing ? $listing->ID : 0;
                    }
                    else{
                        $postID = 0;
                    }


                    // iterate over all the meta input fields; get the column number (defined in partner.json); 
                    // get the value from the csv data row and add to $meta_inputs, an array of [{{meta field key}} => {{value_from_csv}} ]   
                    foreach( $partnerFormArray as $metaBox ){
                        foreach( $metaBox['inputs'] as $input ){
                            $column = $input['column'];
                            $value = $data[$column - 1];
                            if( array_key_exists('type',$input) && 'date' == $input['type'] && strtotime( $value ) > 0){
                                $value = date( 'Y-m-d', strtotime( $value ) );
                            }
                            $key = Constants::WP_POST_META_KEY_PREPEND . $input['key'];
                            if( array_key_exists('importType', $input) && "array" == $input['importType'] ){
                                $checkbox_choices = $input['choices'];
                                $value_array = explode( ",", $value);
                                $update_post_meta_array = [$key =>[] ];
                                foreach($value_array as $val){
                                    foreach($checkbox_choices as $choice){
                                        if( str_replace(' ', '', $val) == str_replace(' ', '', $choice['label'])){
                                            $update_post_meta_array[$key][] = $choice['key'];
                                        }
                                    }
                                }
                            }
                            $meta_inputs[$key] = $value;
                        }
                    }
   

                    if( $partnerImportConfigurations['author']['assign'] ){
                        $geo_url_string = "";
                        $geo_params = $partnerImportConfigurations['geo_keys'];
                        foreach( $geo_params as $url_param => $geo_param){
                            $column_number = get_column_by_key( $geo_param, $partnerFormArray );
                            $geo_url_string .= "&" . $url_param . "=" . urlencode($data[$column_number - 1]);
                        }
                        
                        $url = "https://api.geocod.io/v1.6/geocode?api_key=f8bdaa783bdba2af51d28f888f12d65d165dfff" . $geo_url_string . "&limit=1";
                        $response = wp_remote_get( $url );
                        $code = wp_remote_retrieve_response_code( $response );

                        // We got a response!
                        if ( $code === 200 ) {
                            $body = json_decode( wp_remote_retrieve_body( $response ), true );
                            if ( isset( $body["results"][0]["location"]["lat"] ) ) {
                                $meta_inputs[ Constants::WP_POST_META_KEY_PREPEND .  "latitude"] = floatval( $body["results"][0]["location"]["lat"] );
                            }
                            if ( isset( $body["results"][0]["location"]["lng"] ) ) {
                                $meta_inputs[Constants::WP_POST_META_KEY_PREPEND . "longitude"] = floatval( $body["results"][0]["location"]["lng"] );
                            }
                        }
                    }

                    // get admin email, check if user exists, if not create user (Username is this unique record's post title)
                    $userID = null; 
                    if( $user_email_column ){
                        $user_email = $data[$user_email_column - 1];
                    }
                    if( $user_email_column && $user_email ){
                        $user = get_user_by_email( $user_email );
                        if( $user ){
                            $userID = $user->ID;
                        }
                        else{
                            $login = $data[$post_title_column - 1];
                            $user_args = [
                                'user_login' => $login,
                                'user_pass' => PartnerPortal::generateRandomString(),
                                'user_email' => $user_email,
                                'role' => 'contributor'
                            ];

                            $userID = wp_insert_user( $user_args );
                        }
                    }

                    // create listing from csv row
                    $postarr = [
                        'ID' => $postID,
                        'post_type' => 'listing',
                        'post_status' => 'publish',
                        'post_title' => $data[$post_title_column - 1],
                        'meta_input' => $meta_inputs,
                        'post_author' => $userID
                    ];
                    $postID = wp_insert_post( $postarr );

                    // set featured image from id if included
                    if( array_key_exists('featured_image_id_column',$partnerImportConfigurations) ){
                        // get id from column
                        $featured_id_column = $partnerImportConfigurations['featured_image_id_column'];
                        $featured_id = $data[$featured_id_column - 1];
                        set_post_thumbnail( $postID, $featured_id );
                    }
                    // update those post meta from arrays:
                    if( is_array($update_post_meta_array) ){
                        foreach( $update_post_meta_array as $meta_key => $meta_array){
                            update_post_meta($postID, $meta_key, $meta_array);
                        }
                    }
                    foreach( $taxonomies as $taxonomy_name => $taxonomy ){

                        $category_columns_array = $taxonomy['category_columns_array'];
                        $cats_string = "";
                        foreach($category_columns_array as $category_column){
                            $cats_string .= "," . $data[$category_column - 1];
                        }
                        $cat_ids = [];
                        $cats_array = explode(",", $cats_string);

                        if( is_array($cats_array) && count($cats_array) > 0 ){
                            foreach($cats_array as $category_name){
                                $cat_id = "";
                                if( array_key_exists( $category_name , $cats ) ){
                                    $cat_id = $cats[$category_name]->term_id;
                                }
                                else{
                                    if( $category_name ){
                                        $new_cat_array = wp_insert_term( $category_name , 'listing_categories');
                                    //error_log('new cat array on insert');
                                    //error_log(print_r($new_cat_array,true));

                                        if( is_object($new_cat_array) && property_exists( $new_cat_array,  'error_data') ){
                                            $cat_id = $new_cat_array->error_data['term_exists'];
                                           // error_log('term exists' . $cat_id);

                                        }
                                        else{
                                            $cat_id = $new_cat_array['term_id'];
                                            $cats[$new_cat_array['name']] = $new_cat_array;
                                        }

                                    }
                                }
                                if( $cat_id ){
                                    $cat_ids[] = $cat_id;
                                }
                            }
                            $output = wp_set_object_terms( $postID, $cat_ids, $taxonomy_name );
                        }

                    }
                }
                $row++;
            }
            fclose($handle);
        }
    }   
    /**
     */
    public static function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }  

    public static function change_add_title_text( $input ) {

        global $post_type;

        if( is_admin() && 'Add title' == $input && 'listing' == $post_type )
            return 'Add Business Listing Name...';

        return $input;
    }

    /**
     * Adds the custom taxonomies needed to categorize listing data
     */
    public function add_custom_post_type_taxonomies_and_templates () {

        $strJsonFileContents = file_get_contents( PartnerPortal::get_plugin_file('partner.json' ));
        $partnerportalObject = json_decode($strJsonFileContents, true);
        $partnerConfigurations = array_key_exists( 'configurations', $partnerportalObject ) ? $partnerportalObject['configurations'] : [];
        $variables = ( isset($partnerConfigurations['variables']) && $partnerConfigurations['variables'] ) ? $partnerConfigurations['variables'] : [];

        $partnerCategories = (array_key_exists('required_categories', $partnerConfigurations) ) ? $partnerConfigurations['required_categories'] : [];
        $disableFeatured = (array_key_exists('disable_featured_image', $partnerConfigurations) ) ? $partnerConfigurations['disable_featured_image'] : false;

        $singular = $this->variables['cpt_singular'];
        $plural = $this->variables['cpt_plural'];
        $cpt_taxonomies = $this->variables['cpt_taxonomies'];

        // before we do this, check if either post type or taxonomy already exists
        if (post_type_exists(Utilities::getPluginPostType())) {
            add_action('admin_notices', function() {
                $class = "notice notice-error is-dismissible";
                $message = __( $plural ." is trying to use a post type that is already in use (".Utilities::getPluginPostType()."). This will result in unpredictable behavior and should be resolved.");
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
            });
        }
        foreach ($cpt_taxonomies as $taxonomy => $info) {
            if (taxonomy_exists($taxonomy)) {
                add_action('admin_notices', function() {
                    $class = "notice notice-error is-dismissible";
                    $message = __("{$info["plural"]} is trying to use a taxonomy that is already in use ({$taxonomy}). This will result in unpredictable behavior and should be resolved.");
                    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
                });
            }
        }

        // set up the custom post type
        $labels = array(
            'name' => _x( $plural, 'post type general name'),
            'singular_name' => __($singular, 'post type singular name'),
            'add_new' => __('Add New '.$singular),
            'add_new_item' => __('Add New '.$singular),
            'edit_item' => __('Edit '.$singular),
            'new_item' => __('New '.$singular),
            'view_item' => __('View '.$singular),
            'search_items' => __('Search '.$singular),
            'not_found' =>  __('No '.$plural.' found'),
            'not_found_in_trash' => __('No '.$plural.' found in Trash'),
            'parent_item_colon' => ''
        );

        $thumbnail = "thumbnail";
        if( $disableFeatured ){
            $thumbnail = "";
        }

        $args = array(
            'label'               => $plural,
            'description'         => $plural,
            'labels'              => $labels,
            'rest_base'           => 'listings',
            'show_in_rest'        => true,
            'supports'            => array('title', 'editor', 'template', $thumbnail, 'excerpt','author'),
            'hierarchical'        => true,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => Constants::ADMIN_CUSTOM_POST_TYPE_MENU_POS,
            'menu_icon'              => 'dashicons-store',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'taxonomies'          => array(implode(array_keys($cpt_taxonomies))),
            'publicly_queryable'  => true,
            'map_meta_cap'           => true,
            'rewrite' => array('slug' => Utilities::getPluginPostType(), 'with_front' => false)            
        );
        $obj = register_post_type(Utilities::getPluginPostType(), $args);

        // now the taxonomies
        foreach ($cpt_taxonomies as $taxonomy => $info) {
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
            // now add it
            register_taxonomy(
                $taxonomy,
                Utilities::getPluginPostType(),
                array(
                    'hierarchical' => true,
                    'labels' => $labels,
                    'show_in_rest' => true,
                    'rest_base' => $taxonomy,
                    'query_var' => true, 
                    'show_admin_column' => $info["showAdminColumn"],
                    'capabilities' => array('manage_options', 'edit_posts'),
                    'rewrite' => array(
                        'slug' => $taxonomy,
                        'with_front' => false,
                        'hierarchical' => true
                    )
                )
            );
        }

        foreach( $partnerCategories as $partnerCategory ){

            wp_insert_term( $partnerCategory['name'] , $partnerCategory['taxonomy'], $partnerCategory);

        }
        // initialize the PageTemplate class, this will do all the work to setup and render the templates
        $pageTemplates = new PageTemplates();
    }    
}


function get_column_by_key( $key, $partnerFormArray = null ){
    if(!$partnerFormArray){
        $strJsonFileContents = file_get_contents( $this->config_file );
        $partnerportalObject = json_decode($strJsonFileContents, true);
        $partnerFormArray = $partnerportalObject['metaBoxes'];        
    }
    foreach( $partnerFormArray as $metaBox ){
        foreach( $metaBox['inputs'] as $inputElement ){
            if( $key == $inputElement['key'] ){
                return $inputElement['column'];
            }
        }
    }
}
 function get_listing ( $params = [] ) {
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'listing',
        'meta_query' => array(
            'relation' => 'AND'
        )
    );
    foreach( $params as $param ){
        $args['meta_query'][] = array(
            'key' => $param['key'],
            'value' => $param['value'],
            'compare' => '=',
        );
    }

    $query = new \WP_Query($args);
    if ( $query->have_posts() ){
        while ( $query->have_posts() ){
            $query->the_post();
            return $query->post;
        }
    } 
}

$PartnerPortal = new PartnerPortal();   
