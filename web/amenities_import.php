<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- SCRIPT CONFIGURATION ---
$csv_file_path = __DIR__ . '/amenities_export.csv';
$row_limit_for_testing = 0; // Set to 0 to process all rows

// Load WordPress
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php')) {
    die("❌ WordPress not found. Check the path to wp-load.php");
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Set execution limits
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

echo "<pre>";
echo "🚀 Starting CSV Import Script\n";
echo "📁 CSV File: $csv_file_path\n";

if (!file_exists($csv_file_path)) {
    die("❌ CSV file not found at: $csv_file_path\n");
}

$handle = fopen($csv_file_path, 'r');
if (!$handle) {
    die("❌ Failed to open CSV file.\n");
}

// Get header and map columns to indices
$header = fgetcsv($handle);
$column_map = array_flip($header);

// Validate required columns exist in the CSV
$required_columns = ['post_id', 'meta_key', 'meta_value'];
foreach ($required_columns as $col) {
    if (!isset($column_map[$col])) {
        die("❌ CSV is missing required column: '$col'. Please check the header row.");
    }
}

echo "📋 Header found: " . implode(', ', $header) . "\n";

// --- Initialize Counters ---
$imported = 0;
$skipped = 0;
$processed = 0;
$no_change = 0;
$verified_items = []; // Array to store a few items for dynamic verification

echo "📥 Starting import from CSV...\n\n";

// --- Main Processing Loop ---
while (($row = fgetcsv($handle)) !== false) {
    if ($row_limit_for_testing > 0 && $processed >= $row_limit_for_testing) {
        echo "🏃 Reached testing limit of $row_limit_for_testing rows. Stopping.\n";
        break;
    }
    $processed++;

    $post_id = intval(trim($row[$column_map['post_id']]));
    $meta_key = trim($row[$column_map['meta_key']]);
    $meta_value_raw = trim($row[$column_map['meta_value']]);

    echo "🔄 [Row $processed] Processing Post ID: $post_id, Meta Key: '$meta_key'\n";

    if (empty($post_id) || empty($meta_key)) {
        echo "   ⏭️ Skipping row due to empty Post ID or Meta Key.\n\n";
        $skipped++;
        continue;
    }

    if (empty($meta_value_raw) || $meta_value_raw === 'a:0:{}') {
        echo "   ⏭️ Skipping because meta value is empty.\n\n";
        $skipped++;
        continue;
    }

    if (!get_post($post_id)) {
        echo "   ⏭️ Skipping because Post ID $post_id does not exist.\n\n";
        $skipped++;
        continue;
    }

    try {
        // --- Data Parsing ---
        $cleaned_value = str_replace('""', '"', trim($meta_value_raw, '"'));
        $new_meta_value = [];

        if (strpos($cleaned_value, 'a:') === 0) {
            $unserialized = @unserialize($cleaned_value);
            if (is_array($unserialized)) {
                $new_meta_value = array_values($unserialized);
                echo "   ✅ Unserialized successfully.\n";
            } else {
                if (preg_match_all('/s:\d+:"([^"]+)";/', $cleaned_value, $matches)) {
                    $new_meta_value = $matches[1];
                    echo "   ✅ Parsed successfully using regex fallback.\n";
                } else {
                    echo "   ❌ Failed to parse serialized data. Skipping.\n\n";
                    $skipped++;
                    continue;
                }
            }
        } else {
            $new_meta_value = [$cleaned_value];
            echo "   📝 Treated as a single value.\n";
        }
        
        if (empty($new_meta_value)) {
            echo "   ⏭️ Value is empty after processing. Skipping.\n\n";
            $skipped++;
            continue;
        }

        // --- Data Comparison & Update ---
        $current_meta = get_post_meta($post_id, $meta_key, true);
        if (!is_array($current_meta)) {
            $current_meta = empty($current_meta) ? [] : [$current_meta];
        }

        $current_meta_sorted = $current_meta;
        $new_meta_value_sorted = $new_meta_value;
        sort($current_meta_sorted);
        sort($new_meta_value_sorted);

        if ($current_meta_sorted === $new_meta_value_sorted || !empty($current_meta)) {
            echo "   ✅ NO CHANGE: Meta value is already up-to-date.\n\n";
            $no_change++;
        } else {
            $result = update_post_meta($post_id, $meta_key, $new_meta_value);
            
            if ($result !== false) {
                echo "   ✅ SUCCESS: Meta updated. New value: [" . implode(', ', $new_meta_value) . "]\n\n";
                $imported++;
                // Add the updated item to our verification list (up to 3 items)
                if (count($verified_items) < 3) {
                    $verified_items[] = ['post_id' => $post_id, 'meta_key' => $meta_key];
                }
            } else {
                echo "   ❌ UPDATE FAILED. Trying delete/add method...\n";
                delete_post_meta($post_id, $meta_key);
                $add_result = add_post_meta($post_id, $meta_key, $new_meta_value);

                if ($add_result) {
                    echo "   ✅ SUCCESS: Meta updated via delete/add method.\n\n";
                    $imported++;
                     if (count($verified_items) < 3) {
                        $verified_items[] = ['post_id' => $post_id, 'meta_key' => $meta_key];
                    }
                } else {
                    echo "   ❌ CRITICAL FAILURE: Both methods failed for Post ID $post_id.\n\n";
                    $skipped++;
                }
            }
        }

    } catch (Exception $e) {
        echo "   ❌ EXCEPTION: " . $e->getMessage() . "\n\n";
        $skipped++;
    }

    if (ob_get_level() > 0) { ob_flush(); }
    flush();
}

fclose($handle);

echo "\n🎉 Import Complete!\n";
echo "📊 Results:\n";
echo "   - Processed: $processed rows\n";
echo "   - Imported/Updated: $imported\n";
echo "   - No Change Needed: $no_change\n";
echo "   - Skipped: $skipped\n";

// --- Dynamic Final Verification ---
echo "\n🔍 Quick Verification of Last Updated Items:\n";
if (!empty($verified_items)) {
    foreach ($verified_items as $item) {
        $post = get_post($item['post_id']);
        $post_title = $post ? $post->post_title : 'N/A';
        $test_meta = get_post_meta($item['post_id'], $item['meta_key'], true);
        
        echo "--------------------------------------------------\n";
        echo "Post ID: {$item['post_id']} ('{$post_title}')\n";
        echo "Meta Key: '{$item['meta_key']}'\n";
        echo "Saved Value: " . print_r($test_meta, true) . "\n";
    }
} else {
    echo "No records were imported or updated, so no dynamic verification was run.\n";
}

echo "</pre>";
?>