<?php
function displayEventDates( $id ) {
    ob_start();

    echo '<div class="event-date">';

		include __DIR__ . '/../icons/calendar.php';

		$start_date = tribe_get_start_date( $id, false, 'M j, Y' ); // Example: Jan 9, 2025
		$end_date   = tribe_get_end_date( $id, false, 'M j, Y' );   // Example: Jan 29, 2026

		if ( $start_date === $end_date ) {
			// Single-day event → "May 10"
			echo esc_html( tribe_get_start_date( $id, false, 'M j' ) );
		} else {
			// Multi-day event → range stacked on 2 lines
			echo esc_html( $start_date ) . ' - ' . esc_html( $end_date );
		}

    echo '</div>';

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

function displayEventTimes( $id ) {
    ob_start();

	echo '<div class="event-time">';

		include __DIR__ . '/../icons/clock.php';

		if ( tribe_event_is_all_day( $event_id ) ) {
			// All-day event → no time
			echo esc_html__( 'All day', 'textdomain' );
		} else {
			$start_time = tribe_get_start_time( $event_id, 'g:i a' ); // e.g. "7:00 pm"
			$end_time   = tribe_get_end_time( $event_id, 'g:i a' );   // e.g. "9:00 pm"

			if ( $start_time && $end_time && $start_time !== $end_time ) {
				echo esc_html( $start_time . ' – ' . $end_time );
			} elseif ( $start_time ) {
				echo esc_html( $start_time );
			}
		}

	echo '</div>';

    $output = ob_get_contents();
    ob_end_clean();

	return $output;
}

function displayEventLocation($id) {
	// Need to add this
}
