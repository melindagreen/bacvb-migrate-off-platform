<?php
namespace MaddenMedia\KrakenEvents;

class AdminEventasticConversion {

    public static function init() {

        add_action('admin_menu', [__CLASS__, 'register_conversion_page']);
        add_action('wp_ajax_madden_check_eventastic_data', [__CLASS__, 'ajax_check_eventastic_data']);
        add_action('wp_ajax_madden_create_db_backup', [__CLASS__, 'ajax_create_custom_db_backup']);
        add_action('wp_ajax_madden_convert_events', [__CLASS__, 'ajax_convert_events']);
        add_action('wp_ajax_madden_repair_recurrence', [__CLASS__, 'ajax_repair_recurrence']);

    }

    public static function register_conversion_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        add_submenu_page('kraken-events', 'Eventastic Conversion', 'Eventastic Conversion', 'manage_options', 'kraken-events-conversion', [__CLASS__, 'conversion_page_callback'], 2);
    }
    
    public static function conversion_page_callback() {
       ?>
        <div class="wrap">
            <h1>Eventastic 2.0 &#10132; Kraken Events</h1>
            <h3>Safely convert Eventastic 2.0 events to Kraken Events</h3>
            <hr>
            <p class="medium-font" >Follow these steps in chronological order to properly convert your events data from Eventastic 2.0 to Kraken Events.<br>
                Please note that this will <strong>NOT</strong> make adjustments to any pages that are using the Eventastic Events Page template. The check function below will let you know if pages are using Eventastic Events Page template.</p>
            <form method="post">
                <?php wp_nonce_field('custom_tools_action', 'custom_tools_nonce'); ?>
                <div>
                    <label>1. Check if there Eventastic events on the site.</label>
                </div>
                <div>
                    <button type="button" id="check-eventastic" class="button button-primary">Check for Eventastic Data</button>
                    <span id="check-spinner" style="display:none;" class="spinner is-active"></span>
                    <div id="check-message"></div>

                </div>
                <div>
                    <label>2. Create a database backup</label>
                </div>
                <div>
                    <p style="padding-block:0;margin-block:0;">This should be done in Platform.sh by creating a manual backup.</p>
                </div>
                <div>
                    <label>3. Convert event data for Kraken Events</label>
                </div>
                <div>
                    <button type="button" id="convert-events" class="button button-secondary" disabled>Convert Events</button><span id="convert-spinner" style="display:none;" class="spinner is-active"></span>
                    <p>This step will convert the metadata & taxonomies associated with the <code>event</code> post type, run the Kraken Events cron job and then deactivate the Eventastic plugin. This step may take awhile
                depending on how many events you have.</p>
                    <div id="convert-message"></div>
                </div>
                <div>
                    <label>4. Repair Recurring Events</label>
                </div>
                <div>
                    <button type="button" id="repair-recurrence" class="button" disabled >Repair Recurrence</button><span id="repair-spinner" style="display:none;" class="spinner is-active"></span>
                    <p>This will correct recurrence data so it functions correctly in Kraken Events.</p>
                    <div id="repair-message"></div>
                </div>

            </form>
            <p><a href="edit.php?post_type=event" id="open-in-new-tab">Go to Events</a></p>
        </div><style type="text/css">
            .wrap p.medium-font {
                font-size: 14px;
                font-weight: 500;
                margin-bottom: 2rem
            }
            .wrap form {
                max-width:900px;
                display: grid;
                grid-template-columns: minmax(0,1fr) minmax(0,3fr);
                gap: 1.5rem;
                width: 100%;
            }
            .wrap form label {
                font-weight: 500;
            }
            .wrap form div button {
                display: inline-block;
            }
            .wrap form div span.spinner {
                float: unset;
                margin-top: 0.33rem;
            }
        </style>
        <script type="text/javascript">
            document.getElementById('check-eventastic').addEventListener('click', function () {
                const spinner = document.getElementById('check-spinner');
                const messageDiv = document.getElementById('check-message');
                const actionButtons = ['create-db-backup', 'convert-events', 'repair-recurrence'];

                spinner.style.display = 'inline-block';
                messageDiv.innerHTML = '';

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'madden_check_eventastic_data',
                        _ajax_nonce: '<?php echo wp_create_nonce(); ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    spinner.style.display = 'none';

                    if (data.success) {
                        messageDiv.innerHTML = `<div class="notice notice-success"><p>${data.data.message}</p></div>`;
                        actionButtons.forEach(id => {
                            const btn = document.getElementById(id);
                            if (btn) btn.removeAttribute('disabled');
                        });
                    } else {
                        messageDiv.innerHTML = `<div class="notice notice-error"><p>${data.data.message}</p></div>`;
                        actionButtons.forEach(id => {
                            const btn = document.getElementById(id);
                            if (btn) btn.setAttribute('disabled', 'disabled');
                        });
                    }
                });
            });

            document.getElementById('convert-events').addEventListener('click', function () {
                const spinner = document.getElementById('convert-spinner');
                const messageDiv = document.getElementById('convert-message');

                spinner.style.display = 'inline-block';
                messageDiv.innerHTML = '';

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'madden_convert_events',
                        _ajax_nonce: '<?php echo wp_create_nonce(); ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    spinner.style.display = 'none';
                    if (data.success) {
                        const messages = data.data.messages.map(msg => `<li>${msg}</li>`).join('');
                        messageDiv.innerHTML = `<div class="notice notice-success"><ul>${messages}</ul></div>`;
                    } else {
                        messageDiv.innerHTML = `<div class="notice notice-error"><p>${data.data.message}</p><p>${data.data.output}</p></div>`;
                    }
                });
            });
            document.getElementById('repair-recurrence').addEventListener('click', function () {
                const spinner = document.getElementById('repair-spinner');
                const messageDiv = document.getElementById('repair-message');

                spinner.style.display = 'inline-block';
                messageDiv.innerHTML = '';

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'madden_repair_recurrence',
                        _ajax_nonce: '<?php echo wp_create_nonce(); ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    spinner.style.display = 'none';
                    if (data.success) {
                        messageDiv.innerHTML = `<div class="notice notice-success"><p>${data.data.message}</p></div>`;
                    } else {
                        messageDiv.innerHTML = `<div class="notice notice-error"><p>${data.data.message}</p></div>`;
                    }
                });
            });
            </script>

       <?php
    }

    /**
     * Ajax function to confirm Eventastic data exists.
     * If Eventastic data exists, the rest of the buttons to
     * convert data are enabled, If none exists, the buttons remain
     * disabled.
     */
    public static function ajax_check_eventastic_data() {
        global $wpdb;
    
        $legacy_data_found = false;
        $legacy_template_found = false;
        $messages = [];
    
        // Check for Eventastic meta keys
        $eventastic_meta = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->postmeta}
            WHERE meta_key LIKE 'eventastic\_%'
            LIMIT 1
        ");
    
        if ($eventastic_meta > 0) {
            $legacy_data_found = true;
            $messages[] = '✅ Eventastic custom fields were found.';
        }
    
        // Check for Eventastic page templates
        $eventastic_templates = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$wpdb->postmeta}
            WHERE meta_key = '_wp_page_template' AND meta_value LIKE %s
        ", '%eventastic-events.php'));

        if ($eventastic_templates > 0) {
            $legacy_template_found = true;
            $messages[] = '⚠️ One or more pages are using the Eventastic Events Page template. Make sure you convert that page manually after converting your event data.';
        }
    
        if ($legacy_data_found || $legacy_template_found) {
            wp_send_json_success([
                'message' => implode('<br>', $messages)
            ]);
        } else {
            wp_send_json_error([
                'message' => 'ℹ️ No Eventastic data or templates were found on this site.'
            ]);
        }
    }
    

    /**
     * Function that runs SQL queries to convert Eventastic data into
     * Kraken Events data, deactives Eventastic and runs Kraken Events cron.
     */
    public static function ajax_convert_events() {

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized.']);
        }

        $steps = [];

        // 🔁 Step 1: SQL conversion queries
        // Queries are run in batches through foreach loops
        // to help with performance.
        global $wpdb;

        $sql_queries = <<<SQL
            UPDATE {$wpdb->postmeta}
            SET meta_key = REPLACE(meta_key, 'eventastic_', 'events_')
            WHERE meta_key LIKE 'eventastic_%';

            UPDATE {$wpdb->postmeta}
            SET meta_key = REPLACE(meta_key, '_eventastic_', '_events_')
            WHERE meta_key LIKE '_eventastic_%';

            UPDATE {$wpdb->term_taxonomy}
            SET taxonomy = 'events_venues'
            WHERE taxonomy = 'eventastic_venues';

            UPDATE {$wpdb->term_taxonomy}
            SET taxonomy = 'events_organizers'
            WHERE taxonomy = 'eventastic_organizers';

            UPDATE {$wpdb->term_taxonomy}
            SET taxonomy = 'event_category'
            WHERE taxonomy = 'eventastic_categories';

            UPDATE {$wpdb->terms}
            SET slug = 'events_venues'
            WHERE slug = 'eventastic_venues';

            UPDATE {$wpdb->terms}
            SET slug = 'events_organizers'
            WHERE slug = 'eventastic_organizers';

            UPDATE {$wpdb->terms}
            SET slug = 'event_category'
            WHERE slug = 'eventastic_categories';

            UPDATE {$wpdb->postmeta}
            SET meta_value = 'specific_dates'
            WHERE meta_key = 'events_recurrence_options'
            AND meta_value = 'specific_days';

            UPDATE {$wpdb->postmeta} SET meta_key = 'event_start_time' WHERE meta_key = 'events_start_time';
            UPDATE {$wpdb->postmeta} SET meta_key = 'event_start_date' WHERE meta_key = 'events_start_date';
            UPDATE {$wpdb->postmeta} SET meta_key = 'event_end_time'   WHERE meta_key = 'events_end_time';
            UPDATE {$wpdb->postmeta} SET meta_key = 'event_end_date'   WHERE meta_key = 'events_end_date';
            UPDATE {$wpdb->postmeta} SET meta_key = 'event_phone'      WHERE meta_key = 'events_phone';
            UPDATE {$wpdb->postmeta} SET meta_key = 'event_email'      WHERE meta_key = 'events_email';

        SQL;

        
        $queries = array_filter(array_map('trim', explode(';', $sql_queries)));
        $success_count = 0;
        
        foreach ($queries as $query) {
            if (!$query) continue;
        
            $result = $wpdb->query($query);
        
            if ($result === false) {
                wp_send_json_error([
                    'message'     => 'SQL query failed using $wpdb.',
                    'query'       => $query,
                    'wpdb_error'  => $wpdb->last_error,
                ]);
            }
        
            $success_count++;
        }

        $steps[] = "✅ {$success_count} SQL queries executed on Platform.sh.";
    

        // Correcting the event date format if it is stored in Y-m-d format.
        $query = "
            UPDATE {$wpdb->postmeta}
            SET meta_value = DATE_FORMAT(STR_TO_DATE(meta_value, '%Y-%m-%d'), '%Y%m%d')
            WHERE meta_key IN ('event_start_date', 'event_end_date')
            AND meta_value REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';
            ";

        $wpdb->query( $query );
        $normalized_rows = $wpdb->rows_affected;
        $steps[] = "✅ {$normalized_rows} date values converted from Y-m-d to Ymd using SQL."; 


    

        // ℹ️ Step 2: Run the Kraken Events cron job that sets event_next_occurence
        if (has_action('kraken_events_update_event_occurrences')) {
            do_action('kraken_events_update_event_occurrences');
            $steps[] = '✅ Cron job <code>kraken_events_update_event_occurrences</code> executed.';
        } else {
            $steps[] = '⚠️ Cron hook <code>kraken_events_update_event_occurrences</code> not found.';
        }
        

        // ❌ Step 3: Deactivates Eventastic
        if (is_plugin_active('wp-plugin-eventastic/madden-eventastic.php')) {
            deactivate_plugins('wp-plugin-eventastic/madden-eventastic.php');
            $steps[] = '✅ Eventastic deactivated.';
        } else {
            $steps[] = 'ℹ️ Eventastic was already inactive.';
        }

        // Step 4: Adds the newly converted Eventastic taxonomies to the Kraken Events taxonomies option field.
        $new_taxonomies = [
            [
                'singular' => 'Venue',
                'plural'   => 'Venues',
                'slug'     => 'events_venues',
            ],
            [
                'singular' => 'Organizer',
                'plural'   => 'Organizers',
                'slug'     => 'events_organizers',
            ],
            [
                'singular' => 'Event Category',
                'plural'   => 'Event Categories',
                'slug'     => 'event_category',
            ],
        ];
        
        $json = wp_json_encode($new_taxonomies, JSON_UNESCAPED_SLASHES);
        
        // Check if the option already exists
        $option_exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s",
            'kraken_events_event_taxonomies'
        ));
        
        if ($option_exists) {
            $wpdb->query( $wpdb->prepare(
                "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s",
                $json,
                'kraken_events_event_taxonomies'
            ));
            $steps[] = "✅ Kraken Events taxonomies updated via SQL.";
        } else {
            $wpdb->query( $wpdb->prepare(
                "INSERT INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')",
                'kraken_events_event_taxonomies',
                $json
            ));
            $steps[] = "✅ Kraken Events taxonomies inserted via SQL.";
        }


        // ✅ Conversion complete!
        $steps[] = '<strong>🎉 Event conversion complete!</strong>';

        wp_send_json_success(['messages' => $steps]);
    }

    /**
     * Repairs the data around recurrence with events as the conversion from 
     * Eventastic to Kraken Events is not a seamless one.
     */
    public static function ajax_repair_recurrence() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
    
        global $wpdb;
    
        $posts_fixed = 0;
        $pattern_converted = 0;
        $handled_post_ids = [];
    
        // 🔁 Step 1: Handle 'pattern' recurrence — convert to 'specific_dates' repeater
        $pattern_post_ids = $wpdb->get_col("
            SELECT post_id FROM {$wpdb->postmeta}
            WHERE meta_key = 'events_recurrence_options' AND meta_value = 'pattern'
        ");
    
        foreach ($pattern_post_ids as $post_id) {
            $all_day    = get_post_meta($post_id, 'events_event_all_day', true);
            $start_time = get_post_meta($post_id, 'event_start_time', true);
            $end_time   = get_post_meta($post_id, 'event_end_time', true);
            $dates_raw  = get_post_meta($post_id, 'events_pattern_dates', true);
    
            $dates = maybe_unserialize($dates_raw);
            if (!$dates || !is_array($dates)) {
                continue;
            }
    
            update_post_meta($post_id, 'events_recurrence_options', 'specific_dates');
            update_field('event_specific_dates', [], $post_id); // OK: clears old and boots repeater
    
            foreach ($dates as $date) {
                $parsed = \DateTime::createFromFormat('Ymd', $date) ?: (strtotime($date) ? new \DateTime($date) : null);
                if (!$parsed) continue;
    
                add_row('event_specific_dates', [
                    'date'       => $parsed->format('Ymd'),
                    'all_day'    => $all_day,
                    'start_time' => $start_time,
                    'end_time'   => $end_time,
                ], $post_id);
            }
    
            $handled_post_ids[] = $post_id;
            $pattern_converted++;
        }


        // Step 2. Get specific_days even ts
        $specific_post_ids = $wpdb->get_col("
            SELECT post_id FROM {$wpdb->postmeta}
            WHERE meta_key = 'events_recurrence_options' AND meta_value = 'specific_dates'
        ");
    
        foreach ($specific_post_ids as $post_id) {
            if (in_array($post_id, $handled_post_ids)) continue;

            $all_day    = get_post_meta($post_id, 'events_event_all_day', true);
            $start_time = get_post_meta($post_id, 'event_start_time', true);
            $end_time   = get_post_meta($post_id, 'event_end_time', true);
            $dates_raw  = get_post_meta($post_id, 'events_repeat_dates', true);
    
            $dates = maybe_unserialize($dates_raw);
            if (!$dates || !is_array($dates)) {
                continue;
            }
    
            update_post_meta($post_id, 'events_recurrence_options', 'specific_dates');
            update_field('event_specific_dates', [], $post_id); // OK: clears old and boots repeater
    
            foreach ($dates as $date) {
                $parsed = \DateTime::createFromFormat('Ymd', $date) ?: (strtotime($date) ? new \DateTime($date) : null);
                if (!$parsed) continue;
    
                add_row('event_specific_dates', [
                    'date'       => $parsed->format('Ymd'),
                    'all_day'    => $all_day,
                    'start_time' => $start_time,
                    'end_time'   => $end_time,
                ], $post_id);
            }
    
            $handled_post_ids[] = $post_id;
            $pattern_converted++;
        }

    
        // 🔁 Step 3: Handle 'infinite' recurrence
        $infinite_post_ids = $wpdb->get_col("
            SELECT post_id FROM {$wpdb->postmeta}
            WHERE meta_key = 'events_event_end' AND meta_value = 'infinite'
        ");
    
        foreach ($infinite_post_ids as $post_id) {
            if (in_array($post_id, $handled_post_ids)) continue;
    
            $repeat     = get_post_meta($post_id, 'events_recurring_repeat', true);
            $days_raw   = get_post_meta($post_id, 'events_recurring_days', true);
            $all_day    = get_post_meta($post_id, 'events_event_all_day', true);
            $start_time = get_post_meta($post_id, 'event_start_time', true);
            $end_time   = get_post_meta($post_id, 'event_end_time', true);
    
            $days = maybe_unserialize($days_raw);
            if (!$days || !is_array($days)) continue;
    
            if (empty($repeat)) {
                update_post_meta($post_id, 'events_recurrence_options', 'weekly');
                update_field('event_weekly', [], $post_id); // OK: clears old and boots repeater

                add_row('event_weekly', [
                    'days_of_the_week' => array_map('strtolower', $days),
                    'all_day'          => $all_day,
                    'start_time'       => $start_time,
                    'end_time'         => $end_time,
                ], $post_id);
                $posts_fixed++;
                $handled_post_ids[] = $post_id;
                continue;
            }
    
            if (in_array((int) $repeat, [1, 2, 3, 4])) {
                update_post_meta($post_id, 'events_recurrence_options', 'monthly_by_dotw');
                update_field('monthly_by_dotw', [], $post_id); 
    
                foreach ($days as $day) {
                    add_row('monthly_by_dotw', [
                        'week_number'     => (int) $repeat,
                        'day_of_the_week' => strtolower($day),
                        'all_day'         => $all_day,
                        'start_time'      => $start_time,
                        'end_time'        => $end_time,
                    ], $post_id);
                }
    
                $posts_fixed++;
                $handled_post_ids[] = $post_id;
            }
        }
    
        // 🔁 Step 4: Handle 'finite' or missing recurrence
        $all_event_ids = $wpdb->get_col("
            SELECT ID FROM {$wpdb->posts}
            WHERE post_type = 'event' AND post_status != 'trash'
        ");
    
        foreach ($all_event_ids as $post_id) {
            if (in_array($post_id, $handled_post_ids)) continue;
    
            $event_end_meta = get_post_meta($post_id, 'events_event_end', true);
            $event_recurrence = get_post_meta($post_id, 'events_recurrence_options', true);

            if ($event_end_meta !== 'finite' && !empty($event_end_meta)) {
                continue;
            }

            if ( $event_recurrence === 'monthly_by_dotw' || $event_recurrence === 'specific_dates' || $event_recurrence === 'weekly' ) {
                continue;
            }
    
            $start_date = get_post_meta($post_id, 'event_start_date', true);
            $end_date   = get_post_meta($post_id, 'event_end_date', true);
    
            if (empty($start_date) || empty($end_date)) {
                continue;
            }
    
            if ($start_date === $end_date) {
                update_post_meta($post_id, 'events_recurrence_options', 'one_day');
            } else {
                update_post_meta($post_id, 'events_recurrence_options', 'daily');
            }
    
            $posts_fixed++;
            $handled_post_ids[] = $post_id;
        }
    
        wp_send_json_success([
            'message' => "🎉 Repair complete. {$posts_fixed} recurring events fixed. {$pattern_converted} pattern-based events migrated to specific_dates."
        ]);
    }
}
