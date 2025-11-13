import $ from "jquery";

$(window).on("load", () => {
	initInteractiveMap();
});

export const initInteractiveMap = () => {
	const $container = $(".mapViewArea");
	const $img = $container.find("img");
	const $svg = $container.find("svg");

	let isDragging = false;
	let startX = 0;
	let startLeft = 0;
	let touchMoved = false; // Flag to detect if touch has moved beyond a threshold
	const panThreshold = 5; // Pixels a touch must move to be considered a pan

	let minLeft;
	const maxLeft = 0;

	// New flag to track if the initial swipe bounce animation has played
	let hasSwipedOnce = false;
	// New flag for viewport animation
	let hasViewedOnce = false;

	const setMinLeft = () => {
		const containerWidth = $container.width();
		const imgWidth = $img.width();
		minLeft = -(imgWidth - containerWidth);
		if (minLeft > 0) {
			minLeft = 0;
		}
		// After recalculating, ensure the map stays within bounds
		setLeft(getLeft());
	};

	// Debounce the resize event for setMinLeft
	let resizeTimer;
	$(window).on("resize", () => {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(setMinLeft, 100); // Wait 100ms after resize stops
	});

	// Initial calculation of minLeft
	setMinLeft();

	function setLeft(x) {
		const clampedX = Math.min(maxLeft, Math.max(minLeft, x));
		$img.css("left", clampedX + "px");
		$svg.css("left", clampedX + "px");
	}

	function getLeft() {
		return parseFloat($img.css("left")) || 0;
	}

	// --- Mouse and Touch Event Handlers ---

	// Unified start function for both mouse and touch
	const handleStart = (e) => {
		isDragging = true;
		touchMoved = false; // Reset flag for each new touch/drag
		startX = e.pageX || e.originalEvent.touches[0].pageX;
		startLeft = getLeft();
	};

	// Unified move function for both mouse and touch
	const handleMove = (e) => {
		if (!isDragging) return;

		const currentX = e.pageX || e.originalEvent.touches[0].pageX;
		const deltaX = currentX - startX;

		// Check if enough horizontal movement has occurred to be considered a pan
		if (Math.abs(deltaX) > panThreshold && !touchMoved) {
			touchMoved = true;

			// Trigger bounce animation only on the first detected swipe/drag
			if (!hasSwipedOnce && !hasViewedOnce) {
				// Only animate if neither initial load nor viewport animation played
				const $iconsG = $(".wp-block-madden-theme-bradenton-map svg #ICONS g");
				$iconsG.addClass("bounce-scale");

				// Remove the animation class after it finishes
				setTimeout(() => {
					$iconsG.removeClass("bounce-scale");
				}, 1000); // match the animation duration

				hasSwipedOnce = true; // Set flag so it doesn't trigger again
			}
		}

		if (touchMoved) {
			setLeft(startLeft + deltaX);
			// ONLY prevent default if we are actively panning
			e.preventDefault();
		}
	};

	// Unified end function for both mouse and touch
	const handleEnd = () => {
		isDragging = false;
	};

	// Mouse events
	$container.on("mousedown", handleStart);
	$(document).on("mouseup", handleEnd);
	$(document).on("mousemove", handleMove);

	// Touch events
	$container.on("touchstart", handleStart);
	// Use the container for touchmove/touchend to ensure they fire even if finger leaves map
	$container.on("touchend", handleEnd);
	$container.on("touchmove", handleMove);
	$container.on("touchcancel", handleEnd); // Handle if touch is interrupted

	// Prevent click on map if it was a drag (for icons specifically)
	$container.on("click", (e) => {
		// If touchMoved was true, it means it was a drag, so prevent click propagation
		if (touchMoved) {
			e.preventDefault();
			e.stopImmediatePropagation();
		}
	});

	// Scroll (wheel) — allow horizontal scroll only
	$container.on("wheel", (e) => {
		const isMostlyHorizontal =
			Math.abs(e.originalEvent.deltaX) > Math.abs(e.originalEvent.deltaY);
		if (!isMostlyHorizontal) return;

		const deltaX = e.originalEvent.deltaX;
		const currentLeft = getLeft();
		setLeft(currentLeft - deltaX);

		e.preventDefault();
	});

	const $iconsG = $(".wp-block-madden-theme-bradenton-map svg #ICONS g");
	const $mapContainer = $(".wp-block-madden-theme-bradenton-map"); // Assuming this is the main container you want to observe for viewport

	if ($mapContainer.length) {
		const observerOptions = {
			root: null, // Use the viewport as the root
			rootMargin: "0px", // No extra margin around the root
			threshold: 0.8, // Trigger when 80% of the target element is visible
		};

		const observerCallback = (entries, observer) => {
			entries.forEach((entry) => {
				// Check if the target is intersecting and the animation hasn't played yet
				if (
					entry.isIntersecting &&
					entry.intersectionRatio >= 0.8 &&
					!hasViewedOnce &&
					!hasSwipedOnce
				) {
					$iconsG.addClass("bounce-scale");
					setTimeout(() => {
						$iconsG.removeClass("bounce-scale");
					}, 1000); // match the animation duration

					hasViewedOnce = true; // Set flag so it doesn't trigger again
					observer.unobserve(entry.target); // Stop observing once the animation has played
				}
			});
		};

		const observer = new IntersectionObserver(
			observerCallback,
			observerOptions
		);
		observer.observe($mapContainer[0]); // Observe the native DOM element
	}

	// Handle click on icon
	$iconsG.on("click", function (e) {
		if (touchMoved) {
			e.preventDefault();
			e.stopImmediatePropagation();
			return;
		}

		var stopId = $(this).attr("id");
		console.log(stopId);

		$(".bradenton-card").each(function () {
			$(this).removeClass("pop-in").addClass("pop-out");
		});

		let stopCard = $(".bradenton-card." + stopId);
		stopCard.removeClass("pop-out").addClass("pop-in");
		$(".bradenton-lightbox").addClass("bradenton-lightbox--on");
	});

	// Handle close click
	$(".bradenton-lightbox .close").on("click", function () {
		var $cityCard = $(this).closest(".bradenton-card");
		$cityCard.removeClass("pop-in").addClass("pop-out");
		$(".bradenton-lightbox").removeClass("bradenton-lightbox--on");

		// The bounce animation here is independent of the "first swipe" logic.
		// It's fine to keep it for closing the card if desired.
		$iconsG.addClass("bounce-scale");

		setTimeout(() => {
			$iconsG.removeClass("bounce-scale");
		}, 1000); // match the animation duration
	});
};
