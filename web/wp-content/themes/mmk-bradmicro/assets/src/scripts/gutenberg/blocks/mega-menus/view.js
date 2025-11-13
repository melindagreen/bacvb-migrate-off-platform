import { THEME_PREFIX } from "../../inc/constants";
const timerDelay = 500;
const hoverDelay = 100;
const timers = {};

import "./styles/view.scss";

jQuery(function ($) {
	function hideMenus($menu) {
		$(`.wp-block-madden-theme-mega-menu`).each(function () {
			if (!$(this).is($menu)) {
				hideMenu($(this), $(this).data("id"));
			}
		});
	}

	function showMenu($menu, megaMenuId) {
		hideMenus($menu);
		$(this).css("zIndex", 2);
		$menu.addClass("active hoverable visible");
		$(`li[data-megamenu="${megaMenuId}"]`).addClass("active");
	}

	function hideMenu($menu, megaMenuId) {
		$(this).css("zIndex", 1);
		$menu.removeClass("active hoverable visible");
		$(`li[data-megamenu="${megaMenuId}"]`).removeClass("active");
	}

	// Setup strict mode
	(function () {
		function initMegaMenu($menuItem) {
			var megaMenuId = $menuItem.data("megamenu");

			const $menu = $(`.wp-block-madden-theme-mega-menu--${megaMenuId}`);

			// Listend for mega menu item hover
			$menuItem.hover(
				function (e) {
					// Clear timer and fade out
					clearTimeout(timers[`${megaMenuId}Hover`]);
					clearTimeout(timers[`${megaMenuId}Active`]);
					showMenu($menu, megaMenuId);
				},
				function () {
					// Hide the menu
					$menu.removeClass("visible");
					$menuItem.removeClass("active");

					// Remove the hoverable class
					timers[`${megaMenuId}Hover`] = setTimeout(() => {
						$menu.removeClass("hoverable");
					}, hoverDelay);

					// Completely hide the menu
					timers[`${megaMenuId}Active`] = setTimeout(() => {
						clearTimeout(timers[`${megaMenuId}Active`]);
						hideMenu($menu, megaMenuId);
					}, timerDelay);
				}
			);

			// Listend for mega menu item hover
			$menu.hover(
				function (e) {
					// Clear timer and fade out
					if (!$(this).hasClass("hoverable")) {
						return false;
					}
					clearTimeout(timers[`${megaMenuId}Hover`]);
					clearTimeout(timers[`${megaMenuId}Active`]);
					showMenu($menu, megaMenuId);
				},
				function () {
					// Hide the menu
					$menu.removeClass("visible");
					$menuItem.removeClass("active");

					// Remove the hoverable class
					timers[`${megaMenuId}Hover`] = setTimeout(() => {
						$menu.removeClass("hoverable");
					}, hoverDelay);

					// Completely hide the menu
					timers[`${megaMenuId}Active`] = setTimeout(() => {
						clearTimeout(timers[`${megaMenuId}Active`]);
						hideMenu($menu, megaMenuId);
					}, timerDelay);
				}
			);
		}

		$("li[data-megamenu]").each(function () {
			initMegaMenu($(this));
		});
	})();
});
