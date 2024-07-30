<?php 

include_once get_stylesheet_directory() . '/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

$form_handler = new FormHandler();

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    // Set up query arguments
    $args = array(
        'post_type' => 'event',
        'p' => $event_id, 
        'post_status' => 'publish',
        'posts_per_page' => 1 
    );

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $form_handler->addEvent();
            include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-event-form.php';
        }
        // Restore global post data
        wp_reset_postdata();
    } else {
        echo 'Event not found.';
    }
} else {
    // Display the form for a new event submission
    include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-event-form.php';
}
?>
