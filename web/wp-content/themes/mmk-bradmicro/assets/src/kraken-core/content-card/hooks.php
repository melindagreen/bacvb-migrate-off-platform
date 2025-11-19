<?php
namespace MaddenTheme\KrakenCore\ContentCard;

add_filter( 'kraken-core/content-card/content_elements', __NAMESPACE__ . '\content_elements', 10, 3 );
add_action( 'kraken-core/content-card/action_visit_website', __NAMESPACE__ . '\render_visit_website', 10, 2 );
add_filter( 'kraken-core/content-card/link', __NAMESPACE__ . '\card_link', 10, 3 );
add_filter('kraken-core/content-card/link_target', __NAMESPACE__ . '\card_link_target', 10, 3 );
add_filter('kraken-core/content-card/event_date_format', __NAMESPACE__ . '\date_format' );

function content_elements( $elements, $post_id, $attributes ) {
	return [
		'title',
		'event_details',
		'action_visit_website',
		'address',
		'excerpt',
		'actions',
	];
}

function render_visit_website( $post_id, $attributes ) {
	$post_type = get_post_type( $post_id );
	if ( 'event' == $post_type ) {
		if ( $events_url = get_post_meta( $post_id, 'events_url', true ) ) {
			echo '<div class="visit-website">' . __( 'Visit Website', 'madden-theme' ) . '</div>';
		}
	}
}

function card_link( $link, $id, $attrs ) {
	$post_type = get_post_type( $post_id );
	if ( 'event' == $post_type ) {
		if ( $events_url = get_post_meta( $id, 'events_url', true ) ) {
			return esc_url( $events_url );
		}
		return false;
	}
	return $link;
}

function card_link_target( $target, $id, $attrs ) {
	$post_type = get_post_type( $post_id );
	if ( 'event' == $post_type ) {
		if ( $events_url = get_post_meta( $id, 'events_url', true ) ) {
			return 'target="_blank"';
		}
	}
	return $target;
}

function date_format() {
	return 'F j, Y';
}
