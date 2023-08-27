/*** IMPORTS *******************************************************************/

import { getIsSmall, getIsLarge } from './inc/utilities';
import { THEME_PREFIX } from './inc/constants';
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
    /*Main Toggle Menu Function*/
	function toggleMenu() {
		
		$(`.${THEME_PREFIX}-header`).toggleClass('open');
		toggleTopBarClasses();
		//If menu is now open...
		if ($('.bradenton-header').hasClass('open')) {
			$('.toggle__label').text('Close');
			if ($('.search-form').hasClass('open')) $('.search-form input').focus();
		} else {
			$('.toggle__label').text('Menu');
			searchClose();
		}


		$('body').toggleClass('menu-open');
	};

    /**
	 * Toggle open the mobile nav menu
	 */
	function toggleMobileMenu() {
		$('body').toggleClass('mobile-menu-open');
		$('.header-mobile-wrap').toggleClass('show');
		$('.header-mobile-icon').toggleClass('open');
	}

    // toggle search
    $('.search-icon').click(function () {
        $('.search-form').toggleClass('search-form--open');
    });

    /**Toggle Search Functions ***/
	function searchOpen(element) {
		//Add searchopen class to header
		$('.bradenton-header').addClass('searchopen');

		//Open menu on mobile
		if (!$('.bradenton-header').hasClass('open')) toggleMenu();

		//Hide shortcuts when menu is open (they're not visible but still take up width in layout)
		if ($('.bradenton-header').hasClass('open')) {
			$('.nav__shortcuts > ul').hide();
			//Temporarily hide shortcuts on tablet
		} else if (getIsMedium()) {
			$('.nav__shortcuts > ul').hide();
		}
		var thisform = $(element).closest('.search-form');
		thisform.addClass('open');
		thisform.find('input').first().focus();
	}
    function searchClose(element) {
		$('.bradenton-header').removeClass('searchopen');

		var thisform = (element) ? $(element).closest('.search-form') : $('.search-form');
		thisform.removeClass('open');
		//Unhide shortcuts
		setTimeout(function () { $('.nav__shortcuts > ul').show(); }, 250);

	}

    function searchToggle(element) {
		var thisform = $(element).closest('.search-form');
		if (thisform.hasClass('open')) {
			searchClose(element);
		} else {
			searchOpen(element);
		}
	}

    /**
	 * Switch in the transparent nav class when we're over the hero or if the megamenu is open
	 */
	function toggleTopBarClasses() {
		const scrollTop = $(window).scrollTop();
		const fullHero = $('#bradenton-hero.full');
		// if (
		// 	(
		// 		fullHero.length &&
		// 		scrollTop < fullHero.offset().top + fullHero.outerHeight()
		// 	) ||
		// 	$('.bradenton-header').hasClass('open')
		// ) {
		// 	$('.bradenton-header .top-bar').removeClass('top-bar--solid').addClass('top-bar--transparent');
		// } else {
		// 	$('.bradenton-header .top-bar').removeClass('top-bar--transparent').addClass('top-bar--solid');
		// }
	}

    /*** THEME FRAMEWORK FUNCTIONS *************************************************/

    /**
	 * Fires on initial document load
	 */
	function themeOnLoad() {
		
		// Toggle menu
		$(`.${THEME_PREFIX}-header .toggle`).click(function () {
			toggleMenu();
		});

		$('.header-mobile-toggle').click(toggleMobileMenu);

        // Toggle submenu
		if (getIsLarge()) $('.mega-menu__menu > .menu-item-has-children:first').addClass('open');
		$('.mega-menu__menu > .menu-item-has-children').click(function () {
			var isOpen = $(this).hasClass('open')
			$('.mega-menu__menu > .menu-item-has-children').removeClass('open');
			if (!isOpen) $(this).addClass('open');
		});
		$('.sub-menu > .menu-item-has-children').click(function (e) {
			e.stopPropagation();
			var isOpen = $(this).hasClass('open')
			$('.sub-menu > .menu-item-has-children').removeClass('open');
			if (!isOpen) $(this).addClass('open');
		});

		/*Close search on window resize*/
		window.onresize = function () { searchClose(); }

		/*Close search on escape-key*/
		//We have to wait until google loads the input field. This is inelegant but adding input handler to .on() doesn't work in detecting escape key
		setTimeout(function () {
			$('.search-form input').on('keydown', function (event) {
				if (event.key == "Escape") {
					searchClose();
				}
			})
		}, 1000);

		//Toggle on click.
		$('.search-form__toggle').click(function () {
			searchToggle(this);
		});

		//Programatically add placeholder
		setTimeout(function () {
			$('.search-form input').attr('placeholder', 'TYPE TO SEARCH');
		}, 1000);

		// Submit international redirect on select change
		$('#bradenton-international-redirect').change(function () {
			$(this).submit();
		});

		// Fade in each child of els w/ fade in children utility class
		$('.fade-in-children').each(function (index, el) {
			let delay = $(el).hasClass('fast') ? 0.1 : 0.25;

			$(el)
				.children()
				.each(function (childIndex, childEl) {
					$(childEl).css(
						'transition-delay',
						`${(childIndex + 1) * delay}s`
					);
				});
		});

		// Add new tab target to external links
		// openLinksNewTab();

		// Custom conditional logic for sumbit event form
		// TODO seperate out somewhere more fitting?
		if (typeof gform !== 'undefined' && $('#gform_5').length) {
			// autopopulate times
			$('.gfield_time_hour input').each((i, elem) => {
				if (!$(elem).val()) $(elem).val('12');
			});
			$('.gfield_time_minute input').each((i, elem) => {
				if (!$(elem).val()) $(elem).val('0');
			});
			$('.gfield_time_ampm select').each((i, elem) => {
				if (!$(elem).val()) $(elem).val('am');
			});

			// fix repeater indices
			$('#field_5_250 .gfield_repeater_item').each(
				(repeatIndex, repeatItem) => {
					$(repeatItem)
						.find(':input:not(button):not(.gform_hidden)')
						.each((fieldIndex, fieldItem) => {
							let name = $(fieldItem).attr('name');
							if (name.includes('[]'))
								name = name.replace(
									'[]',
									`[${repeatIndex}][]`
								);
							else name += `[${repeatIndex}]`;
							$(fieldItem).attr('name', name);
							$(fieldItem).attr(
								'id',
								$(fieldItem).attr('id') +
								`-${repeatIndex + 1}`
							);
						});
				}
			);

			/**
			 * Toggle visibilty of event custom date input
			 */
			function toggleDateRepeater() {
				const recurs = $('#choice_5_31_1').prop('checked');
				const recurPattern = $('#input_5_32').val();

				if (recurs && recurPattern === 'Custom Dates') {
					$('#field_5_250')
						.css('display', 'block')
						.find(':input')
						.attr('required', true);
				} else {
					$('#field_5_250')
						.css('display', 'none')
						.find(':input')
						.attr('required', false);
				}
			}

			toggleDateRepeater();

			gform.addAction(
				'gform_input_change',
				function (elem, formId, fieldId) {
					if (
						parseInt(formId) === 5 &&
						parseInt(fieldId) === 32
					) {
						toggleDateRepeater();
					} else if (
						parseInt(formId) === 5 &&
						$(elem).attr('id') === 'input_5_251' &&
						!$('#input_5_253').val()
					) {
						$('#input_5_253').val($('#input_5_251').val());
					}
				},
				10
			);

			$('#input_5_2, #input_5_3, #input_5_21')
				.find('option[value="PLEASE_SELECT"]')
				.attr('disabled', true);

			// autopopulate times
			gform.addFilter('gform_repeater_item_pre_add', function (
				clone,
				item
			) {
				clone.find('.gfield_time_hour input').each((i, elem) => {
					if (!$(elem).val()) $(elem).val('12');
				});
				clone.find('.gfield_time_minute input').each((i, elem) => {
					if (!$(elem).val()) $(elem).val('0');
				});
				clone.find('.gfield_time_ampm select').each((i, elem) => {
					if (!$(elem).val()) $(elem).val('am');
				});
				return clone;
			});
		}
	}

    /**
     * Fires on load and scroll
     */
    function themeOnScroll() {
        
        // Get scroll top
		const scrollTop = $(window).scrollTop();

		// Add class to images as they scroll into view
		$(
			'.wp-block-image, .fade-in, .fade-in-children, raise-in, .slide-in-left, .slide-in-right'
		).each(function (index, el) {
			if (elIsInViewport(el)) {
				$(el).addClass('in-view');
			} else if ($(el).hasClass('wp-block-image')) {
				$(el).removeClass('in-view');
			}
		});

		// Remove top add padding on scroll
		if (scrollTop > 20 + 90) {
			$(`.${THEME_PREFIX}-header`).removeClass('has-header-ad');
		} else {
			if ($('.pre-header-ad').length)
				$(`.${THEME_PREFIX}-header`).addClass('has-header-ad');
		}

		// Switch in the transparent nav class when we're over the hero
		toggleTopBarClasses();
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
            });
        }

		if(!getIsSmall()) {

			if ($('.bradenton-header').hasClass('open')) {
				$(`.${THEME_PREFIX}-header`).removeClass('open');
					toggleTopBarClasses();
					//If menu is now open...
					$('.toggle__label').text('Menu');
					searchClose();
					$('body').removeClass('menu-open');
			}
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
