<?php
namespace MaddenNino\Blocks\ListingsGrid;
use MaddenNino\Library\Constants as C;
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {
	$filter_tax = false;
	switch( $attrs['postType'] ) {
		case 'listing':
			$filter_tax = 'listing_categories';
			break;
		case 'event':
			$filter_tax = 'eventastic_categories';
			break;
		case 'posts':
		case 'page':
			$filter_tax = 'category';
			break;
	}

	$query_args = array(
		'post_type' =>  $attrs['postType'],
		'post_status' => 'publish',
		'fields' => 'ids',
		'posts_per_page' => $attrs['listingsPerPage'],
	);

	if( 
		isset( $attrs['preFilterCat'] ) 
		&& $attrs['preFilterCat'] !== 'none'
		&& isset( $attrs['postType'] ) 
		&& (
			$attrs['postType'] === 'listing' ||
			$attrs['postType'] === 'page'	||
			$attrs['postType'] === 'event'      
		)
	) {
		$query_args['tax_query'][] = array(
			'taxonomy' => $filter_tax,
			'field' => 'slug',
			'terms' => $attrs['preFilterCat'],
			'include_children' => true,
		);
	}


	$listings_query = new \WP_Query( $query_args );

	ob_start();
	?>

	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  
	<section 
		id="listings-grid"
		class="<?php echo C::BLOCK_CLASS ?>-listings-grid listings-grid--<?php echo $attrs["postType"] ?>"
		data-default-thumb="<?php echo $attrs['defaultThumb']; ?>"
		data-postType="<?php echo $attrs['postType']; ?>"
		data-perPage="<?php echo $attrs['listingsPerPage']; ?>"
	>

	<!-- grid body -->
	<div class="grid-body">

		<?php if( isset( $attrs['postType'] ) && $attrs['postType'] !== 'event' ):?>
		<h2 class="grid-title"><?php echo $attrs['listingsTitle']; ?></h2>
		<?php endif; ?>

		<?php
			include_once( 'code/grid-filters.php' );
			render_grid_filter( $attrs, $filter_tax );
		?>

		<!--grid  -->
		<?php if( !$attrs['map'] ) { ?>
	
			<div class="listings-container listings-container--grid">
				<!-- loaded in js -->
				<div class="loading show">
					<span class="sr-only"><?php _e( 'loading' ); ?></span>
					<i class="fas fa-spinner fa-pulse"></i>
				</div>
			</div>
		<?php } ?>
		<!--/ end grid -->

		<?php if( isset($attrs['map']) && $attrs['map'] ) { ?>
			<div class="view view--map" data-view-type="map">			
				<div id="listings-grid__map-container" class="map-container"></div>

				<div id="listings-container--map" class="listings-container listings-container--map swiper">
					<div class="loading show">
						<span class="sr-only"><?php _e( 'loading' ); ?></span>
						<i class="fas fa-spinner fa-pulse"></i>
					</div>
					
					<div class="swiper-wrapper"></div>
				</div>
			</div>
		<?php } ?>

		<?php
			include_once( 'code/grid-pagination.php' );
			render_grid_pagination( $listings_query );
		?>

	</div>
	<!--/ end body -->
</section>
<!--/ end block -->
<?php
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}
