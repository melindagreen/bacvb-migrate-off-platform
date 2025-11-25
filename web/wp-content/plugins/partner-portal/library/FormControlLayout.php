<?php

namespace PartnerPortal\Library;

/**
 * Form control library
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

class_exists('Constants', false) or require_once 'Constants.php';

class FormControlLayout {
	
	/**
	 * Renders a hidden input
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderHiddenInput ($postMeta, $fieldKeyVal) {
				
		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "hidden",
			"", false, false);
	}

	/**
	 * Renders a text input
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderTextInput ($postMeta, $fieldKeyVal, 
			$fontAwesomeIcon="", $cssClass="", $newLineAfter=false, $wrappedLabel=false, $hideLabel=false) {
				
		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "text", 
			$cssClass, $newLineAfter, $wrappedLabel, "", false, $hideLabel, $fontAwesomeIcon);
	}

	/**
	 * Renders a textarea input
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderTextAreaInput ($postMeta, $fieldKeyVal, 
			$fontAwesomeIcon="", $cssClass="", $newLineAfter=false, $wrappedLabel=false, $hideLabel=false) {
				
		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "textarea", 
			$cssClass, $newLineAfter, $wrappedLabel, "", false, $hideLabel, $fontAwesomeIcon);
	}
		
	/**
	 * Renders am input with the specified font awesome icon proceeding it (just a convenience one as we
	 *	move the icon setting up in parameters so that we can avoid setting a bunch of defaults each call)
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderTextFontAwesomeInput ($postMeta, $fieldKeyVal, 
			$cssClass="", $fontAwesomeIcon, $newLineAfter=false, $wrappedLabel=false, $minDate="", $isTime=false, $hideLabel=false) {
				
		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "text", 
			$cssClass, $newLineAfter, $wrappedLabel, $minDate, $isTime, $hideLabel, $fontAwesomeIcon);
	}

	/**
	 * Renders a number input
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderNumberInput ($postMeta, $fieldKeyVal, 
			$fontAwesomeIcon="", $cssClass="", $newLineAfter=false, $wrappedLabel=false, $hideLabel=false) {
				
		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "number", 
			$cssClass, $newLineAfter, $wrappedLabel, "", false, $hideLabel, $fontAwesomeIcon);
	}
	
	/**
	 * Renders a date input
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderDateInput ($postMeta, $fieldKeyVal, $minDate="", 
			$fontAwesomeIcon="", $cssClass="", $newLineAfter=false, $wrappedLabel=false, $hideLabel=false) {

		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "date", 
			$cssClass, $newLineAfter, $wrappedLabel, $minDate, false, $hideLabel, $fontAwesomeIcon);
	}

	
	/**
	 * Renders a time input
	 *
	 * @see _renderInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderTimeInput ($postMeta, $fieldKeyVal, 
			$fontAwesomeIcon="", $cssClass="", $newLineAfter=false, $wrappedLabel=false, $hideLabel=false) {
				
		return FormControlLayout::_renderInput($postMeta, $fieldKeyVal, "text", 
			$cssClass, $newLineAfter, $wrappedLabel, "", true, $hideLabel, $fontAwesomeIcon);
	}
	
	/**
	 * Renders a radio set input
	 *
	 * @see _renderClickInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderRadioInput ($postMeta, $fieldKeyVals, 
			$cssClass="", $newLineAfter=false, $wrappedLabel=false, $elementPerLine=true, $hideLabel=false) {

		return FormControlLayout::_renderClickInput($postMeta, $fieldKeyVals, "radio",
			$cssClass, $newLineAfter, $wrappedLabel, $elementPerLine, $hideLabel);

	}
	
	/**
	 * Renders a checkbox set input
	 *
	 * @see _renderClickInput for parameter information
	 * 
	 * @return string The rendered input and label
	 */
	public static function renderCheckboxInput ($postMeta, $fieldKeyVals, 
			$cssClass="", $newLineAfter=false, $wrappedLabel=false, $elementPerLine=true, $hideLabel=false) {

		return FormControlLayout::_renderClickInput($postMeta, $fieldKeyVals, "checkbox",
			$cssClass, $newLineAfter, $wrappedLabel, $elementPerLine, $hideLabel);
	}
	
	/**
	 * Select input renderer
	 *
	 * @param array $postMeta The related post meta
	 * @param array $fieldKeyVal The key and value data for the field
	 * @param string $cssClass An optional CSS class for the input
	 * @param boolean $newLineAfter Add a newline break after?
	 * @param boolean $wrappedLabel Wrap size of field label to text?
	 * @param array $selectData The select option data
	 * @param boolean $hideLabel Hide the label altogether?
	 * @return string The rendered input and label
	 */
	public static function renderSelectInput ($postMeta, $fieldKeyVal, 
			$cssClass, $newLineAfter, $wrappedLabel, $selectData, $hideLabel=false, $defaultSelect = null) {

		$rhett = "";
		
		// label
		if (! $hideLabel) {
			// wrapping the label?
			$labelClass = ($wrappedLabel) ? " wrapped" : "";
			
			$rhett .= '<label class="partnerportalLabel'.$labelClass.'" for="'.$fieldKeyVal["key"].'">'
				.$fieldKeyVal["label"].'</label>';
		}
					
		// field
		$rhett .= '<select id="'.$fieldKeyVal["key"].'" '
			.'class="partnerportalInput '.$cssClass.'"'
			.'name="'.$fieldKeyVal["key"].'">';
		$selected = null;
		foreach ($selectData as $k => $v) {
			if( $k == $postMeta[$fieldKeyVal["key"]][0]){
				$selected = $k;
			}
		}
		foreach ($selectData as $k => $v) {
			if( $selected ){
				$selectedValue = ($k == $postMeta[$fieldKeyVal["key"]][0] ) ? ' selected="true"' : '';
			}
			else{
				$selectedValue = ( $k == $defaultSelect ) ? ' selected="true"' : '';
			}
			$rhett .= '<option value="'.$k.'"'.$selectedValue.'>'.$v.'</option>';
		}
		$rhett .= '</select>';
		$rhett .= ($newLineAfter) ? "<br/><br/>" : "";
		
		return $rhett;
	}

	/**
	 * Master private input renderer
	 *
	 * @param array $postMeta The related post meta
	 * @param array $fieldKeyVal The key and value data for the field
	 * @param string $type The field input type
	 * @param string $cssClass An optional CSS class for the input
	 * @param boolean $newLineAfter Add a newline break after?
	 * @param boolean $wrappedLabel Wrap size of field label to text?
	 * @param string $minDate An optional minimum date for the input (if a date input)
	 * @param boolean $isTime Is it a time field?
	 * @param boolean $hideLabel Hide the label altogether?
	 * @param string $fontAwesomeIcon The Fontawesome icon to use ahead of the field (optional)
	 * @return string The rendered input and label
	 */
	private static function _renderInput ($postMeta, $fieldKeyVal, $type, 
			$cssClass, $newLineAfter, $wrappedLabel, $minDate="", $isTime=false, $hideLabel=false, $fontAwesomeIcon="") {

		$rhett = "";

		// label
		if ( ($type != "hidden") && (! $hideLabel) ) {
			// wrapping the label?
			$labelClass = ($wrappedLabel) ? " wrapped" : "";
			
			$rhett .= '<label class="partnerportalLabel'.$labelClass.'" for="'.$fieldKeyVal["key"].'">'
				.$fieldKeyVal["label"].'</label>';
		}
		
		// is it awesome?
		if ($fontAwesomeIcon != "") {
			$rhett .= '<span class="add-on"><i class="partnerportalFA '.$fontAwesomeIcon.'"></i></span>';
			$cssClass .= " partnerportalFAInput";
		}

		// the field valuea
		$fieldValue = (isset($postMeta[$fieldKeyVal["key"]]))
			? $postMeta[$fieldKeyVal["key"]][0]
			: "";
		
		// field
		if( 'textarea' == $type ){
			$rhett .= '<textarea id="'.$fieldKeyVal["key"].'" '
				.(($minDate != "") ? 'min="'.$minDate.'" ' : '')
				.(( ($type == "date") || ($isTime) ) ? 'readonly="true" ' : '')
				.'class="partnerportalInput '.$cssClass.'"'
				.'name="'.$fieldKeyVal["key"].'">' . $fieldValue . '</textarea>';			
		}
		else{
			$rhett .= '<input type="'.$type.'" id="'.$fieldKeyVal["key"].'" '
				.(($minDate != "") ? 'min="'.$minDate.'" ' : '')
				.(( ($type == "date") || ($isTime) ) ? 'readonly="true" ' : '')
				.'class="partnerportalInput '.$cssClass.'"'
				.'name="'.$fieldKeyVal["key"].'" '
				.'value="'.$fieldValue.'" />';
		}
		$rhett .= ($newLineAfter) ? "<br/><br/>" : "";
		
		return $rhett;
	}
	
	/**
	 * Renders a radio set input
	 *
	 * @param array $postMeta The related post meta
	 * @param array $fieldKeyVals The key and value data for each radio item
	 * @param string $type The field input type
	 * @param string $type The field input type
	 * @param string $cssClass An optional CSS class for the input
	 * @param boolean $newLineAfter Add a newline break after?
	 * @param boolean $wrappedLabel Wrap size of field label to text?
	 * @param string $minDate An optional minimum date for the input (if a date input)
	 * @param boolean $elementPerLine New line per input element?
	 * @param boolean $hideLabel Hide the label altogether?
	 * @return string The rendered input and label
	 */
	private static function _renderClickInput ($postMeta, $fieldKeyVals, $type, 
			$cssClass="", $newLineAfter=false, $wrappedLabel=false, $elementPerLine=true, $hideLabel=false) {

		$rhett = "";
		$labelClass = "";
		$metaValue = (isset($postMeta[$fieldKeyVals["key"]]))
			? maybe_unserialize($postMeta[$fieldKeyVals["key"]][0])
			: "";
			
		// label
		if (! $hideLabel) {
			// wrapping the label?
			$labelClass = ($wrappedLabel) ? " wrapped" : "";
			// the label for the set
			$rhett = '<label class="partnerportalLabel'.$labelClass.'">'
				.$fieldKeyVals["label"].'</label>';
		}

		// if a checkbox array, add brackets to name
		if ( ($type == "checkbox") && (count($fieldKeyVals["choices"]) > 1) ) {
			$fieldKeyVals["key"] .= "[]";
		}
		
		// fields
		$rhett .= '<div class="partnerportalRadioWrap">';
		foreach ($fieldKeyVals["choices"] as $fkv) {
			// is it checked?
			$checked = false;
			if ( (is_array($metaValue)) && (in_array($fkv["key"], $metaValue)) ) {
				$checked = true;
			} else if (! is_array($metaValue)) {
				$checked = ($fkv["key"] == $metaValue);
			}
			$rhett .= '<div class="partnerportal-input-wrapper"><input type="'.$type.'" id="'.$fkv["key"].'" '
				.'class="partnerportalInput '.$cssClass.'"'
				.'name="'.$fieldKeyVals["key"].'" '
				.'value="'.$fkv["key"].'" '
				.(($checked) ? 'checked="true"' : '')
				.'/>';
			$rhett .= '<label class="partnerportalLabel'.$labelClass.'" for="'.$fkv["key"].'">'
				.$fkv["label"].'</label></div>';
			$rhett .= ($elementPerLine) ? '<br/>' : '';
		}
		$rhett .= '</div>';
		$rhett .= ($newLineAfter) ? "<br/><br/>" : "";
		
		return $rhett;
	}
}

?>
