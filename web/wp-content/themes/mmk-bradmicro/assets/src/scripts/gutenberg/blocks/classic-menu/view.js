document.addEventListener("DOMContentLoaded", () => {
	document
		.querySelectorAll(".wp-block-madden-theme-classic-menu .menu-item-toggle")
		.forEach((toggle) => {
			toggle.addEventListener("click", (e) => {
				e.preventDefault(); // Prevent navigation
				e.stopPropagation(); // Prevent parent anchor from triggering

				const menuItem = toggle.closest(".menu-item");
				if (menuItem) {
					menuItem.classList.toggle("is-toggled");
				}
			});
		});

	// On click
	document
		.querySelectorAll(
			".wp-block-madden-theme-classic-menu.subnav-display-click .menu-item-has-children > a"
		)
		.forEach((toggle) => {
			toggle.addEventListener("click", (e) => {
				e.preventDefault(); // Prevent navigation
				e.stopPropagation(); // Prevent parent anchor from triggering

				const menuItem = toggle.closest(".menu-item");
				if (menuItem) {
					menuItem.classList.toggle("is-toggled");
				}
			});
		});
});
