<?php
	/*
	include realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../blocks/eventastic-calendar/Stencil/Stencil.php'); 
	include realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../blocks/eventastic-calendar/Stencil/Exceptions/TemplateNotFoundException.php'); 
	use Stencil\Stencil;

	$stencil = new Stencil( __DIR__ . DIRECTORY_SEPARATOR .'../templates');

	
	$template = 'hello';
	echo $stencil->render( $template, [
	    'name' => 'Ryan',
	]);
	*/
	
	$devMode = ( isset( $attributes['blockConfig_developerMode'] ) && $attributes['blockConfig_developerMode'] ) ? true : false;
	$imageSize = ( isset( $attributes['cardConfig_imageSize'] ) && $attributes['cardConfig_imageSize'] ) ? $attributes['cardConfig_imageSize'] : 'thumbnail';

    wp_enqueue_script( 'mm-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array(), '', true );

    $useFilterDates = isset( $attributes['filterConfig_useDates'] ) ? $attributes['filterConfig_useDates'] : true;
    if( $useFilterDates ){
	    wp_enqueue_style( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
		wp_enqueue_script( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array(), '', true );
	}

    // file override
    $calendar_js = "/eventastic-theme-files/blocks/eventastic-calendar/block-eventastic-calendar.js";
    wp_enqueue_script('wp-plugin-eventastic-blocks-cal', get_stylesheet_directory_uri() . $calendar_js,  array(), rand(111,9999));

	if( isset( $attributes['preloadEvents'] ) && $attributes['preloadEvents'] ){

		$maxNumberOfGridEventsToPreload = ( isset( $attributes['maxNumberOfGridEventsToPreload'] ) && $attributes['maxNumberOfGridEventsToPreload'] ) ? $attributes['maxNumberOfGridEventsToPreload'] : 5;
		$preLoadStartDate = date('Y-m-01', time() );

		$date = new DateTime('now');
		$date->modify('last day of this month');
		$preLoadEndDate = $date->format('Y-m-d');
        if( 'this_weekend' == $attributes['listConfig_initialLoadDateRange'] ){
			$date = new DateTime('now');
			$date->modify('+1 months');
			$date->modify('last day of this month');
			$preLoadEndDate = $date->format('Y-m-d');
        }            		

        if( '3_months' == $attributes['listConfig_initialLoadDateRange'] ){
			$date = new DateTime('now');
			$date->modify('+3 months');
			$date->modify('last day of this month');
			$preLoadEndDate = $date->format('Y-m-d');

        }            		
		$monthsLoaded = [ $date->format('Y-m') ];
		$args = array(
		    'posts_per_page' => $maxNumberOfGridEventsToPreload,
		    'start_date' => $preLoadStartDate,
		    'end_date' => $preLoadEndDate,
		    'image_size' => $imageSize
		);
        if( is_array( $attributes['contentConfig_categories'] ) && count($attributes['contentConfig_categories']) == 1 && "all" == $attributes['contentConfig_categories'][0] ){
		}
		else{		
			if( isset( $attributes['contentConfig_categories'] ) && count($attributes['contentConfig_categories']) > 0 ){
				$args['category_slug'] = implode(",",$attributes['contentConfig_categories']);
			}
		}
		$preloadEventResults = eventastic_get_events_date_ordered( $args );
	}

    $calendarDataHtml = "";
    foreach( $attributes as $attributeKey => $attributeValue ){
    	if( is_array( $attributeValue) ){
    		$thisValue = json_encode($attributeValue);
    	}
    	else{
    		$thisValue = $attributeValue;
    	}
	    $calendarDataHtml .= " data-" . $attributeKey . "='" . $thisValue . "' ";
    }

?>
	<div>
	<?php
		if( isset( $attributes['layoutStyle']) && "list" != $attributes['layoutStyle'] ){
			eventastic_render_calendar("calendar"); 			// for V2, this just enqueues the fullCalendar files  
		}
	?>			
	<?php if( isset($preloadEventResults) && isset($preloadEventResults['event_objects'] ) ) : ?>
		<script>
			window.preLoadData = {
				monthsLoaded : <?php echo json_encode( $monthsLoaded ); ?>,
				event_objects : <?php echo json_encode( $preloadEventResults['event_objects'] ); ?>,
				monthsEventSourceAdded : [],
				fullCalendarEventsSource : <?php echo json_encode( $preloadEventResults['fullCalendarEventsSource'] ); ?>,
				days: <?php echo json_encode( $preloadEventResults['days'] ); ?>
			};
			console.log(window.preLoadData);
		</script>
	<?php endif; ?>
	<?php if($devMode) : ?> 
		<div class="dev-hint">
			<h3><span class='dm'>DM:</span> Calendar Block Attributes</h3>
			<table>
				<tr>
					<th>Attribute</th>
					<th>Value</th>
				</tr>
				<?php foreach( $attributes as $attrKey => $attrVal ) : 
				?>
					<tr>
						<td><?php echo $attrKey; ?></td>
						<td><?php echo json_encode( $attrVal); ?></td>
					</tr>
				<?php endforeach; ?>	
			</table>
			<?php if ( get_stylesheet_directory_uri() . $calendar_js ) : ?>
				<div class="dev-message">
					Some of the plugin's Javascript functions are being intentionally overriden by the following file:
					<a target="_blank" href='<?php echo get_stylesheet_directory_uri() . $calendar_js; ?>'><?php echo get_stylesheet_directory_uri() . $calendar_js; ?></a> 
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php 
		$devNote = "";
		if( $devMode ){
			$devNote = "{#calendar-container} generated by: /blocks/eventastic-calendar/src/render.php";
		}
	?>
	<div class="eventastic-calendar-block <?php echo $attributes['styleSheet']; ?>">
	    <div id="calendar-container"  <?php echo $calendarDataHtml; ?> data-devNote="<?php echo $devNote; ?>">
	    	<div class="inner-wrapper">	
				<?php
					$root = $_SERVER['DOCUMENT_ROOT'] ;
					$dir = "/wp-content/themes/" . get_option('stylesheet');
					$calendar_template = "/eventastic-theme-files/blocks/eventastic-calendar/calendar-template.php";
					$filepath = $root . $dir . $calendar_template;		
					if( file_exists( $filepath ) ){
						include $filepath;
					}
					else{
						$plugin_root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../'); 
						$pluginFilePath = $plugin_root . "/" . $calendar_template;
						include $pluginFilePath;
					}
				?>
			</div>
	    </div>
	</div>
</div>