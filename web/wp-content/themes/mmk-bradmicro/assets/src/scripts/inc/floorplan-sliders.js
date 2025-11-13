document.addEventListener("DOMContentLoaded", function () {
	// Get all floorplan triggers
	const triggers = document.querySelectorAll(".floorplan-trigger");

	triggers.forEach((trigger) => {
		trigger.addEventListener("click", function () {
			// Extract the ID of the trigger
			const triggerId = this.id;

			// Extract the string after -- (e.g., "exhibit-hall" from "floorplan-trigger--exhibit-hall")
			const triggerKey = triggerId.split("--")[1];

			// Hide all floorplan slides
			const allSlides = document.querySelectorAll(".floorplan-slide");
			allSlides.forEach((slide) => {
				slide.style.display = "none";
			});

			// Show the matching slide (e.g., "floorplan-slide--exhibit-hall")
			const targetSlideId = `floorplan-slide--${triggerKey}`;
			const targetSlide = document.getElementById(targetSlideId);
			if (targetSlide) {
				targetSlide.style.display = "block";
			}
		});
	});

	// Custom scrollbar initialization
	const wrapper = document.querySelector(".floorplan-triggers-wrapper");
	if (wrapper) {
		// Create scrollbar track
		const track = document.createElement("div");
		track.className = "floorplan-scrollbar-track";

		// Create scrollbar handle
		const handle = document.createElement("div");
		handle.className = "floorplan-scrollbar-handle";

		// Create the three white lines inside the handle
		const line1 = document.createElement("div");
		line1.className = "floorplan-scrollbar-line";
		const line2 = document.createElement("div");
		line2.className = "floorplan-scrollbar-line";
		const line3 = document.createElement("div");
		line3.className = "floorplan-scrollbar-line";

		handle.appendChild(line1);
		handle.appendChild(line2);
		handle.appendChild(line3);

		track.appendChild(handle);
		wrapper.parentNode.insertBefore(track, wrapper.nextSibling);

		// Initially hide the track
		track.style.display = "none";

		// Scrollbar functionality
		let isScrolling = false;
		let startX = 0;
		let startScrollLeft = 0;
		let pendingX = 0;
		let animationFrameId = null;

		function checkIfScrollNeeded() {
			const scrollWidth = wrapper.scrollWidth;
			const clientWidth = wrapper.clientWidth;
			const needsScroll = scrollWidth > clientWidth;

			// Show or hide track based on whether content exceeds wrapper width
			track.style.display = needsScroll ? "block" : "none";

			return needsScroll;
		}

		function updateScrollbarPosition() {
			const scrollLeft = wrapper.scrollLeft;
			const scrollWidth = wrapper.scrollWidth;
			const clientWidth = wrapper.clientWidth;
			const scrollableWidth = scrollWidth - clientWidth;

			if (scrollableWidth <= 0) {
				handle.style.transform = "translateX(0px)";
				return;
			}

			// Calculate handle position
			const scrollPercent = scrollLeft / scrollableWidth;
			const trackWidth = track.clientWidth;
			const handleWidth = handle.clientWidth;
			const maxHandleLeft = trackWidth - handleWidth;

			handle.style.transform = `translateX(${scrollPercent * maxHandleLeft}px)`;
		}

		function updateScrollFromDrag() {
			const deltaX = pendingX - startX;
			const scrollWidth = wrapper.scrollWidth;
			const clientWidth = wrapper.clientWidth;
			const scrollableWidth = scrollWidth - clientWidth;

			if (scrollableWidth <= 0) return;

			// Calculate the scroll ratio
			const trackWidth = track.clientWidth;
			const handleWidth = handle.clientWidth;
			const maxHandleLeft = trackWidth - handleWidth;

			if (maxHandleLeft <= 0) return;

			// Convert pixel movement to scroll amount
			const scrollRatio = scrollableWidth / maxHandleLeft;
			const newScrollLeft = startScrollLeft + deltaX * scrollRatio;
			const clampedScrollLeft = Math.max(
				0,
				Math.min(newScrollLeft, scrollableWidth)
			);

			// Update wrapper scroll - this will trigger the scroll event listener
			// which will update the handle position smoothly
			wrapper.scrollLeft = clampedScrollLeft;
		}

		// Handle mouse down on scrollbar handle
		handle.addEventListener("mousedown", (e) => {
			isScrolling = true;
			startX = e.clientX;
			startScrollLeft = wrapper.scrollLeft;
			handle.style.cursor = "grabbing";
			e.preventDefault();
		});

		// Handle mouse move for dragging with requestAnimationFrame
		document.addEventListener("mousemove", (e) => {
			if (!isScrolling) return;

			pendingX = e.clientX;

			if (animationFrameId === null) {
				animationFrameId = requestAnimationFrame(() => {
					updateScrollFromDrag();
					animationFrameId = null;
				});
			}
		});

		// Handle mouse up
		document.addEventListener("mouseup", () => {
			if (isScrolling) {
				isScrolling = false;
				handle.style.cursor = "grab";
				if (animationFrameId !== null) {
					cancelAnimationFrame(animationFrameId);
					animationFrameId = null;
				}
			}
		});

		// Handle touch start on scrollbar handle
		handle.addEventListener("touchstart", (e) => {
			isScrolling = true;
			startX = e.touches[0].clientX;
			startScrollLeft = wrapper.scrollLeft;
			handle.style.cursor = "grabbing";
			e.preventDefault();
		});

		// Handle touch move for dragging with requestAnimationFrame
		document.addEventListener(
			"touchmove",
			(e) => {
				if (!isScrolling) return;

				pendingX = e.touches[0].clientX;

				if (animationFrameId === null) {
					animationFrameId = requestAnimationFrame(() => {
						updateScrollFromDrag();
						animationFrameId = null;
					});
				}
			},
			{ passive: false }
		);

		// Handle touch end
		document.addEventListener("touchend", () => {
			if (isScrolling) {
				isScrolling = false;
				handle.style.cursor = "grab";
				if (animationFrameId !== null) {
					cancelAnimationFrame(animationFrameId);
					animationFrameId = null;
				}
			}
		});

		// Update scrollbar position on scroll
		wrapper.addEventListener("scroll", updateScrollbarPosition);

		// Check and update on window resize
		window.addEventListener("resize", () => {
			checkIfScrollNeeded();
			updateScrollbarPosition();
		});

		// Initial check and position update
		checkIfScrollNeeded();
		updateScrollbarPosition();
	}
});
