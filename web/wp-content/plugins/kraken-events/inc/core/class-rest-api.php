<?php

namespace MaddenMedia\KrakenEvents;

class EventRestApi
{

    public static function init()
    {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);

        add_filter('rewrite_rules_array', [__CLASS__, 'event_url_rules']);
        add_filter('query_vars', [__CLASS__, 'event_url_vars']);


        // Handle custom query variables in the event template.
        add_action('template_redirect', [__CLASS__, 'event_template_redirect']);
    }

    public static function event_url_rules($rules)
    {
        $new_rules = [
            'event/([^/]+)/([0-9]{4}-[0-9]{2}-[0-9]{2})/?$' => 'index.php?post_type=event&name=$matches[1]&event_date=$matches[2]',
        ];
        return $new_rules + $rules; // Prepend new rules to existing ones.
    }

    // Register custom query variables.
    public static function event_url_vars($vars)
    {
        $vars[] = 'event_date'; // Add event_date to query vars.
        return $vars;
    }

    public static function  event_template_redirect()
    {
        global $wp_query;

        // Check if we're on a singular event and the event_date is set.
        if (isset($wp_query->query_vars['event_date']) && is_singular('event')) {
            $event_date = $wp_query->query_vars['event_date'];
            $event_slug = $wp_query->query_vars['name'];

            // Validate the event_date (optional).
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $event_date)) {
                wp_redirect(home_url('/404'));
                exit;
            }

            // Add event_date to the global query for access in the template.
            add_filter('the_content', function ($content) use ($event_date, $event_slug) {
                // Example: Append the event date to the content.
                return $content . '<p class="viewing_details">Viewing details for date: ' . esc_html($event_date) . '</p>';
            }, 10, 1);
        }
    }



    public static function register_routes()
    {
        register_rest_route('kraken/v1', '/events', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_custom_events'],
            'args' => [
                'start_date' => [
                    'required' => false,
                    'type' => 'string',
                    'validate_callback' => 'rest_validate_request_arg'
                ],
                'end_date' => [
                    'required' => false,
                    'type' => 'string',
                    'validate_callback' => 'rest_validate_request_arg'
                ],
                'search' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'content' => [
                    'required' => false,
                    'default' => 'embed',
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'category' => [
                    'required' => false,
                    'default' => 0,
                    'type' => 'integer',
                    'validate_callback' => [__CLASS__, 'validate_numeric']
                ],
                'categories' => [
                    'required' => false,
                    'type' => 'string',
                    'validate_callback' => 'sanitize_text_field'
                ],
                'event_sort' => [
                    'required' => false,
                    'default' => false,
                    'type' => 'boolean',
                    'validate_callback' => [__CLASS__, 'validate_boolean']
                ],
                'date_filter' => [
                    'required' => false,
                    'default' => false,
                    'type' => 'boolean',
                    'validate_callback' => [__CLASS__, 'validate_boolean']
                ],
                'per_page' => [
                    'required'          => false,
                    'type'              => 'integer',
                    'default'           => 10, // Default to 10 items per page
                    'sanitize_callback' => 'absint',
                    'validate_callback' => [__CLASS__, 'validate_numeric'],
                ],
                'page' => [
                    'required'          => false,
                    'type'              => 'integer',
                    'default'           => 1, // Default to the first page
                    'sanitize_callback' => 'absint',
                    'validate_callback' => [__CLASS__, 'validate_numeric'],
                ]
            ],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('kraken/v1', '/events-expanded', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_events_expanded'],
            'permission_callback' => '__return_true',
            'args' => array(
                'page' => array(
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ),
                'per_page' => array(
                    'default' => 10,
                    'sanitize_callback' => function ($value) {
                        return intval($value);
                    },
                ),
                'offset' => array(
                    'default' => 0,
                    'sanitize_callback' => 'absint',
                ),
                'search' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'event_category' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'exclude_category' => array( // Added exclude_category parameter
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'eventastic_cities' => array(
                    'default' => '',
                    'sanitize_callback' => 'absint',
                ),
                'date_filter' => array(
                    'default' => 'false',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'start_date' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'end_date' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'order' => array(
                    'default' => 'ASC',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'orderby' => array(
                    'default' => 'meta_value',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
    }

    public static function validate_numeric($value, $request, $param)
    {
        return is_numeric($value);
    }

    public static function validate_boolean($value, $request, $param)
    {
        return is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
    }

    public static function get_custom_events($request)
    {
        global $wpdb;

        $start_date = $request->get_param('start_date');
        $end_date = $request->get_param('end_date');
        $search = $request->get_param('search');
        $category = $request->get_param('category');
        $categories = $request->get_param('categories');
        $date_filter = $request->get_param('date_filter');

        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $offset = ($page - 1) * $per_page;

        $query_from_joins = "FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} start_meta ON p.ID = start_meta.post_id AND start_meta.meta_key = 'event_start_date'
                LEFT JOIN {$wpdb->postmeta} end_meta ON p.ID = end_meta.post_id AND end_meta.meta_key = 'event_end_date'";

        $query_conditions = [];
        $query_params = [];
        $join_for_categories = false;

        $query_conditions[] = "p.post_type = 'event'";
        $query_conditions[] = "p.post_status = 'publish'";

        if ($search) {
            $query_conditions[] = "p.post_title LIKE %s";
            $query_params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        if ($category && $category !== '0') {
            $join_for_categories = true;
            $query_conditions[] = "tt.taxonomy = 'event_category' AND t.term_id = %d";
            $query_params[] = $category;
        } elseif (!empty($categories)) {
            $join_for_categories = true;
            $category_ids = array_map('intval', explode(',', $categories));

            if (!empty($category_ids)) {
                $placeholders = implode(',', array_fill(0, count($category_ids), '%d'));
                $query_conditions[] = "tt.taxonomy = 'event_category' AND t.term_id IN ($placeholders)";
                $query_params = array_merge($query_params, $category_ids);
            }
        }

        if ($date_filter) {
            $start_date_valid = !empty($start_date) && preg_match('/^\d{8}$/', $start_date);
            $end_date_valid = !empty($end_date) && preg_match('/^\d{8}$/', $end_date);

            if ($start_date_valid && $end_date_valid) {
                $query_conditions[] = "CAST(start_meta.meta_value AS UNSIGNED) <= %s AND CAST(end_meta.meta_value AS UNSIGNED) >= %s";
                $query_params[] = $end_date;
                $query_params[] = $start_date;
            } elseif ($start_date_valid) {
                $query_conditions[] = "CAST(end_meta.meta_value AS UNSIGNED) >= %s";
                $query_params[] = $start_date;
            } elseif ($end_date_valid) {
                $today_date = current_time('Ymd');
                $query_conditions[] = "CAST(start_meta.meta_value AS UNSIGNED) <= %s AND CAST(end_meta.meta_value AS UNSIGNED) >= %s";
                $query_params[] = $end_date;
                $query_params[] = $today_date;
            }

            if ((!empty($start_date) && !$start_date_valid) || (!empty($end_date) && !$end_date_valid)) {
                return new \WP_Error('invalid_date_format', __('Provided dates must be in Ymd format (e.g., 20250701).'), ['status' => 400]);
            }
        }

        if ($join_for_categories) {
            $query_from_joins .= " INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id";
        }

        $where_clause = '';
        if (!empty($query_conditions)) {
            $where_clause = ' WHERE ' . implode(' AND ', $query_conditions);
        }

        $count_query = "SELECT COUNT(DISTINCT p.ID) " . $query_from_joins . $where_clause;
        $prepared_count_query = $wpdb->prepare($count_query, $query_params);
        $total_events = (int) $wpdb->get_var($prepared_count_query);

        $query = "SELECT DISTINCT p.ID, p.post_title, p.post_excerpt, p.post_content " . $query_from_joins . $where_clause;
        $query .= " ORDER BY start_meta.meta_value ASC";
        $query .= " LIMIT %d, %d";

        $query_params[] = $offset;
        $query_params[] = $per_page;

        $prepared_query = $wpdb->prepare($query, $query_params);
        $results = $wpdb->get_results($prepared_query);

        $events = [];

        foreach ($results as $event) {
            $event_id = $event->ID;
            $acf_fields = get_fields($event_id);
            $meta_values = self::get_events_meta_for_rest(['id' => $event_id], '', $request);
            $categories = get_the_terms($event_id, 'event_category');

            $event_data = [
                'id' => $event_id,
                'excerpt' => $event->post_excerpt,
                'acf_fields' => $acf_fields,
                'featured_image' => get_the_post_thumbnail_url($event_id, 'full'),
                'permalink' => get_permalink($event_id),
                'content' => $event->post_content,
                'title' => array('rendered' => $event->post_title),
                'categories' => $categories,
                'events_meta' => $meta_values,
            ];
            $events[] = $event_data;
        }

        $response = new \WP_REST_Response($events);
        $response->header('X-WP-Total', $total_events);
        $response->header('X-WP-TotalPages', ceil($total_events / $per_page));

        return $response;
    }

    /**
     * Retrieves and expands events into individual, single-day instances for a given date range.
     *
     * This endpoint is designed for calendar integrations. It queries for 'event'
     * post types that overlap with a specified date range. It then processes each result
     * to "expand" it into a series of single-day event objects.
     *
     * Expansion logic handles two main cases:
     * 1. Simple Multi-Day Events: If an event has a start and end date (e.g., a 3-day festival),
     * it creates a unique event instance for each day in that range.
     * 2. Specific Recurrence: If an event has specific recurrence dates stored in the
     * 'event_repeat_dates' post meta, it uses those dates to create the instances instead.
     *
     * The final list of all generated instances is then sorted by date and time before being
     * paginated and returned in the response.
     *
     * @since 1.5
     *
     * @param \WP_REST_Request $request The full REST API request object. The following query parameters are used:
     * @type string $start_date     Optional. The start of the date range to filter events (e.g., '2025-10-01').
     *  Effective only when `date_filter` is 'true'.
     * @type string $end_date       Optional. The end of the date range to filter events (e.g., '2025-10-31').
     *  Effective only when `date_filter` is 'true'.
     * @type string $date_filter    Optional. Set to 'true' to enable filtering by `start_date` and `end_date`.
     *  Defaults to 'false', which shows only current and future events.
     * @type string $event_category Optional. A comma-separated string of event category term IDs to filter results.
     * @type string $search         Optional. A search string to filter events by their title.
     * @type int    $per_page       Optional. Number of items to return per page. Use -1 to retrieve all items. Default 10.
     * @type int    $page           Optional. The page number for pagination. Default 1.
     *
     * @return \WP_REST_Response An API response object. The body contains an array of expanded event instances.
     * The response includes 'X-WP-Total' and 'X-WP-TotalPages' headers for pagination.
     */
    public static function get_events_expanded($request)
    {
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');
        $offset = $request->get_param('offset');
        $search = $request->get_param('search');
        $event_category = $request->get_param('event_category');
        $exclude_category = $request->get_param('exclude_category'); // Get the new param
        $date_filter = $request->get_param('date_filter');
        $start_date = $request->get_param('start_date');
        $end_date = $request->get_param('end_date');

        $filter_start_date_ymd = !empty($start_date) ? date('Ymd', strtotime($start_date)) : null;
        $filter_end_date_ymd = !empty($end_date) ? date('Ymd', strtotime($end_date)) : null;

        $args = array(
            'post_type' => 'event',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array('relation' => 'OR'),
        );

        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        $tax_query = [];
        if (!empty($event_category)) {
            $category_ids = array_map('intval', explode(',', $event_category));
            $tax_query[] = array(
                'taxonomy' => 'event_category',
                'field'    => 'term_id',
                'terms'    => $category_ids,
            );
        }
    
        if (!empty($exclude_category)) {
            $exclude_ids = array_map('intval', explode(',', $exclude_category));
            $tax_query[] = array(
                'taxonomy' => 'event_category',
                'field'    => 'term_id',
                'terms'    => $exclude_ids,
                'operator' => 'NOT IN',
            );
        }
        
        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'AND';
            }
            $args['tax_query'] = $tax_query;
        }

        if ($date_filter === 'true') {
            $effective_start_date = $filter_start_date_ymd ?: date('Ymd');
            
            // Condition 1: Event must end on or after the period starts.
            $args['meta_query'][] = array(
                'key' => 'event_end_date',
                'value' => intval($effective_start_date), // Force integer casting
                'compare' => '>=',
                'type' => 'NUMERIC',
            );

            // Condition 2: Event must start on or before the period ends.
            if ($filter_end_date_ymd) {
                $args['meta_query'][] = array(
                    'key' => 'event_start_date',
                    'value' => intval($filter_end_date_ymd), // Force integer casting
                    'compare' => '<=',
                    'type' => 'NUMERIC',
                );
            }
        } else {
            $args['meta_query'][] = array(
                'key' => 'event_end_date',
                'value' => date('Ymd'),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        $query = new \WP_Query($args);
        $all_event_instances = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                $event_categories = get_the_terms($post_id, 'event_category');

                $base_event = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'link' => get_permalink(),
                    'categories' => ($event_categories && !is_wp_error($event_categories)) ? $event_categories : [],
                    'acf' => array(
                        'event_start_date' => get_post_meta($post_id, 'event_start_date', true),
                        'event_end_date' => get_post_meta($post_id, 'event_end_date', true),
                        'event_start_time' => get_post_meta($post_id, 'event_start_time', true),
                        'event_end_time' => get_post_meta($post_id, 'event_end_time', true),
                        'events_addr_multi' => get_post_meta($post_id, 'events_addr_multi', true),
                        'events_addr1' => get_post_meta($post_id, 'events_addr1', true),
                        'events_addr2' => get_post_meta($post_id, 'events_addr2', true),
                        'events_city' => get_post_meta($post_id, 'events_city', true),
                        'events_state' => get_post_meta($post_id, 'events_state', true),
                        'events_zip' => get_post_meta($post_id, 'events_zip', true),
                        'events_url' => get_post_meta($post_id, 'events_url', true),
                        'events_recurrence_options' => get_post_meta($post_id, 'events_recurrence_options', true)
                    ),
                    'kraken' => array(
                        'event_featured_img' => get_the_post_thumbnail_url($post_id, 'full') ?: null,
                        'event_featured_img_alt' => get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true),
                    ),
                );

                $repeat_dates_json = get_post_meta($post_id, 'event_repeat_dates', true);
                $decoded_dates = !empty($repeat_dates_json) ? json_decode($repeat_dates_json) : null;

                if (!empty($decoded_dates) && is_array($decoded_dates)) {
                    foreach ($decoded_dates as $date_info) {
                        $instance = $base_event;
                        $date_value = $date_info->date;
                        $instance['id'] = $post_id . '_' . $date_value;
                        $instance['acf']['event_start_date'] = $date_value;
                        $instance['acf']['event_end_date'] = $date_value;
                        if (isset($date_info->start_time)) {
                            $instance['acf']['event_start_time'] = date("g:i a", strtotime($date_info->start_time));
                        }
                        if (isset($date_info->end_time)) {
                            $instance['acf']['event_end_time'] = date("g:i a", strtotime($date_info->end_time));
                        }
                        $all_event_instances[] = $instance;
                    }
                } else if ($base_event['acf']['event_recurrence_options'] === 'daily') {
                    $start_str = $base_event['acf']['event_start_date'];
                    $end_str = $base_event['acf']['event_end_date'];

                    // only with daily
                    // Ensure we have valid dates to work with
                    if ($start_str && $end_str) {
                        try {
                            $current_date = new \DateTime($start_str);
                            $end_date = new \DateTime($end_str);

                            // Loop through each day from start to end (inclusive)
                            while ($current_date <= $end_date) {
                                $instance = $base_event;
                                $date_value = $current_date->format('Ymd');
                                
                                // Create a unique ID for this instance
                                $instance['id'] = $post_id . '_' . $date_value;
                                
                                // Set the start and end date for this specific instance
                                $instance['acf']['event_start_date'] = $date_value;
                                $instance['acf']['event_end_date'] = $date_value;
                                
                                $all_event_instances[] = $instance;
                                
                                // Move to the next day
                                $current_date->modify('+1 day');
                            }
                        } catch (\Exception $e) {
                            // If date parsing fails, fall back to adding just the base event
                            $all_event_instances[] = $base_event;
                        }
                    } else {
                        // If dates are missing, add the base event as a single instance
                         $all_event_instances[] = $base_event;
                    }
                }
            }
            wp_reset_postdata();
        }

        $filtered_events = $all_event_instances;
        if ($date_filter === 'true' && ($filter_start_date_ymd || $filter_end_date_ymd)) {
            $effective_start_date = $filter_start_date_ymd ?: date('Ymd');
            $filtered_events = array_values(array_filter($all_event_instances, function ($event) use ($effective_start_date, $filter_end_date_ymd) {
                $event_date = $event['acf']['event_start_date'];
                $passes_start = $event_date >= $effective_start_date;
                $passes_end = empty($filter_end_date_ymd) || $event_date <= $filter_end_date_ymd;
                return $passes_start && $passes_end;
            }));
        }

        usort($filtered_events, function ($a, $b) {
            $date_a = $a['acf']['event_start_date'];
            $date_b = $b['acf']['event_start_date'];
            if ($date_a === $date_b) {
                $time_a = $a['acf']['event_start_time'] ?: '00:00:00';
                $time_b = $b['acf']['event_start_time'] ?: '00:00:00';
                return strcmp($time_a, $time_b);
            }
            return strcmp($date_a, $date_b);
        });

        $total_events = count($filtered_events);
        $paginated_events = [];
        $total_pages = 0;

        if (intval($per_page) === -1) {
            $paginated_events = $filtered_events;
            $total_pages = 1;
        } else {
            if (empty($offset) && $page > 1) {
                $offset = ($page - 1) * $per_page;
            }
            $paginated_events = array_slice($filtered_events, $offset, $per_page);
            $total_pages = $per_page > 0 ? ceil($total_events / $per_page) : 1;
        }

        $response = new \WP_REST_Response($paginated_events, 200);
        $response->header('X-WP-Total', $total_events);
        $response->header('X-WP-TotalPages', $total_pages);

        return $response;
    }


    /**
     * Callback function to get all events post meta fields
     */
    public static function get_events_meta_for_rest($object, $field_name, $request)
    {
        $meta_keys = array(
            'event_start_date',
            'event_end_date',
            'event_start_time',
            'event_end_time',
            'events_addr_multi',
            'events_addr1',
            'events_addr2',
            'events_city',
            'events_state',
            'events_zip',
            'events_event_all_day',
            'event_repeat_dates',
            'events_recurrence_options'
        );

        $meta_values = [];
        foreach ($meta_keys as $key) {
            $meta_values[$key] = maybe_unserialize(get_post_meta($object['id'], $key, true));
        }
        $feat_img_array = null;
        $img_alt = null;
        if (!empty($object['featured_media'])) {
            $feat_img_array = wp_get_attachment_image_url($object['featured_media'], 'full', false);
            $img_alt = get_post_meta($object['featured_media'], '_wp_attachment_image_alt', true);

            $meta_values['events_featured_img'] = $feat_img_array;
            $meta_values['events_featured_img_alt'] = $img_alt;
        } else {
            $meta_values['events_featured_img'] = null;
            $meta_values['events_featured_img_alt'] = null;
        }
        $meta_values['events_featured_img'] = $feat_img_array;
        $meta_values['events_featured_img_alt'] = $img_alt;

        $venue_terms = get_the_terms($object['id'], 'events_venues');
        $organizer_terms = get_the_terms($object['id'], 'events_organizers');

        $meta_values['venue'] = [];
        if ($venue_terms && !is_wp_error($venue_terms)) {
            foreach ($venue_terms as $venue_term) {
                $acf_fields = get_fields('term_' . $venue_term->term_id);
                $meta_values['venue'][] = [
                    'term_id' => $venue_term->term_id,
                    'name' => $venue_term->name,
                    'latitude' => $acf_fields['latitude'] ?? '',
                    'longitude' => $acf_fields['longitude'] ?? '',
                    'street_address' => $acf_fields['street_address'] ?? '',
                    'street_address_2' => $acf_fields['street_address_2'] ?? '',
                    'city' => $acf_fields['city'] ?? '',
                    'state' => $acf_fields['state'] ?? '',
                    'zip' => $acf_fields['zip'] ?? '',
                    'phone' => $acf_fields['phone'] ?? '',
                    'email' => $acf_fields['email'] ?? '',
                    'website' => $acf_fields['website'] ?? '',
                ];
            }
        }

        $meta_values['organizer'] = [];
        if ($organizer_terms && !is_wp_error($organizer_terms)) {
            foreach ($organizer_terms as $organizer_term) {
                $meta_values['organizer'][] = [
                    'term_id' => $organizer_term->term_id,
                    'name' => $organizer_term->name,
                    'acf_fields' => get_fields('term_' . $organizer_term->term_id)
                ];
            }
        }

        return $meta_values;
    }
}