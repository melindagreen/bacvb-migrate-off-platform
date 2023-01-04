<?php use \MaddenNino\Library\Constants as C; ?>

<!doctype html>

<html class="no-js" <?php echo language_attributes(); ?>>

<head>
    <title><?php wp_title( '' ); ?></title>

    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />

    <!-- VIEWPORT FIELDS -->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <!-- PRECONNECT/LOAD/FETCH ORIGINS -->
    <?php foreach( C::PRE_ORIGINS as $origin ) { ?>
        <link rel="<?php echo $origin["rel"] ?>" href="<?php echo $origin["href"]; ?>">
    <?php } ?>

    <!-- FAVICON -->
    <link rel="icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" sizes="32x32"/>
    <link rel="icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" sizes="192x192"/>
    <link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico"/>     

    <!-- Google Tag Manager - COMMENT OUT UNTIL LAUNCH -->
	<!-- <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
    <!-- End Google Tag Manager -->    

    <?php wp_head() // WORDPRESS HEADERS; ?>
</head>

<body <?php echo body_class(); ?>>
    <?php wp_body_open() ?>

    <!-- Google Tag Manager (noscript) 
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PGXRLFD"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    End Google Tag Manager (noscript) -->    

    <!-- HEADER -->
    <header class="header">
        <!-- SKIP TO CONTENT -->
        <a class="skip-to-content-link" href="#main">
            <?php _e( 'Skip to content', 'mmnino' ); ?>
        </a>   

        <!-- HEADER BRAND -->
        <div class="header__brand" href="<?php echo home_url(); ?>">
            <?php the_custom_logo(); ?>
        </div>
       
        <!-- HEADER MENU -->
        <nav class="header-menu <?php echo C::THEME_PREFIX ?>-menu" role="navigation">
            <!-- MOBILE TOGGLE -->
            <button class="mobile-toggle" id="mobile-toggle-main-nav" aria-expanded="false">
                <span class="mobile-toggle__icon"></span>
                <span class="sr-only"><?php _e( 'Toggle Navigation', 'mmnino' ); ?></span>
            </button>

            <!-- MENU ITEMS -->
            <div class="header-menu__contents">
                <?php wp_nav_menu( array(
                    'menu' => 'Main Nav', // menu name
                    'theme_location' => 'main-nav',
                    'container' => false,
                    'menu_class' => 'header-menu__items ' . C::THEME_PREFIX . '-menu__items',
                ) ); ?>

                <img class="search-icon" alt="Search icon" src="<?php echo get_stylesheet_directory_uri() . "/assets/images/icons/search.png"; ?>" >

                <!-- SEARCH FORM -->
                <?php echo get_search_form(); ?>

                <!-- SOCIAL ICONS -->
                <div class="social-wrapper mobile-social">
                    <?php get_template_part( C::TEMPLATE_PARTIALS_PATH . 'social-links', null, array(
                        'links' => C::SOCIAL_LINKS,
                    ) ); ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- MAIN CONTENT -->
    <main id="main" class="main main--<?php global $template; echo basename( $template, ".php" ); ?>">
