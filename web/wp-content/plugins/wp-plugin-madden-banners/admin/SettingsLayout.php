<?php /**
 * Layout for admin settings page
 */

namespace MaddenBanners\Admin;

// Include library files this file requires
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');
require_once(__DIR__.'/AdminConstants.php');

// Rename imports
use MaddenBanners\Library\Constants as Constants;
use MaddenBanners\Library\Utilities as Utilities;
use MaddenBanners\Admin\AdminConstants as AdminConstants;

class SettingsLayout {
    // Will hold existing options
    private $_options;

    // Alias some constants for convienience
    private $_prefix = Constants::PLUGIN_PREFIX;
    private $_options_page = Constants::PLUGIN_ADMIN_MENU_SLUG;
    private $_settings_slug = Constants::PLUGIN_SETTING_SLUG;
    private $_settings_group_slug = Constants::PLUGIN_SETTING_GROUP_SLUG;

    // The settings form is build from this object
    private $_settings_sections;

    /**
     * Construct new SettingsLayout
     */
    public function __construct() {
        $this->_settings_sections = AdminConstants::get_settings_fields();

        // Add settings sections:
        foreach($this->_settings_sections as $section) {
            add_settings_section(
                $this->_prefix.$section['id'],
                $section['title'].' settings',
                [$this, 'echo_section'],
                $this->_options_page
            );

            // Add section fields:
            foreach($section['fields'] as $field) {
                $field['args']['label_for'] = $this->_prefix.'-'.$field['id'];
                $field['args']['section'] = $section['id'];
                add_settings_field(
                    $this->_prefix.$field['id'],
                    $field['title'],
                    [$this, 'echo_field'],
                    $this->_options_page,
                    $this->_prefix.$section['id'],
                    $field['args']
                );
            }
        }

        // Load existing options:
        $this->_options = get_option($this->_settings_slug);

        // Register settings field:
        register_setting($this->_settings_group_slug, $this->_settings_slug);
    }

    /**
     * Echoes description for each settings section
     * @param array $args           Data passed to callback from add_settings_section
     */
   public function echo_section($args) {
        // Get current section slug
        $section_slug = str_replace($this->_prefix, '', $args['id']);

        // Search config constant for description
        $description = array_values(array_filter(AdminConstants::get_settings_fields(), function( $section ) use( $section_slug ) {
            return $section['id'] === $section_slug;
        }))[0]['description'];

        // Echo description
        echo "<p class='{$this->_prefix}-section_desc'>$description</p>";
   }

   /**
     * Echoes input for each settings field
     * @param array $args               Data passed to callback from add_settings_field
     * @param boolean $is_subfield      Is this field a subfield of another field?
     * @param string $parent_name       The parent field's name, if present
     * @return null
     */
   public function echo_field( $args ) {
        // Prepare vars
        $id = $args['label_for']; // ID
        $field_group_id = $id . '-field-group';

        $field = str_replace($this->_prefix.'-', '', $id); // Field name

        // Name attribute
        if( isset( $args['parent_name'] ) && isset( $args['index'] ) ) {
            $is_subfield = true;
            $name_attr = $args['parent_name'] . "[{$args['index']}][$field]";
        } else {
            $is_subfield = false;
            $name_attr = $this->_settings_slug."[{$args['section']}][$field]";
        }

        // Default value
        $default = isset($args['default']) ? $args['default'] : '';

        // Placeholder value
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : ''; 

        // Is disabled?
        $disabled = isset( $args['disabled'] ) && $args['disabled'];

        // Current value
        if( isset( $args['field_value'] ) )
            $value = $args['field_value']; 
        else $value = isset($this->_options[$args['section']][$field]) && !empty($this->_options[$args['section']][$field]) 
            ? $this->_options[$args['section']][$field] 
            : $default; 
        $slashed_val = is_string( $value ) ? esc_attr($value) : '';

        // Conditional?
        if( isset( $args['show_if'] ) ) {
            $show_if = $args['show_if'];
            $cond_string = "data-showif='" . implode( "|", $show_if ) . "'";
        } else $cond_string = '';

        echo "<div id='$field_group_id' class='$this->_prefix-field-group' $cond_string>";
 
        // Construct tooltip
        $tooltip = '';
        if( isset($args['tooltip']) && $args['tooltip'] ) {
            $tooltip = "<div class='$this->_prefix-tooltip'><span class='tooltip-trigger'>?</span><div class='tooltip-content'>{$args['tooltip']}</div></div>";
        }

        // Echo output based on input type
        switch($args['type']) {
            // CHECKBOXES
            case 'check':
                if( $is_subfield ) echo "<label for='$id'>{$args["title"]}$tooltip</label>";

                echo "<input type='checkbox' id='$id' name='$name_attr' value='true'". checked('true', $value, false) . " " . disabled( $disabled, true, false ) . " />";
                break;

            // NUMBER
            case 'number':
                if( $is_subfield ) echo "<label for='$id'>{$args["title"]}$tooltip</label>";

                echo "<input type='number' min='{$args['min']}' max='{$args['max']}' id='$id' name='$name_attr' value='$value' " . disabled( $disabled, true, false ) . " />";
                break;

            // RADIO BUTTONS
            case 'radio':
                $radio_html = implode("\n", array_map(function($slug, $label) use($value, $name_attr, $field, $disabled, $tooltip) {
                    return "<div class='{$field}_radio_btn_group'>
                        <input
                            type='radio'
                            class='{$field}_radio_btn'
                            id='{$field}_radio_btn_{$slug}'
                            name='$name_attr'
                            value='$slug'".
                            checked($slug, $value, false) .
                            disabled( $disabled, true, false ) .
                        "/>
                        <label for='{$field}_radio_btn_{$slug}'>{$label}</label>
                    </div>";
                }, array_keys($args['options']), $args['options']));
                echo "<div class='{$field}_radio_group'>$radio_html</div>";
                break;

            // SELECT DROPDOWN
            case 'select':
                if( $is_subfield ) echo "<label for='$id'>{$args["title"]}$tooltip</label>";

                $options_html = implode("\n", array_map(function($slug, $label) use($value) {
                    return "<option value='$slug' ".selected($slug, $value, false).">".__($label, 'madden-cookie-consent')."</option>";
                }, array_keys($args['options']), $args['options']));
                echo "<select name='$name_attr' id='$id' " . disabled( $disabled, true, false ) . ">$options_html</select>";
                break;

            // TEXTAREA
            case 'textarea':
                if( $is_subfield ) echo "<label for='$id'>{$args["title"]}$tooltip</label>";

                echo "<textarea name='$name_attr' rows='5' cols='50' " . disabled( $disabled, true, false ) . ">$slashed_val</textarea>";
                break;

            // URL
            case 'url':
                if( $is_subfield ) echo "<label for='$id'>{$args["title"]}$tooltip</label>";

                echo "<input name='$name_attr' type='url' id='$id' value='$value' placeholder='$placeholder' " . disabled( $disabled, true, false ) . " />";
                break;

            // REPEATER
            case 'repeater':
                $sub_fields = isset( $args['sub_fields'] ) ? $args['sub_fields'] : array();

                echo "<div class='repeater-group-container'>";

                    if( is_array( $value ) ) foreach( array_values( $value ) as $index => $field_group ) {
                        // TODO name toggle button more descriptively
                        echo "<div class='repeater-group' id='$this->_prefix-$id-repeater-group-$index' data-type='{$args['noun']}'>
                            <div class='top-buttons'>
                                <button type='button' class='toggle-repeater-group'>" . ucfirst( $args['noun'] ) . " <span class='repeater-count'>" . ( $index + 1 ) . "</span></button>
                                <button type='button' class='repeater-groups-remove icon-button' data-type='repeater-group'><span class='dashicons dashicons-no'></span></button>
                            </div>
                            
                            <div class='repeater-group-content'>
                                <input type='hidden' name='{$name_attr}[$index][id]' value='$id-$index' disabled='disabled' />";

                                foreach( $sub_fields as $sub_field ) {
                                    echo "<div class='repeater-field'>";

                                    $sub_field['label_for'] = $this->_prefix.'-'.$sub_field['id'];
                                    $sub_field['section'] = $args['section'];
                                    $sub_field['parent_name'] = $name_attr;

                                    if( isset( $field_group[$sub_field["id"]] ) )   
                                        $sub_field['field_value'] = $field_group[$sub_field["id"]];

                                    $sub_field["index"] = $index;
                
                                    $this->echo_field( $sub_field );

                                    echo "</div>";
                                }

                            echo "</div>
                        </div>";
                    }

                echo "</div>
                <div class='repeater-group new' id='$id-repeater-group-new'>
                    <div class='top-buttons'>
                        <button type='button' class='toggle-repeater-group'>" . ucfirst( $args['noun'] ) . " <span class='repeater-count'>new</span></button>
                        <button type='button' class='repeater-groups-remove icon-button' data-type='repeater-group'><span class='dashicons dashicons-no'></span></button>
                    </div>
                    
                    <div class='repeater-group-content'>
                        <input type='hidden' name='{$name_attr}[new][id]' value='$id-new' disabled='disabled' />";

                    foreach( $sub_fields as $sub_field ) {
                        $sub_field['label_for'] = $this->_prefix.'-'.$sub_field['id'];
                        $sub_field['section'] = $args['section'];
                        $sub_field['disabled'] = true;
                        $sub_field['parent_name'] = $name_attr;
                        $sub_field['index'] = 'new';

                        $this->echo_field( $sub_field );
                    }

                echo "</div>
                </div> 
                <button type='button' class='repeater-groups-add button' data-type='repeater-group'>Add " . $args['noun'] . "</button>";
            
                break;

            // CONDITIONAL
            case 'conditional':
                $sub_fields = isset( $args['sub_fields'] ) ? $args['sub_fields'] : array();
            
                echo "<div class='conditions-and condition-and-group-container'>";

                    if( is_array( $value ) ) foreach( array_values( $value ) as $and_index => $and_group ) {
                        echo "<div class='condition-and-group condition-container' id='condtion-and-group-$and_index'>";
                            foreach( array_values( $and_group ) as $or_index => $or_group ) {
                                echo "<div class='condition'>";
                                    foreach( $sub_fields as $subfield_index => $sub_field ) {
                                        echo "<div class='condition-field'>";
        
                                        $sub_field['label_for'] = $this->_prefix.'-'.$sub_field['id'];
                                        $sub_field['section'] = $args['section'];
                                        $sub_field['parent_name'] = $name_attr . "[$and_index]";

                                        if( isset( $or_group[$sub_field["id"]] ) )   
                                            $sub_field['field_value'] = $or_group[$sub_field["id"]];

                                        $sub_field["index"] = $or_index;
                    
                                        $this->echo_field( $sub_field );
            
                                        echo "</div>";
                                    }

                                    echo "<button type='button' class='conditions-add-or icon-button' data-type='condition'><span class='dashicons dashicons-plus'></button>
                                    <button type='button' class='conditions-remove icon-button' data-type='condition'><span class='dashicons dashicons-no'></span></button>
                                </div>";

                                if( $or_index < count( $and_group ) - 1 ) echo "<p id='$this->_prefix-$id-condition-" . ( $or_index + 1 ) . "-joiner'>OR</p>";
                            }

                            echo "<div class='condition new'>";
                                foreach( $sub_fields as $sub_field ) {
                                    echo "<div class='condition-field'>";
                                        $sub_field['label_for'] = $this->_prefix.'-'.$sub_field['id'];
                                        $sub_field['section'] = $args['section'];
                                        $sub_field['disabled'] = true;
                                        $sub_field['parent_name'] = $name_attr . "[$and_index]";
                                        $sub_field['index'] = 'new';
                                        $this->echo_field( $sub_field );
            
                                    echo "</div>";
                                }
                                echo "<button type='button' class='conditions-add-or icon-button' data-type='condition'><span class='dashicons dashicons-plus'></button>
                                <button type='button' class='conditions-remove icon-button' data-type='condition'><span class='dashicons dashicons-no'></span></button>
                            </div>";

                            echo "</div>";

                        if( $and_index < count( $value ) - 1 ) echo "<p id='condtion-and-group-" . ( $and_index + 1 ) . "-joiner'>AND</p>";
                    }
 
                echo "</div>

                <div class='condition-and-group condition-container new' id='condtion-and-group-new'>
                    <div class='condition'>";
                        foreach( $sub_fields as $sub_field ) {
                            echo "<div class='condition-field'>";

                            $sub_field['label_for'] = $this->_prefix.'-'.$sub_field['id'];
                            $sub_field['section'] = $args['section'];
                            $sub_field['disabled'] = true;
                            $sub_field['parent_name'] = $name_attr .  '[new]';
                            $sub_field['index'] = 0;
                            $this->echo_field( $sub_field );

                            echo "</div>";
                        }
                        echo "<button type='button' class='conditions-add-and icon-button' data-type='condition'><span class='dashicons dashicons-plus'></button>
                        <button type='button' class='conditions-remove icon-button' data-type='condition'><span class='dashicons dashicons-no'></span></button>
                    </div>

                    <div class='condition new'>";
                        foreach( $sub_fields as $sub_field ) {
                            echo "<div class='condition-field'>";

                            $sub_field['label_for'] = $this->_prefix.'-'.$sub_field['id'];
                            $sub_field['section'] = $args['section'];
                            $sub_field['disabled'] = true;
                            $sub_field['parent_name'] = $name_attr . '[new]';
                            $sub_field['index'] = 0;
                            $this->echo_field( $sub_field );

                            echo "</div>";
                        }

                        echo "<button type='button' class='conditions-add-and icon-button' data-type='condition'><span class='dashicons dashicons-plus'></button>
                        <button type='button' class='conditions-remove icon-button' data-type='condition'><span class='dashicons dashicons-no'></span></button>
                    </div>
                </div> 
                
                <button type='button' class='banner-preview button'>Preview</button>
                <button type='button' class='conditions-add-and button' data-type='condition-and-group'>Add " . $args['noun'] . "</button>";
            
                break;

            // Default to text input:
            case 'text':
            default:
                if( $is_subfield ) echo "<label for='$id'>{$args["title"]}{$tooltip}</label>";

                echo "<input name='$name_attr' type='text' id='$id' value='$slashed_val' placeholder='$placeholder' " . disabled( $disabled, true, false ) . " />";
                break;
        }

        echo "</div>";
   }
}