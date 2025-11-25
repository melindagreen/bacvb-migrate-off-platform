<?php

// Prevent redeclaration if Eventastic plugin is still active
if ( ! is_plugin_active( 'wp-plugin-eventastic/madden-eventastic.php' ) ) :
    if (!function_exists('eventastic_get_event_meta')) {
        function eventastic_get_event_meta($post_id, $meta_key = null) {
            if ($meta_key) {
                return get_post_meta($post_id, $meta_key, true);
            }

            $meta = [];
            $fields = [
                'event_start_date',
                'event_end_date',
                'event_start_time',
                'event_end_time',
                'event_location',
                'event_phone',
                'event_email',
                'events_url',
                'events_price',
                'events_price_varies',
                'event_specific_dates',
                'events_recurrence_options',
                'events_addr_multi',
                'events_addr1',
                'events_addr2',
                'events_city',
                'events_state',
                'events_zip',
                'events_lat',
                'events_lng',
                'events_ticket_link',
                'events_facebook',
                'events_twitter',
                'events_instagram',
                'events_gallery_images',
                'events_pattern_dates',
                'events_event_all_day',
                'event_repeat_dates',
                'event_weekly',
                'monthly_by_dotw',
            ];

            foreach ($fields as $field) {
                $meta[$field] = get_post_meta($post_id, $field, true); 
            }
            // Legacy fields are created here so that older sites that reference
            // the older event meta can still display data  correctly without extensive
            // code re-working.
            $legacy_fields = [
                'event_start_date' => 'start_date',
                'event_end_date' => 'end_date',
                'event_start_time' => 'start_time',
                'event_end_time' => 'end_time',
                'events_recurrence_options' => 'recurrence_options',
                'event_phone' => 'phone',
                'event_email' => 'email',
                'events_url' => 'url',
                'events_price' => 'price',
                'events_price_varies' => 'price_varies',
                'events_addr_multi' => 'addr_multi',
                'events_addr1' => 'addr1',
                'events_addr2' => 'addr2',
                'events_city' => 'city',
                'events_state' => 'state',
                'events_zip' => 'zip',
                'events_lat' => 'lat',
                'events_lng' => 'lng',
                'events_ticket_link' => 'ticket_link',
                'events_facebook' => 'facebook',
                'events_twitter' => 'twitter',
                'events_gallery_images' => 'gallery_images',
                'events_event_all_day' => 'event_all_day',
            ];

            foreach ( $legacy_fields as $key => $value ) {
                $meta[$value] = get_post_meta($post_id, $key, true);
                if ( $key == 'events_recurrence_options' ) {
                    $meta['event_end'] = ( $meta[$value] != 'one_day' ) ? 'infinite' : 'finite';
                }
                if ( $key == 'event_start_date' || $key == 'event_end_date' ) {
                    if ( ! empty( $meta[$value] ) ) {
                        $date = DateTime::createFromFormat('Ymd', $meta[$value]);
                        $formattedDate = $date->format('Y-m-d');
                        $meta[$value] = $formattedDate;
                    }
                    
                    
                }
            }

            return $meta;
        }
    }

    if (!function_exists('eventastic_get_events')) {
        function eventastic_get_events($args = []) {
            $defaults = [
                'post_type'      => 'event',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'meta_value',
                'meta_key'       => 'event_start_date',
                'order'          => 'ASC',
                'meta_query'     => 
                    array (
                        'key'   => 'event_end_date',
                        'value' => date('Ymd'),
                        'compare' => '>=',
                        'type'   => 'DATE'
                    )
            ];
            $query_args = wp_parse_args($args, $defaults);
            return new WP_Query($query_args);
        }
    }

    if (!function_exists('eventastic_get_categories')) {
        function eventastic_get_categories($post_id = null) {
            $post_id = $post_id ?: get_the_ID();
            return get_the_terms($post_id, 'event_category');
        }
    }

    if (!function_exists('eventastic_render_event_map')) {
        function eventastic_render_event_map($post_id = null) {
            $post_id = $post_id ?: get_the_ID();
            $location = get_field('event_map', $post_id);

            if ($location) {
                echo '<div class="acf-map" data-lat="' . esc_attr($location['lat']) . '" data-lng="' . esc_attr($location['lng']) . '"></div>';
            }
        }
    }

    if (!function_exists('eventasticGetEventsNew')) {
        function eventasticGetEventsNew($args = []) {
            $query = eventastic_get_events($args);
            $results = [];

            while ($query->have_posts()) {
                $query->the_post();
                $results[] = [
                    'id'        => get_the_ID(),
                    'title'     => get_the_title(),
                    'link'      => get_permalink(),
                    'excerpt'   => wp_trim_words(strip_tags(get_the_content()), 30),
                    'thumbnail' => get_the_post_thumbnail_url(null, 'full'),
                    'meta'      => eventastic_get_event_meta(get_the_ID()),
                ];
            }

            wp_reset_postdata();
            return $results;
        }
    }
endif;
