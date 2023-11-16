<?php 
/**
 * Filters for the listings grid
 * 
 * The following pre-filtering GET parameters are available:
 * listings_search : string : A text search query
 * listings_start/listings_end : any valid format for 
 *  https://www.php.net/manual/en/function.strtotime.php : Start and end dates
 * listings_term : int/string : A term slug or ID for the category/location filter
 */
namespace MaddenNino\Blocks\ListingsGrid;
/**
 * Renders the grid filters
 * @param array $attrs          An associative array of block attributes
 * @param string $filter_tax    Name of the taxonomy to filter by
 * @return string               HTML string representing the filters
 */
function render_grid_filter( $attrs, $filter_tax ) {
    // prep labels and pre-filters
    $pre_filter_cat = false;
    $filter_tax_label = false;
    switch( $attrs['postType'] ) {
        case 'posts':
        case 'listing':
        case 'event':
            $filter_tax_label = 'Categories';
            break;
    }

    if( 
        isset( $attrs['preFilterCat'] ) 
        && $attrs['preFilterCat'] !== 'none'
        && isset( $attrs['postType'] ) 
        && (
            $attrs['postType'] === 'listing' ||
            $attrs['postType'] === 'page' ||
            $attrs['postType'] === 'event'
        )
    ) {
        $pre_filter_cat = get_term_by( 'slug', $attrs['preFilterCat'], $filter_tax );
    }
    
    $filter_terms = get_terms( array(
        'taxonomy' => $filter_tax,
        'child_of' => $pre_filter_cat ? $pre_filter_cat->term_id : '',
    ) );

    ?> 
    
    <!-- filters -->
    <form id="filter-form" class="filters">

        <?php if( isset( $attrs['postType'] ) && $attrs['postType'] === 'event' ):?>
        <!-- filter controls -->
        <div class="date-controls">
            <div class="control control--start-date">
                <label for="control__input--start-date" class="control__label control__label--start-date">
                    <?php _e( 'Start Date' ); ?>
                </label>

                <input
                    type="text"
                    name="event_first_date_time"
                    class="control__input"
                    id="control__input--start-date"
                    placeholder="Select Start Date"
                    autocomplete="off"
                    value="<?php echo isset( $_GET['listings_start'] ) ? date( 'F j, Y', strtotime( $_GET['listings_start'] ) ) : ''; ?>"
                />
            </div>

            <div class="control control--end-date">
                <label for="control__input--end-date" class="control__label control__label--end-date">
                    <?php _e( 'End Date' ); ?>
                </label>

                <input
                    type="text"
                    name="event_last_date_time"
                    class="control__input"
                    id="control__input--end-date"
                    placeholder="Select End Date"
                    autocomplete="off"
                    value="<?php echo isset( $_GET['listings_end'] ) ? date( 'F j, Y', strtotime( $_GET['listings_end'] ) ) : ''; ?>"
                />
            </div>
        </div>

        <div class="control control--search">
            <label for="control__input--search" class="control__label control__label--search">
                <?php echo 'Search' ?>
            </label>
            <div class="form-icon-overlay">
                <input
                    type="text"
                    name="search"
                    id="control__input--search"
                    class="control__input"
                    placeholder="Search By Name"
                    value="<?php echo $_GET['listings_search'] ?: ''; ?>"
                />
                <!-- <i class="fas fa-search submit-filter"></i> -->
                <button class="submit-filter"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/search.png" alt="Search icon"></button>
            </div>
        </div>
        <?php endif;?>

        <div class="check-controls">
            <div class="filterWrap">
                <?php if( $filter_tax && $filter_tax_label ) { ?>
                    <div class="control control--categories <?php if( count( $filter_terms ) < 1 ) echo "hide"; ?>">
                        <h4 class="control__title"><?php _e( 'Filters', 'mmnino' ); ?></h4>

                        <div class="categoriesWrap">
                            <label for="control__input--categories" class="control__label control__label--categories all">
                                <input
                                    type="checkbox"
                                    id="control__input--categories-all" 
                                    class="control__input control__input--categories control-input--checkbox" 
                                    name="<?php echo $filter_tax === 'category' ? 'categories' : $filter_tax; ?>"
                                    value="<?php echo $pre_filter_cat ? $pre_filter_cat->term_id : ''; ?>"
                                    <?php checked( !isset( $_GET['listings_term'] ) ); ?>
                                />
                                <span class="control__text"><?php _e( 'All', 'mmnino' ); ?></span>
                            </label>
                            <?php foreach ( $filter_terms as $cat ) { ?>
                            <label for="control__input--categories" class="control__label control__label--categories">
                                <input
                                    type="checkbox"
                                    id="control__input--categories" 
                                    class="control__input control__input--categories control-input--checkbox" 
                                    name="<?php echo $filter_tax === 'category' ? 'categories' : $filter_tax; ?>"
                                    value="<?php echo $cat->term_id; ?>"
                                />
                                <span class="control__text"><?php echo $cat->name; ?></span>
                            </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <!--/ end filter controls -->
    </form>
    <!--/ end filters -->
    <?php
}
