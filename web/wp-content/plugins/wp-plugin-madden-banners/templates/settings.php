<?php

namespace MaddenBanners\Templates;

/**
 * Template for rendering admin settings page for Madden Cookie Consent
 */

// Include library files this file requires
require_once(__DIR__.'/../library/Constants.php');

// Rename imports
use MaddenBanners\Library\Constants as Constants;


// Forbid users without sufficient permissions:
if ( !current_user_can( 'edit_posts' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

$prefix = Constants::PLUGIN_PREFIX;

// Create form:
?><section class='<?php echo $prefix ?>-options-form-wrap'>
    <!-- INTRO -->
    <div class="<?php echo $prefix ?>-intro">
        <div class="<?php echo $prefix ?>-title">
            <img src="<?PHP echo  WP_PLUGIN_URL.'/'.Constants::PLUGIN_DIR_NAME; ?>/assets/images/banner.png" /> 
            <h1><?php _e(Constants::PLUGIN_ADMIN_PAGE_TITLE, 'madden-banners'); ?></h1>
            <img src="<?PHP echo  WP_PLUGIN_URL.'/'.Constants::PLUGIN_DIR_NAME; ?>/assets/images/banner.png" /> 
        </div>
        
        <p><?php _e('Use this page to configure fly-ins, banners, and pop-ups for your site.', 'madden-banners'); ?></p>
    </div>
    
    <!-- OPTIONS FORM -->
    <form method="POST" action="options.php" id="<?php echo Constants::PLUGIN_PREFIX; ?>-options-form">
        <div id="<?php echo $prefix ?>-loader"></div>
        <?php 
            wp_nonce_field();

            settings_fields( Constants::PLUGIN_SETTING_GROUP_SLUG );

            do_settings_sections( Constants::PLUGIN_ADMIN_MENU_SLUG );

            ?><div class="<?php echo $prefix ?>-button-wrap">
                <button type='button' id="<?php echo $prefix ?>-reset" class="button">Reset Default Settings</button>
                <?php submit_button( 'Save Changes', 'primary', 'submit', false ); ?>
            <div><?php
        ?>
    </form>
</section>