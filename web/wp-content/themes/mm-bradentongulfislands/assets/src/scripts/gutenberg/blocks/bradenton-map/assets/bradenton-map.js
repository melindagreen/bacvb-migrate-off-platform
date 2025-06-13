import $ from "jquery";
import Hammer from "hammerjs";
import svgPanZoom from "svg-pan-zoom";

$(window).on("load", () => {
	initInteractiveMap();
	initSvgZoom();
});

export const initInteractiveMap = () => {
	let hasScrolledToIcons = false;

	const $iconsG = $(
		".wp-block-mm-bradentongulfislands-bradenton-map svg #ICONS g"
	);

	// Scroll handler to detect when #ICONS enters the viewport
	$(window).on("scroll", function () {
		if (hasScrolledToIcons) return;

		const $icons = $(
			".wp-block-mm-bradentongulfislands-bradenton-map svg #ICONS"
		);
		const windowBottom = $(window).scrollTop() + $(window).height();
		const iconsTop = $icons.offset().top;

		if (windowBottom > iconsTop) {
			hasScrolledToIcons = true;

			// Add bounce animation
			$iconsG.addClass("bounce-scale");

			// Remove the animation class after it finishes to allow retrigger if needed
			setTimeout(() => {
				$iconsG.removeClass("bounce-scale");
			}, 1000); // match the animation duration
		}
	});

	// Handle click on icon
	$iconsG.on("click", function () {
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

		// Add bounce animation
		$iconsG.addClass("bounce-scale");

		// Remove the animation class after it finishes to allow retrigger if needed
		setTimeout(() => {
			$iconsG.removeClass("bounce-scale");
		}, 1000); // match the animation duration
	});
};

var pzInstance;
const initSvgZoom = () => {
	let svgMaps = document.querySelectorAll(
		".wp-block-mm-bradentongulfislands-bradenton-map"
	);
	svgMaps.forEach((map) => {
		panZoomInstance(map);
	});

	function panZoomInstance(map) {
		let svgMap = map.querySelector(".main-svg-wrapper > svg");
		if (!pzInstance) {
			pzInstance = svgPanZoom(svgMap, {
				zoomEnabled: true,
				fit: true,
				center: true,
				minZoom: 0.5,
				mouseWheelZoomEnabled: false,
				controlIconsEnabled: false,
				customEventsHandler: {
					// Halt all touch events
					haltEventListeners: [
						"touchstart",
						"touchend",
						"touchmove",
						"touchleave",
						"touchcancel",
					],
					init: function (options, x) {
						//initPositions();
						var { instance } = options,
							initialScale = 1,
							pannedX = 0,
							pannedY = 0;

						// Init hammer
						// Listen only for pointer and touch events
						let svghammer = new Hammer(options.svgElement, {
							inputClass: Hammer.SUPPORT_POINTER_EVENTS
								? Hammer.PointerEventInput
								: Hammer.TouchInput,
						});

						// Enable pinch
						svghammer.get("pinch").set({
							enable: true,
						});

						// Handle double tap
						svghammer.on("doubletap", function (ev) {
							instance.zoomIn();
						});

						// Handle pan
						svghammer.on("panstart panmove", function (ev) {
							// On pan start reset panned variables
							if (ev.type === "panstart") {
								pannedX = 0;
								pannedY = 0;
							}

							// Pan only the difference
							instance.panBy({
								x: ev.deltaX - pannedX,
								y: ev.deltaY - pannedY,
							});
							pannedX = ev.deltaX;
							pannedY = ev.deltaY;
						});

						// Handle pinch
						svghammer.on("pinchstart pinchmove", function (ev) {
							// On pinch start remember initial zoom
							if (ev.type === "pinchstart") {
								initialScale = instance.getZoom();
								instance.zoomAtPoint(initialScale * ev.scale, {
									x: ev.center.x,
									y: ev.center.y,
								});
							}
							instance.zoomAtPoint(initialScale * ev.scale, {
								x: ev.center.x,
								y: ev.center.y,
							});
						});

						// Prevent moving the page on some devices when panning over SVG
						options.svgElement.addEventListener("touchmove", function (e) {
							e.preventDefault();
						});
					},
					destroy: function () {
						svghammer.destroy();
					},
				},
			});

			// Ensure the view is resized and adjusted properly when the instance is initialized
			pzInstance.updateBBox();
			pzInstance.resize();
			if (window.innerWidth < 769) {
				// Instantly show zoom visually
				svgMap.style.transform = "scale(1)";
				pzInstance.zoom(2); // Set zoom directly to 200%
				pzInstance.center();
			} else {
				pzInstance.fit();
				pzInstance.center();
			}
		} else {
			// Update the svg element of the existing pzInstance in case it's called again
			pzInstance.update(svgMap);
		}

		// Add the event listener for window resize
		window.addEventListener("resize", function () {
			if (pzInstance) {
				pzInstance.updateBBox(); // Update the bounding box on resize
				pzInstance.resize(); // Ensure the view is resized properly

				if (window.innerWidth < 769) {
					// Instantly show zoom visually
					svgMap.style.transform = "scale(1)";
					pzInstance.zoom(2); // Set zoom directly to 200%
					pzInstance.center();
				} else {
					pzInstance.fit();
					pzInstance.center();
				}
			}
		});

		document.getElementById("zoom-in").addEventListener("click", function (ev) {
			ev.preventDefault();
			pzInstance.zoomIn();
		});
		document
			.getElementById("zoom-out")
			.addEventListener("click", function (ev) {
				ev.preventDefault();
				pzInstance.zoomOut();
			});
	}
};
