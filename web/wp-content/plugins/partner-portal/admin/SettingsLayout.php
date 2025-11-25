<?php /**
 * Layout for admin settings page
 */

namespace PartnerPortal\Admin;

// Include library files this file requires
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');
require_once(__DIR__.'/AdminConstants.php');

// Rename imports
use PartnerPortal\Library\Constants as Constants;
use PartnerPortal\Library\Utilities as Utilities;
use PartnerPortal\Admin\AdminConstants as AdminConstants;

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
        $description = array_values(array_filter(AdminConstants::get_settings_fields(), function($section) use($section_slug) {
            return $section['id'] === $section_slug;
        }))[0]['description'];

        // Echo description
        echo "<p class='{$this->_prefix}-section_desc'>$description</p>";
   }

   /**
     * Echoes input for each settings field
     * @param array $args               Data passed to callback from add_settings_field
     * @return null
     */
   public function echo_field( $args ) {
        // Prepare vars
        $id = $args['label_for']; // ID
        $field_group_id = $id . '-field-group';

        $field = str_replace($this->_prefix.'-', '', $id); // Field name

        $name_attr = $this->_settings_slug."[{$args['section']}][$field]";

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

        // Conditional?
        if( isset( $args['args']['show_if'] ) ) {
            $show_if = $args['args']['show_if'];
            $cond_string = "data-showif='" . implode( "|", $show_if ) . "'";
        } else $cond_string = '';

        echo "<div id='$field_group_id' class='$this->_prefix-field-group' $cond_string>";
 
        // Construct tooltip
        if( isset($args['tooltip']) && $args['tooltip'] ) {
            echo "<div class='$this->_prefix-tooltip' data-field='$id'>";
            _e($args['tooltip'], 'madden-cookie-consent');
            echo "</div>";
        }

        // Echo output based on input type
        switch($args['type']) {
            // CHECKBOXES
            case 'check':
                echo "<input type='checkbox' id='$id' name='$name_attr' value='true'". checked('true', $value, false) . " " . disabled( $disabled, true, false ) . " />";
                break;

            // NUMBER
            case 'number':
                echo "<input type='number' min='{$args['min']}' max='{$args['max']}' id='$id' name='$name_attr' value='$value' " . disabled( $disabled, true, false ) . " />";
                break;

            // RADIO BUTTONS
            case 'radio':
                $radio_html = implode("\n", array_map(function($slug, $label) use($value, $name_attr, $field, $disabled) {
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
                        <label for='{$field}_radio_btn_{$slug}'>$label</label>
                    </div>";
                }, array_keys($args['options']), $args['options']));
                echo "<div class='{$field}_radio_group'>$radio_html</div>";
                break;

            // SELECT DROPDOWN
            case 'select':
                $options_html = implode("\n", array_map(function($slug, $label) use($value) {
                    return "<option value='$slug' ".selected($slug, $value, false).">".__($label, 'madden-cookie-consent')."</option>";
                }, array_keys($args['options']), $args['options']));
                echo "<select name='$name_attr' id='$id' " . disabled( $disabled, true, false ) . ">$options_html</select>";
                break;

            // TEXTAREA
            case 'textarea':
                echo "<textarea name='$name_attr' rows='5' cols='50' " . disabled( $disabled, true, false ) . ">$value</textarea>";
                break;

            // Default to text input:
            case 'text':
            default:
                echo "<input name='$name_attr' type='text' id='$id' value='$value' placeholder='$placeholder' " . disabled( $disabled, true, false ) . " />";
                break;
        }

        echo "</div>";
   }
}