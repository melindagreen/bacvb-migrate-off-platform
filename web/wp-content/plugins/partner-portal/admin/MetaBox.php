<?php

namespace PartnerPortal\Admin;

/**
 * Meta box for listing contact information
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */
 
require_once(__DIR__.'../../partner-portal.php');
require_once(__DIR__.'/AbstractMetaBox.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');
require_once(__DIR__.'/../admin/MetaBoxGallery.php');

use PartnerPortal\PartnerPortal as PartnerPortal;
use PartnerPortal\Admin\MetaBoxGallery as MetaBoxGallery;
use PartnerPortal\Admin\AbstractMetaBox as AbstractMetaBox;
use PartnerPortal\Library\Constants as Constants;
use PartnerPortal\Library\FormControlLayout as FormControlLayout;

/**
 * Meta box for contact information
 */

class MetaBox extends AbstractMetaBox {
	

    /**
     * @var array Holds the input configurations for the meta box
     */
    protected $inputs;
	
	/**
	 * Constructor
	 */
    public function __construct ( $args ) {

		parent::__construct($args['id'], $args['title']);
		$this->inputs = $args['inputs'];

		// add our save action
		add_action('save_post', array($this, 'saveMetaBoxData'));
	}
 
    /**
     * Renders the content of the meta box
	 *
	 * @return void
     */
    public function displayMetaBox ($post) {

		// get the post meta
		$postMeta = get_post_meta($post->ID);

		// security
		//wp_nonce_field(self::$NONCE_BASE, Constants::NONCE_ROOT.$this->ID);

		if(is_array( $this->inputs ) ){
			$has_date_picker = false;
			foreach( $this->inputs as $metaBoxInput ){
				$metaBoxInputType = array_key_exists('type', $metaBoxInput ) ? $metaBoxInput['type'] : null;
				if( "message" == $metaBoxInputType ){
					echo $metaBoxInput['content'];
				}else{
					$metaBoxInput['key'] = Constants::WP_POST_META_KEY_PREPEND . $metaBoxInput['key'];
					$cssClass = (array_key_exists('cssClass',$metaBoxInput)) ? $metaBoxInput['cssClass'] : "";
					$fieldKeyVals = (array_key_exists('fieldKeyVals',$metaBoxInput)) ? $metaBoxInput['fieldKeyVals'] : [];
					$newLineAfter = (array_key_exists('newLineAfter',$metaBoxInput)) ? $metaBoxInput['newLineAfter'] : true;
					$icon = (array_key_exists('icon',$metaBoxInput)) ? $metaBoxInput['icon'] : null;

					$type = 'text';
					if( ( array_key_exists('type', $metaBoxInput) && $metaBoxInput['type']) ){
						$type = $metaBoxInput['type'];
					}
					if( 'checkbox' === $type ){
						echo FormControlLayout::renderCheckboxInput ($postMeta, $metaBoxInput, $cssClass, $newLineAfter, $wrappedLabel, $elementPerLine, $hideLabel);

					}
					if( 'text' === $type ){
						echo FormControlLayout::renderTextFontAwesomeInput($postMeta, $metaBoxInput,$cssClass, $icon, $newLineAfter);
					}
					if( 'textarea' === $type ){
						echo FormControlLayout::renderTextAreaInput($postMeta, $metaBoxInput,$cssClass, $icon, $newLineAfter);
					}
					if('date' == $type ){
						$has_date_picker = true;
						echo FormControlLayout::renderDateInput($postMeta, $metaBoxInput, date(Constants::DATE_FORMAT_MYSQL), "date", $newLineAfter, false, false, $icon);

					}
					if( 'time' === $type ){
						$has_date_picker = true;
						echo FormControlLayout::renderTimeInput($postMeta, $metaBoxInput, "time", $newLineAfter, false, false, $icon);
					}
					if( 'gallery' === $type ){
						$options = [];
						$options['buttonText'] = (array_key_exists('buttonText',$metaBoxInput)) ? $metaBoxInput['buttonText'] : "";
			           $mbGallery = new MetaBoxGallery( $metaBoxInput );
			           $mbGallery->displayMetaBox( $post, $options );

					}					
					if( 'select' === $type ){
						$options = (array_key_exists('options',$metaBoxInput)  ) ? $metaBoxInput['options'] : [];
						if( "SELECT_STATES" == $options ){
							$options = Constants::SELECT_STATES;
						}
						$defaultChoice = (array_key_exists('defaultChoice',$metaBoxInput)) ? $metaBoxInput['defaultChoice'] : "";

						echo FormControlLayout::renderSelectInput($postMeta, $metaBoxInput,$cssClass, $icon, $newLineAfter, $options,"", $defaultChoice);
					}
				}
			}
			if( $has_date_picker ){ ?>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						
						
						
						// set up the date pickers
						var datePickerFormat = "<?php echo Constants::DATE_FORMAT_JS_DATEPICKER ?>";
						jQuery("input[type=date]").each(function() {
							var options = {
								showDropdowns: false,
								singleDatePicker: true,
								autoUpdateInput: false,
								autoApply : true,								
								// minDate: "<?php echo date(Constants::DATE_FORMAT_MYSQL) ?>",
								opens: "right",
								drops: "up",
								locale: {
									format: datePickerFormat,
									firstDay: 0
								}
							};

							// current value?
							if (jQuery(this).val() != "") {
								options.startDate = moment(jQuery(this).val()).format(datePickerFormat);
							}
							
							// init it
							jQuery(this).daterangepicker(options);

							// act upon selection
							jQuery(this).on('show.daterangepicker', function(ev, picker) {
								if (jQuery(this).val() != "") {
									var sd = moment(jQuery(this).val()).format(datePickerFormat);
									jQuery(this).data('daterangepicker').setStartDate(sd);
								}
							});
							jQuery(this).on('apply.daterangepicker', function(ev, picker) {
								jQuery(this).val(picker.startDate.format('<?php echo Constants::DATE_FORMAT_JS_MYSQL ?>'));
							});
							jQuery(this).on('cancel.daterangepicker', function(ev, picker) {
								jQuery(this).val('');
							});
						});
						
						// set up the time pickers
						var timePickerFormat = "<?php echo Constants::TIME_FORMAT_JS_PRETTY ?>";
						jQuery("input.time").each(function() {
							var options = {
								singleDatePicker: true,
								timePicker: true,
								timePicker24Hour: true,
								timePickerIncrement: 1,
								timePickerSeconds: false,
								timePickerIncrement: 15,
								timePickerSeconds: false,
								autoUpdateInput : true,
								autoApply : true,
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
					});
					
					jQuery(window).on("load", function() {
						// recurrence picker
						
					});
					
				</script>
			<?php 
			}

		}
    }
 
	/**
	 * Listing meta box content save upon submission
	 *
	 * @param int $postId The related post id
	 * @return void
	 */
	public static function saveMetaBoxData ($postId) {
		//error_log('post:' . $postId );
			$config_file = PartnerPortal::get_plugin_file('partner.json');
            $strJsonFileContents = file_get_contents( $config_file );            
			$partnerportalObject = json_decode($strJsonFileContents, true);
            $partnerFormArray = $partnerportalObject['metaBoxes'];            
            if( is_array($partnerFormArray) ){
                foreach( $partnerFormArray as $partnerMetaBox ){
                	$id = $partnerMetaBox['id'];
                	$input_array = [];
					if(is_array( $partnerMetaBox['inputs'] ) ){
                		foreach( $partnerMetaBox['inputs'] as $metaBoxInput ){
                			if( !( array_key_exists('type', $metaBoxInput) && 'message' == $metaBoxInput['type'] ) ){
								$input_array[] = Constants::WP_POST_META_KEY_PREPEND . $metaBoxInput['key'];
							}
                			if( array_key_exists('type', $metaBoxInput) && 'checkbox' == $metaBoxInput['type'] ){
					        	delete_post_meta($postId, Constants::WP_POST_META_KEY_PREPEND . $metaBoxInput['key']);
							}

						}
					}
					else{
						//error_log('input is not an array:::');
						//error_log(print_r($partnerMetaBox,true));
					}

					if(count($input_array)>0){
						self::_savePassedData(
							$id,
							$input_array,
							$postId
						);		
					}
                }
            }        
	}
}
