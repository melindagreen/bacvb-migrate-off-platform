<?php

namespace Eventastic\Admin;

/**
 * Meta box for event date information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */
 
require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');
require_once(__DIR__.'/../library/FormControlLayout.php');

use Eventastic\Admin\AbstractMetaBox as AbstractMetaBox;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\Utilities as Utilities;
use Eventastic\Library\FormControlLayout as FormControlLayout;

/**
 * Meta box for event dates
 */
class MetaBoxDates extends AbstractMetaBox {
 
	public const ID = "event_dates_box";
	public const TITLE = "Event Date Information";

	public const META_KEY_START_DATE = 		array("key" => Constants::WP_POST_META_KEY_PREPEND."start_date", "label" => "Start Date", "icon" => "far fa-calendar-alt");
	public const META_KEY_END_DATE =		array("key" => Constants::WP_POST_META_KEY_PREPEND."end_date", "label" => "End Date", "icon" => "far fa-calendar-alt");
	public const META_KEY_END_DATE_LOGIC =	array("key" => Constants::WP_POST_META_KEY_PREPEND."event_end", "label" => "End Date", "choices" => 
												array(
													array("key" => "finite", "label" => "My event does not repeat"),
													array("key" => "infinite", "label" => "My event is ongoing")
												)
											);

	public const META_KEY_RECURRENCE = 		array("key" => Constants::WP_POST_META_KEY_PREPEND."recurrence_options", "label" => "When Does the Event Occur?", "options" => 
												 array(
												 	'one_day' => 'On a Single Day',
													'daily' => 'Over Multiple Consecutive Days',
													'pattern' => 'Repeats as a Pattern',
													'specific_days' => 'Repeats on Unique Days'
												)
											);

	public const META_KEY_REPEAT_PATTERN = 		array("key" => Constants::WP_POST_META_KEY_PREPEND."repeat_pattern", "label" => "Event Repeats Every", "options" => 
												 array(
													'custom' => 'Custom',
												)
											);	

	public const META_KEY_REPEAT_NUMBER = 	array("key" => Constants::WP_POST_META_KEY_PREPEND."repeat_number", "label" => "Repeat Every");
	public const META_KEY_REPEAT_TYPE = 	array("key" => Constants::WP_POST_META_KEY_PREPEND."repeat_type", "label" => "", "options" => 
												 array(
													'day' => 'Days',
													'week' => 'Weeks',
													'month' => 'Month'
												)
											);	



	public const META_KEY_WEEKDAYS =		array("key" => Constants::WP_POST_META_KEY_PREPEND."recurring_days", "label" => "Recurs On", "choices" => 
												array() // CODE CAN HANDLE THIS ARRAY
											);
	public const META_KEY_WEEKDAYS_V2 =		array("key" => Constants::WP_POST_META_KEY_PREPEND."recurring_days_v2", "label" => "Recurs On", "choices" => 
												array() // CODE CAN HANDLE THIS ARRAY
											);
	public const META_KEY_WEEKS =			array("key" => Constants::WP_POST_META_KEY_PREPEND."recurring_weeks", "label" => "Recurs On Weeks", "options" => 
												array(
													array("key" => "1", "label" => "1st Week"),
													array("key" => "2", "label" => "2nd Week"),
													array("key" => "3", "label" => "3rd Week"),
													array("key" => "4", "label" => "4th Week"),
													array("key" => "5", "label" => "5th Week"),
													array("key" => "6", "label" => "6th Week")
												)
											);												
	public const META_KEY_FREQUENCY = 		array("key" => Constants::WP_POST_META_KEY_PREPEND."recurring_repeat", "label" => "Every", "options" => 
												 array(
													'' => 'Occurrence each month',
													'1' => 'First occurrence per month',
													'2' => 'Second occurrence per month',
													'3' => 'Third occurrence per month',
													'4' => 'Fourth occurrence per month'
												)
											);
	public const META_KEY_TIME_ALL_DAY =	array("key" => Constants::WP_POST_META_KEY_PREPEND."event_all_day", "label" => "Event Time", "choices" => 
												array(
													array("key" => "true", "label" => "Event runs all day")
												)
											);
	public const META_KEY_START_TIME = 		array("key" => Constants::WP_POST_META_KEY_PREPEND."start_time", "label" => "Start Time", "icon" => "far fa-clock");
	public const META_KEY_END_TIME =		array("key" => Constants::WP_POST_META_KEY_PREPEND."end_time", "label" => "End Time", "icon" => "far fa-clock");

	public const META_KEY_REPEAT_DATES = 	array("key" => Constants::WP_POST_META_KEY_PREPEND."repeat_dates", "label" => "Repeat Dates");
	public const META_KEY_REPEAT_DATES_CLONE = 	array("key" => Constants::WP_POST_META_KEY_PREPEND."repeat_dates[]", "label" => "Repeat Dates");
	public const META_KEY_PATTERN_DATES = 	array("key" => Constants::WP_POST_META_KEY_PREPEND."pattern_dates", "label" => "Pattern Dates");

	
	/**
	 * Constructor
	 */
    public function __construct () {

		parent::__construct(self::ID, self::TITLE);

		// add our save action
		add_action('save_post', array($this, 'saveMetaBoxData'));
    }
 
    /**
     * Renders the content of the meta box
	 *
	 * @param object $post The parent post data
	 * @return void
     */
    public function displayMetaBox ($post) {

		// get the post meta
		$postMeta = get_post_meta($post->ID);

		// security
		wp_nonce_field(self::$NONCE_BASE, Constants::NONCE_ROOT.self::ID);
	
		// get the post meta
		$postMeta = get_post_meta($post->ID);

		// security (this will cover all fields - won't add in other callbacks)s
		wp_nonce_field( plugin_basename( __FILE__ ), Constants::NONCE_ROOT.$post->ID);

		//
		$recurrence_option = null;
		if( array_key_exists( self::META_KEY_RECURRENCE['key'], $postMeta ) &&  is_array( $postMeta[self::META_KEY_RECURRENCE['key']] ) && count(  $postMeta[self::META_KEY_RECURRENCE['key']] ) ){
			$recurrence_option = $postMeta[self::META_KEY_RECURRENCE['key']][0];
		}				

		$repeat_type = null;
		if( array_key_exists( self::META_KEY_REPEAT_TYPE['key'], $postMeta ) &&  is_array( $postMeta[self::META_KEY_REPEAT_TYPE['key']] ) && count(  $postMeta[self::META_KEY_REPEAT_TYPE['key']] ) ){
			$repeat_type = $postMeta[self::META_KEY_REPEAT_TYPE['key']][0];
		}						


		// start and end date
		$post_start_date = null;
		$min_end_date = null;
		if( array_key_exists( self::META_KEY_START_DATE['key'], $postMeta ) &&  is_array( $postMeta[self::META_KEY_START_DATE['key']] ) && count(  $postMeta[self::META_KEY_START_DATE['key']] ) ){
			$post_start_date = $postMeta[self::META_KEY_START_DATE['key']][0];
			$min_end_date = $post_start_date;
		}

		if( Utilities::getRecurrenceVersion() ){

			echo '<br>';
			echo FormControlLayout::renderSelectInput($postMeta, self::META_KEY_RECURRENCE, "", false, false, self::META_KEY_RECURRENCE["options"]);
			echo '<br><br>';
		}		
		echo "<div id='startDayWrapper'>";		
		echo FormControlLayout::renderDateInput($postMeta, self::META_KEY_START_DATE, 
			null, "date", false, false, false, self::META_KEY_START_DATE["icon"]);
		if( Utilities::getRecurrenceVersion() ){
			echo "<button class='remove clear-datepicker' title='Clear Date' id='clear_start_date'></button></br></br>";
		}
		echo "</div>";
		$end_date_attr = ( "one_day" == $recurrence_option ) ? "style='display:none;'" : "";


        if( Utilities::getRecurrenceVersion() ){
			echo "<div id='endDayWrapper'" . $end_date_attr . ">";
			echo FormControlLayout::renderDateInput($postMeta, self::META_KEY_END_DATE, 
				$min_end_date, "date", false, false, false, self::META_KEY_END_DATE["icon"]);
			echo "<button class='remove clear-datepicker' title='Clear Date' id='clear_end_date'></button></br></br>";
			echo "</div>";
		}
		else{
			
			echo "<div id='endDayWrapper'" . $end_date_attr . ">";
			echo FormControlLayout::renderDateInput($postMeta, self::META_KEY_END_DATE, 
				$min_end_date, "date", false, false, false, self::META_KEY_END_DATE["icon"]);
			echo '<a href="javascript:eventasticSetEndToStart()">'.__("This is the same as the start date").'</a>';
			echo '<p class="description eventasticDescription">'.__("Optional; leave blank if event is ongoing (e.g. Farmer's Market)").'</p>';
			echo "</div>";			
		}
        if( Utilities::getRecurrenceVersion() ){
			echo '<br>';


			// custom recurrence
			echo '<style>
					.repeater-input-wrapper .eventasticLabel{
						visibility:hidden;
					} 
					#dateRepeaterWrap, #patternRecurrenceWrap{ 
						border:solid 1px rgba(0,0,0,.2); 
						border-radius:10px; 
						padding:20px; 
						margin-bottom:30px; 
						width: 50%;
					}
					#dateRepeaterWrap h2{
						display:inline-block;
						width:unset !important;
						margin-bottom:3rem;
					}
					#dateRepeaterWrap button#add-repeater-date{
						float:right;
						margin-right:2rem;
						background: #2271b1;
					    border-color: #2271b1;
					    color: #fff;
					    text-decoration: none;
					    text-shadow: none;						
					}

					.repeat-number{
						width:50px;
						padding-right:0 !important;
					}
					button.remove, button.remove-repeater-date {
						border-radius:50%;
						border:unset;
						height:1.5rem;
						border:solid 1px grey;
						cursor:pointer;
						width:1.5rem;
						vertical-align:middle;
						position:relative;
					}
					button.remove::after, button.remove-repeater-date::after{
						content:"✖";
						color:grey;
					}
				</style>';
			$patternRecurrenceWrap_attr = "";
			if( "specific_days" != $recurrence_option ){
				$patternRecurrenceWrap_attr = "style='display:none;'";
			}

			echo '<div id="dateRepeaterWrap" ' . $patternRecurrenceWrap_attr . '><h2 class="hndle">List of Unique Dates</h2>';
			echo "<button id='add-repeater-date' class='wp-core-ui button-primary'>Add an Additional Event Date</button>";
			$repeater_key = self::META_KEY_REPEAT_DATES['key'];
			$orig_repeater_dates =  array_key_exists($repeater_key, $postMeta) ? unserialize($postMeta[$repeater_key][0] ) : null;
			$repeater_content = "";
			$incr_attr = " data-increment='0'";
			if( is_array( $orig_repeater_dates) && count($orig_repeater_dates) > 0 ){
				$repeater_dates = [];
				foreach( $orig_repeater_dates as $orig_date ){
					$repeater_dates[ strtotime($orig_date)] = $orig_date;
				}				
				sort( $repeater_dates );
				$i = 0;

				foreach( $repeater_dates as $repeater_date ){
					$attributes = "";
					$incr = count( $repeater_dates );
					$btn_class="";
					if( !$i ){
						$btn_class =" original";
						$attributes = "id='repeater-input-wrapper'";
						$incr_attr =  " data-increment='" . ( $incr - 1 ). "'";
					}
					$i++;
					$value_array[self::META_KEY_REPEAT_DATES['key']][0] = $repeater_date;
					$repeater_content .= "<div class='repeater-input-wrapper' " . $attributes . ">";
						$repeater_content .= FormControlLayout::renderDateInput($value_array, self::META_KEY_REPEAT_DATES, 
								"", "date", false, false, true, self::META_KEY_START_DATE["icon"]);
						$repeater_content .= "<button class='remove remove-repeater-date " . $btn_class ."' title='Delete this Date'></button></br></br>";
					$repeater_content .= "</div>";
				}
			}
			else{
				$repeater_content .= "<div class='repeater-input-wrapper' id='repeater-input-wrapper' data-increment='0'>";
					$repeater_content .= FormControlLayout::renderDateInput($postMeta, self::META_KEY_REPEAT_DATES, 
						"", "date", false, false, true, self::META_KEY_START_DATE["icon"]);
				$repeater_content .= "<button class='remove remove-repeater-date original' title='Delete this Date'></button></br></br>";
				$repeater_content .= "</div>";
			}
			$origRepeater = "<div class='repeater-input-wrapper'>" . FormControlLayout::renderDateInput([], self::META_KEY_REPEAT_DATES_CLONE, 
								"", "date", false, false, true, self::META_KEY_START_DATE["icon"]) . "<button class='remove remove-repeater-date original' title='Delete this Date'></button></br></br></div>";
			echo '<script> var originalDateRepeater = `' . $origRepeater . '`; console.log(originalDateRepeater );</script>';			
			echo "<div id='repeater-wrapper' " . $incr_attr .">";
				echo $repeater_content;
			echo "</div>";
			echo "</div>";

			if( "custom" != $recurrence_option ){
				$patternRecurrenceWrap_attr = "style='display:none;'";
			}

			echo '<div id="patternRecurrenceWrap" ' . $patternRecurrenceWrap_attr . '>';
echo '<p id="recurrence_pattern_message"><i>Change the starting date above to see other patterns. If your starting date is July 8, the second Monday of the month, your options will include things like "Monthly on the Second Monday" and "Monthly on the 8th" etc.</i></p>';
			echo FormControlLayout::renderSelectInput($postMeta, self::META_KEY_REPEAT_PATTERN, "", true,false,self::META_KEY_REPEAT_PATTERN["options"],false,"1","0");

				if( "custom" != $recurrence_option ){
					$customRecurrenceWrap_attr = "style='display:none;'";
				}

				echo '<div id="customRecurrenceWrap" ' . $customRecurrenceWrap_attr . '>';

					echo FormControlLayout::renderNumberInput($postMeta, self::META_KEY_REPEAT_NUMBER, "repeat-number", false,false,false,false,"1","1");
					echo FormControlLayout::renderSelectInput($postMeta, self::META_KEY_REPEAT_TYPE, "", true, false, self::META_KEY_REPEAT_TYPE["options"], true);

					$monthlyRecurrenceWrap_attr = "style='display:block;'";
					if( "month" != $repeat_type ){
						$monthlyRecurrenceWrap_attr = "style='display:none; '";
					}

					echo '<div id="monthlyRecurrenceWrap" class="eventasticDateToggleBox" '. $monthlyRecurrenceWrap_attr . '>';
						$month = array(
							"key" => self::META_KEY_WEEKS["key"],
							"label" => self::META_KEY_WEEKS["label"],
							"choices" => self::META_KEY_WEEKS["options"]
						);
						echo FormControlLayout::renderCheckboxInput($postMeta, $month, "", true);
						echo '</div>';

					$weeklyRecurrenceWrap_attr = "style='display:block;'";
					if( "weekly" != $repeat_type ){
						$weeklyRecurrenceWrap_attr = "style='display:none; '";
					}
					echo '<div id="weeklyRecurrenceWrap" class="eventasticDateToggleBox" '. $weeklyRecurrenceWrap_attr . '>';
						// we can save ourselves a constant for all the weekdays
						$weekdays = array(
							"key" => self::META_KEY_WEEKDAYS_V2["key"],
							"label" => self::META_KEY_WEEKDAYS_V2["label"],
							"choices" => Utilities::generateDaysOfWeek()
						);
						echo FormControlLayout::renderCheckboxInput($postMeta, $weekdays, "", true);
						echo '</div>';


				echo '</div>';
			echo '</div>';

		}
		else{
			echo FormControlLayout::renderRadioInput(
				$postMeta, self::META_KEY_END_DATE_LOGIC, "", true);

			// finite end
			echo '<div id="finiteWrap" class="eventasticDateToggleBox">';
			echo '</div>';

			// ongoing event
			echo '<div id="infiniteWrap" class="eventasticDateToggleBox">';
			// we can save ourselves a constant for all the weekdays
			$weekdays = array(
				"key" => self::META_KEY_WEEKDAYS["key"],
				"label" => self::META_KEY_WEEKDAYS["label"],
				"choices" => Utilities::generateDaysOfWeek()
			);
			echo FormControlLayout::renderCheckboxInput($postMeta, $weekdays, "", true);
			echo FormControlLayout::renderSelectInput($postMeta, self::META_KEY_FREQUENCY, "", true, false, self::META_KEY_FREQUENCY["options"]);
			echo '</div>';
		}
		// time fields
		echo FormControlLayout::renderCheckboxInput($postMeta, self::META_KEY_TIME_ALL_DAY, "", true);
		echo '<div id="timeWrap" class="eventasticDateToggleBox" style="display:block;">';
		echo FormControlLayout::renderTimeInput($postMeta, self::META_KEY_START_TIME, 
			"time", true, false, false, self::META_KEY_START_TIME["icon"]);
		echo FormControlLayout::renderTimeInput($postMeta, self::META_KEY_END_TIME, 
			"time", true, false, false, self::META_KEY_END_TIME["icon"]);
		echo '</div>';

		?>
		<script type="text/javascript">

		// Returns the ISO week of the date.
		Date.prototype.getWeek = function(exact) {
	        var month = this.getUTCMonth()
	            , year = this.getUTCFullYear()
	            , firstWeekday = new Date(year, month, 1).getUTCDay()
	            , lastDateOfMonth = new Date(year, month + 1, 0).getUTCDate()
	            , offsetDate = this.getUTCDate() + firstWeekday - 1
	            , index = 1 // start index at 0 or 1, your choice
	            , weeksInMonth = index + Math.ceil((lastDateOfMonth + firstWeekday - 7) / 7)
	            , week = index + Math.floor(offsetDate / 7)
	        ;
	        if (exact || week < 2 + index) return week;
	        return week === weeksInMonth ? index + 5 : week;
	    };
	    function removeRepeaterDate(e){
	    	var dateCount = jQuery('.repeater-input-wrapper').length;
			var $target = jQuery(e.target);
			if( $target.hasClass('original') ){
//				alert('original')
			}
	    	if( dateCount > 1 ){
				$target.closest('.repeater-input-wrapper').remove();
			}
			else{
				$target.siblings('input').val(null);				
			}
	    }
	    function setUpRepeatOptions( options = null, repeatPatternSelectedValue  ){
	    	if( !options ){
	    		options = {
	    			'custom' : 'Custom'
	    		}
	    	}
	    	var selectSize =  Object.keys(options).length;
			jQuery('#eventastic_repeat_pattern').empty();
			jQuery.each(options, function(key,value) {
				if( repeatPatternSelectedValue  == key){
					jQuery('#eventastic_repeat_pattern').append( jQuery("<option selected='selected'></option>").attr("value", key).text(value));
				}
				else{
					jQuery('#eventastic_repeat_pattern').append( jQuery("<option></option>").attr("value", key).text(value));					
				}
			});
			jQuery('#eventastic_repeat_pattern').attr('size', selectSize);
			if( !repeatPatternSelectedValue){
				jQuery('#eventastic_repeat_pattern').val('custom');
			}
			setUpCustomPattern();
	    }
	    function setUpRepeatPatterns( repeatPatternSelectedValue ){
			var startVal = jQuery('#eventastic_start_date').val();
			if( startVal ){
				const weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
				const month = ["January","February","March","April","May","June","July","August","September","October","November","December"];

				var startDateVal = new Date( startVal );
				var startMonth = startDateVal.getUTCMonth();
				var startDateUTC = startDateVal.getUTCDate();
				var startDayUTC = startDateVal.getUTCDay();
				
				let monthName = month[startMonth];
				let dayName = weekday[startDayUTC];
				let occurenceNum = startDateVal.getWeek(true);

				const ords = {
					'1' : 'st',
					'2' : 'nd',
					'3' : 'rd',
				}
				if( 'undefined' == typeof ords[occurenceNum] ){
					occNumOrd = 'th';
				}
				else{
					occNumOrd = ords[occurenceNum];
				}
				if( 'undefined' == typeof ords[startDateUTC] ){
					dateNumOrd = 'th';
				}
				else{
					dateNumOrd = ords[startDateUTC];
				}

				// Day of WeekNumber
				var day_of_week = 'Weekly on ' + dayName;
				var key_day_of_week = JSON.stringify(
					{
						'freq' : 'weekly',
						'days' : [startDayUTC]  //day of week 
					}
				);
				var occurence_of_month = 'Monthly on the ' + occurenceNum + occNumOrd + ' ' + dayName;
				var key_occurence_of_month = JSON.stringify(
					{
						'freq' : 'monthly_occurence',
						'occurences' : [occurenceNum],  // '2' for second occurence, '4' for fourth occurence, etc 
						'days' : [startDayUTC] //day of week
					}
				);
				var day_of_month = 'Monthly on the ' + startDateUTC + dateNumOrd;
				var key_day_of_month = JSON.stringify(
					{
						'freq' : 'monthly',
						'days' : [startDateUTC]  //day of month 
					}
				);
				var annual_month_occurence = 'Annually on the ' + occurenceNum + occNumOrd + ' ' + dayName + " of " + monthName;
				var key_annual_month_occurence = JSON.stringify(
					{
						'freq' : 'annual_month_occurence',
						'months' : [startMonth],
						'occurences' : [occurenceNum],  // '2' for second occurence, '4' for fourth occurence, etc 
						'days' : [startDayUTC] //day of week
					}
				);
				var day_of_month_annually = 'Annually on the ' + startDateUTC + dateNumOrd + " of " + monthName;

				var options = {};
				options[key_day_of_week] = day_of_week;
				options[key_occurence_of_month] = occurence_of_month;
				options[key_day_of_month] = day_of_month;
				options[key_annual_month_occurence] = annual_month_occurence;
//						4: occurence_of_month_annually,
//						5: day_of_month_annually,
				options['custom'] = 'Custom';
			}
			else{
				var options = {
					'custom' : 'Custom'
				}
			}
			setUpRepeatOptions( options, repeatPatternSelectedValue )

	    }
	    function checkAfterStartChange(){	  
			var recurrenceOption = jQuery("#eventastic_recurrence_options").find(':selected').val();
			var startDate = jQuery('#eventastic_start_date').data('daterangepicker').startDate;
			var endDate = jQuery('#eventastic_end_date').data('daterangepicker').endDate;
			if('one_day' == recurrenceOption){
				jQuery('#eventastic_end_date').val(startDate);				
			}
			if( endDate <= startDate ){
				jQuery('#eventastic_end_date').val(null);				
			}

			jQuery('#eventastic_end_date').data('daterangepicker').minDate = startDate;
	    }
	    function checkAfterEndChange(){	  
	    }
		function bindDatePicker( el = null ){
			// set up the date pickers
				var datePickerFormat = "<?php echo Constants::DATE_FORMAT_JS_DATEPICKER ?>";

				var options = {
					showDropdowns: true,
					singleDatePicker: true,
					autoUpdateInput: false,
					opens: "right",
					drops: "up",
					locale: {
						format: datePickerFormat,
						firstDay: 0
					},
					autoApply:true
				};

				// current value?
				if (jQuery(el).val() != "") {
					options.startDate = moment(jQuery(el).val()).format(datePickerFormat);
				}
				
				// init it
				jQuery(el).daterangepicker(options);

				// act upon selection
				jQuery(el).on('show.daterangepicker', function(ev, picker) {
					if (jQuery(el).val() != "") {
						var sd = moment(jQuery(el).val()).format(datePickerFormat);
						jQuery(el).data('daterangepicker').setStartDate(sd);
					}
				});
				jQuery(el).on('apply.daterangepicker', function(ev, picker) {
					jQuery(el).val(picker.startDate.format('<?php echo Constants::DATE_FORMAT_JS_MYSQL ?>'));
				});
				jQuery(el).on('cancel.daterangepicker', function(ev, picker) {
					jQuery(el).val('');
				});
				setUpRecurrence();

		}
		jQuery(document).ready(function($) {
			


			// if event repeats logic
			$("input[name=<?php echo self::META_KEY_END_DATE_LOGIC["key"] ?>]").click(function() {
				$("#infiniteWrap").hide();
				$("#" + $(this).val() + "Wrap").show();
			});


			
			// if all day is checked, hide start and end
			jQuery("input[name=<?php echo self::META_KEY_TIME_ALL_DAY["key"] ?>]").change( function() {
				if (this.checked) {
					jQuery("#timeWrap").hide();
				} else {
					jQuery("#timeWrap").show();
				}
			});

			// if event recurrence
			$("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").on('change',function() {
				var recVal = jQuery("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").val();
				if ( 'custom' == recVal ) {
					jQuery("#patternRecurrenceWrap").show();
				}
				else{
					jQuery("#patternRecurrenceWrap").hide();
				}
			});
			$("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").on('change',function() {
				cleanUpForm();				
			});
			jQuery("input[type=date]").each(function() {
				var el = this;
				bindDatePicker( el );
			});
			
			jQuery('#clear_start_date').click(function(){
				jQuery('#eventastic_start_date').val(null);
				setUpRepeatOptions();
				setUpCustomPattern();						
			})

			jQuery('.remove-repeater-date').click(function(e){
				removeRepeaterDate(e);
			})

			jQuery('#clear_end_date').click(function(){
				jQuery('#eventastic_end_date').val(null);
			})
			jQuery('#eventastic_repeat_pattern').on('click',function(){
				if( 1 != jQuery('#eventastic_repeat_pattern').attr('size') ){
					jQuery('#eventastic_repeat_pattern').attr('size', 1);
				}
			})
			jQuery('#eventastic_start_date').on('apply.daterangepicker',function(ev,picker){
				setUpRepeatPatterns();
				setUpCustomPattern();
				checkAfterStartChange();
			})
			jQuery('#eventastic_end_date').on('apply.daterangepicker',function(ev,picker){
				checkAfterEndChange();
			})

			// set up the time pickers
			var timePickerFormat = "<?php echo Constants::TIME_FORMAT_JS_PRETTY ?>";
			jQuery("input.time").each(function() {
				var options = {
					singleDatePicker: true,
					timePicker: true,
					timePicker24Hour: false,
					timePickerIncrement: 1,
					timePickerSeconds: false,
					timePickerIncrement: 15,
					timePickerSeconds: false,
					autoUpdateInput : true,
					opens: "right",
					drops: "down",
					autoUpdateInput: false,
					viewDate: moment(jQuery(this).val(), '<?php echo Constants::TIME_FORMAT_JS_MYSQL ?>').format(timePickerFormat),
					locale: {
						format : timePickerFormat
					}
				};
			
				// init it
				jQuery(this).daterangepicker(options);

				// act upon selection
				jQuery(this).on('show.daterangepicker', function(ev, picker) {
					picker.container.find('.calendar-table').hide();
				});

				jQuery(this).on('apply.daterangepicker', function(ev, picker) {
					jQuery(this).val(picker.startDate.format(timePickerFormat));
				});

				jQuery(this).on('cancel.daterangepicker', function(ev, picker) {
					jQuery(this).val('');
				});
			});

			jQuery("select[name=<?php echo self::META_KEY_REPEAT_TYPE["key"] ?>]").on('change',function() {
				setUpRepeat();
			});

			jQuery("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").on('change',function() {
				setUpRecurrence();
			});
			jQuery("select[name=<?php echo self::META_KEY_REPEAT_PATTERN["key"] ?>]").on('change',function() {
				setUpCustomPattern();
			});
			var startDate = jQuery('#eventastic_start_date').data('daterangepicker').startDate;
			jQuery('#eventastic_end_date').data('daterangepicker').minDate = startDate;

		});
		function cleanUpForm(){
			var recVal = jQuery("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").val();
			if( 'one_day' == recVal ){
				var start_date = jQuery('#eventastic_start_date').val();
				jQuery('#eventastic_end_date').val( start_date );
			}
			if( 'one_day' == recVal || 'daily' == recVal || 'specific_days' == recVal ){
				var elementsToClear = [
					'eventastic_repeat_pattern',
					'eventastic_repeat_number',	
					'eventastic_repeat_type',
					'eventastic_recurring_days_v2'
				];
			}
			if( 'undefined' != typeof elementsToClear ){
				elementsToClear.forEach(( el_id ) => {
					if( "eventastic_recurring_days_v2" == el_id ){
						jQuery('[name="eventastic_recurring_days_v2[]"]').prop("checked",false);
					}
					else{

						jQuery('#' + el_id ).val(null ).prop( "checked", false );
					}
				})
			}

			
		}
		function setUpCustomPattern(){
			var $patternSelect = jQuery("select[name=<?php echo self::META_KEY_REPEAT_PATTERN["key"] ?>]");
			var $options = jQuery("select[name=<?php echo self::META_KEY_REPEAT_PATTERN["key"] ?>] option");
			var patternVal = $patternSelect.val();
	    	var selectSize =  $options.length;
			jQuery('#eventastic_repeat_pattern').attr('size', selectSize);			
			if( 'custom' == patternVal ){
				jQuery('#customRecurrenceWrap').show();
			}
			else{
				jQuery('#customRecurrenceWrap').hide();
			}
		}
		function setUpRepeat(){
				var recVal = jQuery("select[name=<?php echo self::META_KEY_REPEAT_TYPE["key"] ?>]").val();
				if ( 'day' == recVal ) {
					jQuery("#weeklyRecurrenceWrap").hide();
					jQuery("#eventastic_repeat_number").css('visibility','visible');
					jQuery("#monthlyRecurrenceWrap").hide();
					jQuery("#eventastic_repeat_number").show();						
				}
				if ( 'week' == recVal ) {
					jQuery("#weeklyRecurrenceWrap").show();
					jQuery("#eventastic_repeat_number").css('visibility','visible');
					jQuery("#eventastic_repeat_number").show();						
					jQuery("#monthlyRecurrenceWrap").hide();
				}
				if ( 'month' == recVal ) {
					jQuery("#weeklyRecurrenceWrap").show();
					jQuery("#monthlyRecurrenceWrap").show();
					jQuery("#eventastic_repeat_number").css('visibility','hidden');
				}
				setUpCustomPattern();
		}
		function setUpRecurrence(){
			var recVal = jQuery("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").val();
			if ( 'one_day' == recVal ) {
				jQuery("#endDayWrapper").hide();
				jQuery("#dateRepeaterWrap").hide();					
				jQuery("#patternRecurrenceWrap").hide();

				jQuery("#startDayWrapper").show();
				jQuery("label[for='eventastic_start_date']").html("Day of Event:");
			}
			if ( 'daily' == recVal ) {
				jQuery("#weeklyRecurrenceWrap").show();
				jQuery("#dateRepeaterWrap").hide();
				jQuery("#patternRecurrenceWrap").hide();

				jQuery("#startDayWrapper").show();
				jQuery("#endDayWrapper").show();
				jQuery("label[for='eventastic_start_date']").html("First Day of Event:");
				jQuery("label[for='eventastic_end_date']").html("Last Day of Event:");

			}
			if ( 'specific_days' == recVal ) {
				jQuery("#startDayWrapper").hide();
				jQuery("#endDayWrapper").hide();
				jQuery("#dateRepeaterWrap").show();
				jQuery("#patternRecurrenceWrap").hide();

			}

			if ( 'pattern' == recVal ) {
				jQuery("#dateRepeaterWrap").hide();
				jQuery("#startDayWrapper").show();
				jQuery("#endDayWrapper").show();
				jQuery("#patternRecurrenceWrap").show();

				jQuery("label[for='eventastic_start_date']").html("First Event Occurs On:");
				jQuery("label[for='eventastic_end_date']").html("Events Stops Repeating On:");

			}			
			setUpRepeat();
		}
		jQuery(window).on("load", function() {
			// recurrence picker
			var edVal = jQuery("input[name=<?php echo self::META_KEY_END_DATE_LOGIC["key"] ?>]:checked").val();
			if (edVal === undefined) {
				// set the default
				jQuery("#<?php echo self::META_KEY_END_DATE_LOGIC["choices"][0]["key"] ?>").prop("checked", true);
			} else if (edVal == "<?php echo self::META_KEY_END_DATE_LOGIC["choices"][1]["key"] ?>") {
				jQuery("#<?php echo self::META_KEY_END_DATE_LOGIC["choices"][1]["key"] ?>Wrap").show();
			}
			// if all day is checked, hide start and end
			var edVal = jQuery("input[name=<?php echo self::META_KEY_TIME_ALL_DAY["key"] ?>]").is(":checked");
			if (edVal) {
				jQuery("#timeWrap").hide();
			}


			// recurrence v2 picker
			var recVal = jQuery("select[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").val();
			if ( 'no_recurrence' == recVal ) {
				jQuery("#patternRecurrenceWrap").hide();
			}

			// v2 date repeater
			jQuery('.repeater-input-wrapper input').attr('id', '<?php echo self::META_KEY_REPEAT_DATES["key"] . "[]"; ?>');
			jQuery('.repeater-input-wrapper input').attr('name', '<?php echo self::META_KEY_REPEAT_DATES["key"] . "[]"; ?>');
			jQuery("#add-repeater-date").click(function() {
				var original = jQuery('#repeater-input-wrapper'),
					incr = original.closest('#repeater-wrapper').data('increment'),
					dateClone = jQuery(originalDateRepeater).clone(),
					dateInput = jQuery(dateClone).children('input').val(null);

					dateClone.find('.remove-repeater-date').removeClass('original').click(function(e){
						removeRepeaterDate(e);
					})
					incr++;
					original.data('increment', incr);
				//dateInput.attr('id', original.children('input').attr('id') + "_" + incr);
				//dateInput.attr('name', original.children('input').attr('id') + "_" + incr);
				bindDatePicker( dateInput );

				jQuery('#repeater-wrapper').append( dateClone );
			});

			var repeater = jQuery("[name=<?php echo self::META_KEY_RECURRENCE["key"] ?>]").val();
			if ( 'no_recurrence' == recVal ) {
				jQuery("#patternRecurrenceWrap").hide();
			}
			var repeatPatternSelectedValue="";
			<?php 
				if( array_key_exists( self::META_KEY_REPEAT_PATTERN['key'], $postMeta ) &&  is_array( $postMeta[self::META_KEY_REPEAT_PATTERN['key']] ) && count(  $postMeta[self::META_KEY_REPEAT_PATTERN['key']] ) ){
						$repeatPattern = $postMeta[self::META_KEY_REPEAT_PATTERN['key']][0];
						if( $repeatPattern ){
							echo "var repeatPatternSelectedValue = '". $repeatPattern . "';";
						}
				}
			?>
			setUpRepeatPatterns( repeatPatternSelectedValue );
		});
		
		function eventasticSetEndToStart () {
			jQuery("#<?php echo self::META_KEY_END_DATE["key"] ?>").val(jQuery("#<?php echo self::META_KEY_START_DATE["key"] ?>").val());
		}
		</script>
		<?php
    }
 
	/**
	 * Event meta box content save upon submission
	 *
	 * @param int $postId The related post id
	 * @return void
	 */
	public static function saveMetaBoxData ($postId) {
		parent::_savePassedData(
			self::ID,
			array(
				self::META_KEY_START_DATE["key"],
				self::META_KEY_END_DATE["key"],
				self::META_KEY_END_DATE_LOGIC["key"],
				self::META_KEY_WEEKDAYS["key"],
				self::META_KEY_FREQUENCY["key"],
				self::META_KEY_TIME_ALL_DAY["key"],
				self::META_KEY_START_TIME["key"],
				self::META_KEY_END_TIME["key"],
				self::META_KEY_RECURRENCE["key"],
				self::META_KEY_WEEKDAYS_V2["key"],
				self::META_KEY_WEEKS["key"],
				self::META_KEY_REPEAT_NUMBER["key"],
				self::META_KEY_REPEAT_TYPE["key"],
				self::META_KEY_REPEAT_DATES["key"],
				self::META_KEY_REPEAT_PATTERN["key"],
				self::META_KEY_PATTERN_DATES["key"]
			),
			$postId
		);
	}
}
