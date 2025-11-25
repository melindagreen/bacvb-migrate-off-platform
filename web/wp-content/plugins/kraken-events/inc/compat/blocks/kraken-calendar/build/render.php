<?php
use MaddenMedia\KrakenEvents\EventRestApi;

wp_enqueue_script('mm-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true);

$useFilterDates = isset($attributes['filterConfig_useDates']) ? $attributes['filterConfig_useDates'] : true;
if ($useFilterDates) {
	wp_enqueue_style('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
	wp_enqueue_script('daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true);
}


// Preload current month
$request = new \WP_REST_Request('GET', '/kraken/v1/events');
$first_day = date('Ym01');
$last_day = date('Ymt');
$request->set_param('start_date', $first_day);
$request->set_param('end_date', $last_day);
$request->set_param('date_filter', true);

if (is_array($attributes['contentConfig_categories']) && count($attributes['contentConfig_categories']) == 1 && "all" == $attributes['contentConfig_categories'][0]) {
} else {
	if (isset($attributes['contentConfig_categories']) && count($attributes['contentConfig_categories']) > 0) {
		$request->set_param('categories', implode(",", $attributes['contentConfig_categories']));
	}
}

$response = EventRestApi::get_custom_events($request);

if ($response instanceof \WP_REST_Response) {
    // Use get_data() to access the payload.
    $events = $response->get_data();

	// var_dump($events);

	if(isset($events['events'])){
		$data_for_javascript = array(
			'event_objects'   => $events['events'],
			'rest_url'        => esc_url_raw(rest_url())
		);
		wp_localize_script( 'madden-media-kraken-calendar-view-script', 'preLoadData', $data_for_javascript );
	}

} elseif (is_wp_error($response)) {
    // Handle any WP_Error returned by your function.
    $error_message = $response->get_error_message();
    error_log('A calendar error occurred: ' . esc_html($error_message));
}

$calendarDataHtml = "";
foreach ($attributes as $attributeKey => $attributeValue) {
	if (is_array($attributeValue)) {
		$thisValue = json_encode($attributeValue);
	} else {
		$thisValue = $attributeValue;
	}
	$calendarDataHtml .= " data-" . $attributeKey . "='" . $thisValue . "' ";
}

?>
<div>
	<?php
	wp_enqueue_script('fullcalendar_full', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js', array(), '', true);
	?>
	<div class="eventastic-calendar-block <?php echo $attributes['styleSheet']; ?>">
		<div id="calendar-container" <?php echo $calendarDataHtml; ?>>
			<div class="inner-wrapper">
				<!-- MERGED TEMPLATE -->
				<?php
				$useFilters = isset($attributes['useFilters']) ? $attributes['useFilters'] : true;
				?>
				<?php if ($useFilters) : ?>
					<?php $categoryElementType = isset($attributes['filterConfig_categoryElementType']) ? $attributes['filterConfig_categoryElementType'] : "select"; ?>
					<div class="filters">
						<?php if (isset($attributes['filterConfig_useDates']) && $attributes['filterConfig_useDates']) : ?>
							<div class="dateAndKeyworkFilters">
								<div class="dateFilter">
									<label for="start_date">Start Date:</label>
									<div class="dateInput">
										<input id="StartDate" maxlength="10" name="start_date" readonly="true" type="date" value="<?php echo date('Y-m-d'); ?>" class="eventasticDatePicker hasDatepicker">
									</div>
								</div>
								<div class="dateFilter">
									<label for="end_date">End Date:</label>
									<div class="dateInput">
										<input id="EndDate" maxlength="10" name="end_date" readonly="true" type="date" value="" class="eventasticDatePicker hasDatepicker">
									</div>
								</div>
							</div>
						<?php endif; ?>
						<?php if (isset($attributes['filterConfig_showSearch']) && $attributes['filterConfig_showSearch']) : ?>
							<div class="dateAndKeyworkFilters">
								<div class="keywordFilter">
									<label for="keyword">Search By Name:</label>
									<input type="text" id="Keyword" name="keyword" value="" maxlength="50" class="keywords">
								</div>
							</div>
						<?php endif; ?>
						<?php if (isset($attributes['filterConfig_useCategories']) && $attributes['filterConfig_useCategories']): ?>
							<?php
							$categories = [];
							if (!isset($attributes['contentConfig_categories']) || (count($attributes['contentConfig_categories']) == 1 && "all" == $attributes['contentConfig_categories'][0])) {
								$categories = get_terms([
									'taxonomy' => 'event_category',
									'hide_empty' => false,
								]);
							} else {
								if (isset($attributes['contentConfig_categories'])) {
									foreach ($attributes['contentConfig_categories'] as $categorySlug) {
										$term = get_term_by('id', $categorySlug, 'event_category');
										if (isset($term->term_id)) {
											$categories[] = $term;
										}
									}
								}
							}
							?>
							<?php if ($categories): ?>

								<div class="categoryFilters">
									<div class="filterToggle">Categories:</div>
									<div class="categories">
										<?php if ("select" == $categoryElementType): ?>
											<select name="category" class="category-filter">
												<option value="">All Categories</option>
												<?php foreach ($categories as $cat): ?>
													<option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
												<?php endforeach; ?>
											</select>
										<?php endif; ?>
										<?php if ("buttons" == $categoryElementType): ?>
											<?php
											foreach ($categories as $cat):
												$img = get_field('category_icon', $cat->taxonomy . '_' . $cat->term_id);
												$img_hover = get_field('category_icon_hover', $cat->taxonomy . '_' . $cat->term_id);
											?>
												<button class="event-category-filter button" data-category="<?php echo $cat->slug; ?>">
													<span><?php echo $cat->name; ?></span>
													<?php if ($img && isset($img['url'])) : ?>
														<img class="default-icon" src='<?php echo $img['url']; ?>'>
													<?php endif; ?>
													<?php if ($img_hover && isset($img_hover['url'])) : ?>
														<img class="hover-icon" src='<?php echo $img_hover['url']; ?>'>
													<?php endif; ?>
												</button>
											<?php endforeach; ?>
										<?php endif; ?>
										<?php if ("checkboxes" == $categoryElementType): ?>
											<?php foreach ($categories as $cat): ?>
												<div class='checkbox-wrapper'>
													<input type="checkbox" class="category-checkbox" name="<?php echo $cat->slug; ?>" id="<?php echo $cat->slug; ?>" value="<?php echo $cat->slug; ?>">
													<label for="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></label>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<?php if (isset($attributes['filterConfig_useFilterSubmit']) && $attributes['filterConfig_useFilterSubmit']) : ?>
							<button class="eventFilterSubmit">Search</button>
						<?php endif; ?>
						<?php if (isset($attributes['filterConfig_useFilterReset']) && $attributes['filterConfig_useFilterReset']) : ?>
							<button class="resetFilters">Reset Filter</button>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="eventasticEvents active" id="eventsCalendar">
					<div class="eventastic-sidebar">
						<div id="calendar"></div>
						<div class="eventastic-loader"></div>
					</div>
					<?php
					$listConfig_showTitle = isset($attributes['listConfig_showTitle']) ? $attributes['listConfig_showTitle'] : true;
					?>
					<div class="calendarListWrapper merch test">
						<?php if ($listConfig_showTitle) :  ?>
							<div id="events-list-title"></div>
						<?php endif; ?>
						<div id="calendarList">

							<?php
							// var_dump($events);
							// $html = '';

							// if ($events && $events['events']) {
							// 	$eventCount = 0;
							// 	foreach ($events['events'] as $event) {
							// 		$eventCount++;
							// 		$eventID = $event['id'];
							// 		$eventMeta = $event['events_meta'];

							// 		$meta_start_date = $eventMeta['event_start_date'];
							// 		$meta_end_date = $eventMeta['event_end_date'];

							// 		$startDate = strtotime($meta_start_date);
							// 		$endDate = strtotime($meta_end_date);
							// 		$allDay = !empty($eventMeta['events_event_all_day']);
							// 		$startTime = !$allDay ? ($eventMeta['event_start_time'] ?? '') : '';
							// 		$endTime = !$allDay ? ($eventMeta['event_end_time'] ?? '') : '';

							// 		// Start card HTML
							// 		$html .= '<div class="events-card' . ($eventCount > 20 ? ' hidden' : '') . '">';
							// 		$html .= '<a href="' . get_the_permalink($eventID) . '" target="_blank">';
							// 		$html .= '<div class="wrapper">';
							// 		$html .= '<div class="content">';

							// 		// Event Details
							// 		$html .= '<div class="text">';
							// 		$html .= '<h3 class="title">' . esc_html(get_the_title($eventID)) . '</h3>';

							// 		// Date Section

							// 		// Additional Event Information
							// 		$html .= '<p class="description">' . wp_trim_words(strip_tags(get_the_content(null, null, $eventID)), 50) . '</p>';
							// 		$html .= '<div class="location">' . esc_html($eventMeta['street_address'] ?? 'Location not available') . '</div>';
							// 		$html .= '</div>'; // End Text Section

							// 		$html .= '</div>'; // End Content
							// 		$html .= '</div>'; // End Wrapper
							// 		$html .= '</a>';
							// 		$html .= '</div>'; // End Card
							// 	}
							// } else {
							// 	$html = '<p class="noResults">No events were found, try adjusting your filters.</p>';
							// }

							// echo $html;

							?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>