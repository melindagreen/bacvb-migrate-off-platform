/*** IMPORTS *******************************************************************/

import { getIsSmall, getIsLarge } from './inc/utilities';
import './library/madden-parallax-layout-v1.3-min';
import './library/madden-lazy-load-v1.5-min';
import '../styles/style.scss';

/*** SERVICE WORKER ************************************************************/

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then((reg) => {
            // registration worked
            console.log('Registration succeeded. Scope is ' + reg.scope);
        }).catch((error) => {
            // registration failed
            console.log('Registration failed with ' + error);
        });
}

(function ($) {
    /*** GLOBAL VARS *****************************************************************/

    let _lazyLoadObject;

    /*** FUNCTIONS *****************************************************************/

    /**
     * Toggle the mobile menu
     * @returns {null}
     */
    function toggleMobileNav() {
        $('.header-menu__contents').toggleClass('header-menu__contents--open');
        $(this).toggleClass('mobile-toggle--open');
        $('body').toggleClass('menu-open');

        if ($(this).hasClass('mobile-toggle--open')) $(this).attr('aria-expanded', true);
        else $(this).attr('aria-expanded', false);
    }

    // Toggle mobile sub-meu
    $('#menu-menu-1 .menu-item-has-children').click(function () {
        $(this).toggleClass('menu-item--open');
        $(this).find('.sub-menu').toggleClass('sub-menu--open');
    });

    // toggle search
    $('.search-icon').click(function () {
        $('.search-form').toggleClass('search-form--open');
    });

    /*** THEME FRAMEWORK FUNCTIONS *************************************************/

    /**
     * Fires on initial document load
     */
    function themeOnLoad() {
        // Toggle mobile menu
        $('#mobile-toggle-main-nav').click(toggleMobileNav);
    }

    /**
     * Fires on load and scroll
     */
    function themeOnScroll() {

    }

    /**
     * Fires on load and window resize
     */
    function themeOnResize() {
        // Lazy load
        if ($('*[data-load-type]').length) {
            _lazyLoadObject = MMLazyLoad.init({ loadElements: document.querySelectorAll("[data-load-type]") });
        }

        if (getIsSmall()) {
            // Toggle mobile sub-meu
            $('#menu-main-menu .menu-item-has-children').click(function () {
                $(this).toggleClass('menu-item--open');
                $(this).find('.sub-menu').toggleClass('sub-menu--open');
            });
        }

        // parallax
        const parallaxEls = $('.has-parallax');
        if (parallaxEls.length && getIsLarge()) {
            parallaxEls.each((i, el) => {
                $(el).parallaxBG({
                    adjustY: 0.12,
                    bgXPosition: 'center',
                    bgYPosition: 'center',
                });
            });
        }
    }

    $(document).ready(function ($) {
        /*** EVENT LISTENERS **************************************************************/
        themeOnLoad();

        themeOnScroll();
        $(window).scroll(themeOnScroll);

        themeOnResize();
        $(window).resize(themeOnResize);
    });
})(jQuery);
