import { THEME_PREFIX } from 'scripts/inc/constants';
import "./styles/style.scss";

window.addEventListener('DOMContentLoaded', () => {

    const blocks = document.querySelectorAll(".wp-block-"+THEME_PREFIX+"-search-toggle");

	blocks.forEach((block) => {
		
		//Search toggle events
		let searchToggleEl = block.querySelector(".search-toggle");
		let searchSelector = block.getAttribute("data-searchtarget");

		if (searchToggleEl && searchSelector) {
			searchToggleEl.addEventListener("click", () => {
				let isActive = searchToggleEl.classList.toggle("search-toggle--open");
				let targetEl = document.querySelector(searchSelector);
				if (targetEl) {
					targetEl.classList.toggle("is-toggled", isActive);
					searchToggleEl.setAttribute('aria-expanded', isActive);
					if (isActive) {
						let input = targetEl.querySelector('input[type="search"]');
						input.focus();
					}
				}
			});
		}
	});

});
