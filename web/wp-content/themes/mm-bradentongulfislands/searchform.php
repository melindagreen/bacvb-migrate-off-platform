<?php /**
 * Template piece: Search Form
 * 
 * This is the search form rendered when the WP function get_search_form is called.
 */

use \MaddenNino\Library\Constants as C; ?>

<!-- The search overlay-->
<form class="search-form search-form--open" method="get" role="search" action="<?php echo home_url( '/' ); ?>">
    <label>
        <span class="sr-only"><?php _e( "Search", 'mmnino' ); ?></span>
        <input class='search-form__field' type="search" name="s" placeholder="Search" value="<?php echo get_search_query(); ?>">
    </label>

    <button class="search-form__submit <?php echo C::THEME_PREFIX ?>-button" type="submit"><img src="<?php echo get_theme_file_uri() ?>/assets/images/icons/search.png" alt="<?php _e( 'Search', 'mmnino' ); ?>" width="18px" height="18px"></button>
</form>