<?php
/**
 * Walks a WordPress uploads directory for files that match the
 *  pattern. Then attempts to find the file name in post content. If
 *  it's not present, the upload is deleted from the db and system. 
 */

set_time_limit(0);
ini_set('memory_limit', '-1');

if ($argc < 3) {
    echo "Usage: php _scaled_images.php <pattern> <directory> [--debug]".PHP_EOL;
    exit(1);
}

$pattern = $argv[1];
$directory = realpath($argv[2]);
$debugMode = in_array('--debug', $argv);

$batchSize = 100;
$totalFilesFound = 0;
$totalFilesDeleted = 0;

if (! $directory || !is_dir($directory)) {
    echo "[ERROR] Invalid directory: {$argv[2]}".PHP_EOL;
    exit(1);
}

// load wp
$wordpress_path = dirname(__DIR__) . '/wp-load.php';
if (!file_exists($wordpress_path)) {
    echo "[ERROR] wp-load.php not found".PHP_EOL;
    exit(1);
}
require_once $wordpress_path;

global $wpdb;
if (! isset($wpdb)) {
    echo "[ERROR] WordPress database connection not found".PHP_EOL;
    exit(1);
}

// Find files matching the pattern (ignoring WordPress thumbnails)
$files = localFindFiles($directory, $pattern);
$logEntries = [];

$logEntries[] = sprintf("[OK] Searching for %s in %s", $pattern, $directory);

foreach ($files as $batch) {
    $totalFilesFound += count($batch);

    foreach ($batch as $file) {
        if (localIsWordPressThumbnail($file)) {
            continue; 
        }

        $inContent = localIsImageInContent($file);
        $attachmentId = localGetAttachmentId($file);
        
        if ($inContent) {
            $logEntries[] = sprintf("[SKIP] %s - found in content", $file);
            continue;
        }

        if ($attachmentId) {
            $logEntries[] = localDeleteAttachment($attachmentId, $debugMode);
            $logEntries[] = sprintf("[DELETE] %s from WP", $file);
        } else {
            $logEntries[] = sprintf("[404] %s", $file);
        }

        if (! $debugMode) {
            $logEntries[] = sprintf("[DELETE] %s from wp-uploads", $file);
            unlink($file);
            $totalFilesDeleted++;
        } else {
            echo "[DEBUG] Would delete file {$file}".PHP_EOL;
            $totalFilesDeleted++;
        }
    }
}

// Log output
echo implode(PHP_EOL, $logEntries).PHP_EOL;

// Display Summary
echo "\nSummary:\n";
echo "Total files found: $totalFilesFound\n";

if ($debugMode) {
    echo "Number of files to delete: $totalFilesDeleted\n";
} else {
    echo "Number of files deleted: $totalFilesDeleted\n";
}

/**
 * Recursively find files while ignoring WordPress thumbnails
 */
function localFindFiles(string $dir, string $pattern, int $batchSize = 50): iterable {
    $batch = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($iterator as $file) {
        if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
            if (!localIsWordPressThumbnail($file->getFilename())) {
                $batch[] = $file->getPathname();
                
                if (count($batch) >= $batchSize) {
                    yield $batch;
                    $batch = [];
                }
            }
        }
    }

    if (!empty($batch)) {
        yield $batch;
    }
}

/**
 * Check if an image is referenced in WordPress content or post meta
 */
function localIsImageInContent (string $filename): bool {
    global $wpdb;
    $basename = basename($filename);
    
    $query = $wpdb->prepare("
        SELECT COUNT(*) FROM {$wpdb->posts} 
        WHERE post_content LIKE %s 
        OR post_title LIKE %s
    ", "%$basename%", "%$basename%");
    
    $foundInPosts = ($wpdb->get_var($query) > 0);

    $query = $wpdb->prepare("
        SELECT COUNT(*) FROM {$wpdb->postmeta} 
        WHERE meta_value LIKE %s
    ", "%$basename%");
    
    $foundInMeta = ($wpdb->get_var($query) > 0);

    return $foundInPosts || $foundInMeta;
}

/**
 * Check if an image is an attachment in WordPress
 */
function localGetAttachmentId (string $filename): ?int {
    global $wpdb;
    $basename = basename($filename);
    
    $query = $wpdb->prepare("
        SELECT ID FROM {$wpdb->posts} 
        WHERE post_type = 'attachment' 
        AND post_title = %s
    ", $basename);
    
    return $wpdb->get_var($query) ?: null;
}

/**
 * Delete an attachment from WordPress
 */
function localDeleteAttachment (int $attachment_id, bool $debugMode): string {
    return $debugMode 
        ? "[DEBUG] Delete attachment ID {$attachment_id}" . PHP_EOL 
        : wp_delete_attachment($attachment_id, true);
}

/**
 * Check if a filename matches a WordPress-generated thumbnail
 */
function localIsWordPressThumbnail (string $filename): bool {
    return preg_match('/-\d{2,4}x\d{2,4}\.(jpg|jpeg|png|gif|webp)$/i', $filename);
}
?>
