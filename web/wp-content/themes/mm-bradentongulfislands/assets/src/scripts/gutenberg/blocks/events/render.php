<?php
namespace MaddenNino\Blocks\Events;
use \MaddenNino\Library\Constants as Constants;
function events( $attr ) {
      if( function_exists( 'eventastic_get_events' ) ) {
      	//echo $attr['numEvents'];
      	$cats = [];
      	$events = [];
      	$exclude = [];
 				if( is_array( $attr['selectedEvent'] ) && count( $attr['selectedEvent'])>0 ){
					foreach( $attr['selectedEvent'] as $eventID ){
						if( "none" != $eventID ){
							$event = get_post($eventID);
							$event->meta = eventastic_get_event_meta($eventID);
							$events[] = $event;
							$exclude[] = $eventID;
						}
					} 					
 				}
      	foreach($attr['selectedCategories'] as $slug ){
      		if( 'all' != $slug ){
	      		$cats[] = $slug;
	      	}
      	}
      	if( $attr['useFeaturedOnly'] ){
      		$cats[] = 'featured';
      	}
      	$args = [ 
      		'start_date' => date('Y-m-d'),
	        'category_slug' => $cats,
	        'post__not_in' => $exclude,
	        'posts_per_page' => $attr['numEvents'],
        ];
        $raw_events = eventastic_get_events( $args, true );
				$raw_events = array_merge( $raw_events, $events);
        $clean_events = $raw_events && is_array( $raw_events )
          ? array_map( function( $event ) {

            $location = '';
            if( isset( $event->meta['addr_multi'] ) && !empty( $event->meta['addr_multi'] ) )
              $location = $event->meta['addr_multi'];
            else if( isset( $event->meta['city'] ) && !empty( $event->meta['city'] ) )
              $location = $event->meta['city'];

						$month = date("M",strtotime($event->meta['start_date']));
						$day = date("d",strtotime($event->meta['start_date']));
						$excerpt = $event->post_excerpt ? $event->post_excerpt : $event->post_content;
//						$img = get_the_post_thumbnail($event->ID);
$img = wp_get_attachment_image_src( get_post_thumbnail_id($event->ID), 'post'); 
            return array(
              'title' => $event->post_title,
              'url' => get_the_permalink( $event->ID ),
              'date' => "",//Utilities::generate_dates( $event->meta ),
              'excerpt' => $excerpt,
              'location' => $location,
              'month' => $month,
              'day' => $day,
              'img' => $img[0]
            );
          }, $raw_events )
          : array();

        return $clean_events;
      }

      return array();
    }
function render_events( $attributes, $content ) {
	$theme_prefix = Constants::THEME_PREFIX;
	$block_name = json_decode( file_get_contents( __DIR__ . "/block.json" ) )->name;
	$events = events( $attributes );
	$link = array_key_exists('link', $attributes) ? $attributes['link'] : "";
	$url = "";

	if( is_array($link) && array_key_exists('url', $link) && $link['url'] ){
		$url = $link['url'];
		$target = $link['opensInNewTab'] ? "_blank" : ""; 
	}
	$button_class = " natchez-btn ribbon arrow";
	if( array_key_exists('buttonStyle', $attributes) ){
		if( 'square' == $attributes['buttonStyle'] ){
			$button_class = " natchez-btn";
		}
		if( 'blue-ribbon' == $attributes['buttonStyle'] ){
			$button_class = " natchez-btn ribbon arrow";
		}
		if( 'green-ribbon' == $attributes['buttonStyle'] ){
			$button_class = " natchez-btn ribbon arrow green";
		}
	}
	$src = "";
	if( array_key_exists('image', $attributes) ){
		$image = $attributes['image'];
		if( is_array($image) && array_key_exists('url', $image) && $image['url'] ){
			$src = $image['url'];
		}
	}
//	print_r($attributes);
	ob_start(); ?>
	<div class="mm-block-wrapper">

		<section class="container-med wp-block-<?php echo $theme_prefix; ?>-<?php echo $block_name; ?>">
<?php //print_r($attributes); ?>
			<div class="title-wrapper">
					<div class="block-title">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/events-ticket.png">
					</div>
					<div class="block-content">
						<div class="pill">
							<div class="img-wrapper" style="background-image:url('<?php echo $src; ?>')"></div>
							<div class="pill-content">
								<p>								
									<?php if( array_key_exists('content', $attributes)) : ?>
										<?php echo $attributes['content']; ?>
									<?php endif; ?>
								</p>
								<?php if( array_key_exists('link', $attributes)) : ?>
									<a class="mmn-btn " href="<?php echo $attributes['link']['url']; ?>"><?php echo $attributes['link']['title']; ?></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
			</div>
			<div class="col-wrapper">
				<div class="col">
					<?php if( is_array( $events ) ): ?>
					<?php $i = 0; foreach( $events as $event ):
									if( $i <= 2 ):
										$_width = 80;
										$parts = preg_split('/([\s\n\r]+)/', $event['excerpt'], null, PREG_SPLIT_DELIM_CAPTURE);
										$parts_count = count($parts);
										$length = 0;
										$last_part = 0;
										for (; $last_part < $parts_count; ++$last_part) {
											$length += strlen($parts[$last_part]);
											if ($length > $_width) { break; }
										}
										$event_excerpt = implode(array_slice($parts, 0, $last_part));
										if( strlen($event_excerpt) < strlen( $event['excerpt'] ) ){
											$event_excerpt .= "...";
										}
										$i++;
						?>
						<div class="tile-side">
							<a href="<?php echo $event['url']; ?>">
								<div class="bg-img-wrapper">
									<div class="bg-img" style="background:url('<?php echo $event['img']; ?>') no-repeat center border-box; background-size:cover; ">
									</div>
								</div>
								<div class="tile">
									<div class="details">
										<h3><?php echo $event['title']; ?></h3>
										<p class="description"><?php //echo strip_tags($event_excerpt); ?></p>
										<?php if($event['location']): ?>
											<div class="location-wrapper">
												<img alt="Map Marker Icon" src="<?php echo get_stylesheet_directory_uri() . "/assets/images/marker.png"; ?>" >
												<span><?php echo $event['location']; ?></span>
											</div>
										<?php endif; ?>
									</div>
									<div class="date-wrapper">
										<div class="date">
											<span class="month"><?php echo $event['month']; ?></span>
											<span class="day"><?php echo $event['day']; ?></span>
										</div>
									</div>
								</div>
							</a>
						</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				</div>				
			</div>
		</section>
	</div>
	<?php $output = ob_get_contents();
	ob_end_clean();
	return $output;
}
