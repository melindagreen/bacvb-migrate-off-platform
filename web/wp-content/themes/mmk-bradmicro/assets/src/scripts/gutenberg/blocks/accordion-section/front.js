const block = `wp-block-madden-theme-accordion-section`;

const expandSection = (sectionElement) => {
	// Get the .accordion__body element within the section
	const accordionBody = sectionElement.querySelector(`.${block}__body`);

	if (accordionBody) {
		// Measure the height of the content inside the .accordion__body
		const contentHeight = accordionBody.scrollHeight;

		// Add the "open" class to the .accordion__body
		accordionBody.parentNode.classList.add("open");

		// Set the height of the .accordion__body to match the content height
		accordionBody.style.height = `${contentHeight}px`;

		// when the next css transition finishes (which should be the one we just triggered)
		accordionBody.addEventListener("transitionend", function (e) {
			if (accordionBody.parentNode.classList.contains("open")) {
				accordionBody.style.height = "";
			}
		});
	}
};

const collapseSection = (sectionElement) => {
	// Get the .accordion__body element within the section
	const accordionBody = sectionElement.querySelector(`.${block}__body`);

	if (accordionBody) {
		// Remove the "open" class from the .accordion__body
		accordionBody.parentNode.classList.remove("open");

		// Set the height of the .accordion__body to 0
		const contentHeight = accordionBody.scrollHeight;
		accordionBody.style.height = `${contentHeight}px`;
		setTimeout(() => {
			accordionBody.style.height = "0";
		}, 10);
	}
};

// Get the parent accordion element
const sectionNodes = document.querySelectorAll(`.${block}`);
const accordionSections =
	sectionNodes instanceof NodeList ? Array.from(sectionNodes) : [sectionNodes];

// Loop through each accordion section element
accordionSections.forEach((sectionElement) => {
	// Attach custom event listener to open event
	sectionElement.addEventListener("open", () => expandSection(sectionElement));

	// Attach custom event listener to close event
	sectionElement.addEventListener("close", () =>
		collapseSection(sectionElement)
	);

	// Get the .accordion__header element within the section
	const accordionHeader = sectionElement.querySelector(`.${block}__header`);

	// Add click listener to the .accordion__header
	accordionHeader.addEventListener("click", (event) => {
		// Toggle the "open" class on the .accordion__body element
		const accordionBody = sectionElement.querySelector(`.${block}__body`);

		if (accordionBody) {
			if (accordionBody.parentNode.classList.contains("open")) {
				collapseSection(sectionElement);
			} else {
				expandSection(sectionElement);

				// Create a custom event with the section element as detail
				const customEvent = new CustomEvent("sectionOpening", {
					detail: { sectionElement },
				});

				// Dispatch the custom event
				sectionElement.parentNode.dispatchEvent(customEvent);
			}
		}
	});
});
