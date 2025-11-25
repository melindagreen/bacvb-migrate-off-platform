<?php /**
 * Constants for Madden Cookie Consent
 */

namespace PartnerPortal\Admin;

class AdminConstants {
    public static function get_settings_fields() {
        return [
            [
                'id' => 'example_section',
                'title' => __('Example Section', 'madden-plugin'),
                'description' => __('This is an example of a settings section', 'madden-plugin'),
                'fields' => [
                    [
                        'id' => 'example-field-select',
                        'title' => __('Example Field - Select', 'madden-plugin'),
                        'args' => [
                            'type' => 'select',
                            'default' => 'option1',
                            'options' => [
                                'option1' => __('Option 1', 'madden-plugin'), 
                                'option2' => __('Option 2', 'madden-plugin'), 
                                'option3' => __('Option 3', 'madden-plugin'), 
                            ]
                        ]
                    ],
                ],
            ]
        ];
    }
}