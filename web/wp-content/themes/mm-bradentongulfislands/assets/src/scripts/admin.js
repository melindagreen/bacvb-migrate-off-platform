import $ from "jquery";
import "../styles/style-admin.scss";
import MMLazyLoad from "./library/madden-lazy-load-v1.5-min";

import domReady from "@wordpress/dom-ready";

/*** GLOBAL VARS *****************************************************************/

let _lazyLoadObject;

/**
 * Initialize Lazy Load
 */
function initLazyLoad() {
	// Destroy the existing lazy load instance if applicable
	if (_lazyLoadObject && typeof _lazyLoadObject.destroy === "function") {
		_lazyLoadObject.destroy();
	}

	// Reinitialize lazy load
	if (MMLazyLoad && $("*[data-load-type]").length) {
		_lazyLoadObject = MMLazyLoad.init({
			loadElements: document.querySelectorAll("[data-load-type]"),
		});
	}
}

/**
 * Add window
 */
function injectSizeElements() {
	if (!document.getElementById("isSmall")) {
		const sizeContainer = document.createElement("div");
		sizeContainer.innerHTML = `
            <!-- SIZE ELEMENTS (for viewport utilities) -->
            <div id="isSmall" style="display:none;"></div>
            <div id="isMedium" style="display:none;"></div>
            <div id="isLarge" style="display:none;"></div>
        `;
		document.body.appendChild(sizeContainer);
	}
}

/**
 * Fires on load and window resize
 */
function themeOnResize() {
	initLazyLoad();
}

// Run when the window loads
$(window).on("load", function () {
	injectSizeElements();
	setTimeout(themeOnResize, 500); // Delay by 500ms

	// Use vanilla JS instead of jQuery for better performance
	window.addEventListener("resize", themeOnResize);
});

// Ensure WordPress editor is ready
domReady(() => {
	let previousContent = wp.data.select("core/editor").getEditedPostContent();

	wp.data.subscribe(() => {
		const newContent = wp.data.select("core/editor").getEditedPostContent();

		if (newContent !== previousContent) {
			previousContent = newContent;
			initLazyLoad();
		}
	});

	// Listen for block selection changes (fix for issues when only resizing works)
	wp.data.subscribe(() => {
		const selectedBlock = wp.data
			.select("core/block-editor")
			.getSelectedBlock();

		if (selectedBlock) {
			initLazyLoad();
		}
	});
});
