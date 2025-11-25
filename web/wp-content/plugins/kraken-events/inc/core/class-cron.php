<?php
namespace MaddenMedia\KrakenEvents;

/*
This is intended for recurring events
- Grabs event_repeat_dates
- Finds the nearest next future date
- Updates event_next_occurrence meta with that date
This meta can be used to output events based on occurrence date instead of start date
*/

class EventRecurrenceCron {

    public static $event_slug = null;
    const POSTS_PER_BATCH = 50;

    public static function init() {
        self::$event_slug = get_option('kraken_events_event_slug', 'event');

        if (!wp_next_scheduled('kraken_events_update_event_occurrences')) {
            wp_schedule_event(time(), 'daily', 'kraken_events_update_event_occurrences');
        }

        add_action('kraken_events_update_event_occurrences', [__CLASS__, 'update_event_occurrences']);
    }

    public static function update_event_occurrences($paged = 1) {
        $today = new \DateTime();

        //only fetch & update events with a future end date.
        $args = array(
			'post_type'     => self::$event_slug,
            'posts_per_page' => self::POSTS_PER_BATCH,
            'post_status'    => 'publish',
            'paged'          => $paged,
            'meta_query'     => array(
                'relation'   => 'AND',
                array(
                    'key'     => 'event_repeat_dates',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key'     => 'event_end_date',
                    'value'   => $today->format('Ymd'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ),
            ),
        );

        $events = new \WP_Query($args);

        if ($events->have_posts()) {
            while ($events->have_posts()) {
                $events->the_post();

                $event_id = get_the_ID();
                $repeat_dates_json = get_post_meta($event_id, 'event_repeat_dates', true);
                $repeat_dates = json_decode($repeat_dates_json, true);
                PartnerEvents::set_next_occurrence($event_id, $repeat_dates);
            }
        }


        error_log('✅ Kraken Events next occurrence updated ' . $events->post_count .' events at '. date('Y-m-d H:i:s'));

        // If more events exist, process the next batch
        if ($events->max_num_pages > $paged) {
            wp_schedule_single_event(time() + 10, 'kraken_events_update_event_occurrences', [(int)$paged + 1]);
            error_log('⏳ Scheduled next Kraken Events cron for page '. ((int)$paged + 1) .' at '.date('Y-m-d H:i:s', time() + 10));
        }

        wp_reset_postdata();
    }
}
