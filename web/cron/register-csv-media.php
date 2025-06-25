<?php
require_once dirname(__DIR__) . '/wp-load.php';

$file_path = WP_CONTENT_DIR . '/uploads/listings_with_urls.csv';

if (!file_exists($file_path)) {
    echo "❌ File does not exist: $file_path\n";
    exit;
}

// File info
$filename     = basename($file_path);
$upload_dir   = wp_upload_dir();
$target_path  = $upload_dir['path'] . '/' . $filename;
$target_url   = $upload_dir['url'] . '/' . $filename;

// Move the file to the current upload folder if it's not already there
if ($file_path !== $target_path) {
    if (!copy($file_path, $target_path)) {
        echo "❌ Failed to copy file to upload directory.\n";
        exit;
    }
    echo "✅ Moved file to: $target_path\n";
}

// Register in media library
$attachment = [
    'guid'           => $target_url,
    'post_mime_type' => 'text/csv',
    'post_title'     => sanitize_file_name($filename),
    'post_content'   => '',
    'post_status'    => 'inherit',
];

$attach_id = wp_insert_attachment($attachment, $target_path);

// Generate attachment metadata
require_once ABSPATH . 'wp-admin/includes/image.php';
wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $target_path));

echo "✅ File registered in Media Library with ID: $attach_id\n";
echo "🔗 Access it at: $target_url\n";
