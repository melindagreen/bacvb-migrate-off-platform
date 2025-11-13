<?php /**
 * This file contains static constants used across the theme
 */

namespace MaddenTheme\Library;
class Constants {
    const THEME_PREFIX = "madden-theme";
    const BLOCK_NAME_PREFIX = "[MM] "; // include a space at the end!
    const BLOCK_CLASS = "wp-block-madden-theme";
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
        ],
        'tiktok' => [
            'name' => 'TikTok',
            'url' => '#',
        ],
    ];
}
