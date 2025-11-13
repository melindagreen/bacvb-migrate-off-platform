const block = `wp-block-madden-theme-accordion`;
const blockSection = `wp-block-madden-theme-accordion-section`;

// Get all elements with the class .wp-block-mm-nino-theme-accordion
const accordionNodes = document.querySelectorAll(`.${block}`);
const accordionElements =
	accordionNodes instanceof NodeList
		? Array.from(accordionNodes)
		: [accordionNodes];

// Loop through each accordion element
accordionElements.forEach((accordionElement) => {
	// Access the data attributes
	const openFirstItem = accordionElement.getAttribute("data-openFirstItem");
	const autoCloseOthers = accordionElement.getAttribute("data-autoCloseOthers");

	// Get all elements with the class .wp-block-mm-nino-theme-accordion-section within this accordion element
	const sectionNodes = accordionElement.querySelectorAll(`.${blockSection}`);
	const accordionSections =
		sectionNodes instanceof NodeList
			? Array.from(sectionNodes)
			: [sectionNodes];

	// Open the first section
	if (true === openFirstItem && accordionSections.length) {
		accordionSections[0].dispatchEvent(new Event("open"));
	}

	// Listen for opening of section and close others
	accordionElement.addEventListener("sectionOpening", (e) => {
		const currentSection = e.detail.sectionElement;
		// Loop through each accordion section element
		if ("true" === String(autoCloseOthers)) {
			accordionSections.forEach((sectionElement) => {
				if (currentSection !== sectionElement) {
					sectionElement.dispatchEvent(new Event("close"));
				}
			});
		}
	});
});
