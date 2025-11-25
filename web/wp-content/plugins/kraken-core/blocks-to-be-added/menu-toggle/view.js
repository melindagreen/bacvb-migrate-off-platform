import { THEME_PREFIX } from 'scripts/inc/constants';
import "./styles/style.scss";

/**
 * Dispatches a custom "maddenMenuToggleClick" event from a block element.
 *
 * @param {HTMLElement} block - The .wp-block-madden-theme-menu-toggle element.
 * @param {boolean} isActive - Whether the menu toggle is currently active/open.
 * @param {string} targetSelector - The CSS selector string pointing to the menu target element.
 */
function dispatchMaddenMenuToggleClick(block, isActive, targetSelector) {
	const customEvent = new CustomEvent("maddenMenuToggleClick", {
		bubbles: true,
		detail: {
			isActive: isActive, // Boolean: whether the menu is active
			targetSelector: targetSelector, // String: the selector for the target element
			block: block, // HTMLElement: the toggle block element
		},
	});

	block.dispatchEvent(customEvent);
}

window.addEventListener('DOMContentLoaded', () => {

    const blocks = document.querySelectorAll(".wp-block-"+THEME_PREFIX+"-menu-toggle");

	blocks.forEach((block) => {
		let menuToggleEl = block.querySelector(".mobile-menu-toggle");
		let menuSelector = block.getAttribute("data-menutarget");
		
		if (menuToggleEl && menuSelector) {
			menuToggleEl.addEventListener("click", () => {
				let isActive = menuToggleEl.classList.toggle("mobile-menu-toggle--open");
				let targetEl = document.querySelector(menuSelector);
				if (targetEl) {
					targetEl.classList.toggle("is-toggled", isActive);
					menuToggleEl.setAttribute('aria-expanded', isActive);
					document.querySelector('html').classList.toggle("has-menu-open", isActive);
					dispatchMaddenMenuToggleClick(block, isActive, targetEl);
					if (isActive) {
						//find elements with tabindex to focus first item
						//this usually contains a nav menu
						let tabIndex = targetEl.querySelectorAll("[tabindex]");
						if (tabIndex.length) {
							//focus fails without timeout
							setTimeout(() => {
								tabIndex[0].focus();
							}, 50);
						}
					}
				}
			});
		}
	});

});
