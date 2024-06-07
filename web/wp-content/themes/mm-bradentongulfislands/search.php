<?php /**
 * Template Name: Search Page
 * 
 * This is the template file for displaying search results. For the search form, see searchform.php.
 */

global $query_string;

$search_str = isset( $_GET['s'] ) ? $_GET['s'] : '';
wp_parse_str($query_string, $search_query);


$exclude_post_type = 'memberpressgroup';
$all_post_types = get_post_types(array('public' => true));

// Remove the excluded post type
if (isset($all_post_types[$exclude_post_type])) {
    unset($all_post_types[$exclude_post_type]);
}

$search_query['post_type'] = array_keys($all_post_types);

$search = new WP_Query($search_query);

use MaddenNino\Library\Utilities as U;

get_header(); ?>

<section class="wp-block-mm-bradentongulfislands-hero is-style-secondary-header">
	<div class="hero" data-load-type="bg" data-load-onload="true" data-load-lg="/wp-content/uploads/2023/11/694-3304.jpg" data-load-md="/wp-content/uploads/2023/11/694-3304.jpg" data-load-sm="/wp-content/uploads/2023/11/694-3304.jpg" data-load-lg-bg-position="39% 28%" data-load-md-bg-position="39% 28%" style="background-image: url(&quot;/wp-content/uploads/2023/11/694-3304.jpg&quot;); background-position: 39% 28%;"></div>
	<h1 class="title alt-title">Search</h1>
</section>

<div id="searchContainer">

	<div class="results-form-wrap">
		<p class="showing-text"><?php printf( __( 'Showing results for "%s"', 'mmnino' ), $search_str ) ?></p>
		<?php get_search_form(array(
			'echo' => true,
			'aria_label' => 'page_search'
		)); ?>
		<script>
		// clear any other search forms on the page
		jQuery(document).ready(function($) {
			$('.search-form__field').each(function() {
				if ($(this).attr("id") != "page_search_input") {{
					$(this).val("");
				}}
			});
		});
		</script>
		<p class="count-text"><?php printf( __( 'Found %d results', 'mmnino' ), $search->found_posts ); ?></p>
	</div>

	<?php
	if( have_posts() && !empty($search_str) ) {
		while( have_posts() ) {
			the_post(); ?>

			<article class="search-result">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

				<p><?php echo U::excerpt_by_sentences( get_the_ID(), 1, 20 ); ?></p>
			</article>
		<?php }
	} else { ?>
		<p><?php _e( 'No results found. Try another search?', 'mmnino' ); ?></p>
	<?php }

	the_posts_pagination( array(
		'mid_size'  => 2,
	) ); ?>
	
</div>


<?php get_footer();
