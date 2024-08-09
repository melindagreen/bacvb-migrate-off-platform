<?php /**
 * This file contains static constants used across the theme
 */

namespace MaddenNino\Library;
class Constants {
    const THEME_PREFIX = "mm-bradentongulfislands";

    const TIME_ZONE = "America/New_York";

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
            'url' => 'https://www.facebook.com/VisitBradentonGulfIslands',
        ],
        'pinterest' => [
            'name' => 'Pinterest',
            'url' => 'https://www.pinterest.com/visitbradenton/',
        ],
        'twitter' => [
            'name' => 'Twitter',
            'url' => 'https://twitter.com/VisitBradenton',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'url' => 'https://www.instagram.com/visitbradentongulfislands',
        ]
    ];
}
