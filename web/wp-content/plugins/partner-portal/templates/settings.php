<?php

namespace PartnerPortal\Templates;

/**
 * Main settings page
 *
 * Handy reference for custom fields: https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

use PartnerPortal\PartnerPortal as PartnerPortal;
use PartnerPortal\Library\Constants as Constants;

// nonce
$nonceAction = "update-settings";
$nonceField = Constants::NONCE_ROOT."settings";

//
// stdout
//
$PartnerPortal = new PartnerPortal();
$vars = json_decode(json_encode($PartnerPortal->variables));
$pageTitle = $vars->cpt_plural;
?>

<form method="POST" action="options.php">
    <?php
        wp_nonce_field($nonceAction, $nonceField);
        settings_fields($vars->cpt_plural);
        do_settings_sections($vars->cpt_plural);
    ?>
    
    <?php
    submit_button('Save Settings');
    ?>
</form>

<h2 class="PartnerPortal">Plugin information</h2>