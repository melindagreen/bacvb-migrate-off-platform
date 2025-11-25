document.addEventListener("DOMContentLoaded", () => {

	//For mobile menus when the toggle icon should always be displayed
	//This is in place since the toggle icon may not be displayed on desktop and the other function only triggers when the subnav click option is enabled
	document
		.querySelectorAll(".wp-block-madden-theme-classic-menu .sub-menu-toggle")
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

	//Display submenu on click when setting is enabled
	//Prevents links from triggering
	//Submenu is displayed with focus on desktop
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
