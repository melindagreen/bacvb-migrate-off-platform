<?php

namespace Eventastic\Templates;

/**
 * Main settings page
 *
 * Handy reference for custom fields: https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */

use Eventastic\Eventastic as Eventastic;
use Eventastic\Library\Constants as Constants;

// nonce
$nonceAction = "update-settings";
$nonceField = Constants::NONCE_ROOT."settings";

//
// stdout
//
$pageTitle = Constants::PLUGIN_NAME_PLURAL;
include_once(plugin_dir_path( __FILE__ ).'../inc/header.php');
?>

<form method="POST" action="options.php" id='eventastic_settings_form'>
	<?php
	wp_nonce_field($nonceAction, $nonceField);
	settings_fields(Constants::PLUGIN_NAME_PLURAL);
	do_settings_sections(Constants::PLUGIN_NAME_PLURAL);
	submit_button();
	?>
</form>

<h2 class="eventastic">Plugin information</h2>
<p class="eventasticKeyValueSet">
	<?php
	$pluginInfo = Eventastic::get_plugin_info();
	echo "<b>Version:</b> {$pluginInfo["Version"]}<br/>"; 
	echo "<b>Author:</b> {$pluginInfo["Author"]}<br/>"; 
	?>
	<img style="height:200px" src="<?php echo plugin_dir_url( __FILE__ ).'../images/mascot.gif' ?>" alt="Awwwwwwwwwwww" />
</p>

<?php
include_once(plugin_dir_path( __FILE__ ).'../inc/footer.php');
?>
