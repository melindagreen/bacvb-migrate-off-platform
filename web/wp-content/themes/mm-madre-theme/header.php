<!doctype html>

<html class="no-js" <?php echo language_attributes(); ?>>

<head>
    <?php echo wp_head() // WORDPRESS HEADERS; ?>

    <title><?php wp_title( '' ); ?></title>

    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />

    <!-- VIEWPORT FIELDS -->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <!-- PRECONNECT/LOAD/FETCH ORIGINS -->
    <?php use \MaddenMadre\Library\Constants as Constants; ?>
    <?php foreach( Constants::PRE_ORIGINS as $origin ) { ?>
        <link rel="<?php echo $origin["rel"] ?>" href="<?php echo $origin["href"]; ?>">
    <?php } ?>
</head>

<body <?php echo body_class(); ?>>
    <?php wp_body_open() ?>

    <header class="header">
        <a class="header__brand" href="<?php echo home_url(); ?>">
            <span class="sr-only"><?php echo bloginfo('name'); ?> homepage</span>
            <?php echo file_get_contents(get_template_directory() . "/assets/images/logo.svg"); ?>
        </a>
       
        <nav class="header-menu <?php echo Constants::THEME_PREFIX ?>-menu" role="navigation">
            <button class="mobile-toggle" id="mobile-toggle-main-nav" aria-expanded="false">
                <span class="mobile-toggle__icon"></span>
                <span class="sr-only"><?php _e( 'Toggle Navigation', 'mmnino' ); ?></span>
            </button>

            <div class="header-menu__contents">
                <?php wp_nav_menu( array(
                    'theme_location' => 'main-nav',
                    'container' => false,
                    'menu_class' => 'header-menu__items ' . Constants::THEME_PREFIX . '-menu__items',
                ) ); ?>

                <?php echo get_search_form(); ?>
            </div>
        </nav>
    </header>

    <main class="main main--<?php global $template; echo basename($template, ".php"); ?>">