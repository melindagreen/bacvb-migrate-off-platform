<?php /*
This file contains customization to the admin menu and search systems
*/

namespace MaddenTheme\Library\Admin;

class AdminMenus {
    function __construct () {
        // Add separator
        // FUTURE: The admin_init calls in set_admin_menu_separator directly seem to cause
        //    Gravity Forms to lose saved information in a form settings entry area. Both actions
        //    are commented out here as add is not needed if set is not there. It'd be nice to have
        //    admin separators, but this is the seemingly "official" way to do it, so if it breaks
        //    GF, for now let's just comment it out.
        // add_action( 'admin_init', array( \get_called_class(), 'add_admin_menu_separator' ) );
        // add_action( 'admin_menu', array( \get_called_class(), 'set_admin_menu_separator' ) );

        // Marginalize comments
        add_action( '_admin_menu', array( get_called_class(), 'marginalize_comments' ) );

        // Adjust tag queries
        add_filter( 'parse_query', array( get_called_class(), 'filter_posts_adminlists' ) );

        // Allow search on slug
        add_filter( 'posts_search', array( get_called_class(), 'posts_search' ), 10, 2 );

        // Add tax filters to post table
        add_action( 'restrict_manage_posts', array( get_called_class(), 'add_taxonomy_filters' ) );

        // Make comments less prominent
        add_action( '_admin_menu', array( get_called_class(), 'marginalize_comments' ) );
    }

    /**
    * Admin menu separator
    */
    public static function add_admin_menu_separator ( $position ) {

        global $menu;

        $menu[$position] = array(
            0 => '',
            1 => 'read',
            2 => "separator{$position}",
            3 => '',
            4 => 'wp-menu-separator'
        );
    }

    /**
    * Sets a menu separator
    */
    public static function set_admin_menu_separator () {
        do_action( 'admin_init', 11 );
        do_action( 'admin_init', 35 );
    }

    /**
     * Comments is too prominent - move it to a sub of posts
     */
    public static function marginalize_comments () {
        add_submenu_page( 'edit.php', 'Comments', 'Comments', 'edit_pages', 'edit-comments.php' ); 
        remove_menu_page( 'edit-comments.php' );
    }

    /**
     * Query adjustment for the tag admin table filter
     */
    public static function filter_posts_adminlists ( $query ) {
        global $pagenow;

        if ( $pagenow == 'edit.php' ) {
            $qVars = &$query->query_vars;
            if ( ( isset( $_GET['post_tag'] ) ) && ( $_GET['post_tag'] != '' ) ) {
                $qVars['tag'] = $_GET['post_tag']; 
            } 
        }
    }

    /**
     * Adding slug to page admin table
     */
    public static function manage_pages_columns ( $defaults ) {
        $rhett = array();

        foreach ( $defaults as $k => $v ) {
            $rhett[$k]  = $v;
            if ( $k == 'title' ) {
                $rhett['post_name']  = __( 'Slug' );
            }
        }
        
        return $rhett;
    }

    /**
     * Showing slug value in admin table
     */
    public static function manage_pages_custom_column ( $column_name, $id ) {

        if ( 'post_name' == $column_name ) {
            // build out the whole slug
            $slug = '';
            $ancestors = array_reverse( get_post_ancestors( $id ) );
            foreach ( $ancestors as $a ) {
                $slug .= '/'.get_post_field( 'post_name', $a, 'raw' );
            }
            $slug .= '/'.get_post_field( 'post_name', $id, 'raw' );
        
            // gussy up some nested cpts
            $cpt = get_post_type( $id );
            if ( $cpt == 'neighborhoods' ) {
                $slug = "/explore/{$cpt}{$slug}";
            } else if ( ( $cpt == 'events' ) || ( $cpt == 'meetings' ) || ( $cpt == 'hotels' ) || 
                    ( $cpt == 'things-to-do' ) || ( $cpt == 'explore' ) || ( $cpt == 'dining' ) || ( $cpt == "50fun" ) ) {
                $slug = "/{$cpt}{$slug}";
            }

            echo $slug;
        }
    }
   
    /**
     * Allowing search on slug in admin table view
     * 
     * danke https://wordpress.stackexchange.com/a/233781
     */
    public static function posts_search( $search, $q ) {
        
        global $wpdb;

        // check if we are on the right page & performing a search & for the right post type
        // Nothing to do
        if ( ( !did_action( 'load-edit.php' ) ) || ( !is_admin() ) || ( !$q->is_search() ) || ( !$q->is_main_query() ) ) {
            // MAY EXIT THIS BLOCK
            return $search;
        }
        
        // Get the search input
        $s = $q->get( 's' );

        // Check for "slug:" part in the search input
        if ( 'slug:' === mb_substr( trim( $s ), 0, 5 ) ) {
            // Override the search query 
            $search = $wpdb->prepare(
                " AND {$wpdb->posts}.post_name LIKE %s ",
                str_replace( 
                    [ '**', '*' ], 
                    [ '*',  '%' ],  
                    mb_strtolower( 
                        $wpdb->esc_like( 
                            trim( mb_substr( $s, 5 ) ) 
                        ) 
                    )
                )
            );
            
            // Adjust the ordering
            $q->set( 'orderby', 'post_name' );
            $q->set( 'order', 'ASC' );
        }
        
        return $search;
    }

    
    /**
     * Adding taxonomy filter to post table
     */
    public static function add_taxonomy_filters () {
        $taxonomies = array( 'post_tag' );
    
        foreach ( $taxonomies as $tax_slug ) {
            $tax_obj = get_taxonomy( $tax_slug );
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms( $tax_slug );
            if ( count( $terms ) > 0 ) {
                echo '<select name="'.$tax_slug.'" id="'.$tax_slug.'" class="postform alignleft actions">';
                echo '<option value="">&nbsp;All '.$tax_name.'</option>';
                foreach ( $terms as $term ) {
                    // PENDING this is the full count - need to track down the count for this specific query
                    // $showCount = ( ( isset( $_GET[$tax_slug] ) ) && ( $_GET[$tax_slug] == $term->slug ) ) ? " ( {$term->count} )" : "";
                    $showCount = 0;
                    $selected = ( ( isset( $_GET[$tax_slug] ) ) && ( $_GET[$tax_slug] == $term->slug ) ) ? ' selected' : '';
                    echo '<option value="'.$term->slug.'"'.$selected.'>&nbsp;'.$term->name.$showCount.'&nbsp;&nbsp;&nbsp;&nbsp;</option>';
                }
                echo '</select>';
            }

        }
    }
}