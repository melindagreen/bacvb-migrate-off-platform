/*** IMPORTS *******************************************************************/

import { getIsSmall, getIsLarge } from "./inc/utilities";
import "./library/madden-parallax-layout-v1.3-min";
import "./library/madden-lazy-load-v1.5-min";
import "./inc/aos";
import "./inc/pinned-images";
import "./inc/floorplan-sliders";
import "../styles/style.scss";

/*** SERVICE WORKER ************************************************************/

if ("serviceWorker" in navigator) {
	navigator.serviceWorker
		.register("/service-worker.js")
		.then((reg) => {
			// registration worked
			console.log("Registration succeeded. Scope is " + reg.scope);
		})
		.catch((error) => {
			// registration failed
			console.log("Registration failed with " + error);
		});
}

(function ($) {
	/*** GLOBAL VARS *****************************************************************/

	let _lazyLoadObject;
	let isMobile = false;

	const screenSm = 782;
	const screenMd = 980;
	const screenLg = 1100;
	const screenXl = 1400;

	/*** FUNCTIONS *****************************************************************/

	/**
	 * Listen for the mobile menu click
	 */
	const $menu = $("#menu-main-menu");
	const $mobileMenu = $("#mobile-header__contents");
	document.addEventListener("maddenMenuToggleClick", function (e) {
		const { isActive, targetSelector } = e.detail;
		const $target = jQuery(targetSelector);
		if (!$target.length) {
			return false;
		}
		if ("#mobile-header__contents" === targetSelector) {
			const mobileMargin = $target.outerHeight();
			if (isActive) {
				$target.stop(true).animate({ marginTop: 0 }, 700, "easeInOutQuint"); // open animation
			} else {
				$target
					.stop(true)
					.animate({ marginTop: `-${mobileMargin}px` }, 700, "easeInOutQuint"); // open animation
			}
		}
	});

	function resetDesktopMenu() {
		$menu.removeAttr("style");
	}

	function resetMobileMenu() {
		const mobileMargin = $mobileMenu.outerHeight();

		$mobileMenu.find(".sub-menu").each(function () {
			var subHeight = 0;
			$(this)
				.children()
				.each(function () {
					subHeight += $(this).outerHeight();
				});
			$(this)
				.children("li:first-child")
				.css({
					marginTop: `-${subHeight}px`,
				});
		});

		$mobileMenu.css({
			marginTop: `-${mobileMargin}px`,
		});
	}

	function mobileSwitch() {
		const windowWidth = $(window).width();
		if (windowWidth > screenLg) {
			if (isMobile) {
				isMobile = false;
				resetDesktopMenu();
			}
		} else {
			if (!isMobile) {
				isMobile = true;
				resetMobileMenu();
			}
			if (!$menu.hasClass("is-toggled")) {
				resetMobileMenu();
			}
		}
	}

	/*** THEME FRAMEWORK FUNCTIONS *************************************************/

	/**
	 * Fires on initial document load
	 */
	function themeOnLoad() {
		mobileSwitch();
	}

	/**
	 * Fires on load and scroll
	 */
	function themeOnScroll() {
		var scroll = $(window).scrollTop();
		if (scroll > 100) {
			$("body").addClass("site-scrolled");
		} else {
			$("body").removeClass("site-scrolled");
		}
	}

	/**
	 * Fires on load and window resize
	 */
	function themeOnResize() {
		// Lazy load
		if ($("*[data-load-type]").length) {
			_lazyLoadObject = MMLazyLoad.init({
				loadElements: document.querySelectorAll("[data-load-type]"),
			});
		}

		// parallax
		const parallaxEls = $(".has-parallax");
		if (parallaxEls.length && getIsLarge()) {
			parallaxEls.each((i, el) => {
				$(el).parallaxBG({
					adjustY: 0.12,
					bgXPosition: "center",
					bgYPosition: "center",
				});
			});
		}

		mobileSwitch();
	}

	$(document).ready(function ($) {
		//loadWeather();

		/*** EVENT LISTENERS **************************************************************/
		themeOnLoad();

		themeOnScroll();
		$(window).scroll(themeOnScroll);

		themeOnResize();
		$(window).resize(themeOnResize);
	});
})(jQuery);
