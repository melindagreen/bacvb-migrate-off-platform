<?php use \MaddenNino\Library\Constants as C; ?>
    
    </main>
    
    <!-- FOOTER -->
    <footer class="footer">
        <!-- BRAND -->
        <div class="footer__brand" href="<?php echo home_url(); ?>">
            <?php the_custom_logo(); ?>
        </div>

        <div class="footer-col">
            <p class="partner-login"><a href="">Partner Login</a></p>
            <div class="search-wrap">
                <?php get_search_form(); ?>
            </div>
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
        
        <div class="footer-col footer-col--logos">
        
        <a href="http://visitflorida.com" target="_blank"><img 
            data-load-type="img"
            data-load-offset="lg" 
            data-load-all="<?php echo get_theme_file_uri().'/assets/images/visitflorida.png';?>" 
            data-load-alt="visitflorida.com" 
            src="<?php echo get_theme_file_uri() ?>/assets/images/pixel.png" 
        /></a>
        <span>
        <a href="https://destinationsinternational.org/" target="_blank"><img 
            data-load-type="img"
            data-load-offset="lg" 
            data-load-all="<?php echo get_theme_file_uri().'/assets/images/dm-logo.png';?>" 
            data-load-alt="Destination International" 
            src="<?php echo get_theme_file_uri() ?>/assets/images/pixel.png" 
        /></a>
        <a href="https://frla.org/" target="_blank"><img 
            data-load-type="img"
            data-load-offset="lg" 
            data-load-all="<?php echo get_theme_file_uri().'/assets/images/frla-logo.png';?>" 
            data-load-alt="Florida Restaurant & Lodging Association" 
            src="<?php echo get_theme_file_uri() ?>/assets/images/pixel.png" 
        /></a>
        </span>
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