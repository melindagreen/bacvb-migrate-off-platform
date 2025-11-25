<?php

namespace PartnerPortal\Admin;

/**
 * Import Partners Class
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 *
 * Copyright (c) 2020 Madden Media
 */

require_once(__DIR__.'/../library/Utilities.php');
require_once( __DIR__.'/../library/Constants.php' );
use PartnerPortal\Library\Constants as Constants;
use PartnerPortal\Library\Utilities as Utilities;

class ImportPartners {
    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    public function __construct() {

        $this->templates = array();
    }
}
