<?php use \MaddenNino\Library\Constants as C; ?>
    
    </main>
    
    <!-- FOOTER -->
    <footer class="footer">
        <!-- BRAND -->
        <div class="footer__brand" href="<?php echo home_url(); ?>">
            <?php the_custom_logo(); ?>
        </div>

        <nav class="footer-menu <?php echo C::THEME_PREFIX ?>-menu" role="navigation">
            <?php wp_nav_menu( array(
                'menu' => 'Footer Nav', // menu name
                "theme_location" => "footer-nav",
                "container" => false,
                "depth" => 1,
                'menu_class' => 'footer-menu__items ' . C::THEME_PREFIX . '-menu__items',
            ) ) ; ?>
        </nav>

        <p class="footer__copyright"><?php _e( 'Madden Media', 'mmnino' ); ?> &copy; <?php echo date( 'Y' ); ?></p>
        
        <!-- SOCIAL ICONS -->
        <div class="social-wrapper">
            <?php get_template_part( C::TEMPLATE_PARTIALS_PATH . 'social-links', null, array(
                'links' => C::SOCIAL_LINKS,
            ) ); ?>
        </div>
    </footer>

    <?php wp_footer();
    print_late_styles(); ?>

    <!-- SIZE ELEMENTS (for viewport utilities) -->
    <div id="isSmall"></div>
    <div id="isMedium"></div>
    <div id="isLarge"></div>
</body>

</html>