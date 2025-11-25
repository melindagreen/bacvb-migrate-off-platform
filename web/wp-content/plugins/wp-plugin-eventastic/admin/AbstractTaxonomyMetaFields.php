<?php

namespace Eventastic\Admin;

/**
 * Abstract meta box for event data collection
 * 
 * Danke https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/
 *
 * PERNDING This is not very abstract now, but the thinking is that children
 *	can declare their fields to use in a nice clean way in that function as opposed
 *	to calling this with that logic involved there.
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/FormControlLayout.php');

use Eventastic\Library\Constants as Constants;
use Eventastic\Library\FormControlLayout as FormControlLayout;

abstract class AbstractTaxonomyMetaFields {
 
	public $taxonomy = "";
	public $fields = array();
	
	/**
	 * Constructor
	 *
	 * @param string $taxonomy The meta box taxonomy
	 */
	public function __construct ($taxonomy) {
		
		$this->taxonomy = $taxonomy;
	}
	
	/**
	 * Builds out the fields array for the taxonomy. Must assign results to $fields!
	 *
	 * @return void
	 */
	 public abstract function buildFields ();
	 
	/**
	 * Adds the actions - should be called after buildFields()
	 *
	 * @return void
	 */
	public function addActions () {
		
		// add the build and save listeners
		add_action('create_term', array($this, 'saveEditMetaFields'), 10, 3);
		add_action('edit_term', array($this, 'saveEditMetaFields'), 10, 3);
		add_action($this->taxonomy.'_add_form_fields', array($this, 'displayAddMetaFields'), 10, 2);
		add_action($this->taxonomy.'_edit_form_fields', array($this, 'displayEditMetaFields'), 10, 2);
	}
 
	/**
	 * Renders the meta fields for radding
	 *
	 * @return void
	 */
	public function displayAddMetaFields () {
 
		if ($this->taxonomy == "") {
			// MAY EXIT THIS BLOCK
			return;
		}
		
		// show fields
		foreach ($this->fields as $field) {
			$field["key"] = "term_meta[{$field["key"]}]";
			echo '<div class="form-field">';
			if (isset($field["options"])) {
				echo FormControlLayout::renderSelectInput(null, $field, "", false, true, $field["options"]);
			} else {
				// is there a related font awesome icon?
				$fontAwesomeIcon = (isset($field["icon"])) ? $field["icon"] : "";
				// output field
				echo FormControlLayout::renderTextInput(null, $field, "regular-text", true, false, false, $fontAwesomeIcon);
			}
			echo (isset($field["description"])) 
				? '	<p class="description">'.$field["description"].'</p>'
				: '';
			echo '</div>';
		}
	}
 
	/**
	 * Renders the meta fields in edit mode
	 *
	 * @param object $term The parent term data
	 * @return void
	 */
	public function displayEditMetaFields ($term) {

		if ($this->taxonomy == "") {
			// MAY EXIT THIS BLOCK
			return;
		}
		
		$term_meta = get_term_meta($term->term_id); 
		foreach ($this->fields as $field) {
			$curTermValueMeta = array("term_meta[{$field["key"]}]" => array(esc_attr($term_meta[$field["key"]][0])));
			$field["key"] = "term_meta[{$field["key"]}]";
			// now output the row
			echo '<tr class="form-field term-group-wrap">';
			echo '	<th scope="row"><label for="term_meta['.$field["key"].']">'.$field["label"].'</label></th>';
			echo '	<td>';
			if (isset($field["options"])) {
				echo FormControlLayout::renderSelectInput($curTermValueMeta, $field, "", false, true, $field["options"], true);
			} else {
				// is there a related font awesome icon?
				$fontAwesomeIcon = (isset($field["icon"])) ? $field["icon"] : "";
				// output field
				echo FormControlLayout::renderTextInput($curTermValueMeta, $field, "regular-text", false, false, true, $fontAwesomeIcon);
			}

			echo (isset($field["description"])) 
				? '	<p class="description">'.$field["description"].'</p>'
				: '';
			echo '</td>';
			echo '</tr>';
		}
	}
	 
	/**
	 * Saves the meta fields
	 *
	 * @param object $termId The term id to act upon
	 * @return void
	 */
	public function saveEditMetaFields ($termId) {

		if (isset( $_POST["term_meta"])) {
			foreach (array_keys($_POST["term_meta"]) as $key) {
				if (isset($_POST["term_meta"][$key])) {
					update_term_meta($termId, $key, $_POST["term_meta"][$key]);
				}
			}
		}
	}
}