<?php
/**
 * The template for responding to 404 - Not Found errors
 */

get_header(); ?>

<h1><?php _e( 'Page not found', 'mmmadre' ); ?></h1>

<p><?php _e( 'We couldn\'t find the page you were looking for.', 'mmmadre' ); ?></p>

<p><?php _e( 'It looks like you were looking for: ', 'mmmadre' ); echo home_url( $_SERVER['REQUEST_URI'] ); ?></p>

<p>
	<?php printf(
		__( 'You can use the navigation above, %ssearch our site%s, or return to %sour homepage%s.', 'mmmadre' ),
		'<a href="' . home_url() . '?s=">',
		'</a>',
		'<a href="' . home_url() . '">',
		'</a>'
	); ?>
</p>

<?php get_footer(); ?>