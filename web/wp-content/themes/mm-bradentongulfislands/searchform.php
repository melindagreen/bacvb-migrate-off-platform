<?php /**
 * Template piece: Search Form
 * 
 * This is the search form rendered when the WP function get_search_form is called.
 */

use \MaddenNino\Library\Constants as C; 

$aria_label = (empty($args['aria_label'])) ? 'search_form' : $args['aria_label'];
$search_color = empty($args['search_color']) ? '' : '-' . $args['search_color'];
?>

<!-- The search overlay-->
<form class="search-form" method="get" role="search" action="<?php echo home_url( '/' ); ?>" id="<?php echo $aria_label ?>" aria-label="<?php echo esc_attr($aria_label); ?>">
    <label>
        <span class="sr-only"><?php _e( "Search", 'mmnino' ); ?></span>
        <input id="<?php echo $aria_label ?>_input" class='search-form__field' type="search" name="s" placeholder="Search" value="<?php echo get_search_query(); ?>">
    </label>

    <button id="<?php echo $aria_label ?>_submit" class="search-form__submit <?php echo C::THEME_PREFIX ?>-button" type="submit"><img src="<?php echo get_theme_file_uri() ?>/assets/images/icons/search<?= $search_color; ?>.png" alt="<?php _e( 'Search', 'mmnino' ); ?>" width="18px" height="18px"></button>
</form>