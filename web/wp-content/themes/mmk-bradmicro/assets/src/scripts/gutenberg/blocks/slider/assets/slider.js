//Import Swiper assets
import Swiper from "swiper/bundle";

export function initSwiperSliders(slider = null, options = {}) {
	if (slider && slider.swiper) {
		//slider.swiper.destroy();
	}

	//console.log(slider);
	//console.log(options);

	let adminSlider = options?.adminSlider ?? false;

	//pagination & nav elements
	let navWrapper = slider.nextElementSibling;

	let paginationEl;
	let navPrevEl;
	let navNextEl;
	let scrollbarEl;

	if (navWrapper) {
		paginationEl = navWrapper.querySelector(".swiper-pagination");
		navPrevEl = navWrapper.querySelector(".swiper-button-prev");
		navNextEl = navWrapper.querySelector(".swiper-button-next");
		scrollbarEl = navWrapper.querySelector(".swiper-scrollbar");
	}

	let setAutoHeight = slider.dataset.enablegridrows ? false : true;
	//autoheight hates the block editor
	if (adminSlider) {
		setAutoHeight =
			slider.dataset.sliderdirectiondesktop === "vertical" ? true : false;
	}

	let swiperslider = new Swiper(slider, {
		simulateTouch: adminSlider ? false : true,
		autoHeight: setAutoHeight,
		mousewheel: {
			enabled: slider.dataset.enablemousescroll ? true : false,
			releaseOnEdges: true,
		},

		//general settings
		initialSlide: slider.dataset.initialslide ?? 0,
		autoplay: slider.dataset.enableautoplay ? true : false,
		centeredSlides: slider.dataset.centeredslides ? true : false,
		effect: slider.dataset.effect ?? "slide",
		loop: slider.dataset.loop ? true : false,
		loopPreventsSliding: false,
		loopAdditionalSlides: 1,
		loopAddBlankSlides: true,
		freeMode: {
			enabled: slider.dataset.freemode ? true : false,
		},

		//navigation settings
		navigation: slider.dataset.enablearrownavigation
			? {
					nextEl: navNextEl,
					prevEl: navPrevEl,
			  }
			: false,
		pagination: slider.dataset.enablepagination
			? {
					el: paginationEl,
					type: "bullets",
					clickable: true,
			  }
			: false,
		scrollbar: slider.dataset.enablescrollbar
			? {
					el: scrollbarEl,
					draggable: true,
			  }
			: false,

		direction: slider.dataset.sliderdirectionmobile,

		//responsive settings
		grid: {
			rows: slider.dataset.enablegridrows
				? Number(slider.dataset.gridrowsmobile)
				: 1,
			fill: "row",
		},
		spaceBetween: slider.dataset.enablespacebetween
			? Number(slider.dataset.spacebetweenmobile)
			: 0,
		slidesPerGroup: slider.dataset.enableslidespergroup
			? Number(slider.dataset.slidespergroupmobile)
			: 1,
		slidesPerGroupAuto: slider.dataset.enableslidespergroupauto ? true : false,
		slidesPerView: slider.dataset.enableslidesperview
			? slider.dataset.enableslidesperviewauto
				? "auto"
				: Number(slider.dataset.slidesperviewmobile)
			: 1,
		breakpoints: {
			783: {
				direction: slider.dataset.sliderdirectiontablet,
				grid: {
					rows: slider.dataset.enablegridrows
						? Number(slider.dataset.gridrowstablet)
						: 1,
				},
				spaceBetween: slider.dataset.enablespacebetween
					? Number(slider.dataset.spacebetweentablet)
					: 0,
				slidesPerGroup: slider.dataset.enableslidespergroup
					? Number(slider.dataset.slidespergrouptablet)
					: 1,
				slidesPerView: slider.dataset.enableslidesperview
					? slider.dataset.enableslidesperviewauto
						? "auto"
						: Number(slider.dataset.slidesperviewtablet)
					: 1,
			},
			981: {
				direction: slider.dataset.sliderdirectiondesktop,
				grid: {
					rows: slider.dataset.enablegridrows
						? Number(slider.dataset.gridrowsdesktop)
						: 1,
				},
				spaceBetween: slider.dataset.enablespacebetween
					? Number(slider.dataset.spacebetweendesktop)
					: 0,
				slidesPerGroup: slider.dataset.enableslidespergroup
					? Number(slider.dataset.slidespergroupdesktop)
					: 1,
				slidesPerView: slider.dataset.enableslidesperview
					? slider.dataset.enableslidesperviewauto
						? "auto"
						: Number(slider.dataset.slidesperviewdesktop)
					: 1,
			},
		},

		//other settings
		observer: true,
		observeParents: true,
		observeSlideChildren: true,

		//used in block editor
		slideClass: options?.slideClass ?? "swiper-slide",
		wrapperClass: options?.wrapperClass ?? "swiper-wrapper",

		on: {
			slideChangeTransitionStart: function () {
				const slides = this.slides;
				const activeIndex = this.activeIndex;

				// Clear previous custom classes
				slides.forEach((slide) => {
					if (slide.classList) {
						slide.classList.remove("swiper-slide-prev-prev");
						slide.classList.remove("swiper-slide-next-next");
					}
				});

				// Get the second previous and second next slide indexes
				const prevPrevIndex = (activeIndex - 2 + slides.length) % slides.length;
				const nextNextIndex = (activeIndex + 2) % slides.length;

				// Add classes to the appropriate slides
				if (slides[prevPrevIndex]) {
					slides[prevPrevIndex].classList.add("swiper-slide-prev-prev");
				}
				if (slides[nextNextIndex]) {
					slides[nextNextIndex].classList.add("swiper-slide-next-next");
				}
			},
		},

		//slider events
		//on: {
		//  'observerUpdate': function() {
		//    this.update();
		//  }
		//}
	});

	//console.log(swiperslider.passedParams);
	//console.log(swiperslider.slides);

	return swiperslider;
}
