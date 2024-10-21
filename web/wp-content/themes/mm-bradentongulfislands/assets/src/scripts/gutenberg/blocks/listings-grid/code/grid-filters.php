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
    $exclude_cat = false;
    switch( $attrs['postType'] ) {
        case 'posts':
        case 'listing':
        case 'event':
            $filter_tax_label = 'Categories';
            break;
    }

    // prefiltered cat
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

    $allCats = '';
    foreach ($filter_terms as $cat_slug) {
        $allCats .= $cat_slug->term_id.',';
    }

    // categories to exclude
    if( 
        isset( $attrs['excludeCat'] ) 
        && $attrs['excludeCat'] !== 'none'
        && isset( $attrs['postType'] ) 
        && (
            $attrs['postType'] === 'listing' ||
            $attrs['postType'] === 'event'
        )
    ) {
        $exclude_cat = get_term_by( 'slug', $attrs['excludeCat'], $filter_tax );
    }
    $removeCat = '';
    if ($exclude_cat && !is_wp_error($exclude_cat)) {
        $removeCat = $exclude_cat->term_id;
    }

    // Remove the exclude category id from $allCats
    $categoriesArray = explode(',', $allCats);
    
    // Find the index of the category to remove
    $index = array_search($removeCat, $categoriesArray);
    
    // If the category is found, remove it from the array
    if ($index !== false) {
        unset($categoriesArray[$index]);
    }
    
    // Convert the array back to a comma-separated string
    $updatedCats = implode(',', $categoriesArray);
    $updatedCats = rtrim($updatedCats, ',');

    ?> 
    
    <div class="filterContainer <?php echo isset( $attrs['postType'] ) && $attrs['postType'] === 'event' ? 'is-style-collage-square' : ''; ?>">
        <!-- filters -->
        <?php if( isset( $attrs['postType'] ) && $attrs['postType'] === 'event' ):?>
        <h2 class="grid-title"><?php echo $attrs['listingsTitle']; ?></h2>
        <?php endif; ?>
        <form id="filter-form" class="filters">

            <?php if( isset( $attrs['postType'] ) && $attrs['postType'] === 'event' ):?>
            <!-- filter controls -->
            <label for="control__input--categories" class="control__label control__label--categories all">
            <input
                type="checkbox"
                id="control__input--categories-all" 
                class="control__input control__input--categories control-input--checkbox <?php echo $attrs['filterType'] === 'categories' ? 'control__input--catscheck' : ''; ?>" 
                name="<?php echo $filter_tax === 'category' ? 'categories' : $filter_tax; ?>"
                value="<?php echo $pre_filter_cat ? $pre_filter_cat->term_id : ''; ?>"
                <?php checked( !isset( $_GET['listings_term'] ) ); ?>
                />
            <span class="control__text"><?php _e( 'All', 'mmnino' ); ?></span>
            </label>
            <label for="control__input--categories" class="events control__label control__label--categories exclude">
                <input
                type="checkbox"
                id="control__input--categories-exclude" 
                class="control__input control__input--categories control-input--checkbox" 
                name="eventastic_categories_exclude"
                value="<?php 
                echo $removeCat;
                ?>"
                <?php checked( !isset( $_GET['listings_term'] ) ); ?>
                />
            </label>
            <div class="date-controls">
                <div class="control control--start-date">
                    <label for="control__input--start-date" class="control__label control__label--start-date">
                        <?php _e( 'Filter By Date' ); ?>
                    </label>
                    <div>
                    <input
                        type="text"
                        name="eventastic_start_date"
                        class="control__input"
                        id="control__input--start-date"
                        placeholder="Select Start Date"
                        autocomplete="off"
                        value="<?php echo isset( $_GET['eventastic_start_date'] ) ? date( 'F j, Y', strtotime( $_GET['eventastic_start_date'] ) ) : ''; ?>"
                    />
                    <input
                        type="text"
                        name="eventastic_end_date"
                        class="control__input"
                        id="control__input--end-date"
                        placeholder="Select End Date"
                        autocomplete="off"
                        value="<?php echo isset( $_GET['eventastic_end_date'] ) ? date( 'F j, Y', strtotime( $_GET['eventastic_end_date'] ) ) : ''; ?>"
                    />
                    </div>
                </div>

            </div>

            <div class="control control--search">
                <label for="control__input--search" class="control__label control__label--search">
                    <?php echo 'Event Name' ?>
                </label>
                <div class="form-icon-overlay">
                    <input
                        type="text"
                        name="search"
                        id="control__input--search"
                        class="control__input"
                        placeholder="Search By Name"
                        value="<?php echo isset($_GET['listings_search']) ? $_GET['listings_search'] : ''; ?>"
                    />
                    <!-- <i class="fas fa-search submit-filter"></i> -->
                    <button class="submit-filter"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/search.png" alt="Search icon"></button>
                </div>
            </div>
            <?php endif;?>

            <?php if( isset( $attrs['postType'] ) && $attrs['postType'] === 'listing' ):?>
            <div class="check-controls">
                <div class="filterWrap">
                    <?php if( $filter_tax ) { ?>
                    <div class="control control--categories">

                        <?php if(count($filter_terms) > 0 || $attrs['filterType'] != 'categories') { ?>
                        <h4 class="control__title"><?php _e( 'Filters', 'mmnino' ); ?></h4>
                        <?php } ?>

                        <div class="categoriesWrap">
                            <label for="control__input--categories" class="control__label control__label--categories all">
                                <input
                                    type="checkbox"
                                    id="control__input--categories-all" 
                                    class="control__input control__input--categories control-input--checkbox <?php echo $attrs['filterType'] === 'categories' ? 'control__input--catscheck' : ''; ?>" 
                                    name="<?php echo $filter_tax === 'category' ? 'categories' : $filter_tax; ?>"
                                    value="<?php echo $pre_filter_cat ? $pre_filter_cat->term_id : ''; ?>"
                                    <?php checked( !isset( $_GET['listings_term'] ) ); ?>
                                />
                                <span class="control__text"><?php _e( 'All', 'mmnino' ); ?></span>
                            </label>

                            <?php if($attrs['filterType'] == 'accommodations' || $attrs['map']) { 
                               if(!$attrs['map']) {
                            ?>
                            <label class='control__label control__label--categories control__label--accomodations'>
                                <input
                                    class='control__input control__input--categories control__input--checkbox'
                                    type='checkbox'
                                    value="beachfront"
                                    name="accomodations-location"
                                />
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/beachfront.png" class="icon" alt="Beachfront icon">
                                <span class='control__text'>Beachfront</span>
                            </label>
                            <label class='control__label control__label--categories control__label--accomodations'>
                                <input
                                    class='control__input control__input--categories control__input--checkbox'
                                    type='checkbox'
                                    value="waterfront"
                                    name="accomodations-location"
                                />
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/waterfront.png" class="icon" alt="Waterfront icon">
                                <span class='controsl__text'>Waterfront</span>
                            </label>
                            <?php } ?>
                            <label class='control__label control__label--categories control__label--accomodations'>
                                <input
                                    class='control__input control__input--categories control__input--checkbox'
                                    type='checkbox'
                                    value='pet-friendly'
                                    name="accomodations-facility-amenities"
                                />
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/pet-friendly.png" class="icon" alt="Pet friendly icon">
                                <span class='control__text'>Pet Friendly</span>
                            </label>
                            <label class='control__label control__label--categories control__label--accomodations'>
                                <input
                                    class='control__input control__input--categories control__input--checkbox'
                                    type='checkbox'
                                    value='on-site-dining'
                                    name="accomodations-facility-amenities"
                                />
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/on-site-dining.png" class="icon" alt="Dining icon">
                                <span class='control__text'>On-site Dining</span>
                            </label>
                            <label class='control__label control__label--categories control__label--accomodations'>
                                <input
                                    class='control__input control__input--categories control__input--checkbox'
                                    type='checkbox'
                                    value='eco-friendly'
                                    name="accomodations-facility-amenities"
                                />
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icons/eco-friendly.png" class="icon" alt="Eco friendly icon">
                                <span class='control__text'>Eco Friendly</span>
                            </label>
                            <?php } ?>
                            
                            <?php if ($attrs['filterType'] == 'room-count') { ?>
                                <label class='control__label control__label--categories'>
                                    <input
                                        class='control__input control__input--categories control__input--checkbox'
                                        type='checkbox'
                                        value="25-100"
                                        name="rooms"
                                    />
                                    <span class='control__text'>25-100 rooms</span>
                                </label>
                                <label class='control__label control__label--categories'>
                                    <input
                                        class='control__input control__input--categories control__input--checkbox'
                                        type='checkbox'
                                        value="101-125"
                                        name="rooms"
                                    />
                                    <span class='control__text'>101-125 rooms</span>
                                </label>
                                <label class='control__label control__label--categories'>
                                    <input
                                        class='control__input control__input--categories control__input--checkbox'
                                        type='checkbox'
                                        value="126-150"
                                        name="rooms"
                                    />
                                    <span class='control__text'>126-150 rooms</span>
                                </label>
                                <label class='control__label control__label--categories'>
                                    <input
                                        class='control__input control__input--categories control__input--checkbox'
                                        type='checkbox'
                                        value="151"
                                        name="rooms"
                                    />
                                    <span class='control__text'>151+ rooms</span>
                                </label>
                            <?php } ?>
                            
                            <?php if ($attrs['filterType'] == 'categories') { ?>
                            <?php foreach($filter_terms as $term) {  

                                if(!empty($attrs['catFilterSelections'])){
                                    
                                    // Split the string into an array using commas as the delimiter
                                    $catFiltersSelections = explode(",", $attrs['catFilterSelections']);
                                    
                                    // Check if the search term is in the array
                                    if (!in_array($term->slug, $catFiltersSelections)) {
                                        continue;
                                    }
                                }
                                ?>
                            <label class="control__label control__label--categories">
                                    <input
                                        type="checkbox"
                                        class="control__input control__input--categories control__input--checkbox" 
                                        name="<?php echo $filter_tax === 'category' ? 'categories' : $filter_tax; ?>"
                                        value="<?php echo $term->term_id  ?>"
                                    />
                                    <span class="control__text"><?php echo $term->name  ?></span>
                            </label>
                            <?php } }?>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
            <!--/ end filter controls -->
            <?php endif;?>
        </form>
        <!--/ end filters -->
    </div>
    <?php
}
