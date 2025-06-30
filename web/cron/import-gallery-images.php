<?php
// Load WordPress environment
require_once dirname(__DIR__) . '/wp-load.php';

// Path to CSV file in wp-content/uploads
$csv_path = WP_CONTENT_DIR . '/uploads/listings_w_urls.csv';

if (!file_exists($csv_path)) {
    echo "CSV not found at $csv_path\n";
    exit;
}

if (($handle = fopen($csv_path, 'r')) !== FALSE) {
    $header = fgetcsv($handle); // Get the column headers

    while (($data = fgetcsv($handle)) !== FALSE) {
        $row = array_combine($header, $data);

        $post_title = trim($row['Title'] ?? '');
        $filenames_raw = trim($row['Image Filename'] ?? '');

        if (empty($post_title) || empty($filenames_raw)) {
            echo "Skipping row with empty title or filenames.\n";
            continue;
        }

        $filenames = preg_split('/\s*[,;|]\s*/', $filenames_raw);
        $filenames = array_map('trim', $filenames);
        echo "   Filenames to match: " . implode(', ', $filenames) . "\n";
        $post = get_page_by_title($post_title, OBJECT, 'listing'); // Change 'listing' to your post type

        if (!$post) {
            echo "❌ Post not found: $post_title\n";
            continue;
        }

        $image_ids = [];

        foreach ($filenames as $filename) {
            $basename = basename($filename);

            $base_title = pathinfo($basename, PATHINFO_FILENAME);

            $attachment = get_posts([
                'post_type' => 'attachment',
                'posts_per_page' => 1,
                'title' => $base_title,
                'orderby' => 'ID', // older first
                'order' => 'DESC',
                'fields' => 'ids',
            ]);


            if (!empty($attachment)) {
                echo "   → Matched title: $base_title, Attachment ID: {$attachment[0]}\n";
                $image_ids[] = (int) $attachment[0];
            } else {
                echo "⚠️  Image not found for: $basename\n";
            }
        }

        if (!empty($image_ids)) {
            echo "   → Updating post ID {$post->ID} for '{$post_title}'\n";
            echo "   Featured: [" . $image_ids[0] . "]\n";
            echo "   Gallery: [" . implode(',', array_slice($image_ids, 1)) . "]\n";

            // Store both as JSON-style arrays
            update_post_meta($post->ID, 'partnerportal_gallery_square_featured_image', '[' . $image_ids[0] . ']');
            update_post_meta($post->ID, 'partnerportal_gallery_images', '[' . implode(',', array_slice($image_ids, 1)) . ']');

            echo "✅ Updated post '{$post_title}' with images: " . implode(',', $image_ids) . "\n";
        } else {
            echo "⚠️  No image IDs found for: $post_title\n";
        }
    }

    fclose($handle);
}
