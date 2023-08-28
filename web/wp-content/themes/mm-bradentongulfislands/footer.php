<?php use \MaddenNino\Library\Constants as C; ?>
    
    </main>
    
    <!-- FOOTER -->
    <footer class="footer">
        <!-- BRAND -->
        <div class="footer__brand" href="<?php echo home_url(); ?>">
            <?php the_custom_logo(); ?>
        </div>

        <div class="footer-col">
            <p>Partner Login</p>
        </div>

        <div class="footer-col">
            <nav class="footer-menu <?php echo C::THEME_PREFIX ?>-menu" role="navigation">
                <?php wp_nav_menu( array(
                    'menu' => 'Footer One', // menu name
                    "theme_location" => "footer-one",
                    "container" => false,
                    "depth" => 1,
                    'menu_class' => 'footer-menu__items ' . C::THEME_PREFIX . '-menu__items',
                ) ) ; ?>
                <?php wp_nav_menu( array(
                    'menu' => 'Footer Two', // menu name
                    "theme_location" => "footer-two",
                    "container" => false,
                    "depth" => 1,
                    'menu_class' => 'footer-menu__items ' . C::THEME_PREFIX . '-menu__items',
                ) ) ; ?>
                <?php wp_nav_menu( array(
                    'menu' => 'Footer Three', // menu name
                    "theme_location" => "footer-three",
                    "container" => false,
                    "depth" => 1,
                    'menu_class' => 'footer-menu__items ' . C::THEME_PREFIX . '-menu__items',
                ) ) ; ?>
            </nav>
            <!-- SOCIAL ICONS -->
            <div class="social-container">
                <?php get_template_part( C::TEMPLATE_PARTIALS_PATH . 'social-links', null, array(
                    'links' => C::SOCIAL_LINKS,
                ) ); ?>
            </div>
        </div>
        
        <div class="footer-col">
            <p>Partner Login</p>
        </div>
        
        <p class="footer__copyright"><?php _e( '©2023 Bradenton Area Convention and Visitor’s Bureau in Bradenton, Florida | All Rights Reserved', 'mmnino' ); ?> &copy; <?php echo date( 'Y' ); ?></p>
    </footer>

    <?php wp_footer();
    print_late_styles(); ?>

    <!-- SIZE ELEMENTS (for viewport utilities) -->
    <div id="isSmall"></div>
    <div id="isMedium"></div>
    <div id="isLarge"></div>
</body>

</html>