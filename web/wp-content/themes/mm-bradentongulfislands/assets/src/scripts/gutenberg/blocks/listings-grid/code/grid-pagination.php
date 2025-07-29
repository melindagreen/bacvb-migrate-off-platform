<?php 
/**
 * Mobile pagination for the listings grid
 */

namespace MaddenNino\Blocks\ListingsGrid;

/**
 * Renders the grid pagination
 * @param array $listings_query		The query args for all listings
 * @return string               	HTML string representing the pagination buttons
 */
function render_grid_pagination( $listings_query ) {
    ?>
        <!-- mobile pagination -->
        <div class="pagination pagination__footer" data-current-page="1">
            <button type="button" class="pagination__button pagination__button--first" data-page="1" disabled>
                <span class="sr-only"><?php _e( 'First' ); ?></span>
                <span class="arrow">&lsaquo;&lsaquo;</span>
            </button> 
            
            <button type="button" class="pagination__button pagination__button--prev" data-page="1" disabled>
                <span class="sr-only"><?php _e( 'Previous' ); ?></span>
                <span class="arrow">&lsaquo;</span>
            </button> 
            
            <p class="counts show">
                <span class="count__page-start">1</span>-<span class="count__page-end">
                <?php echo $listings_query->found_posts < $page_size ? $listings_query->found_posts : $page_size; ?>
                </span> of <span class="count__page-total"><?php echo $listings_query->found_posts; ?></span>
            </p>

            <p class="pagination__loading">
                <span class="sr-only"><?php _e( 'loading' ); ?></span>
            </p>
            
            <button type="button" class="pagination__button pagination__button--next" data-page="2" <?php disabled( $listings_query->max_num_pages < 2 ); ?>>
                <span class="sr-only"><?php _e( 'Next' ); ?></span>
                <span class="arrow">&rsaquo;</span>
            </button>

            <button type="button" class="pagination__button pagination__button--last" data-page="<?php echo $listings_query->max_num_pages ?>" <?php disabled( $listings_query->max_num_pages < 2 ); ?>>
                <span class="sr-only"><?php _e( 'Last' ); ?></span>
                <span class="arrow">&rsaquo;&rsaquo;</span>
            </button>
        </div>
        <!--/ end mobile pagination -->
    <?php
}