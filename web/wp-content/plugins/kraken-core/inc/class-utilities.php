<?php
/**
 * Reusable utility functions intended for use in front-end templates and blocks
 */
namespace MaddenMedia\KrakenCore;

class Utilities {
    /**
     * Notify admin if required plugins are not active.
     */
    public static function to_kebab_case($string) {
		return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
	}
}
