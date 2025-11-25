<?php
/*
Template Name: Search Page
*/
global $query_string;

$search_str = isset( $_GET['s'] ) ? $_GET['s'] : '';

wp_parse_str( $query_string, $search_query );
$search = new WP_Query( $search_query );

use MaddenMadre\Library\Utilities as Utilities;

get_header(); ?>

<main class="search-page-content">
	<h1><?php _e( "Greater Zion Search", 'mmmadre' ); ?></h1>

	<div class="results-form-wrap">
		<p class="showing-text"><?php printf( __( 'Showing results for "%s"', 'mmmadre' ), $search_str ) ?></p>
		<?php get_search_form( true ); ?>
		<p class="count-text"><?php printf( __( 'Found %d results' ), $search->found_posts ); ?></p>
	</div>

	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post(); 
			$post_type = get_post_type(); ?>

			<article class="search-result">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                <p><?php echo Utilities::excerpt_by_sentences( get_the_ID(), 1, 20 ); ?></p>
			</article>
			<hr class="wp-block-separator">
		<?php }
	} else { ?>
		<p><?php _e( "No results found. Try another search?", 'mmmadre' ); ?></p>
	<?php }

	the_posts_pagination( array(
		'mid_size'  => 2,
	) ); ?>
</main>

<?php get_footer();
