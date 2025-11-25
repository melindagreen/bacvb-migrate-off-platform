<?php
namespace MaddenMedia\KrakenEvents;


if (!defined('WP_CLI') || ! WP_CLI) {
    return;
}

class CLI_Commands extends \WP_CLI_Command {

    /**
     * Creates the event occurrences table and syncs all event meta.
     *
     * This is a comprehensive command that ensures the database is set up
     * and all events have their recurrence rules and occurrences populated.
     * It is safe to run this command multiple times.
     *
     * ## EXAMPLES
     *
     * wp kraken-events upgrade_database_and_meta
     *
     */
    public function upgrade_database_and_meta() {
        \WP_CLI::line('Starting event database and meta synchronization...');

        // 1. Create the database table (includes a check to see if it exists)
        \WP_CLI::line('Step 1/2: Ensuring event occurrences table exists...');
        PartnerEvents::create_event_occurrences_table();
        \WP_CLI::success('Database table verified.');

        // 2. Run the backfill and meta update process
        \WP_CLI::line('Step 2/2: Processing all events to sync meta and occurrences...');
        $count = PartnerEvents::run_backfill_process();

        if ($count > 0) {
           \WP_CLI::success("Synchronization complete! Processed {$count} events.");
        } else {
            \WP_CLI::success('Synchronization complete. No events found to process.');
        }
    }
}
