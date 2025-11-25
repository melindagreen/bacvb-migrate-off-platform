<?php

/**
 * Constants for Madden Cookie Consent
 */

namespace MaddenBanners\Admin;

use GFAPI;

class AdminConstants
{
    private static function get_default_conditions()
    {
        return [
            [
                'id' => 'matchtype',
                'title' => __('Condition type', 'madden-banners'),
                'type' => 'select',
                'default' => 'is_true',
                'args' => [],
                'options' => [
                    'is_true' => __('Show if', 'madden-banners'),
                    'is_false' => __('Don\'t show if', 'madden-banners'),
                ],
            ],
            [
                'id' => 'condition_field',
                'title' => __('Condition field', 'maden-banners'),
                'type' => 'select',
                'default' => 'page_url',
                'options' => [
                    'page_url' => __('Page URL', 'madden-banners'),
                    'url_parameters' => __('URL Parameters', 'madden-banners'),
                    'device_type' => __('Device Type', 'madden-banners'),
                    'is_repeat_user' => __('Is repeat user', 'madden-banners'),
                    'survey_value' => __('Persona survey has value', 'madden-banners'),
                ],
            ],
            [
                'id' => 'condition_match',
                'title' => __('Condition type', 'maden-banners'),
                'type' => 'select',
                'default' => 'match_exactly',
                'show_if' => [
                    'field' => 'condition_field',
                    'match' => '==',
                    'value' => 'page_url'
                ],
                'options' => [
                    'match_exactly' => __('Matches exactly', 'madden-banners'),
                    'contains' => __('Contains', 'madden-banners'),
                    'regex' => __('Regex match', 'madden-banners'),
                ],
            ],
            [
                'id' => 'condition_value',
                'title' => __('Condition value', 'madden-banners'),
                'type' => 'text',
                'default' => '',
                'show_if' => [
                    'field' => 'condition_field',
                    'match' => '==',
                    'value' => 'page_url'
                ],
            ],
            // todo rather than have dupes here, should be able to pass array of show_ifs
            [
                'id' => 'parameter_match',
                'title' => __('Condition type', 'maden-banners'),
                'type' => 'select',
                'default' => 'match_exactly',
                'show_if' => [
                    'field' => 'condition_field',
                    'match' => '==',
                    'value' => 'url_parameters'
                ],
                'options' => [
                    'match_exactly' => __('Matches exactly', 'madden-banners'),
                    'contains' => __('Contains', 'madden-banners'),
                    'regex' => __('Regex match', 'madden-banners'),
                ],
            ],
            [
                'id' => 'parameter_value',
                'title' => __('Condition value', 'madden-banners'),
                'type' => 'text',
                'default' => '',
                'show_if' => [
                    'field' => 'condition_field',
                    'match' => '==',
                    'value' => 'url_parameters'
                ],
            ],
            [
                'id' => 'device_type',
                'title' => __('Device type', 'madden-banners'),
                'type' => 'select',
                'default' => 'desktop',
                'show_if' => [
                    'field' => 'condition_field',
                    'match' => '==',
                    'value' => 'device_type'
                ],
                'options' => [
                    'desktop_any' => __('Desktop computer', 'madden-banners'),
                    'tablet_any' => __('Tablet', 'madden-banners'),
                    'mobile_any' => __('Any mobile device', 'madden-banners'),
                    'mobile_android' => __('Android mobile device', 'madden-banners'),
                    'mobile_ios' => __('iOS mobile device', 'madden-banners'),
                ],
            ],
            [
                'id' => 'survey_value',
                'title' => __('Survey value', 'madden-banners'),
                'type' => 'select',
                'default' => 'desktop',
                'show_if' => [
                    'field' => 'condition_field',
                    'match' => '==',
                    'value' => 'survey_value'
                ],
                'options' => [
                    'arts-culture' => 'Arts & Culture',
                    'dining' => 'Dining',
                    'family-fun' => 'Family Fun',
                    'local-events' => 'Local Events',
                    'music' => 'Music',
                    'neighborhoods' => 'Neighborhoods',
                    'nightlife' => 'Nightlife',
                    'outdoor-activities' => 'Outdoor Activities',
                    'sports' => 'Sports',
                ],
            ],
        ];
    }

    public static function get_settings_fields()
    {
        $forms = class_exists('GFAPI') ? GFAPI::get_forms() : [];
        $array = [];
        foreach ($forms as $value) {
            $array[$value['id']] =  __($value['title'], 'madden-banners');
        }
        return [
            [
                'id' => 'tickers',
                'title' => __('Tickers', 'madden-banners'),
                'description' => __('Tickers to display at the top of each page', 'madden-banners'),
                'fields' => [
                    [
                        'id' => 'all_tickers',
                        'title' => __('All tickers', 'madden-banners'),
                        'args' => [
                            'default' => [],
                            'type' => 'repeater',
                            'noun' => 'ticker',
                            "sub_fields" => [
                                [
                                    'id' => 'id_slug',
                                    'title' => __('Identifying slug', 'madden-banners'),
                                    'type' => 'text',
                                    'tooltip' => '<p>' . __('A unique slug for use in reporting to identify this ticker', 'madden-banners') . '</p>',
                                ],
                                [
                                    'id' => 'content',
                                    'title' => __('Ticker content', 'madden-banners'),
                                    'type' => 'textarea',
                                    'default' => '<p>This is a ticker with some <strong>HTML Content</strong>.</p>',
                                    'tooltip' => "<p>" . __('Enter HTML content to display within your banner. Not all tags are permitted.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'link',
                                    'title' => __('Ticker link', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => '',
                                    'tooltip' => "<p>" . __('Where should the ticker link to?', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'cta',
                                    'title' => __('Ticker CTA', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => false,
                                    'tooltip' => "<p>" . __('Show the link as a button rather than wrapping content by adding button text here.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'image',
                                    'title' => __('Ticker image URL', 'madden-banners'),
                                    'type' => 'url',
                                    'default' => false,
                                    'tooltip' => "<p>" . __('Add a small image to the ticker. Will render at 50x50 px.', 'madden-banners') . "</p>",
                                    'placeholder' => '',
                                ],
                                [
                                    'id' => 'exit_pos',
                                    'title' => __('Position of exit button'),
                                    'type' => 'select',
                                    'default' => 'right',
                                    'options' => [
                                        'right' => __('Right', 'madden-banners'),
                                        'left' => __('Left', 'madden-banners'),
                                    ],
                                    'tooltip' => "<p>" . __('Show the close button on the left- or right-hand side.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'bg_color',
                                    'title' => __('Background color', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => '#000000',
                                ],
                                [
                                    'id' => 'text_color',
                                    'title' => __('Text color', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => '#FFFFFF',
                                ],
                                [
                                    'id' => 'frequency',
                                    'title' => __('Frequency', 'madden-banners'),
                                    'type' => 'select',
                                    'default' => 'no_limit',
                                    'options' => [
                                        'no_limit' => __('No limits', 'madden-banners'),
                                        'daily' => __('Once a day', 'madden-banners'),
                                        'weekly' => __('Once a week', 'madden-banners'),
                                        'only_once' => __('Once per visitor', 'madden-banners'),
                                    ],
                                    'tooltip' => "<p>" . __('How often to show the ticker.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'priority',
                                    'title' => __('Ticker priority', 'madden-banners'),
                                    'type' => 'number',
                                    'default' => 10,
                                    'tooltip' => "<p>" . __('Lower numbers will take priority over higher numbers', 'madden-banners') . "</p>",
                                    'min' => 0,
                                    'max' => 10,
                                ],
                                [
                                    'id' => 'conditions',
                                    "title" => __('Ticker display conditions', 'madden-banners'),
                                    'type' => 'conditional',
                                    'default' => [],
                                    'noun' => 'condition',
                                    'default' => [],
                                    "sub_fields" => self::get_default_conditions(),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'flyins',
                'title' => __('Fly-ins', 'madden-banners'),
                'description' => __('Fly-ins that appear over content', 'madden-banners'),
                'fields' => [
                    [
                        'id' => 'all_flyins',
                        'title' => __('All fly-ins', 'madden-banners'),
                        'args' => [
                            'default' => [],
                            'type' => 'repeater',
                            'noun' => 'fly-in',
                            "sub_fields" => [
                                [
                                    'id' => 'id_slug',
                                    'title' => __('Identifying slug', 'madden-banners'),
                                    'type' => 'text',
                                    'tooltip' => '<p>' . __('A unique slug for use in reporting to identify this fly-in', 'madden-banners') . '</p>',
                                ],
                                [
                                    'id' => 'title',
                                    'title' => __('Fly-in title', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => 'Fly-in Title',
                                ],
                                [
                                    'id' => 'content',
                                    'title' => __('Fly-in content', 'madden-banners'),
                                    'type' => 'textarea',
                                    'default' => 'Fly-in content',
                                ],
                                [
                                    'id' => 'bg_image',
                                    'title' => __('Background image', 'madden-banners'),
                                    'type' => 'url',
                                    'default' => '',
                                    'placeholder' => 'leave blank for no Image',
                                ],
                                [
                                    'id' => 'cta_text',
                                    'title' => __('CTA text', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => '',
                                    'show_if' => [
                                        'field' => 'template',
                                        'match' => '==',
                                        'value' => 'none'
                                    ],
                                ],
                                [
                                    'id' => 'cta_link',
                                    'title' => __('CTA link', 'madden-banners'),
                                    'type' => 'text',
                                    'default' => '',
                                    'show_if' => [
                                        'field' => 'template',
                                        'match' => '==',
                                        'value' => 'none'
                                    ],
                                ],
                                [
                                    'id' => 'corner',
                                    'title' => __('Fly to corner', 'madden-banners'),
                                    'type' => 'select',
                                    'default' => 'bottom_right',
                                    'options' => [
                                        'top_left' => __('Top left', 'madden-banners'),
                                        'top_right' => __('Top right', 'madden-banners'),
                                        'bottom_left' => __('Bottom left', 'madden-banners'),
                                        'bottom_right' => __('Bottom right', 'madden-banners'),
                                    ]
                                ],
                                [
                                    'id' => 'scroll_depth',
                                    'title' => __('Show on scroll depth (px)', 'madden-banners'),
                                    'type' => 'number',
                                    'default' => 0,
                                    'min' => 0,
                                    'max' => 10000,
                                    'step' => 1,
                                    'tooltip' => "<p>" . __('Don\'t show the fly-in until user has scrolled this far.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'time_passed',
                                    'title' => __('Show after delay (seconds)', 'madden-banners'),
                                    'type' => 'number',
                                    'default' => 0,
                                    'min' => 0,
                                    'max' => 100,
                                    'step' => 1,
                                    'tooltip' => "<p>" . __('Don\'t show the fly-in until after a delay.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'template',
                                    'title' => __('Fly-in template', 'madden-banners'),
                                    'type' => 'select',
                                    'default' => 'cta',
                                    'options' => [
                                        'cta' => __('Call To Action', 'madden-banners'),
                                        // 'mail_signup' => __('eNews sign-up', 'madden-banners'),
                                        'survey' => __('Form', 'madden-banners'),
                                    ],
                                    'tooltip' => "<p>" . __('Additional content to show with the fly-in.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'form',
                                    'title' => __('Gravity Form', 'madden-banners'),
                                    'type' => 'select',
                                    'default' => '2',
                                    'options' => $array,
                                    'tooltip' => "<p>" . __('Additional content to show with the fly-in.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'frequency',
                                    'title' => __('Frequency', 'madden-banners'),
                                    'type' => 'select',
                                    'default' => 'no_limit',
                                    'options' => [
                                        'no_limit' => __('No limits', 'madden-banners'),
                                        'daily' => __('Once a day', 'madden-banners'),
                                        'weekly' => __('Once a week', 'madden-banners'),
                                        'only_once' => __('Once per visitor', 'madden-banners'),
                                    ],
                                    'tooltip' => "<p>" . __('How often to show the fly-in.', 'madden-banners') . "</p>",
                                ],
                                [
                                    'id' => 'priority',
                                    'title' => __('Fly-in priority', 'madden-banners'),
                                    'type' => 'number',
                                    'default' => 10,
                                    'tooltip' => "<p>" . __('Lower numbers will take priority over higher numbers', 'madden-banners') . "</p>",
                                    'min' => 0,
                                    'max' => 10,
                                ],
                                [
                                    'id' => 'conditions',
                                    "title" => __('Ticker display conditions', 'madden-banners'),
                                    'type' => 'conditional',
                                    'default' => [],
                                    'noun' => 'condition',
                                    'default' => [],
                                    "sub_fields" => self::get_default_conditions(),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }
}
