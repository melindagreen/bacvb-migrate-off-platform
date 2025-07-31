<?php
/**
 * WordPress Amenities Import Script
 *
 * This script reads a CSV file containing post_id, meta_key, and meta_value
 * for amenities and updates the corresponding post meta in WordPress.
 *
 * IMPORTANT:
 * 1. Place this file in your WordPress root directory.
 * 2. Ensure the CSV file (e.g., 'amenities_export.csv') is in the same directory,
 * or update the $csv_file_path variable.
 * 3. Back up your database before running this script!
 * 4. Access this script via your browser (e.g., http://yourdomain.com/import_amenities.php)
 * or via WP-CLI if configured.
 * 5. The meta_value from the CSV is expected to be in the same serialized format
 * as it was exported (e.g., PHP serialized array).
 */

// Load WordPress environment
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' ); // Adjust path if script is not in root

// Set max execution time to prevent timeouts for large CSVs
set_time_limit(0);

// Define the path to your CSV file
$csv_file_path = __DIR__ . '/amenities_export.csv'; // Assumes CSV is in the same directory as this script

// Log file for errors and success messages
$log_file_path = __DIR__ . '/amenities_import_log.txt';

// Function to log messages
function log_message($message) {
    global $log_file_path;
    file_put_contents($log_file_path, date('[Y-m-d H:i:s]') . ' ' . $message . PHP_EOL, FILE_APPEND);
    echo $message . '<br>'; // Also output to browser for immediate feedback
}

log_message("--- Starting Amenities Import Script ---");

if (!file_exists($csv_file_path)) {
    log_message("ERROR: CSV file not found at: " . $csv_file_path);
    log_message("--- Script Finished with Errors ---");
    exit;
}

$row_count = 0;
$updated_count = 0;
$skipped_count = 0;

if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
    // Read the header row (and skip it if it exists)
    $header = fgetcsv($handle);
    if ($header === false) {
        log_message("ERROR: Could not read CSV header or file is empty.");
        fclose($handle);
        log_message("--- Script Finished with Errors ---");
        exit;
    }

    // Expected header columns (adjust if your CSV has different names/order)
    // Note: The SQL query now uses 'meta_value_csv_friendly' as the column name.
    // Ensure your CSV export uses this name, or adjust this array if it's different.
    $expected_columns = ['post_id', 'meta_key', 'meta_value'];
    $column_map = [];
    foreach ($expected_columns as $expected_col) {
        $index = array_search($expected_col, $header);
        if ($index === false) {
            log_message("ERROR: Missing expected column in CSV header: " . $expected_col);
            fclose($handle);
            log_message("--- Script Finished with Errors ---");
            exit;
        }
        $column_map[$expected_col] = $index;
    }

    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        $row_count++;

        // Skip empty rows
        if (empty(array_filter($data))) {
            log_message("Skipping empty row " . $row_count);
            continue;
        }

        // Ensure row has enough columns
        if (count($data) < max($column_map) + 1) {
            log_message("WARNING: Row " . $row_count . " has too few columns. Skipping.");
            $skipped_count++;
            continue;
        }

        $post_id = intval($data[$column_map['post_id']]);
        $meta_key = sanitize_text_field($data[$column_map['meta_key']]);
        // fgetcsv automatically handles the unquoting and unescaping (e.g., "" becomes ")
        // So, $meta_value will contain the clean PHP serialized string.
        $meta_value = $data[$column_map['meta_value_csv_friendly']];

        if ($post_id <= 0) {
            log_message("WARNING: Invalid Post ID (" . $post_id . ") in row " . $row_count . ". Skipping.");
            $skipped_count++;
            continue;
        }

        if (empty($meta_key)) {
            log_message("WARNING: Empty meta_key in row " . $row_count . " for Post ID " . $post_id . ". Skipping.");
            $skipped_count++;
            continue;
        }

        // Check if the post exists
        if (!get_post($post_id)) {
            log_message("WARNING: Post with ID " . $post_id . " does not exist. Skipping row " . $row_count . ".");
            $skipped_count++;
            continue;
        }

        // Update the post meta
        // update_post_meta will correctly store the PHP serialized string
        $result = update_post_meta($post_id, $meta_key, $meta_value);

        if ($result !== false) {
            $updated_count++;
            log_message("Successfully updated/added meta_key '{$meta_key}' for Post ID: {$post_id}");
        } else {
            // This might mean the value was the same, or an actual error occurred.
            log_message("NOTE: Failed to update or no change for meta_key '{$meta_key}' for Post ID: {$post_id}. (Value might be identical or an error occurred)");
        }
    }
    fclose($handle);
} else {
    log_message("ERROR: Could not open the CSV file: " . $csv_file_path);
}

log_message("--- Import Script Summary ---");
log_message("Total rows processed from CSV: " . $row_count);
log_message("Successfully updated/added meta entries: " . $updated_count);
log_message("Rows skipped due to errors or warnings: " . $skipped_count);
log_message("Check 'amenities_import_log.txt' for detailed logs.");
log_message("--- Script Finished ---");

?>
