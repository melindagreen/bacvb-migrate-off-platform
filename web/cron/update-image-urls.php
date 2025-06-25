<?php
require_once dirname(__DIR__) . '/wp-load.php';

$csv_path = WP_CONTENT_DIR . '/uploads/listings-update.csv';
$output_path = WP_CONTENT_DIR . '/uploads/listings_with_urls.csv';

if (!file_exists($csv_path)) {
    echo "CSV file not found.\n";
    exit;
}

$input = fopen($csv_path, 'r');
$output = fopen($output_path, 'w');

$input_headers = fgetcsv($input);
$output_headers = $input_headers;
$output_headers[] = 'Updated Image URLs';

fputcsv($output, $output_headers);

while (($row = fgetcsv($input)) !== false) {
    $row_assoc = array_combine($input_headers, $row);

    $id_field = $row_assoc['partnerportal_gallery_images'] ?? '';
    $urls = [];

    if (!empty($id_field)) {
        $ids = json_decode($id_field, true);

        if (!is_array($ids)) {
            // Fallback if stored as "[123,456]"
            $ids = eval("return $id_field;");
        }

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $url = wp_get_attachment_url((int)$id);
                if ($url) {
                    $urls[] = $url;
                }
            }
        }
    }

    $row_assoc['Updated Image URLs'] = implode('|', $urls);

    // Write output using updated header list
    $output_row = [];
    foreach ($output_headers as $header) {
        $output_row[] = $row_assoc[$header] ?? '';
    }

    fputcsv($output, $output_row);
}

fclose($input);
fclose($output);

echo "✅ Updated CSV written to: $output_path\n";
