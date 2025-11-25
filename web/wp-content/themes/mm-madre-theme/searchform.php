<?php

// $rid = md5( uniqid( rand(), true )); 

?>

<!-- The search overlay-->
<form class="search-form" method="get" role="search" action="<?php echo home_url( '/' ); ?>">
    <label>
        <span class="sr-only"><?php _e( "Search this site", 'mmnino' ); ?></span>
        <input class='search-form__field' type="search" name="s" placeholder="Search" value="<?php echo get_search_query(); ?>">
    </label>

    <button class="search-form__submit <?php echo \MaddenMadre\Library\Constants::THEME_PREFIX ?>-button" type="submit"><?php _e( 'Search', 'mmnino' ); ?></button>
</form>