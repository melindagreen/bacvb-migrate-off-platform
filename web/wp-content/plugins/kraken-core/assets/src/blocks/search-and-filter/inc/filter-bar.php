<?php
namespace MaddenMedia\KrakenCore\Blocks\SearchResultsFilter;
$theme_slug = get_template();
?>

<div class="filter-bar">

	<?php
	$filter_elements = apply_filters( 'kraken-core/search-and-filter/filter_elements', [
		'search',
		'taxonomies',
		'event_start',
		'event_end',
		'sort',
	], $attrs );

	$title_label = __("Alphabetical", $theme_slug);
	$date_label = is_event($attrs['postType']) ? "Event Date" : "Newest";
	$sort_options = apply_filters( 'kraken-core/search-and-filter/sort_options', [
		'date' => $date_label,
		'title' => $title_label,
	], $attrs );

	ob_start();
	include __DIR__ . '/../icons/calendar.php';
	$calendar_icon = ob_get_clean();

	ob_start();
	include __DIR__ . '/../icons/arrow-down.php';
	$arrow_down_icon = ob_get_clean();

    if ( is_array( $filter_elements ) && ! empty( $filter_elements ) ) {
		foreach ( $filter_elements as $element ) {
			switch( $element ) {
				case 'search':
					// Search Input
					if ($attrs['enableSearchInput']) {
						$search_query = isset($queryParams['search']) ? $queryParams['search'] : '';
						?>

						<div class="filter-wrapper filter-search">
							<?php
									$search_label    = "Search";
									$search_label_el = '<label for="filter-search-input" class="sr-only screen-reader-text">' . __( $search_label, $theme_slug) . '</label>';
									$search_label_el = apply_filters( 'kraken-core/search-and-filter/search_label', $search_label_el, $search_label, $attrs );
									echo $search_label_el;
								?>
							<div class="filter-search-input-wrapper">
								<?php include __DIR__ . '/../icons/search.php'; ?>
								<input id="filter-search-input" name="filter-search-input" class="filter-search-input" type="text" aria-label="Search" placeholder="Search" data-type="search" value="<?php echo $search_query; ?>" />
							</div>
						</div>
					<?php
					}
					break;

				case 'taxonomies':
					// Tax Filters
					if ($attrs['enableTaxonomyFilter'] && count($attrs['taxonomyFilters']) > 0) {

						foreach ($attrs['taxonomyFilters'] as $filter) {

							$terms = null;

							// If taxonomy query type matches filter and we have query terms
							if ($attrs['enableTaxonomyQuery'] && $filter === $attrs['taxonomyQueryType'] && !empty($attrs['taxonomyQueryTerms'])) {
								$queryTerms = array_values((array) $attrs['taxonomyQueryTerms']); // normalize indexes

								// Multiple selected → show only those terms
								if (count($queryTerms) > 1) {
									$terms = get_terms([
											'taxonomy'   => $filter,
											'hide_empty' => true,
											'include'    => $queryTerms,
									]);
								}

								// Single selected → try children
								elseif (count($queryTerms) === 1) {
									$children = get_terms([
											'taxonomy'   => $filter,
											'hide_empty' => true,
											'parent'     => (int) $queryTerms[0],
									]);

									// If children, show filter
									if (!empty($children)) {
											$terms = $children;
									}
									// Skip filter if no children
									else {
										continue;
									}
								}
							}

							// Fetch only taxonomy terms used for the selected post type
							if ($terms === null) {
								global $wpdb;

								$post_types = (array) $attrs['postType'];
								$taxonomies = (array) $filter;

								$post_type_placeholders = implode(', ', array_fill(0, count($post_types), '%s'));
								$taxonomy_placeholders  = implode(', ', array_fill(0, count($taxonomies), '%s'));

								// Base SQL
								$sql = "
									SELECT t.*, COUNT(*) as count
									FROM $wpdb->terms AS t
									INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
									INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_id
									INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
									WHERE p.post_type IN ($post_type_placeholders)
										AND tt.taxonomy IN ($taxonomy_placeholders)
										AND p.post_status = 'publish'
								";

								// Add Kraken event date filtering
								if ( $attrs['postType'] === 'event' ) {
									$sql .= " AND EXISTS (
										SELECT 1
										FROM $wpdb->postmeta pm
										WHERE pm.post_id = p.ID
										AND pm.meta_key = 'event_end_date'
										AND pm.meta_value >= %s
									)";
								}

								// Add The Events Calendar event date filtering
								if ( $attrs['postType'] === 'tribe_events' ) {
									$sql .= " AND EXISTS (
										SELECT 1
										FROM $wpdb->postmeta pm
										WHERE pm.post_id = p.ID
										AND pm.meta_key = '_EventEndDate'
										AND pm.meta_value >= %s
									)";
								}

								// Restrict to posts that also match taxonomyQueryTerms
								if ( $attrs['enableTaxonomyQuery'] && ! empty( $attrs['taxonomyQueryTerms'] ) ) {
									$placeholders = implode( ', ', array_fill( 0, count( $attrs['taxonomyQueryTerms'] ), '%d' ) );

									$sql .= " AND EXISTS (
										SELECT 1
										FROM $wpdb->term_relationships AS qr
										INNER JOIN $wpdb->term_taxonomy AS qtt ON qr.term_taxonomy_id = qtt.term_taxonomy_id
										WHERE qr.object_id = p.ID
										AND qtt.taxonomy = %s
										AND qtt.term_id IN ($placeholders)
									)";
								}

								// Final clause
								$sql .= " GROUP BY t.term_id";

								// Prepare arguments
								$sql_args = array_merge( $post_types, $taxonomies );

								if ( in_array( $attrs['postType'], [ 'event', 'tribe_events' ], true ) ) {
									$today = gmdate( 'Y-m-d 00:00:00', current_time( 'timestamp' ) );
									$sql_args[] = $today;
								}

								if ( $attrs['enableTaxonomyQuery'] && ! empty( $attrs['taxonomyQueryTerms'] ) ) {
									$sql_args[] = $attrs['taxonomyQueryType']; // for qtt.taxonomy
									$sql_args   = array_merge( $sql_args, $attrs['taxonomyQueryTerms'] );
								}

								$prepared_sql = $wpdb->prepare( $sql, $sql_args );
								$terms        = $wpdb->get_results( $prepared_sql );
							}

							// Rewrite category to categories for API queries
							if ($filter === 'category') {
								$filter = 'categories';
							}

							// Display filters if terms
							if ($terms && is_array($terms) && !empty($terms)) {

								usort($terms, function($a, $b) {
									return strcmp($a->name, $b->name);
								});

								$selected = isset($queryParams[$filter]) ? explode(",", $queryParams[$filter]) : null;

								// Set filter label
								$taxonomy = get_taxonomy( $filter );

								if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
									$filterName =  $taxonomy->labels->name;
								} else {
									$filterName = ucwords(str_replace('_', ' ', $filter));
									$filterName = ($filterName === 'Eventastic Categories') ? 'Categories' : $filterName;
								}

								$terms_array = [];
								foreach ($terms as $term) {
									$terms_array[$term->term_id] = $term->name;
								}

								$tax_args = [
									'type' => 'taxonomy',
									'filter' => $filter,
									'name' => $filterName,
									'options' => $terms_array,
									'selected' => $selected,
								];
								render_filter_dropdown( $attrs, $tax_args );
							}
						}
					}
					break;

				case 'event_start':
					// Event Start
					if ( is_event( $attrs['postType'] ) && $attrs['enableStartDateFilter']) {
						$start_date_label = __("Start Date", $theme_slug);
						?>
						<div class="filter-wrapper filter-event-dates" data-type="event-date">
							<div class="filter-date-picker">
								<?php
									$start_date_label_el = '<label for="start_date" class="sr-only screen-reader-text">' . $start_date_label . '</label>';
									$start_date_label_el = apply_filters( 'kraken-core/search-and-filter/start_date_label', $start_date_label_el, $start_date_label, $attrs );
									echo $start_date_label_el;
								?>

								<input aria-label="<?php echo $start_date_label; ?>" placeholder="<?php echo $start_date_label; ?>" data-type="start_date" id="start_date" name="start_date" type="text" value="<?php echo $start_date; ?>">
								<?php
								echo apply_filters( 'kraken-core/search-and-filter/start_date_icon', $calendar_icon, $attrs );
								?>
							</div>
						</div>

					<?php
					}
					break;

				case 'event_end':
					// Event End
					if ( is_event( $attrs['postType'] ) && $attrs['enableEndDateFilter']) {
						$end_date_label = __("End Date", $theme_slug);
						?>
						<div class="filter-wrapper filter-event-dates" data-type="event-date">
							<div class="filter-date-picker">
								<?php
									$end_date_label_el = '<label for="end_date" class="sr-only screen-reader-text">' . $end_date_label . '</label>';
									$end_date_label_el = apply_filters( 'kraken-core/search-and-filter/end_date_label', $end_date_label_el, $end_date_label, $attrs );
									echo $end_date_label_el;
								?>

								<input aria-label="<?php echo $end_date_label; ?>" placeholder="<?php echo $end_date_label; ?>" data-type="end_date" id="end_date" name="end_date" type="text" value="<?php echo $end_date; ?>">
								<?php
								echo apply_filters( 'kraken-core/search-and-filter/end_date_icon', $calendar_icon, $attrs );
								?>
							</div>
						</div>
					<?php
					}
					break;

				case 'sort':
					// Sorting
					if ($attrs['enableSortingFilter']) {

						// Labels
						$sort_by_label = __("Sort By", $theme_slug);
						$alpha_label = __("Alphabetical", $theme_slug);
						$date_label = is_event($attrs['postType']) ? "Event Date" : "Newest";
						?>

                        <div class="filter-wrapper filter-sort">
							<button class="filter-toggle-btn" aria-haspopup="true" aria-expanded="false">
								<span class="filter-toggle-btn-label">
                                    <?php echo $sort_by_label; ?>
                                </span>
                                <?php
                                    ob_start();
                                    include __DIR__ . '/../icons/arrow-down.php';
                                    $icon = ob_get_clean();
                                    echo apply_filters( 'kraken-core/search-and-filter/filter_dropdown_icon', $icon, $attrs );
                                ?>
							</button>

							<div class="filter-dropdown filter-orderby" tabindex="-1">
								<ul>
									<?php
									if ( is_array( $sort_options ) && ! empty( $sort_options ) ) {
										foreach ($sort_options as $key => $label) {
											?>
											<li>
												<label for="orderby-<?php echo esc_attr($key); ?>">
													<input type="radio" id="orderby-<?php echo esc_attr($key); ?>" name="orderby" value="<?php echo esc_attr($key); ?>" data-type="orderby" <?php if ($orderby === $key) { echo "checked"; } ?>>
													<span class="label"><?php echo $label; ?></span>
												</label>
											</li>
											<?php
										}
									}
									?>
								</ul>
							</div>
						</div>
					<?php

					}
					break;

				default:
					if ( str_starts_with( $element, 'action_' ) ) {
						do_action("kraken-core/search-and-filter/{$element}", $attrs);
					}
					break;
			}
		}
	}
	?>
</div>
