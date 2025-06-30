<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
// Disable output buffering
while (ob_get_level()) {
    ob_end_flush();
}
ob_implicit_flush(true);

if ($argc < 3) {
    echo "Usage: php _scaled_images.php <pattern> <directory> [--debug] [--orphaned] [--moveonly]".PHP_EOL;
    exit(1);
}

$pattern = $argv[1];
$directory = realpath($argv[2]);
$debugMode = in_array('--debug', $argv);
$orphanedOnly = in_array('--orphaned', $argv);
$moveOnly = in_array('--moveonly', $argv);

if ($debugMode && $moveOnly) {
    echo "[WARN] --moveonly + --debug = dry run; no files will be moved.\n";
}

$flaggedDir = $directory . DIRECTORY_SEPARATOR . 'flagged';
if (!is_dir($flaggedDir)) {
    mkdir($flaggedDir, 0755, true);
}

$batchSize = 100;
$totalFilesFound = 0;
$totalFilesDeleted = 0;
$totalFilesMoved = 0;
$filesFlatList = [];

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

// Find all files matching the pattern
$logEntries[] = sprintf("[OK] Searching for %s in %s", $pattern, $directory);
foreach (localFindFiles($directory, $pattern) as $batch) {
    foreach ($batch as $file) {
        $filesFlatList[] = $file;
    }
}

$totalFilesFound = count($filesFlatList);
$currentIndex = 0;

foreach ($filesFlatList as $file) {
    $currentIndex++;

    if (localIsWordPressThumbnail($file)) {
        continue;
    }

    $inContent = localIsImageInContent($file);
    $attachmentId = localGetAttachmentId($file);

    if ($inContent) {
        $logEntries[] = sprintf("[SKIP] %s - found in content", $file);
    } elseif ($attachmentId) {
        if ($orphanedOnly) {
            // Skip if we're only processing orphaned files
            $logEntries[] = sprintf("[SKIP] %s - registered as attachment (orphaned only mode)", $file);
            continue;
        }

        $logEntries[] = localDeleteAttachment($attachmentId, $debugMode);
        $logEntries[] = sprintf("[DELETE] %s from WP", $file);

        if ($debugMode) {
            echo "[DEBUG] Would " . ($moveOnly ? "move" : "delete") . " file: {$file}" . PHP_EOL;
        } elseif ($moveOnly) {
            $flagPath = $flaggedDir . DIRECTORY_SEPARATOR . uniqid('', true) . '-' . basename($file);
            if (!@rename($file, $flagPath)) {
                $logEntries[] = "[ERROR] Failed to move $file to $flagPath";
            } else {
                $logEntries[] = "[MOVED] $file to flagged/";
                $totalFilesMoved++;
            }
        } else {
            if (unlink($file)) {
                $logEntries[] = "[DELETE] Deleted $file";
                $totalFilesDeleted++;
            } else {
                $logEntries[] = "[ERROR] Failed to delete $file";
            }
        }

    } else {
        $logEntries[] = sprintf("[404] %s", $file);

        if ($debugMode) {
            echo "[DEBUG] Would " . ($moveOnly ? "move" : "delete") . " file: {$file}" . PHP_EOL;
        } elseif ($moveOnly) {
            $flagPath = $flaggedDir . DIRECTORY_SEPARATOR . uniqid('', true) . '-' . basename($file);
            if (!@rename($file, $flagPath)) {
                $logEntries[] = "[ERROR] Failed to move $file to $flagPath";
            } else {
                $logEntries[] = "[MOVED] $file to flagged/";
                $totalFilesMoved++;
            }
        } else {
            if (unlink($file)) {
                $logEntries[] = "[DELETE] Deleted $file";
                $totalFilesDeleted++;
            } else {
                $logEntries[] = "[ERROR] Failed to delete $file";
            }
        }
    }

    // Progress bar
    $progress = floor(($currentIndex / $totalFilesFound) * 100);
    echo "\rProgress: {$progress}% ({$currentIndex}/{$totalFilesFound})";
    flush();
}
echo PHP_EOL;

// Log output
echo implode(PHP_EOL, $logEntries).PHP_EOL;

// Display Summary
echo "\nSummary:\n";
echo "Total files found: $totalFilesFound\n";

if ($debugMode) {
    echo "Number of files that would be deleted: $totalFilesDeleted\n";
    echo "Number of files that would be moved: $totalFilesMoved\n";
} else {
    echo "Number of files deleted: $totalFilesDeleted\n";
    echo "Number of files moved to flagged/: $totalFilesMoved\n";
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

function localIsImageInContent(string $filename): bool {
    global $wpdb;
    $basename = basename($filename);

    // Check if it's referenced in post_content of non-attachment posts
    $query = $wpdb->prepare("
        SELECT COUNT(*) FROM {$wpdb->posts}
        WHERE post_type != 'attachment'
        AND post_content LIKE %s
    ", "%$basename%");
    $foundInPosts = ($wpdb->get_var($query) > 0);

    // Check if used in postmeta of non-attachment posts
    $query = $wpdb->prepare("
        SELECT COUNT(*) FROM {$wpdb->postmeta} m
        INNER JOIN {$wpdb->posts} p ON m.post_id = p.ID
        WHERE p.post_type != 'attachment'
        AND m.meta_value LIKE %s
    ", "%$basename%");
    $foundInMeta = ($wpdb->get_var($query) > 0);

    return $foundInPosts || $foundInMeta;
}

function localGetAttachmentId(string $filename): ?int {
    global $wpdb;
    $basename = basename($filename);
    $query = $wpdb->prepare("
        SELECT ID FROM {$wpdb->posts} 
        WHERE post_type = 'attachment' 
        AND post_title = %s
    ", $basename);
    return $wpdb->get_var($query) ?: null;
}

function localDeleteAttachment(int $attachment_id, bool $debugMode): string {
    if ($debugMode) {
        return "[DEBUG] Delete attachment ID {$attachment_id}";
    }

    $result = wp_delete_attachment($attachment_id, true);

    if ($result instanceof WP_Post) {
        return "[OK] Deleted attachment ID {$attachment_id}";
    }

    return "[ERROR] Failed to delete attachment ID {$attachment_id}";
}


function localIsWordPressThumbnail(string $filename): bool {
    return preg_match('/-\d{2,4}x\d{2,4}\.(jpg|jpeg|png|gif|webp)$/i', $filename);
}
