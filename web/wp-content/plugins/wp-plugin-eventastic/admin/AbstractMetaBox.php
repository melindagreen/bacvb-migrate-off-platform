<?php

namespace Eventastic\Admin;

/**
* Abstract meta box for event data collection
*
* GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
*
* Copyright (c) 2020 Madden Media
*/

require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Library\Constants as Constants;
use Eventastic\Library\Utilities as Utilities;
use Eventastic\Admin\MetaBoxDates as MetaBoxDates;

abstract class AbstractMetaBox {

  public $id = "";
  public $title = "";

  public static $NONCE_BASE = -1;
  public static $VALIDATE_JS_OUTPUTTED = false;

  /**
  * Constructor
  *
  * @param string $id The meta box identifier
  * @param string $title The meta box title
  */
  public function __construct ($id, $title) {

    $this->id = $id;
    $this->title = $title;

    // the nonce root to use
    self::$NONCE_BASE = plugin_basename(__FILE__);

    // validator
    // PENDING: this also breaks ajax saves throughout wordpress, so it's a no-go
    add_action('admin_footer', __NAMESPACE__.'\AbstractMetaBox::outputValidateJavascript');

    // add the save listener
    add_action('add_meta_boxes', array($this, 'addMetaBox'));
  }

  /**
  * Outputs the trigger to validate meta fields - static so that we can
  *	ensure it only happens once
  */
  public static function outputValidateJavascript () {

    // PENDING this uh...isn't working at all.
    if (! self::$VALIDATE_JS_OUTPUTTED) {
      // echo it
      ?>
      <script type="text/javascript">
      jQuery(document).ready(function() {

        var functionsMetaBoxJS = {
          isValidHttpUrl: function(string) {
            let url;
            try {
              url = new URL(string);
            } catch (_) {
              return false;
            }
            return url.protocol === "http:" || url.protocol === "https:";
          }
        }

        //Disable saving if the URL is invalid
          jQuery("body").on("keyup", "input[type=url]", function(e) {
                        
            var __this = jQuery(this);
              var lockName = __this.attr('id') + '_invalid_lock';
              if(__this.val() !== "" && ! functionsMetaBoxJS.isValidHttpUrl(__this.val())) {
                  wp.data.dispatch( 'core/editor' ).lockPostSaving(lockName);
                  if(__this.next('.validation-warning').length <= 0) {
                      __this.after('<p class="validation-warning" style="margin: 10px 0 0; color: #a34504"><span class="dashicons dashicons-warning"></span>Please enter a valid URL. URLs should begin with HTTP or HTTPS</p>');
                  }
              } else {
                __this.next('.validation-warning').remove();
                wp.data.dispatch( 'core/editor' ).unlockPostSaving(lockName);
              } 
       
          });

        /*
        // clean up fields that have quotes in them
        jQuery("input[id*=phone]").each(function() {
          jQuery(this).blur(function() {
            var cleaned = (jQuery(this).val()).replace(/\D/g, "");
            var match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
            if (match) {
              jQuery(this).val("(" + match[1] + ") " + match[2] + "-" + match[3]);
            }
          });
        });
        jQuery('#post').submit(function() {

          var form_data = jQuery('#post').serializeArray();
          form_data = jQuery.param(form_data);
          var data = {
            action: 'my_pre_submit_validation',
            security: '<?php echo wp_create_nonce('pre_publish_validation'); ?>',
            form_data: form_data
          };
          jQuery.post(ajaxurl, data, function(response) {
            if ( (response.indexOf('True') > -1) || (response.indexOf('true') > -1) || (response = true) || (response) ) {
              jQuery('#ajax-loading').hide();
              jQuery('#publish').removeClass('button-primary-disabled');
              return true;
            }else{
              alert("please correct the following errors: " + response);
              jQuery('#ajax-loading').hide();
              jQuery('#publish').removeClass('button-primary-disabled');
              return false;
            }
          });
          return false;
        });*/
      });
      </script>
      <?php
      // note it
      self::$VALIDATE_JS_OUTPUTTED = true;
    }
  }

  /**
  * Add the box to the view
  */
  public function addMetaBox () {

    if ( ($this->id == "") || ($this->title == "") ) {
      // MAY EXIT THIS BLOCK
      return;
    }

    add_meta_box(
      $this->id,
      __($this->title, Utilities::getPluginPostType()),
      array($this, 'displayMetaBox'),
      Utilities::getPluginPostType(),
      'normal',
      'default'
    );
  }

  /**
  * Renders the content of the meta box
  *
  * @param object $post The parent post data
  * @return void
  */
  public abstract function displayMetaBox ($post);

  /**
  * Renders the content of the meta box
  *
  * @param object $post The parent post data
  * @return void
  */
  public abstract static function saveMetaBoxData ($post);

  /**
  * Saves the meta box data after a bit of security checking. This should be called by the saveMetaBoxData override.
  *
  * @param string $id The caller id (used to check nonce)
  * @param array $keys The $_POST keys to save
  * @param int $postId The related post id
  * @param array $postData The optional post data to save (used instead of $_POST)
  * @return void
  */
  protected static function _savePassedData ($id, $keys, $postId, $postData=null) {

    $postData = ($postData == null) ? $_POST : $postData;

    // saving post submitted with XHR?
    if ( (defined('DOING_AUTOSAVE')) && (DOING_AUTOSAVE) ) {
      // MAY EXIT THIS BLOCK
      return;
    }

    // nonce test
    if ( isset($postData[Constants::NONCE_ROOT.$id])) {
      if ( ! wp_verify_nonce($postData[Constants::NONCE_ROOT.$id], self::$NONCE_BASE)) {
        // MAY EXIT THIS BLOCK
        return;
      }
    }

    // security tests
    if (isset($postData['post_type'])) {
      if (Utilities::getPluginPostType() == $postData['post_type']) {
        if (! current_user_can('edit_page', $postId )) {
          // MAY EXIT THIS BLOCK
          return;
        }
      } else {
        if (! current_user_can('edit_post', $postId)) {
          // MAY EXIT THIS BLOCK
          return;
        }
      }
    }

    // build patternDates from custom rules
    if( $patternDates = (new Utilities)->convertRecurringEvents( $postData ) ){
      $postData[MetaBoxDates::META_KEY_PATTERN_DATES["key"]] = $patternDates;
      $postData[MetaBoxDates::META_KEY_REPEAT_DATES["key"]] = null;
    }


    if( Utilities::getRecurrenceVersion() ){
      $deleteArray = [
        'eventastic_recurring_days_v2',
        'eventastic_repeat_pattern',
        'eventastic_repeat_type',
        'eventastic_pattern_dates'        
      ];
      foreach( $deleteArray as $key => $el_id ){
        if( !isset($postData[$el_id] ) ){
          delete_post_meta( $postId , $el_id );
        }
      }
    }
    if( array_key_exists( MetaBoxDates::META_KEY_RECURRENCE["key"], $postData)  && "specific_days" == $postData[MetaBoxDates::META_KEY_RECURRENCE["key"]]) {
      $postData[MetaBoxDates::META_KEY_PATTERN_DATES["key"]] = null;
      /// set start and end to start and end of unique
      // get first
      $repeat_dates_array_original = $postData[MetaBoxDates::META_KEY_REPEAT_DATES["key"]];
      $repeat_dates_array = [];
      foreach ($repeat_dates_array_original as $date_key => $strTime) {
        $repeat_dates_array[strtotime($strTime)] = $strTime;
      }
      sort($repeat_dates_array);
      $postData[MetaBoxDates::META_KEY_REPEAT_DATES["key"]] = $repeat_dates_array;
      $possibleStart = $repeat_dates_array[array_key_first($repeat_dates_array)];
      $possibleEnd = $repeat_dates_array[array_key_last($repeat_dates_array)];
      if( $possibleEnd ){
        $postData[MetaBoxDates::META_KEY_END_DATE['key']] = $possibleEnd;        
      }
      if( $possibleStart ){
        $postData[MetaBoxDates::META_KEY_START_DATE['key']] = $possibleStart;
        if( !$possibleEnd ){
          $postData[MetaBoxDates::META_KEY_END_DATE['key']] = $possibleStart;    
        }
        else{
          $postData[MetaBoxDates::META_KEY_END_DATE['key']] = $possibleEnd;          
        }
      }
    }
    if( array_key_exists( MetaBoxDates::META_KEY_RECURRENCE["key"], $postData)  && "daily" == $postData[MetaBoxDates::META_KEY_RECURRENCE["key"]]) {
      $postData[MetaBoxDates::META_KEY_REPEAT_DATES["key"]] = "";
      $postData[MetaBoxDates::META_KEY_PATTERN_DATES["key"]] = "";
    }
    // if we got here, it's all good
    foreach ($keys as $k) {
      if( array_key_exists( 'eventastic_event_all_day', $postData)  && !$postData['eventastic_event_all_day']) {
        $postData['eventastic_event_all_day'] = false;
      }
      if (isset($postData[$k])) {

        update_post_meta($postId, $k, $postData[$k]);
      }
    }
  }
}
