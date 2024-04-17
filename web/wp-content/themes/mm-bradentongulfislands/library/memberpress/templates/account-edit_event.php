<?php
include_once get_stylesheet_directory() .'/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

if (isset($_GET['event_id'])) {
// Get the event ID from the URL parameter
$post_id = intval($_GET['event_id']);
$meta_data = get_post_meta($post_id);     
// Retrieve the event post using the event ID
$event = get_post($post_id);
        
// Check if the event post exists and is of type 'event'
if ($event && $event->post_type === 'event') {
    $form_handler = new FormHandler();
    $form_handler->updateEvent($post_id);
    include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-event-form.php';
}
}