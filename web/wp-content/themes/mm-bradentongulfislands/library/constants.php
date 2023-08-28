<?php /**
 * This file contains static constants used across the theme
 */

namespace MaddenNino\Library;
class Constants {
    const THEME_PREFIX = "mm-bradentongulfislands";

    const BLOCK_NAME_PREFIX = "[MM] "; // include a space at the end!

    const TEMPLATE_PARTIALS_PATH = "template-parts/";

    const BLOCK_CLASS = "wp-block-mm-bradentongulfislands";

    const TEMPLATE_STATIC_BLOCKS = array();

    const PRE_ORIGINS = array(
        array(
            "rel" => "preconnect",
            "href" => "https://fonts.gstatic.com/",
        ),
    );

    const SOCIAL_LINKS = [
        'facebook' => [
            'name' => 'Facebook',
            'url' => '#',
        ],
        'pinterest' => [
            'name' => 'Pinterest',
            'url' => '#',
        ],
        'twitter' => [
            'name' => 'Twitter',
            'url' => '#',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'url' => '#',
        ]
    ];
}
