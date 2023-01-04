<?php /**
 * Template Name: Search Page
 * 
 * This is the template file for displaying search results. For the search form, see searchform.php.
 */

global $query_string;

$search_str = isset( $_GET['s'] ) ? $_GET['s'] : '';

wp_parse_str( $query_string, $search_query );
$search = new WP_Query( $search_query );

use MaddenNino\Library\Utilities as U;

get_header(); ?>

<h1><?php _e( 'MMNino Search', 'mmnino' ); ?></h1>

<div class="results-form-wrap">
	<p class="showing-text"><?php printf( __( 'Showing results for "%s"', 'mmnino' ), $search_str ) ?></p>
	<?php get_search_form( true ); ?>
	<p class="count-text"><?php printf( __( 'Found %d results', 'mmnino' ), $search->found_posts ); ?></p>
</div>

<?php
if( have_posts() ) {
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

<?php get_footer();
