<?php
// Load WordPress
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'); // Adjust path if needed

// Increase limits for large imports
set_time_limit(0);
ini_set('memory_limit', '512M');

// CSV file path (adjust as needed)
$csv_path = ABSPATH . 'amenities_export.csv';

if (!file_exists($csv_path)) {
    exit("❌ CSV file not found at: $csv_path");
}

$handle = fopen($csv_path, 'r');
if (!$handle) {
    exit("❌ Failed to open CSV file.");
}

// Skip header
fgetcsv($handle);

$imported = 0;
$skipped = 0;

while (($row = fgetcsv($handle)) !== false) {
    list($post_id, $meta_key, $meta_value) = $row;

    $meta_value = trim($meta_value, '"');
    $meta_value = stripslashes($meta_value);

    // Skip empty serialized values
    if ($meta_value === 'a:0:{}') {
        $skipped++;
        continue;
    }

    $post = get_post($post_id);
    if (!$post) {
        $skipped++;
        continue;
    }

    // Check if meta key exists and has a value
    $existing = get_post_meta($post_id, $meta_key, true);
    if (!empty($existing)) {
        $skipped++;
        continue;
    }

    // Add meta
    update_post_meta($post_id, $meta_key, maybe_unserialize($meta_value));
    $imported++;
}

fclose($handle);

// Output results
echo "<h2>✅ Import Complete</h2>";
echo "<p><strong>Imported:</strong> $imported</p>";
echo "<p><strong>Skipped:</strong> $skipped</p>";
