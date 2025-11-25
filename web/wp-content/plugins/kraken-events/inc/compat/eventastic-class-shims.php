<?php
/**
 * This is meant to replace the Eventastic Utilities 
 * class. All functions that were in that class will 
 * appear in this Legacy Utilities class 
 * 
 */

namespace KrakenEvents\Compatibility;

// Only create class alias if the original Eventastic class exists
if (class_exists('Eventastic\Library\Utilities')) {
    class_alias('Eventastic\Library\Utilities', __NAMESPACE__ . '\LegacyUtilities');
}

/* Replacing Eventastic classes with ones defined in Kraken Events */
//require_once  KRAKEN_EVENTS_PLUGIN_DIR . 'inc/compat/admin/eventastic-settings-admin-layout.php';
class_exists('Constants', false) or require_once 'eventastic-constants.php';
class_exists('Encoding', false) or require_once 'eventastic-encoding.php';
                                                
require_once KRAKEN_EVENTS_PLUGIN_DIR .'inc/compat/admin/eventastic-settings-admin-layout.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR .'inc/compat/library/opencage/AbstractGeocoder.php';
require_once KRAKEN_EVENTS_PLUGIN_DIR .'inc/compat/library/opencage/Geocoder.php';

/* Create a class alias so we don't have to manually change all of the references to Eventastic Utilities */

use KrakenEvents\Compatibility\Admin\LegacySettingsAdminLayout as SettingsAdminLayout;

class LegacyUtilities {
     /**
  * Are we on the command line? (danke https://www.binarytides.com/php-check-running-cli/)
  */
  public static function runningOnCLI () {

    if (defined('STDIN')) {
      // MAY EXIT THIS BLOCK
      return true;
    }

    if ( (empty($_SERVER['REMOTE_ADDR'])) &&
    (! isset($_SERVER['HTTP_USER_AGENT'])) && (count($_SERVER['argv']) > 0) ) {
      // MAY EXIT THIS BLOCK
      return true;
    }

    return false;
  }

	/**
	 * Are we in debug mode?
	 *
	 * @return boolean
	 */
	public static function getDebugMode () {
		
		$ro = get_option(SettingsAdminLayout::SETTING_PLUGIN_DEBUG["key"], Constants::DEBUG_MODE);

		return filter_var($ro, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	}

  /**
   * Are we using Venues?
   *
   * @return boolean
   */
  public static function getVenueMode () {
    
    $ro = get_option(SettingsAdminLayout::SETTING_PLUGIN_VENUE["key"], Constants::VENUE_MODE);

    return filter_var($ro, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }  

  /**
   * Are we using Organizers?
   *
   * @return boolean
   */
  public static function getOrganizerMode () {
    
    $ro = get_option(SettingsAdminLayout::SETTING_PLUGIN_ORGANIZER["key"], Constants::ORGANIZER_MODE);

    return filter_var($ro, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }    

  /**
   * Is Category in Sidebar or Main?
   *
   * @return boolean
   */
  public static function getCategoryLocation () {
    
    $ro = get_option(SettingsAdminLayout::SETTING_PLUGIN_CATEGORY_SIDEBAR["key"], Constants::CATEGORY_SIDEBAR);

    return filter_var($ro, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }      
  
  /**
   * Are we using Featured Images?
   *
   * @return boolean
   */
  public static function getFeaturedMode () {
    
    $ro = get_option(SettingsAdminLayout::SETTING_PLUGIN_FEATURED["key"], Constants::FEATURED_MODE);

    return filter_var($ro, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }

  /**
   * Are we using Version 2 of the Recurrence Options?
   *
   * @return boolean
   */
  public static function getRecurrenceVersion () {
    
    $ro = 2;

    return filter_var($ro, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }      

  /**
  * Gets the post type for the plugin either from settings or a default
  *
  * @return string The plugin post type
  */
  public static function getPluginPostType () {

    $cpt = get_option(SettingsAdminLayout::SETTING_PLUGIN_POST_TYPE["key"]);

    return (! $cpt) ? Constants::PLUGIN_DEFAULT_CUSTOM_POST_TYPE : $cpt;
  }

  /**
  * Looks up the taxonomies stored in the constants and finds the requested one for the passed class
  *
  * @param string $className The class name to look up
  * @return string The taxonomy name
  */
  public static function getTaxonomyForClass ($className) {

    foreach (Constants::PLUGIN_TAXONOMIES as $taxonomy => $data) {
      if (isset($data["customFieldsClass"])) {
        if ($data["customFieldsClass"] == "\\{$className}") {
          // MAY EXIT THIS BLOCK
          return $taxonomy;
        }
      }
    }

    return "";
  }

	/**
	 * Convience function for formatting output to the WordPress debug log
	 * 
	 * @param string $key The key for the item (suggest using caller __FUNCTION__)
	 * @param mixed $data The data to log
	 */
	public static function doLogDebug ($key, $data) {

		error_log($key .PHP_EOL
			.((is_array($data)) ? print_r($data, true) : $data) 
			."---------------------".PHP_EOL
		);
	}

  /**
	 * Convience function for formatting output to the WordPress debug log
	 * 
   * danke http://webdeveloperblog.tiredmachine.com/php-converting-an-integer-123-to-ordinal-word-firstsecondthird/
	 * 
	 * @param string $num The number (int) to convert
	 * @param boolean $capitalize Should the result be properly capitalized?
   */
  public static function numToOrdinalWord ($num, $capitalize=false) {

    $rhett = "";

    $first_word = array('eth','First','Second','Third','Fourth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
    $second_word =array('','','Twenty','Thirty','Forty','Fifty');

    if($num <= 20) {
      // MAY EXIT THIS BLOCK
      return ($capitalize) ? $first_word[$num] : strtolower($first_word[$num]);
    }

    $first_num = substr($num,-1,1);
    $second_num = substr($num,-2,1);

    $rhett = $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);

    return ($capitalize) ? $rhett : strtolower($rhett);
  }

  /**
  * Prepends the meta storage snippet to the passed key for storage in the wp_postsmeta table
  *
  * @param string $key The key to prepend to
  * @return string The adjusted string
  */
  public static function prependMetaKey ($key) {

    return str_replace("[KEY]", $key, Constants::WP_POST_META_KEY_DETAILS);
  }

  /**
  * Gets a GET or POST variable if it's set or returns a default
  *
  * @param $name The variable
  * @param $method Variable in GET or POST? If "ALL", then check either.
  * @param $defNotExists What to return if the variable doesn't exist?
  * @param $defExists What to return if the variable does exist?
  * @return mixed
  */
  public static function getVar ($name, $method="GET", $defNotExists="", $defExists="") {

    $rhett = false;
    $formData = array();

    if ($method == "ALL") {
      $formData["GET"] = $_GET;
      $formData["POST"] = $_POST;
    } else {
      if ($method == "GET") {
        $formData["GET"] = $_GET;
      } else {
        $formData["POST"] = $_POST;
      }
    }
    foreach ($formData as $method => $data) {
      if (isset($data[$name])) {
        $rhett = ($defExists == "") ? $data[$name] : $defExists;
        break;
      } else {
        $rhett = $defNotExists;
      }
    }

    return $rhett;
  }

  /**
  * Looks up a lat/lng for an address
  *
  * @param $address: An address (e.g. "345 E. Toole Ave Tucson AZ 85701")
  * @return string
  */
  public static function getLatLngFromAddress ($address) {

    $apiKey = get_option(SettingsAdminLayout::SETTING_OPENCAGE_API_KEY["key"], "");
    $rhett = array("lat" => "", "lng" => "");

    if ($apiKey == "") {
      // MAY EXIT THIS BLOCK
      return $rhett;
    }

    $geocoder = new \KrakenEvents\Compatibility\OpenCage\Geocoder\Geocoder($apiKey);
    $data = $geocoder->geocode($address);
    if (count($data["results"]) > 0) {
      $rhett["lat"] = $data["results"][0]["geometry"]["lat"];
      $rhett["lng"] = $data["results"][0]["geometry"]["lng"];
    }

    return $rhett;
  }

  /**
  * Generates am array of the days of the week
  *
  * @param boolean $simpleArray Just return an array? (If false, returns key/label array)
  * @return array The days of the week
  */
  public static function generateDaysOfWeek ($simpleArray=false) {

    $rhett = array();

    for ($i=0; $i < 7; $i++) {
      $day = jddayofweek($i, 1);
      if ($simpleArray) {
        $rhett[$i] = $day;
      } else {
        $rhett[$i] = array("key" => $day, "label" => $day);
      }
    }

    return $rhett;
  }

  /**
  * Creates a pretty time string (danke https://stackoverflow.com/a/4096727)
  *
  * @param int $seconds The number of seconds
  * @return string The formatted time string
  */
  public static function secondsToWords ($seconds) {

    $rhett = "";

    // get the hours
    $hours = intval(intval($seconds) / 3600);
    if ($hours > 0) {
      $rhett .= "{$hours} hours ";
    }
    // get the minutes
    $minutes = bcmod((intval($seconds) / 60), 60);
    if ($hours > 0 || $minutes > 0) {
      $rhett .= "{$minutes} minutes ";
    }

    // get the seconds
    $seconds = bcmod(intval($seconds), 60);
    $rhett .= "{$seconds} seconds";

    return $rhett;
  }

  /**
  * Adds a protocol to a URL string if needed
  *
  * Regex courtesy of http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
  *
  * @param $url The URL to check
  * @param $makeSSL Make the protocol SSL?
  * @return string $url The updated URL
  */
  public static function addProtocolToAddress ($url, $makeSSL=false) {

    $protocol = "";

    // add a protocol?
    if (! preg_match("~^(?:f|ht)tps?://~i", $url)) {
      $protocol = ($makeSSL) ? "https://" : "http://";
    }

    // prepend the protocol string
    $url = ($protocol.$url);

    return $url;
  }

  /**
  * Cleans a string from odd characters for database entry
  *
  * @param string $string The string to clean
  * @return string The cleaned string
  */
  public static function cleanString ($string) {

    // encode to utf8 if it is not
    if (!preg_match('!!u', $string)) {
      $string = Encoding::fixUTF8($string);
    }

    $replacements = array(
      chr(130) => ',',    // baseline single quote
      chr(132) => '"',    // baseline double quote
      chr(133) => '...',  // ellipsis
      chr(145) => "'",    // left single quote
      chr(146) => "'",    // right single quote
      chr(147) => '"',    // left double quote
      chr(148) => '"',    // right double quote
      chr(150) => '-',    // long hyphen
      chr(151) => '-',    // long hyphen
      chr(25)  => '',        // end of medium
      '&#x91;' => "'",     // msword single quote
      '&#x92;' => "'",     // msword single quote
      '&#x93;' => '"',     // msword quote
      '&#x94;' => '"',     // msword quote
      '&#x96;' => '&',     // ampersand
      '&#x97;' => ' ',     // not defined
      '&#x99;' => '',     // not defined
      '&#x85;' => ' ',     // abnormal space
      '&#8211;' => '-',     // long hyphen
      '&#xe3;' => 'a',     // a with tilde
      '&#xe9;' => 'e',     // e with tilde
      // double encoded characters introduced in Tucson feed 2014-08-27
      '&amp;reg;' => '&reg;',
      '&amp;amp;' => '&amp;',
    );

    $string = trim(str_replace(array_keys($replacements), array_values($replacements), $string));

    // remove any weird double spaces
    while(strpos($string, '  ') !== false) {
      $string = str_replace('  ', ' ', $string);
    }

    return $string;
  }

  /**
  * Compares two images to see if they are the same (danke https://stackoverflow.com/a/30114215)
  *
  * @param string $firstPath The first local image path
  * @param string $secondPath The second local image path
  * @param int $chunkSize The chunk size to use for comparison
  * @return bool Are they equal or not
  */
  public static function imagesAreEqual ($firstPath, $secondPath, $chunkSize=500) {

    // First check if file are not the same size as the fastest method
    if (filesize($firstPath) !== filesize($secondPath)) {
      // MAY EXIT THIS BLOCK
      return false;
    }

    // Compare the first ${chunkSize} bytes
    // This is fast and binary files will most likely be different
    $fp1 = fopen($firstPath, 'r');
    $fp2 = fopen($secondPath, 'r');
    $chunksAreEqual = fread($fp1, $chunkSize) == fread($fp2, $chunkSize);
    fclose($fp1);
    fclose($fp2);

    if (! $chunksAreEqual) {
      // MAY EXIT THIS BLOCK
      return false;
    }

    // Compare hashes
    // SHA1 calculates a bit faster than MD5
    $firstChecksum = sha1_file($firstPath);
    $secondChecksum = sha1_file($secondPath);
    if ($firstChecksum != $secondChecksum) {
      // MAY EXIT THIS BLOCK
      return false;
    }

    return true;
  }

  /**
  * Turn all URLs in clickable links (danke https://gist.github.com/jasny/2000705)
  *
  * @param string $text The text to replace links in
  * @param array $protocols  HTTP/HTTPS, FTP, mail, twitter
  * @param array $attributes The optional tag attributes to add to each link
  * @return string Original text with links inserted
  */
  public static function linkify ($text, $protocols=array('http', 'mail'), $attributes=array()) {

    // link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
      $attr .= ' '.$key.'="'.htmlentities($val).'"';
    }

    $links = array();

    // extract existing links and tags
    $text = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
      return '<' . array_push($links, $match[1]) . '>';
    }, $text);

    // extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
      switch ($protocol) {
        case 'http':
        case 'https':
        $text = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $text); break;
          case 'mail':
          $text = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; }, $text); break;
          case 'twitter':
          $text = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; }, $text); break;
          default:
          $text = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $text); break;
        }
      }

      // insert all links and return
      return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $text);
    }

    /**
    * Check the current active theme for the file path provided
    *
    * @param string $file_path The relative file path to the root of the default theme
    * @return bool if the file exists or not
    */
    public static function is_file_in_theme( $file_path = __FILE__ ) {
      // Get the them root folder
      $root = get_theme_root().'/'.get_stylesheet();
      // Change all '\' to '/' to compensate for localhosts
      $root = str_replace( '\\', '/', $root ); // This will output E:\xampp\htdocs\wordpress\wp-content\themes
      // Make sure we do that to $file_path as well
      $file_path = str_replace( '\\', '/', $file_path );
      // Make entire path now
      $full_path = $root.$file_path;
      // We can now look for the file
      $bool = file_exists($full_path);
      if ( false === $bool )
      return false;

      return true;
    }

    /**
    * Check the current active theme for the calendar block component
    *
    * @param string $template the requested template
    * @return inlcudes file
    */
    public static function include_template( $template, $attributes = []) {
        //$error = "LegacyUtilities::include_template >> Error Unknown";
        if( "filters" == $template ){
            if( isset( $attributes['useFilters'] ) && $attributes['useFilters'] ){
                $subTemplateFile = '/eventastic-theme-files/blocks/eventastic-calendar/component-filters.php';
            }
        }
        if( "calendar" == $template ){
            $subTemplateFile = '/eventastic-theme-files/blocks/eventastic-calendar/subtemplate-calendar.php';
        }        
        if( "integrated" == $template ){
            $subTemplateFile = '/eventastic-theme-files/blocks/eventastic-calendar/subtemplate-integrated.php';
        }
        if( "list" == $template ){
            $subTemplateFile = '/eventastic-theme-files/blocks/eventastic-calendar/subtemplate-list.php';
        }        
        if( "toggled" == $template ){
            $subTemplateFile = '/eventastic-theme-files/blocks/eventastic-calendar/subtemplate-toggled.php';
        }
        if( $subTemplateFile ){
            $filePath = ( LegacyUtilities::is_file_in_theme( $subTemplateFile ) ) ? get_stylesheet_directory() . $subTemplateFile : realpath( __DIR__ . DIRECTORY_SEPARATOR . '../') . $subTemplateFile;
            include $filePath;
        }
        else{
            //echo $error;
            return false;
        }
    }
    /*
    * @params $nbr  (number) -> first, second, third or fourth day of month
    * @params $day  (string) -> name of the day in the week
    * @params $mon  (number) -> number of the month to seach
    * @params $year (number) -> number of the year to search
    * @author MrXploder
    */

    public function nthDayOfMonth($nbr, $day, $mon, $year){ 
      $date = mktime(0, 0, 0, $mon, 0, $year);
      if($date == 0){ 
        user_error(__FUNCTION__."(): Invalid month or year", E_USER_WARNING); 
        return(FALSE); 
      } 
      $day = ucfirst(strtolower($day));
      if(!in_array($day, array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'))){ 
        user_error(__FUNCTION__."(): Invalid day", E_USER_WARNING); 
        return(FALSE); 
      }
      for($week = 1; $week <= $nbr; $week++){ 
        $date = strtotime("next $day", $date); 
      }
      return($date); 
    } 

    /**
    * 
    */
    public function buildUniqueDates( $postData ) {
//      error_log(  print_r( $postData , true) );
    }

    /**
    * Return Readable Recurrence V2
    */
    public static function recurrenceToString( $args ) {
      if( LegacyUtilities::getRecurrenceVersion() ){

        $dayNames = [
          0 => 'Sunday',
          1 => 'Monday',
          2 => 'Tuesday',
          3 => 'Wednesday',
          4 => 'Thursday',
          5 => 'Friday',
          6 => 'Saturday'
        ];      
        if( isset( $args['post'] ) ){
          $post = $args['post'];
          $meta = eventastic_get_event_meta($post->ID);
        }
        if( isset( $args['meta'] ) ){
          $meta = $args['meta'];
        }
        $startDate = ( array_key_exists( 'event_start_date', $meta)  && $meta['event_start_date'] ) ? date( strtotime( $meta['event_start_date'] ) ) : null;
        $endDate = ( array_key_exists( 'event_end_date', $meta)  && $meta['event_end_date'] ) ? date( strtotime( $meta['event_end_date'] ) ) : null;      
        $dates = isset($meta['events_pattern_dates']) ? $meta['events_pattern_dates'] : null;
        $date_text = "";
        if( "custom" == $meta['repeat_pattern'] ){
          if( 'day' == $meta['repeat_type'] ){
            if( isset( $meta['repeat_number'] ) && $meta['repeat_number'] ){
              $date_text = "Every " . LegacyUtilities::numToOrdinalWord( $meta['repeat_number'] ) . " day";
            }
          }
          else{ 
            $textdays = $meta['recurring_days_v2'];
            if( is_array($textdays) ){
              $date_text = "Every ";
              $daysArray = [];
              foreach( $textdays as $k => $day){
                $daysArray[] = $day;
              };
              $last = array_pop($daysArray);
              $date_text .= count($daysArray) ? implode(", ", $daysArray) . " & " . $last : $last;

              if( isset( $meta['repeat_number'])  && $meta['repeat_number'] > 1 ) {
                 $date_text .= " every " . $meta['repeat_number'] . " weeks";
              }
            }

          }                       

        }
        else{
            $pattern = $meta['repeat_pattern'];
            if( $pattern ){
                $pattern_try = json_decode($pattern);
                if( is_object($pattern_try ) ){
                    $pattern = $pattern_try;
                }
                if( "weekly" == $pattern->freq ){
                    $date_text = "Every ";
                    $daysArray = [];
                    foreach( $pattern->days as $k => $day){
                      $daysArray[] = $dayNames[$day];
                    };
                    $last = array_pop($daysArray);
                    $date_text .= count($daysArray) ? implode(", ", $daysArray) . " & " . $last : $last;
                }
                if( "monthly_occurence" == $pattern->freq ){
                    $date_text = "Every ";
                    $occursArray = [];
                    foreach( $pattern->occurences as $m => $occurence){
                      $occursArray[] = LegacyUtilities::numToOrdinalWord($occurence);
                    };
                    $lastOccurence = array_pop($occursArray);
                    $date_text .= " ";
                    $date_text .= count($occursArray) ? implode(", ", $occursArray) . " & " . $lastOccurence : $lastOccurence;


                    $daysArray = [];
                    foreach( $pattern->days as $k => $day){
                      $daysArray[] = $dayNames[$day];
                    };
                    $last = array_pop($daysArray);
                    $date_text .= " ";
                    $date_text .= count($daysArray) ? implode(", ", $daysArray) . " & " . $last : $last;
                }       
                if( "monthly" == $pattern->freq ){
                    $date_text .= "Every ";
                    $daysArray = [];
                    foreach( $pattern->days as $k => $day){
                      $daysArray[] = LegacyUtilities::numToOrdinalWord( $day );
                    };
                    $last = array_pop($daysArray);
                    $date_text .= count($daysArray) ? implode(", ", $daysArray) . " & " . $last : $last;
                    $date_text .= " of the month";
                } 
                if( "annual_month_occurence" == $pattern->freq ){
                    $date_text .= "Annually on the";

                    $occursArray = [];
                    foreach( $pattern->occurences as $m => $occurence){
                      $occursArray[] = LegacyUtilities::numToOrdinalWord($occurence);
                    };
                    $lastOccurence = array_pop($occursArray);
                    $date_text .= " ";
                    $date_text .= count($occursArray) ? implode(", ", $occursArray) . " & " . $lastOccurence : $lastOccurence;

                    $daysArray = [];
                    foreach( $pattern->days as $k => $day){
                      $daysArray[] = $dayNames[$day];
                    };
                    $last = array_pop($daysArray);
                    $date_text .= " ";
                    $date_text .= count($daysArray) ? implode(", ", $daysArray) . " & " . $last : $last;
                    $date_text .= " ";

                    $monthsArray = [];
                    foreach( $pattern->months as $l => $month){
                      $monthName = date('F', mktime(0, 0, 0, $month, 10)); // March
                      $monthsArray[] = $monthName;
                    };
                    $last = array_pop($monthsArray);
                    $date_text .= " of ";
                    $date_text .= count($monthsArray) ? implode(", ", $monthsArray) . " & " . $last : $last;
                    $date_text .= " ";
                }

            }
        }
        return $date_text;
      }
      else{
        return false;
      }
    }

    /**
    * Return Readable Dates V2
    */
    public static function datesToString( $args ) {
      $post = $args['post'];
      $meta = eventastic_get_event_meta($post->ID);
      $startDate = ( array_key_exists( 'event_start_date', $meta)  && $meta['event_start_date'] ) ? date( strtotime( $meta['event_start_date'] ) ) : null;
      $endDate = ( array_key_exists( 'event_end_date', $meta)  && $meta['event_end_date'] ) ? date( strtotime( $meta['event_end_date'] ) ) : null;      
      if( LegacyUtilities::getRecurrenceVersion() ){
        $date_text = "";
        if( "pattern" == $meta['events_recurrence_options'] ){
          $recurrenceToString_args = [
            'post' => $post
          ];
          $recurrenceString = LegacyUtilities::recurrenceToString( $recurrenceToString_args );
          if( $recurrenceString ){
            $date_text .= "<span class='event-date-recurrence'>" . $recurrenceString ."</span>";
            $date_text .= " <span class='optional-break'></span><span class='event-date-range'>From " . date('M j, Y', $startDate) . ' to '.date('M j, Y', $endDate) ."</span>";
          }

        }
        elseif( "daily" == $meta['events_recurrence_options'] ){
          $day_seconds = 24*3600;
          $diff = ($endDate - $startDate) / $day_seconds;
          if( $diff == 1 ){
            $date_text .= date('M j, Y', $startDate) . ' and '.date('M j, Y', $endDate);
          }elseif( $diff > 1){
            $date_text .= "Daily " . date('M j, Y', $startDate) . ' to '.date('M j, Y', $endDate);
          }
          else{
            $date_text .= date('M j, Y', $startDate);            
          }
        }
        elseif( "one_day" == $meta['events_recurrence_options'] ){
          $date_text .= date('M j, Y', $startDate);
        }
        elseif( "specific_days" == $meta['events_recurrence_options'] ){
          if( ( $endDate - $startDate ) > 1 ){
            $date_text = "Select Dates from " . date('M j, Y', $startDate) . ' to '.date('M j, Y', $endDate);
          }
          else{
            $date_text = date('M j, Y', $startDate) ;            
          }
        }
 
        return $date_text;
      }
      else{
        return false;
      }
    }

    /**
    * Get Upcoming Occurences of Repeating Events
    */
    public static function getUpcomingOcccurences( $post ) {
      $meta = eventastic_get_event_meta($post->ID);
      $days = [];
      $html = "";
      $today = date('Ymd');
      $test_dates = [];
      if( isset($meta['events_recurrence_options'] ) ){
        if( "specific_dates" == $meta['events_recurrence_options']  ){
          if( get_field( 'event_specific_dates', $post->ID ) ) {
            $test_dates = get_field( 'event_specific_dates', $post->ID );
          }
        }
        if( "pattern" == $meta['events_recurrence_options']  ){
          if( isset( $meta['events_pattern_dates']  ) ){
            $test_dates = $meta['events_pattern_dates'];
          }
        }        
        if( "one_day" == $meta['events_recurrence_options']  ){
          $days[] = $meta['event_start_date'];
        }
        if( "daily" == $meta['events_recurrence_options']  ){
          $start_test_incr = strtotime($meta['event_start_date']);
          $end_test = strtotime($meta['event_end_date']);
          while( $start_test_incr <= $end_test ){
            if( $start_test_incr >= $today ){
              $days[] = date('Y-m-d',$start_test_incr);
            }
            $start_test_incr = $start_test_incr + 3600*24;
          }
        } 
        if ( "monthly_by_date" == $meta['events_recurrence_options'] ) {
          $test_dates = json_decode($meta['event_repeat_dates'], true);
        }    
        if ( "monthly_by_dotw" == $meta['events_recurrence_options'] ) {
          $test_dates = json_decode($meta['event_repeat_dates'], true);
        }
        if ( "weekly" == $meta['events_recurrence_options'] ) {
          $test_dates = json_decode($meta['event_repeat_dates'], true);
        }      

      }
      foreach( $test_dates as $repeatDate ){
        
        $test = date( strtotime($repeatDate['date']) );
        if( $test >= $today ){
          $days[] = $repeatDate['date'];
        }
      }
      $returnObject = [
        'days' => $days
      ];
      if( count( $days ) > 0 ){
        $last = array_pop($days);
        $html = count($days) ? implode(", ", $days) . " & " . $last : $last;
      }
      $returnObject['html'] = $html;
      return $returnObject;
    }
    /**
    * Convert Recurrence to Serialized String of Repeated Event Dates
    */
    public function convertRecurringEvents( $postData ) {
      $maxMonths = 36; // keeps loops to 3 years 
      if( LegacyUtilities::getRecurrenceVersion() ){
        $dayNames = [
          0 => 'Sunday',
          1 => 'Monday',
          2 => 'Tuesday',
          3 => 'Wednesday',
          4 => 'Thursday',
          5 => 'Friday',
          6 => 'Saturday'
        ];
        $dayNumberLookup = [
          'Sunday' => 0,
          'Monday' => 1,
          'Tuesday' => 2,
          'Wednesday' => 3,
          'Thursday' => 4,
          'Friday' => 5,
          'Saturday' => 6
        ];
        $recurringDates = [];
        $startDate = ( array_key_exists( 'eventastic_start_date', $postData)  && $postData['eventastic_start_date'] ) ? date( strtotime( $postData['eventastic_start_date'] ) ) : null;
        $endDate = ( array_key_exists( 'eventastic_end_date', $postData)  && $postData['eventastic_end_date'] ) ? date( strtotime( $postData['eventastic_end_date'] ) ) : null;
        if( $endDate > ($startDate + (2 * 365 * 24 * 3600) ) ){
          $endDate = $startDate + (2 * 365 * 24 * 3600);
        }
        $currentDate = $startDate;
        $weekSeconds = 7 * 24 * 60 * 60;
        $daySeconds = 24 * 60 * 60;
        $freg = "";
  
        if( array_key_exists('eventastic_recurrence_options', $postData) && ('pattern' == $postData['eventastic_recurrence_options'] ) ){
          if( array_key_exists('eventastic_repeat_pattern', $postData) ){
            if( "custom" != $postData['eventastic_repeat_pattern'] ){
              $pattern = json_decode( preg_replace("#\\\#", "", $postData['eventastic_repeat_pattern']));
            }
            else{
              $pattern = $postData['eventastic_repeat_pattern'];
            }
            if( !is_object($pattern) && $pattern == 'custom' ){
              $number = 1; // not saving when 1 for some reason
              if( array_key_exists('eventastic_repeat_number', $postData) && $postData['eventastic_repeat_number'] ){
                $number = $postData['eventastic_repeat_number'];
              }
              if( array_key_exists('eventastic_repeat_type', $postData) && $postData['eventastic_repeat_type'] ){
                $type = $postData['eventastic_repeat_type'];
                if( "day" == $type ){
                  $typeIncrement = $daySeconds;
                }
                if( "week" == $type ){
                  $typeIncrement = $weekSeconds;
                  if( array_key_exists('eventastic_recurring_days_v2', $postData) && $postData['eventastic_recurring_days_v2'] ){
                    foreach( $postData['eventastic_recurring_days_v2'] as $recurring_day_v2_name ){
                      $days[] = $dayNumberLookup[$recurring_day_v2_name];
                    }
                  }
                }
                if( "month" == $type ){
                  $typeIncrement = $weekSeconds;
                  if( array_key_exists('eventastic_recurring_days_v2', $postData) && $postData['eventastic_recurring_days_v2'] ){
                    foreach( $postData['eventastic_recurring_days_v2'] as $recurring_day_v2_name ){
                      $days[] = $dayNumberLookup[$recurring_day_v2_name];
                    }
                  }
                }                
              }

//              error_log('type'. $type);
  //            error_log('number'. $number);
    //          error_log('currentDate'. $currentDate);
      //        error_log('endDate'. $endDate);
        //      error_log(print_r($postData,true));
              if( $number && $type ){
                if( $currentDate && ( $currentDate <= $endDate ) ){ 
                  while( $currentDate <= $endDate ){
                    //increment date by a week
                    if( "week" == $type ){
                      $thatSunday = strtotime('last sunday, 12pm', $currentDate);
                      $testDay = $currentDate + $number * $typeIncrement;
                      // iterate over days
                      foreach( $days as $dayNumber ){
                        $testDay = $thatSunday + $dayNumber * 24 * 60 * 60;
                        if ($testDay >= $startDate && $testDay <= $endDate ){
                          $recurringDates[$testDay] = date('Y-m-d', $testDay);
                        }                        
                      }
                    }
                    if( "day" == $type ){
                      $testDay = $currentDate + $number * $typeIncrement;
                      if ($testDay >= $startDate && $testDay <= $endDate ){
                        $recurringDates[$testDay] = date('Y-m-d', $testDay);
                      }
                    }
                    if( "month" == $type ){
                      $i = 0;
                      $currentMonth = date('m',$currentDate);
                      $currentDatestamp = $currentDate;

                      $Y2 = (date('Y', $endDate));
                      $Y1 = (date('Y', $startDate));
                      $M1 = (date('m', $startDate));
                      $M2 = (date('m', $endDate));

                      $maxMonths = ( ( $Y2 - $Y1 ) * 12 ) + ( $M2 - $M1 );
                      while( (($endDate - $currentDate) >= 0 ) && ($i <= $maxMonths) ){
                        $year = date("Y", $currentDate); 
                        $month = date("F", $currentDate); 
                        if( array_key_exists('eventastic_recurring_weeks', $postData) && is_array($postData['eventastic_recurring_weeks']) ){
                          foreach( $postData['eventastic_recurring_weeks'] as $weekNumber ){
                            if( array_key_exists('eventastic_recurring_days_v2', $postData) && is_array($postData['eventastic_recurring_days_v2']) ){
                              foreach( $postData['eventastic_recurring_days_v2'] as $dayName ){
                                $ordinal = LegacyUtilities::numToOrdinalWord($weekNumber);
                                $string = $ordinal . ' ' . $dayName . ' of ' . $month . ' ' . $year;
                                $stamp = strtotime( $string );
                                $recurringDates[$stamp] = date('Y-m-d', $stamp);
                              }
                            }                        
                          }
                        }                       
                        $i++;
                        $currentDate = strtotime("+1 month", $currentDate);
                      }
                    }
                    else{  
                      $currentDate = $currentDate + $number * $typeIncrement;
                    }
                  }
                } 
              }
            }
            elseif( is_object($pattern) && property_exists( $pattern, 'freq') ){
              $freq = $pattern->freq;
              if( 'weekly' == $freq ){
                if( property_exists( $pattern, 'days') ){
                  $days = $pattern->days;
                  // build all days MIGHT BE A FUNCTION
                  if( $currentDate ){ 
                    while( $currentDate <= $endDate ){
                      // get date for sunday of this week
                      $thatSunday = strtotime('last sunday, midnight', $currentDate);
                      // iterate over days
                      foreach( $days as $dayNumber ){
                        $testDay = $thatSunday + $dayNumber * 24 * 60 * 60;
                        if ($testDay >= $startDate && $testDay <= $endDate ){
                          $recurringDates[$testDay] = date('Y-m-d', $testDay);
                        }                        
                      }
                      //increment date by a week
                      $currentDate = $currentDate + $weekSeconds;
                    }
                  }
                }
              }
              if( 'monthly' == $freq ){
                if( property_exists( $pattern, 'days') ){
                  $days = $pattern->days;
                  // build all days MIGHT BE A FUNCTION
                  if( $currentDate ){ 
                    $i = 0;
                    $currentMonth = date('m',$currentDate);
                    $currentDatestamp = $currentDate;

                    while( (($endDate - $currentDatestamp) > 0 ) && ($i <= $maxMonths) ){
                      // iterate over days
                      foreach( $days as $dayNumber ){
                        $testDay = strtotime( date('Y-m-' . $dayNumber,$currentDatestamp) );
                        if ($testDay >= $startDate && $testDay <= $endDate ){
                          $recurringDates[$testDay] = date('Y-m-d', $testDay);
                        }
                      }
                      //increment date by a week
                      $i++;
                      $currentDatestamp = strtotime("+1 month", $currentDatestamp);
                    }
                  }
                }
              }              
              if( 'monthly_occurence' == $freq ){
                if(  is_object($pattern) && property_exists( $pattern, 'days') && property_exists( $pattern, 'occurences') ){
                  $occurences = $pattern->occurences; //array
                  $days = $pattern->days; //array
                  if( $currentDate ){ 
                    //increment by month 
                    $currentMonth = date('m',$currentDate);
                    $currentDatestamp = $currentDate;
                    $i = 0;
                    // loop and increment by month 
                    while( (($endDate - $currentDatestamp) > 0 ) && ($i <= $maxMonths) ){
                      // loop and increment by occurence
                      $firstOfMonth = date('Y-m-01',$currentDatestamp);
                      $month = date('m', $currentDatestamp);
                      $year = date('Y', $currentDatestamp);                      
                      foreach( $occurences as $occurence ){
                        foreach( $days as $dayNumber ){
                          $testDay = self::nthDayOfMonth( $occurence, $dayNames[$dayNumber], $month, $year);
                          if ( ($testDay >= $startDate) && ($testDay <= $endDate) ){
                            $recurringDates[$testDay] = date('Y-m-d', $testDay);
                          }                        
                          //increment date by a week
                          $currentDate = $currentDate + $weekSeconds;
                        }
                      }
                      $i++;
                      $currentDatestamp = strtotime("+1 month", $currentDatestamp);
                    }
                  }
                }
              }
              
              if( 'annual_month_occurence' == $freq ){
                if(  is_object($pattern) && property_exists( $pattern, 'days') && property_exists( $pattern, 'occurences') ){
                  $occurences = $pattern->occurences; //array
                  $days = $pattern->days; //array
                  if( $currentDate ){ 
                    //increment by month 
                    $currentMonth = date('m',$currentDate);
                    $currentDatestamp = $currentDate;
                    $i = 0;
                    // loop and increment by month 
                    while( (($endDate - $currentDatestamp) > 0 ) && ($i <= $maxMonths) ){
                      // loop and increment by occurence
                      $firstOfMonth = date('Y-m-01',$currentDatestamp);
                      $month = date('m', $currentDatestamp);
                      $year = date('Y', $currentDatestamp);                      
                      foreach( $occurences as $occurence ){
                        foreach( $days as $dayNumber ){
                          $testDay = self::nthDayOfMonth( $occurence, $dayNames[$dayNumber], $month, $year);
                          if ( ($testDay >= $startDate) && ($testDay <= $endDate) ){
                            $recurringDates[$testDay] = date('Y-m-d', $testDay);
                          }                        
                          //increment date by a week
                          $currentDate = $currentDate + $weekSeconds;
                        }
                      }
                      $i++;
                      $currentDatestamp = strtotime("+1 month", $currentDatestamp);
                    }
                  }
                }
              }            
            }
          }
        }
        sort($recurringDates);
        if( count( $recurringDates) > 0  ){
          return $recurringDates;
        }
        else{
          return false;
        }
      }
    }
  }
