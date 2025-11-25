    <?php use \MaddenMadre\Library\Constants as Constants; ?>
    
    </main><!--mainContent-->
    
    <footer class="footer">
        <nav class="footer-menu <?php echo Constants::THEME_PREFIX ?>-menu" role="navigation">
            <?php wp_nav_menu( array(
                "theme_location" => "footer-nav",
                "container" => false,
                "depth" => 1,
                'menu_class' => 'footer-menu__items ' . Constants::THEME_PREFIX . '-menu__items',
            ) ); ?>
        </nav>

        <p class="footer__copyright"><?php _e( "Madden Media", "mmnino" ); ?> &copy; <?php echo date( "Y" ); ?></p>
    </footer>

    <?php wp_footer();
    print_late_styles(); ?>

    <div id="isSmall"></div>
    <div id="isMedium"></div>
    <div id="isLarge"></div>
</body>

</html>