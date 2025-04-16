<?php use \MaddenNino\Library\Constants as C; ?>

    </main>
    
    <!-- FOOTER -->
    <footer class="footer">
        <!-- BRAND -->
        <div class="footer__brand" href="<?php echo home_url(); ?>">
            <?php the_custom_logo(); ?>
        </div>

        <div class="footer-col search-login">
            <p class="partner-login"><a href="/account">Partner Login</a></p>
            <div class="search-wrap">
            <?php get_search_form(array(
                'echo' => true,
                'aria_label' => 'footer_search',
                'search_color' => 'teal'
            )); ?>
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
        
        <a href="https://visitflorida.com" target="_blank"><img 
            data-load-type="img"
            data-load-offset="lg" 
            data-load-all="<?php echo get_theme_file_uri().'/assets/images/VF_Logo2019_Primary_Teal.png';?>" 
            data-load-alt="visitflorida.com" 
            src="<?php echo get_theme_file_uri() ?>/assets/images/pixel.png" 
        /></a>
        <span>
        <a href="https://destinationsinternational.org/" target="_blank"><img 
            data-load-type="img"
            data-load-offset="lg" 
            data-load-all="<?php echo get_theme_file_uri().'/assets/images/icon-DMO-teal.svg';?>" 
            data-load-alt="Destination International" 
            src="<?php echo get_theme_file_uri() ?>/assets/images/pixel.png" 
        /></a>
        <a href="https://frla.org/" target="_blank"><img 
            data-load-type="img"
            data-load-offset="lg" 
            data-load-all="<?php echo get_theme_file_uri().'/assets/images/icon-florida-teal.svg';?>" 
            data-load-alt="Florida Restaurant & Lodging Association" 
            src="<?php echo get_theme_file_uri() ?>/assets/images/pixel.png" 
        /></a>
        </span>
        </div>
        
        <p class="footer__copyright">&copy;<?php echo date( 'Y' ); ?><?php _e( ' Bradenton Area Convention and Visitors Bureau in Bradenton, Florida | All Rights Reserved', 'mmnino' ); ?> </p>
    </footer>

    <?php wp_footer();
    print_late_styles(); ?>

    <!-- Stay Connected  -->
    <div class="stay-connected">
        <div class="stay-connected__links">
            <a href="/travel-guide"><button class="stay-connected-guide">Get Your <br> Destination Guide</button></a>
            <a href="/enews"><button class="stay-connected-subscribe">Subscribe To <br> Our Newsletter</button></a>
        </div>
        <button class="stay-connected__toggle">Stay Connected</button>
    </div>

    <!-- SIZE ELEMENTS (for viewport utilities) -->
    <div id="isSmall"></div>
    <div id="isMedium"></div>
    <div id="isLarge"></div>
</body>

</html>