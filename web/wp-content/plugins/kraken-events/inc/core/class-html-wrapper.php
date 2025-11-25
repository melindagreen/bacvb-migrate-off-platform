<?php
namespace MaddenMedia\KrakenEvents;

use WP_Query;

class EventHTMLWrapper {

    public static function init() {
    }
    public static function partner_html_wrapper_start($class) {
        return '<section class="kraken-events-partner-portal '.$class.'">';
    }

    public static function partner_html_wrapper_end() {
        return '</section>';
    }
}
