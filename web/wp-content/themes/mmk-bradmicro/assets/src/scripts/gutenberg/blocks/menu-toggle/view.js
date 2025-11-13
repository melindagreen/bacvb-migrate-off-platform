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

document.addEventListener("DOMContentLoaded", function () {
	/**
	 * Finds all menu toggle blocks and attaches behavior to each.
	 * Each block should contain a `.hamburger` element and a `data-target` attribute
	 * pointing to the menu container to toggle.
	 */
	const blocks = document.querySelectorAll(
		".wp-block-madden-theme-menu-toggle"
	);

	blocks.forEach((block) => {
		const toggle = block.querySelector(".hamburger");
		const targetSelector = block.getAttribute("data-target");

		// Skip this block if there's no toggle element
		if (!toggle) return;

		/**
		 * Handle click on the hamburger toggle.
		 * Toggles visual state and dispatches a custom event.
		 */
		toggle.addEventListener("click", function (e) {
			e.preventDefault();

			const isActive = toggle.classList.toggle("is-active");

			// Toggle the target element's class if it exists
			if (targetSelector) {
				const target = document.querySelector(targetSelector);
				if (target) {
					target.classList.toggle("is-toggled", isActive);
				}
			}

			// Notify other scripts of the toggle state
			dispatchMaddenMenuToggleClick(block, isActive, targetSelector);
		});
	});

	/**
	 * Listen for a custom "maddenMenuToggleReset" event.
	 * Used to programmatically close a menu toggle by providing the target selector.
	 *
	 * @example
	 * document.dispatchEvent(new CustomEvent("maddenMenuToggleReset", {
	 *   detail: { targetSelector: "#main-menu" }
	 * }));
	 */
	document.addEventListener("maddenMenuToggleReset", function (e) {
		const { targetSelector } = e.detail;

		// Locate the corresponding block with the matching data-target
		const block = document.querySelector(
			`.wp-block-madden-theme-menu-toggle[data-target="${targetSelector}"]`
		);

		if (!block) return;

		const toggle = block.querySelector(".hamburger");
		const target = document.querySelector(targetSelector);

		// Remove active/toggled states
		if (toggle) {
			toggle.classList.remove("is-active");
		}

		if (target) {
			target.classList.remove("is-toggled");
		}

		// Dispatch the same toggle event with isActive: false
		dispatchMaddenMenuToggleClick(block, false, targetSelector);
	});
});
