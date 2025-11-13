<?php
namespace MaddenTheme\Library\Hooks;

/**
 * Add general hooks and actions
 */
add_action( 'wp_head', __NAMESPACE__ . '\header_snippets', 20);
add_action( 'wp_footer', __NAMESPACE__ . '\footer_snippets');
add_action( 'wp_body_open', __NAMESPACE__ . '\body_snippets' );
add_filter( 'acf/settings/save_json', __NAMESPACE__ . '\acf_json_save_point' );
add_filter( 'acf/settings/load_json', __NAMESPACE__ . '\acf_json_load_point' );
add_filter( 'upload_mimes', __NAMESPACE__ . '\upload_mime_types' );
add_action( 'send_headers', __NAMESPACE__ . '\add_compliance_headers' );
add_action( 'wp', __NAMESPACE__ . '\find_content' );

/**
 * Manage tracking codes and other snippets in the <head>
 */
function header_snippets() {
    ?>
<link rel="stylesheet" href="https://use.typekit.net/vww5ibj.css">
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MZ8H2VT');</script>
<!-- End Google Tag Manager -->
    <?php
}

/**
 * Add snippets just after the <body> tag.
 */
function body_snippets() {
  ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZ8H2VT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  <?php
}


/**
 * Manage tracking codes and other snippets before closing body tag
 */
function footer_snippets() {
    /* add any custom footer snippets here*/
}

/**
 * Set the save point for acf fields
 */
function acf_json_save_point( $path ) {
  return get_stylesheet_directory() . '/assets/acf-json';
}

/**
 * Remove the default acf load path and set our custom path
 */
function acf_json_load_point( $paths ) {
  unset($paths[0]);
  $paths[] = get_stylesheet_directory() . '/assets/acf-json';
  return $paths;
}

/**
 * Add additional upload mime types
 */
function upload_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}

/**
 * Best practice headers for a site
 */
function add_compliance_headers() {
  header("Permissions-Policy: geolocation=(self), camera=(), microphone=(), payment=(), fullscreen=(self)");
  header("Content-Security-Policy: default-src *; script-src * 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com; style-src * 'unsafe-inline'; img-src * data:; font-src * data:; frame-ancestors *");
  header("Referrer-Policy: no-referrer-when-downgrade");
  header("x-content-type-options: nosniff");
  header("x-frame-options: SAMEORIGIN");
}

/**
 * Find content
 */
function find_content() {
  if ( ! isset( $_GET['find_content'] ) || is_admin() || ! current_user_can( 'administrator' ) ) {
    return false;
  }
  global $wpdb;

  $find = urldecode( $_GET['find_content'] );

  $query = "
    SELECT ID, post_title, post_name, post_content
    FROM {$wpdb->prefix}posts
    WHERE post_content LIKE '%{$find}%'
    AND post_status = 'publish'
  ";

  $all_posts = [];
  $results = $wpdb->get_results($query);

  // Output the results
  foreach ($results as $result) {
    $all_posts[] = [
      'title' => $result->post_title,
      'url' => get_permalink( $result->ID ),
    ];
  }

  if ( ! empty( $all_posts ) ) {

    // Define the CSV filename
    $filename = "content_links.csv";

    // Create a file handle in memory
    $output = fopen('php://temp', 'w');

    // Write CSV header
    fputcsv($output, array_keys($all_posts[0]));

    // Write CSV data rows
    foreach ($all_posts as $row) {
      fputcsv($output, $row);
    }

    // Set appropriate HTTP headers for the download
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    // Output the CSV content
    rewind($output); // Move the pointer to the beginning
    fpassthru($output); // Output the CSV data to the browser
    fclose($output); // Close the file handle

    // Terminate the script to prevent any additional output
    exit();
  } else {
    $response = sprintf( __( 'Nothing found for %s', 'madden-theme' ), $find );
    echo $response;
  }
}
