/*** IMPORTS *******************************************************************/

import { getIsSmall, getIsLarge } from './inc/utilities';
import { THEME_PREFIX } from './inc/constants';
import './library/madden-parallax-layout-v1.3-min';
import './library/madden-lazy-load-v1.5-min';
import '../styles/style.scss';

/*** SERVICE WORKER ************************************************************/

if ('serviceWorker' in navigator) {

	navigator.serviceWorker.getRegistrations().then(registrations => {
		for (const registration of registrations) {
			registration.unregister();
		} 
	});

    // navigator.serviceWorker.register('/service-worker.js')
    //     .then((reg) => {
    //         // registration worked
    //         console.log('Registration succeeded. Scope is ' + reg.scope);
    //     }).catch((error) => {
    //         // registration failed
    //         console.log('Registration failed with ' + error);
    //     });
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
		
		//If menu is now open...
		if ($('.bradenton-header').hasClass('open')) {
			$('.toggle__label').text('Close');
			if ($('.search-form').hasClass('open')) $('.search-form input').focus();
		} else {
			$('.toggle__label').text('Menu');
			searchClose();
		}

		toggleTopBarClasses();
		$('body').toggleClass('menu-open');
	};

     /**
	 * Toggle open the stay connected section
	 */
    function toggleStayConnected() {

        // toggle stay connected
        $('.stay-connected__toggle').click(function () {
        $('.stay-connected').toggleClass('stay-connected--open');
        });
    }

    /**
	 * Toggle open the mobile nav menu
	 */
	function toggleMobileMenu() {
		$('body').toggleClass('mobile-menu-open');
		$('.header-mobile-wrap').toggleClass('show');
		$('.header-mobile-icon').toggleClass('open');
	}

    // toggle search
    $('.search-form__submit').on("click", function (e) {
		const rootAria = $(this).attr("id").replace("_submit", "");
		const parentForm = $(this).parent(`#${rootAria}`);
		const siblingSearchField = parentForm.find($(`#${rootAria}_input`));
		console.log(siblingSearchField);
		console.log(siblingSearchField.val());
		
		if ( (siblingSearchField.length) && (siblingSearchField.val() == '') ) {
			e.preventDefault();
		}
		if (parentForm.length) {
			parentForm.toggleClass('open');
			if (parentForm.hasClass('open')) {
				siblingSearchField.focus();
			}
		}
    });


	/** Lightbox **/
	function lightBox() {

		$('.lb-content:not(.wp-lightbox-overlay .lightbox-image-container .lb-content), .lightbox-imagecarousel:not(.wp-lightbox-overlay .lightbox-image-container .lightbox-imagecarousel').each(function() {
			$(this).remove();  
		});

	}

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
		const topBar = $('.bradenton-header .top-bar');
	  
		if (scrollTop < 500 || $('.bradenton-header').hasClass('open')) {
		  
		  topBar.removeClass('top-bar--solid');
		} else {
		  
			topBar.addClass('top-bar--solid');
		}
	}

	   /**
	 * Switch in the transparent nav class when we're over the hero or if the megamenu is open
	 */
	function toggleTopBannerClasses() {

		const scrollTop = $(window).scrollTop();
		const topBar = $('.bradenton-header .top-banner--global');
	  
		if (scrollTop < 500 || !getIsSmall()) {
		  
		  topBar.removeClass('top-banner--hide');
		} else {
		  
			topBar.addClass('top-banner--hide');
		}
	}

	// truncate long body of text
	function truncateText(description, maxWords) {
	    // Split the description into an array of words
	    let words = description.split(' ');

	    // Check if the number of words is greater than the specified maximum
	    if (words.length > maxWords) {
	        // Join only the first 'maxWords' words and append ellipses
	        return words.slice(0, maxWords).join(' ') + "...";
	    } else {
	        // If the number of words is within the limit, return the original description
	        return description;
	    }
	}

    /*** THEME FRAMEWORK FUNCTIONS *************************************************/

    /**
	 * Fires on initial document load
	 */
	function themeOnLoad() {

		// Scroll
		$('a[href^="#"]').not('a[href="#"]').on('click', function (e) {
			const targetId = $(this).attr('href');
			const targetElement = $(targetId);
		
			if (targetElement.length) {
				e.preventDefault(); // Prevent only if a valid target exists
				const offset = 110;
				const offsetPosition = targetElement.offset().top - offset;
		
				$('html, body').animate({ scrollTop: offsetPosition }, 100);
			}
		});

		// Heading Scroll Animation
		scrollAnimation('.is-style-heading-shadow', 400, 'is-style-heading-shadow--shadow', true);
		scrollAnimation('.rock-flag', 400, 'rock-flag--rocking');

		scrollAnimation('.fade-up', 500, 'fade-up--animate', true);
		scrollAnimation('.small-circ-down', 400, 'small-circ-down--rotate', true);
		scrollAnimation('.sticker', 500, 'sticker--zoom-in', true);
		scrollAnimation('.share-the-love-bg::after', 400, 'share-the-love-bg::after--zoom-in', true);
		scrollAnimation('.share-the-love-bg::before', 400, 'share-the-love-bg::before--zoom-in', true);

		// Toggle Top Bar Banner
		toggleTopBannerClasses();
		
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

		//LightBox

		var lightboxCarousels = $('.lightbox-imagecarousel');

		// Loop through each element
		lightboxCarousels.each(function(index, element) {
			// Unique class based on index
			var uniqueClass = 'swiper-buttons-' + index;

			// Add unique classes to next and prev buttons
			$(element).find('.swiper-button-next-imagecarousel').addClass(uniqueClass + '-next');
			$(element).find('.swiper-button-prev-imagecarousel').addClass(uniqueClass + '-prev');

			var uniqueContainer = 'swiper-container-' + index;

			// Add unique class to the Swiper container element
			$(element).addClass(uniqueContainer);
	
			// Initialize Swiper for each element with different options
			var swiper = new Swiper('.' + uniqueContainer, {
				loop: false,
				autoplay: {
					delay: 5500,
					disableOnInteraction: false,
				},
				clickable: true,
				navigation: {
					nextEl: '.' + uniqueClass + '-next', // Use unique class for next button
					prevEl: '.' + uniqueClass + '-prev'  // Use unique class for prev button
				}
			});
		});

		  $('.swiper-button-next-imagecarousel, .swiper-button-prev-imagecarousel, .swiper-slide, .scrim, .swiper-slide-next, .swiper-slide-duplicate').on('click', function(event) {
			// Prevent the event from reaching parent elements
			event.stopPropagation();
		});

		lightBox();

		// Query Block Placeholder Image 
		insertPlaceholderImage();

		//Query Block Carousel On Mobile
		// queryCarouselOnMobile();

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


		// media/text mobile slidedown
		if ($(window).width() < 600) {	
		    if ($('.wp-block-media-text').hasClass('is-style-icon')) {
		        $('.wp-block-media-text.is-style-icon .wp-block-heading').on('click', function () {
		          $(this).toggleClass('open');
		          $(this).siblings('p').slideToggle();
		        });
		    }
		}

        //toggle stay connected
        toggleStayConnected();


        // load bouncing ball on homepage when user scrolls in view
		var bounceBall = $('#bounceBall'); // Get the bounceBall element
		var isImageLoaded = false; // Flag to track if image is already loaded

		$(window).scroll(function() {
		    if (!isImageLoaded && isElementInViewport(bounceBall[0])) { // Check if bounceBall is in viewport and image is not already loaded
		        var img = bounceBall.find('img'); // Find the img inside bounceBall
		        var src = img.attr('src'); // Get the src attribute of the img
		        img.attr('src', src); // Reload the image by setting the src attribute again
		        isImageLoaded = true; // Set the flag to true to indicate image is now loaded
		    }
		});

		// Top Banner
		if ($('.top-banner').length && $('.top-banner').css('display') !== 'none') {
			// Change padding-bottom of .nav
			$('.mega-menu__nav-wrap').css('padding-top', '5.5rem');
		}
		// Add click event listener to .top-banner__close
		$('.top-banner__close').click(function() {
			// Hide the .top-banner
			$('.top-banner').hide();
	
			// Change padding-bottom of .nav on click
			$('.mega-menu__nav-wrap').css('padding-top', 'unset');
		});

		// Function to check if an element is in the viewport
		function isElementInViewport(el) {

			if(el === undefined) {
				return false;
			}
		    var rect = el.getBoundingClientRect();
		    return (
		        rect.top >= 0 &&
		        rect.left >= 0 &&
		        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
		        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
		    );
		}
	}

    /**
     * Load the current temperature and forecast
     * @returns {null}
     */
    jQuery.get("https://maddencdn.com/global/code/weather/getWeatherData.php?cityName=Bradenton",
        function () { })
        .done(function (data) {
            var forecastHTML = "";
            try {
                var weather = JSON.parse(data);
                var mainWeather = weather['conditions'];
                var night = (weather['icon'].indexOf("/nt_") != -1) ? true : false;

                // What's the weather like?
                // TO DO Night weather icons
                if (mainWeather === "Clear") {
                    forecastHTML += (night)
                        ? '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/moon-cloud.png" alt="clear" />'
                        : '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/sun-regular.png" alt="sunny" />';
                } else if (mainWeather === "Rain" || mainWeather === "Drizzle") {
                    forecastHTML += '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/cloud-sun-rain-solid.png" alt="rain" />';
                } else if (mainWeather === "Clouds" || mainWeather === "Clouds") {
                    forecastHTML += '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/cloud-sun.png" alt="cloudy" />';
                } else if (mainWeather === "Thunderstorm") {
                    forecastHTML += '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/hurricane-solid.png" alt="chancerain" />';
                } else {
                    forecastHTML += (night)
                        ? '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/moon-cloud.png" alt="clear" />'
                        : '<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/weather-icons/sun-regular.png" alt="sunny" />';
                }
                forecastHTML += `<p class="header-weather-forecast__temp">${weather['temp']}${weather['temp']>99 ? '<br>' : ''}&deg;F</p>`;

            } catch (err) { }

            // add or hide if no forecast
            if (forecastHTML != "") {
                jQuery(".header-weather-forecast__now").each(function(){
					
					jQuery(this).html(forecastHTML);
				});
            } else {
				jQuery(".header-weather-forecast__now").each(function(){
					
					jQuery(this).css({ "display": "none" });
				});
            }
        })

		/**
		 * Animation on scroll
		 * @returns {null}
		 */
		function scrollAnimation(selector, scrollThreshold, animationClass, permanent = false) {
			$(window).scroll(function() {
				const scrollPosition = $(this).scrollTop();
			
				$(selector).each(function() {
				  const itemOffset = $(this).offset().top;
			
				  if (scrollPosition > itemOffset - scrollThreshold && !$(this).hasClass(animationClass) && !$(this).hasClass('animationoff')) {
					$(this).addClass(animationClass);
					const that = this;
					if(!permanent) {
					setTimeout(function() {
					  $(that).removeClass(animationClass);
					  $(that).addClass('animationoff');
					}, 400); 
					}
				  }
				});
			  });
		  }

		  /**
		 * Query Block Placeholder Image Injection
		 * @returns {null}
		 */
		  function insertPlaceholderImage() {
			$('.wp-block-query .wp-block-post').each(function() {
				const post = $(this);
				const postImage = post.find('.wp-block-post-featured-image img');
		
				if (!postImage.length) {
					var placeholderImg = $('<img>', {
						src: '/wp-content/themes/mm-bradentongulfislands/assets/images/placeholder.jpg', 
						alt: 'Placeholder Image',
						class: 'attachment-post-thumbnail size-post-thumbnail wp-post-image'
					});
	
					var figureElement = $('<figure>', {
						class: 'wp-block-post-featured-image'
					}).append(placeholderImg);

					// wp-block-post__content
					const postContent = post.find('.wp-block-post__featured-image');
					if (postContent.length) {
						figureElement.append(postContent);
					}
				}
			});
		}


		 /**
		 * Query Block Turn into carousel on mobile
		 * @returns {null}
		 */
		function queryCarouselOnMobile() {

			const blockQueryLoop = $('.wp-block-query');

        if (getIsSmall() && blockQueryLoop.length > 0) {
            const slides = blockQueryLoop.find('.wp-block-post');

            if (slides.length > 0) {
                const swiperWrapper = $('<div class="wp-block-post-template swiper-wrapper"></div>');

                slides.addClass('swiper-slide').appendTo(swiperWrapper);

                const swiperContainer = $('<div class="wp-block-query swiper-container"></div>').append(swiperWrapper);

                blockQueryLoop.replaceWith(swiperContainer);

                new Swiper(swiperContainer[0], {
                    slidesPerView: 'auto',
                    spaceBetween: 20,
                    breakpoints: {
                        768: {
                            slidesPerView: 1,
                            spaceBetween: 10,
                        }
                        // Add more breakpoints as needed
                    }
                });
            }
        }
		else {
			const swiperContainer = blockQueryLoop.find('.swiper-container');

            if (swiperContainer.length > 0) {
                const slides = swiperContainer.find('.swiper-slide');

                slides.removeClass('swiper-slide').appendTo(blockQueryLoop.find('.wp-block-query__content'));

            }
		}
	}


    /**
     * Fires on load and scroll
     */
    function themeOnScroll() {
        
		toggleTopBarClasses();
		toggleTopBannerClasses();
		scrollAnimation('.is-style-arrow-button', 600, 'is-style-arrow-button--rotate');
		scrollAnimation('.grid-item-body--2 .grid-item-body__arrow', 600, 'grid-item-body__arrow--forward');
		scrollAnimation('.grid-item-body--3 .grid-item-body__arrow', 600, 'grid-item-body__arrow--rotate');
		scrollAnimation('.grid-item-body--1 .grid-item-body__arrow', 600, 'grid-item-body__arrow--rotate');

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

		// show hover state on load on mobile view for portrait/cover blocks
		if(getIsSmall() || $(window).width() < 768) {
			if ($('.is-style-portrait').length > 0) {
				$('.is-style-portrait').each(function(){
					$(this).addClass('mobile');
				});
			}
		}


		// truncate text on mobile for the portrait block in blog/trip inspiration page 
		if(getIsSmall() || $(window).width() < 768) {
		    if ($('.wp-block-image figcaption').length > 0) {
		        $('.wp-block-image figcaption').each(function () {
		            // Get the text content of figcaption
		            let originalText = $(this).text();

		            // Truncate the text using truncateText function
		            let truncatedText = truncateText(originalText, 6);

		            $(this).text(truncatedText);
		        });
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

		//Query Block Carousel On Mobile
		// queryCarouselOnMobile();
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
