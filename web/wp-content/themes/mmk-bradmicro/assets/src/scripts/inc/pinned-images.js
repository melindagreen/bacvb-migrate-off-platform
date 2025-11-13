// Set the minimum breakpoint for applying the wide style
const BREAKPOINT = 781;

/**
 * Adjusts the width of elements with the 'is-style-pinned-right' or 'is-style-pinned-left' class
 * to stretch to the respective viewport edge, only when the browser width exceeds the defined breakpoint.
 * It intelligently handles both wp-block-image (targets the img) and wp-block-cover (targets the cover block,
 * but uses an outer container's position for calculation).
 */
const adjustPinnedRightElements = () => {
	// Find all target elements/containers:
	// This selector targets the element that holds the pinning class and whose position (rect) we need to calculate from.
	const elements = document.querySelectorAll(
		".wp-block-image.is-style-pinned-right, .wp-block-group.is-style-pinned-right, " +
			".wp-block-image.is-style-pinned-left, .wp-block-group.is-style-pinned-left"
	);
	const windowWidth = window.innerWidth;

	if (windowWidth > BREAKPOINT) {
		elements.forEach((element) => {
			// Source for rectangle calculation
			const rectSource = element;
			const rect = rectSource.getBoundingClientRect();

			let targetElement = element;
			let isCoverBlock = false;

			// Determine the specific element to apply the width/transform to (the targetElement)
			if (element.classList.contains("wp-block-image")) {
				// For image blocks, the inner <img> element is the target
				targetElement = element.querySelector("img");
			} else if (element.classList.contains("wp-block-group")) {
				// For cover blocks, the inner .wp-block-cover element is the target,
				// but the rect is calculated from the wrapper (.wp-block-cover-wrapper)
				targetElement = element.querySelector(".wp-block-cover");
				isCoverBlock = true;
			}

			if (!targetElement) return; // Skip if no target is found

			if (element.classList.contains("is-style-pinned-right")) {
				// --- Pinned Right Logic ---
				const leftPosition = rect.left;
				// Required width is the distance from the element's left edge to the viewport's right edge
				const newWidth = windowWidth - leftPosition;

				// Apply the calculated width and ensure no left-pin transform interferes
				targetElement.style.setProperty("width", `${newWidth}px`, "important");
				targetElement.style.setProperty("max-width", "none", "important");
				targetElement.style.removeProperty("transform");
			} else if (element.classList.contains("is-style-pinned-left")) {
				// --- Pinned Left Logic ---
				const rightPosition = rect.right;
				const leftPosition = rect.left;

				// Required width is the distance from the viewport's left edge (0) to the element's right edge
				const newWidth = rightPosition;

				// Shift the element horizontally left by its current offset to align its left edge with the viewport's edge (0)
				const shiftLeft = leftPosition * -1;

				// Apply the calculated width and transformation
				targetElement.style.setProperty("width", `${newWidth}px`, "important");
				targetElement.style.setProperty("max-width", "none", "important");
				targetElement.style.setProperty(
					"transform",
					`translateX(${shiftLeft}px)`,
					"important"
				);
			}
		});
	} else {
		// 5. Reset styles when the screen is below the breakpoint
		elements.forEach((element) => {
			let targetElement = element;

			if (element.classList.contains("wp-block-image")) {
				targetElement = element.querySelector("img");
			} else if (element.classList.contains("wp-block-group")) {
				targetElement = element.querySelector(".wp-block-cover");
			}

			if (targetElement) {
				// Clear all custom inline styles
				targetElement.style.removeProperty("width");
				targetElement.style.removeProperty("max-width");
				targetElement.style.removeProperty("transform");
			}
		});
	}
};

// --- Event Listeners ---

// Run on initial load
window.addEventListener("load", adjustPinnedRightElements);

// Use a debounce to optimize performance during rapid resizing
let resizeTimer;
window.addEventListener("resize", () => {
	clearTimeout(resizeTimer);
	resizeTimer = setTimeout(adjustPinnedRightElements, 50);
});
