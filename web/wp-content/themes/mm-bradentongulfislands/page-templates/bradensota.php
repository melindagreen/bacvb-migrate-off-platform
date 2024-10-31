<?php
/*
 Template Name: Bradensota
*/
?>

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

    <!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-MZ8H2VT');</script>
    <!-- End Google Tag Manager -->    

    <?php wp_head() // WORDPRESS HEADERS; ?>
</head>

<body <?php echo body_class(); ?>>
    <?php wp_body_open() ?>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZ8H2VT"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->    

    <!-- HEADER -->
    <header class="bradensota-header">
        <img class="bradensota-header__logo" src="/wp-content/themes/mm-bradentongulfislands/assets/images/Sara-Brad-logo-top.png" alt="Sarasota Bradenton Logo">
    </header>

    <!-- MAIN CONTENT -->
    <main id="main" class="main main--<?php global $template; echo basename( $template, ".php" ); ?>">

    <?php if ( have_posts() ) {
	while ( have_posts() ) {
		the_post(); 

		the_content();
	}
} ?>
    </main>
    
    <!-- FOOTER -->
    <footer class="bradensota-footer">

    </footer>

    <?php wp_footer();
    print_late_styles(); ?>

    <!-- SIZE ELEMENTS (for viewport utilities) -->
    <div id="isSmall"></div>
    <div id="isMedium"></div>
    <div id="isLarge"></div>
</body>

</html>