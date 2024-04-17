<?php 

include_once get_stylesheet_directory() .'/library/memberpress/form-handler.php';

use MaddenNino\Library\Memberpress\MemberPressFormHandler as FormHandler;

$form_handler = new FormHandler();
$form_handler->addEvent();

include get_stylesheet_directory() . '/library/memberpress/templates/forms/account-event-form.php';
