<?php
/**
 * Server-side render for Crowdriff block
 */

if (!function_exists('extract_crowdriff_id')) {
    function extract_crowdriff_id($input) {
        if (!$input) return '';
        if (preg_match('/id=["\'](cr-init__[a-zA-Z0-9]+)["\']/', $input, $matches)) {
            return $matches[1];
        }
        if (preg_match('/cr-init__[a-zA-Z0-9]+/', $input, $matches)) {
            return $matches[0];
        }
        return '';
    }
}

$attrs = $attributes;
$crowdriff_input = isset($attrs['crowdriffInput']) ? $attrs['crowdriffInput'] : '';
$extracted_id = extract_crowdriff_id($crowdriff_input);

if (!$extracted_id) {
    echo '<div class="wp-block-mm-bradentongulfislands-crowdriff"><div class="crowdriff-embed-placeholder">Crowdriff Embed: (invalid or missing ID)</div></div>';
    return;
}

// Only enqueue the script if not already present
add_action('wp_footer', function() {
    if (!wp_script_is('crowdriff-js', 'enqueued')) {
        wp_enqueue_script('crowdriff-js', 'https://starling.crowdriff.com/js/crowdriff.js', [], null, true);
    }
});

// Output the embed div
printf('<div class="wp-block-mm-bradentongulfislands-crowdriff"><div id="%s" class="crowdriff-embed"></div></div>', esc_attr($extracted_id)); 