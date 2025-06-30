<?php
/**
 * Category Reassignment Script for listing_categories taxonomy
 * 
 * This script reads the category-mapping.txt file and reassigns
 * WordPress taxonomy terms from old categories to new categories.
 * 
 * IMPORTANT: Backup your database before running this script!
 * 
 * Usage: 
 * 1. Upload this file to your WordPress root directory
 * 2. Access via browser: yoursite.com/reassign-categories.php
 * 3. Or run via CLI: php reassign-categories.php
 */

// Prevent direct access if not in WordPress context
if (!defined('ABSPATH')) {
    // If running standalone, we need to bootstrap WordPress
    if (!file_exists('wp-config.php')) {
        die('Error: wp-config.php not found. Please place this script in your WordPress root directory.');
    }
    
    // Bootstrap WordPress
    require_once('wp-config.php');
    require_once('wp-load.php');
}

// Security check - only allow admin users
if (!current_user_can('manage_options')) {
    die('Error: Insufficient permissions. You must be an administrator.');
}

class CategoryReassigner {
    private $mapping_file = 'mapping.txt';
    private $taxonomy = 'listing_categories';
    private $mappings = [];
    private $new_categories = [];
    private $errors = [];
    private $success_count = 0;
    private $dry_run = true; // Set to false to actually make changes
    private $categories_to_keep = [
        'names' => [],
        'slugs' => []
    ]; // Track categories that should be kept by name and slug

    public function __construct() {
        $this->parse_mapping_file();
    }

    /**
     * Parse the category mapping file
     */
    private function parse_mapping_file() {
        if (!file_exists($this->mapping_file)) {
            $this->errors[] = "Mapping file not found: {$this->mapping_file}";
            return;
        }

        $lines = file($this->mapping_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments and headers
            if (empty($line) || strpos($line, '#') === 0 || strpos($line, '##') === 0) {
                continue;
            }

            // Parse mapping lines
            if (strpos($line, '->') !== false) {
                $parts = explode('->', $line);
                if (count($parts) >= 2) {
                    $old_part = trim($parts[0]);
                    $new_main = trim($parts[1]);
                    $new_sub = isset($parts[2]) ? trim($parts[2]) : '';

                    // Parse old category info
                    $old_parts = explode(',', $old_part);
                    if (count($old_parts) >= 2) {
                        $old_name = trim($old_parts[0]);
                        $old_slug = trim($old_parts[1]);
                        // $old_count = isset($old_parts[2]) ? trim($old_parts[2]) : null; // Not used

                        // Skip categories marked for removal
                        if (strpos($new_main, '[REMOVE]') !== false || 
                            strpos($new_main, '[LOCATION - CONSIDER REMOVING]') !== false ||
                            strpos($new_main, '[ADMIN - REMOVE]') !== false ||
                            strpos($new_main, '[NAV - REMOVE]') !== false) {
                            continue;
                        }

                        $this->mappings[] = [
                            'old_name' => $old_name,
                            'old_slug' => $old_slug,
                            'new_main' => $new_main,
                            'new_sub' => $new_sub
                        ];

                        // Track categories to keep (both old and new, by name and slug)
                        $this->categories_to_keep['names'][] = $old_name;
                        $this->categories_to_keep['slugs'][] = sanitize_title($old_slug);
                        $this->categories_to_keep['names'][] = $new_main;
                        $this->categories_to_keep['slugs'][] = sanitize_title($new_main);
                        if ($new_sub) {
                            $this->categories_to_keep['names'][] = $new_sub;
                            $this->categories_to_keep['slugs'][] = sanitize_title($new_sub);
                        }

                        // Track new categories
                        if (!isset($this->new_categories[$new_main])) {
                            $this->new_categories[$new_main] = [];
                        }
                        if ($new_sub && !in_array($new_sub, $this->new_categories[$new_main])) {
                            $this->new_categories[$new_main][] = $new_sub;
                        }
                    }
                }
            }
        }
        // Add "Uncategorized" to the list of categories to keep
        $this->categories_to_keep['names'][] = 'Uncategorized';
        $this->categories_to_keep['slugs'][] = 'uncategorized';
    }

    /**
     * Create new categories if they don't exist
     */
    private function create_new_categories() {
        foreach ($this->new_categories as $main_category => $sub_categories) {
            // Always fetch main category term ID
            $main_term = term_exists($main_category, $this->taxonomy);
            if (!$main_term) {
                $main_term = wp_insert_term($main_category, $this->taxonomy, [
                    'slug' => sanitize_title($main_category)
                ]);
                if (is_wp_error($main_term)) {
                    $this->errors[] = "Failed to create main category '{$main_category}': " . $main_term->get_error_message();
                } else {
                    echo "✓ Created main category: {$main_category}\n";
                }
            }
            // Always get the term ID
            $main_term_id = is_array($main_term) ? $main_term['term_id'] : $main_term;

            // Create sub categories
            foreach ($sub_categories as $sub_category) {
                $sub_term = get_term_by('name', $sub_category, $this->taxonomy);
                if ($sub_term) {
                    // Check if parent is correct
                    if ($sub_term->parent != $main_term_id) {
                        // Update parent
                        $updated = wp_update_term($sub_term->term_id, $this->taxonomy, [
                            'parent' => $main_term_id
                        ]);
                        if (is_wp_error($updated)) {
                            $this->errors[] = "Failed to update parent for sub category '{$sub_category}': " . $updated->get_error_message();
                        } else {
                            echo "✓ Updated parent for sub category: {$sub_category} (now under {$main_category})\n";
                        }
                    }
                } else {
                    // Create as child
                    $sub_term = wp_insert_term($sub_category, $this->taxonomy, [
                        'slug' => sanitize_title($sub_category),
                        'parent' => $main_term_id
                    ]);
                    if (is_wp_error($sub_term)) {
                        $this->errors[] = "Failed to create sub category '{$sub_category}': " . $sub_term->get_error_message();
                    } else {
                        echo "✓ Created sub category: {$sub_category} (under {$main_category})\n";
                    }
                }
            }
        }
    }

    /**
     * Get term by name or slug
     */
    private function get_term($name, $slug) {
        $term = get_term_by('name', $name, $this->taxonomy);
        if (!$term) {
            $term = get_term_by('slug', sanitize_title($slug), $this->taxonomy);
        }
        return $term;
    }

    /**
     * Reassign categories
     */
    public function reassign_categories() {
        echo "Starting category reassignment...\n";
        echo "Mode: " . ($this->dry_run ? "DRY RUN (no changes will be made)" : "LIVE (changes will be made)") . "\n\n";
        
        // Create new categories first
        $this->create_new_categories();
        
        foreach ($this->mappings as $mapping) {
            $old_term = $this->get_term($mapping['old_name'], $mapping['old_slug']);
            
            if (!$old_term) {
                $this->errors[] = "Old category not found: {$mapping['old_name']} ({$mapping['old_slug']})";
                continue;
            }
            
            // Find new main category
            $new_main_term = $this->get_term($mapping['new_main'], $mapping['new_main']);
            if (!$new_main_term) {
                $this->errors[] = "New main category not found: {$mapping['new_main']}";
                continue;
            }
            
            // Find new sub category if specified
            $new_sub_term = null;
            if ($mapping['new_sub']) {
                $new_sub_term = $this->get_term($mapping['new_sub'], $mapping['new_sub']);
                if (!$new_sub_term) {
                    $this->errors[] = "New sub category not found: {$mapping['new_sub']}";
                    continue;
                }
            }
            
            // Get posts with the old term
            $posts = get_posts([
                'post_type' => 'any',
                'posts_per_page' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => $this->taxonomy,
                        'field' => 'term_id',
                        'terms' => $old_term->term_id
                    ]
                ]
            ]);
            
            if (empty($posts)) {
                echo "No posts found for category: {$mapping['old_name']}\n";
                continue;
            }
            
            echo "Processing {$mapping['old_name']} -> {$mapping['new_main']}" . 
                 ($mapping['new_sub'] ? " -> {$mapping['new_sub']}" : "") . 
                 " ({$old_term->count} posts)\n";
            
            foreach ($posts as $post) {
                if (!$this->dry_run) {
                    // Remove old term
                    wp_remove_object_terms($post->ID, $old_term->term_id, $this->taxonomy);
                    // Assign both main and subcategory if subcategory exists
                    $terms_to_assign = [$new_main_term->term_id];
                    if ($new_sub_term) {
                        $terms_to_assign[] = $new_sub_term->term_id;
                    }
                    wp_set_object_terms($post->ID, $terms_to_assign, $this->taxonomy, true);
                    $this->success_count++;
                } else {
                    $this->success_count++;
                }
            }
        }
        
        // Clean up old categories if not in dry run mode
        if (!$this->dry_run) {
            $this->cleanup_old_categories();
        }
        
        // Remove categories that aren't part of the mapping
        $this->remove_unmapped_categories();
        
        // Reassign posts without categories to Uncategorized, and remove Uncategorized if post has other categories
        $this->reassign_uncategorized_posts();
        
        echo "\n=== SUMMARY ===\n";
        echo "Total posts processed: {$this->success_count}\n";
        echo "Errors: " . count($this->errors) . "\n";
        
        if (!empty($this->errors)) {
            echo "\n=== ERRORS ===\n";
            foreach ($this->errors as $error) {
                echo "- {$error}\n";
            }
        }
    }

    /**
     * Clean up old categories that are no longer needed
     */
    private function cleanup_old_categories() {
        echo "\nCleaning up old categories...\n";
        
        $old_terms = get_terms([
            'taxonomy' => $this->taxonomy,
            'hide_empty' => false
        ]);
        
        foreach ($old_terms as $term) {
            $is_old = false;
            foreach ($this->mappings as $mapping) {
                if ($term->name === $mapping['old_name'] || $term->slug === $mapping['old_slug']) {
                    $is_old = true;
                    break;
                }
            }
            
            if ($is_old && $term->count == 0) {
                wp_delete_term($term->term_id, $this->taxonomy);
                echo "✓ Deleted empty old category: {$term->name}\n";
            }
        }
    }

    /**
     * Remove categories that aren't part of the mapping (by name or slug)
     */
    private function remove_unmapped_categories() {
        echo "\nRemoving categories not in mapping...\n";
        
        $all_terms = get_terms([
            'taxonomy' => $this->taxonomy,
            'hide_empty' => false
        ]);
        
        foreach ($all_terms as $term) {
            // Skip if this category should be kept (by name or slug)
            if (in_array($term->name, $this->categories_to_keep['names']) || in_array($term->slug, $this->categories_to_keep['slugs'])) {
                continue;
            }
            
            // If category has posts, reassign them to Uncategorized first
            if ($term->count > 0) {
                echo "Reassigning {$term->count} posts from '{$term->name}' to 'Uncategorized'...\n";
                
                $posts = get_posts([
                    'post_type' => 'listing',
                    'posts_per_page' => -1,
                    'tax_query' => [
                        [
                            'taxonomy' => $this->taxonomy,
                            'field' => 'term_id',
                            'terms' => $term->term_id
                        ]
                    ]
                ]);
                
                $uncategorized_term = get_term_by('name', 'Uncategorized', $this->taxonomy);
                if (!$uncategorized_term) {
                    $uncategorized_term = wp_insert_term('Uncategorized', $this->taxonomy, [
                        'slug' => 'uncategorized'
                    ]);
                    if (is_wp_error($uncategorized_term)) {
                        $this->errors[] = "Failed to create Uncategorized category: " . $uncategorized_term->get_error_message();
                        continue;
                    }
                    $uncategorized_term = get_term($uncategorized_term['term_id'], $this->taxonomy);
                }
                
                foreach ($posts as $post) {
                    if (!$this->dry_run) {
                        // Remove the old term
                        wp_remove_object_terms($post->ID, $term->term_id, $this->taxonomy);
                        // Add to Uncategorized
                        wp_set_object_terms($post->ID, $uncategorized_term->term_id, $this->taxonomy, true);
                    }
                }
            }
            
            // Now delete the category
            if (!$this->dry_run) {
                wp_delete_term($term->term_id, $this->taxonomy);
                echo "✓ Deleted category: {$term->name}\n";
            } else {
                echo "Would delete category: {$term->name} ({$term->count} posts)\n";
            }
        }
    }

    /**
     * Create Uncategorized category if it doesn't exist
     */
    private function ensure_uncategorized_exists() {
        $uncategorized_term = get_term_by('name', 'Uncategorized', $this->taxonomy);
        if (!$uncategorized_term) {
            $uncategorized_term = wp_insert_term('Uncategorized', $this->taxonomy, [
                'slug' => 'uncategorized'
            ]);
            if (is_wp_error($uncategorized_term)) {
                $this->errors[] = "Failed to create Uncategorized category: " . $uncategorized_term->get_error_message();
                return false;
            }
            echo "✓ Created Uncategorized category\n";
            return get_term($uncategorized_term['term_id'], $this->taxonomy);
        }
        return $uncategorized_term;
    }

    /**
     * Find and reassign posts without categories to Uncategorized, and remove Uncategorized if post has other categories
     */
    private function reassign_uncategorized_posts() {
        echo "\nEnsuring correct Uncategorized assignment...\n";
        
        $uncategorized_term = $this->ensure_uncategorized_exists();
        if (!$uncategorized_term) {
            return;
        }
        $uncategorized_id = is_object($uncategorized_term) ? $uncategorized_term->term_id : $uncategorized_term['term_id'];
        
        // Get all listing posts
        $all_listings = get_posts([
            'post_type' => 'listing',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ]);
        
        $added_count = 0;
        $removed_count = 0;
        
        foreach ($all_listings as $post) {
            $current_terms = wp_get_object_terms($post->ID, $this->taxonomy, ['fields' => 'ids']);
            
            if (empty($current_terms) || is_wp_error($current_terms)) {
                // No categories: assign Uncategorized
                if (!$this->dry_run) {
                    wp_set_object_terms($post->ID, $uncategorized_id, $this->taxonomy, false);
                }
                $added_count++;
            } else {
                // Has categories: if Uncategorized is present and there are other categories, remove it
                if (in_array($uncategorized_id, $current_terms) && count($current_terms) > 1) {
                    $new_terms = array_diff($current_terms, [$uncategorized_id]);
                    if (!$this->dry_run) {
                        wp_set_object_terms($post->ID, $new_terms, $this->taxonomy, false);
                    }
                    $removed_count++;
                }
            }
        }
        
        if ($added_count > 0) {
            echo ($this->dry_run ? "Would assign" : "✓ Assigned") . " Uncategorized to {$added_count} posts with no categories\n";
        }
        if ($removed_count > 0) {
            echo ($this->dry_run ? "Would remove" : "✓ Removed") . " Uncategorized from {$removed_count} posts that have other categories\n";
        }
        if ($added_count === 0 && $removed_count === 0) {
            echo "No Uncategorized changes needed\n";
        }
    }

    /**
     * Set dry run mode
     */
    public function set_dry_run($dry_run) {
        $this->dry_run = $dry_run;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reassigner = new CategoryReassigner();
    
    if ($_POST['action'] === 'dry_run') {
        $reassigner->set_dry_run(true);
        echo "<h2>DRY RUN RESULTS</h2>";
        echo "<pre>";
        $reassigner->reassign_categories();
        echo "</pre>";
    } elseif ($_POST['action'] === 'live_run') {
        $reassigner->set_dry_run(false);
        echo "<h2>LIVE RUN RESULTS</h2>";
        echo "<pre>";
        $reassigner->reassign_categories();
        echo "</pre>";
    }
    exit;
}

// Display the interface
?>
<!DOCTYPE html>
<html>
<head>
    <title>Category Reassignment Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; margin: 5px; }
        .button.danger { background: #dc3545; }
        .button:hover { opacity: 0.8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Category Reassignment Tool</h1>
        
        <div class="warning">
            <strong>⚠️ IMPORTANT:</strong> 
            <ul>
                <li>Backup your database before running this script!</li>
                <li>This will modify your listing_categories taxonomy</li>
                <li>Always run a dry run first to see what changes will be made</li>
            </ul>
        </div>
        
        <h2>Instructions:</h2>
        <ol>
            <li>First, run a <strong>Dry Run</strong> to see what changes will be made without actually making them</li>
            <li>Review the results carefully</li>
            <li>If everything looks correct, run the <strong>Live Run</strong> to actually make the changes</li>
        </ol>
        
        <form method="post">
            <button type="submit" name="action" value="dry_run" class="button">Run Dry Run (Preview Changes)</button>
            <button type="submit" name="action" value="live_run" class="button danger">Run Live (Make Actual Changes)</button>
        </form>
        
        <h2>What this script does:</h2>
        <ul>
            <li>Reads the category mapping from <code>mapping.txt</code></li>
            <li>Creates new categories if they don't exist</li>
            <li>Reassigns posts from old categories to new categories (assigns both main and subcategory if applicable)</li>
            <li>Removes empty old categories</li>
            <li>Removes categories that aren't part of the mapping (reassigns their posts to "Uncategorized" first)</li>
            <li>Creates an "Uncategorized" category if it doesn't exist</li>
            <li>Finds all posts of type "listing" without categories and assigns them to "Uncategorized"</li>
            <li>Skips categories marked for removal (location, admin, navigation categories)</li>
        </ul>
    </div>
</body>
</html> 