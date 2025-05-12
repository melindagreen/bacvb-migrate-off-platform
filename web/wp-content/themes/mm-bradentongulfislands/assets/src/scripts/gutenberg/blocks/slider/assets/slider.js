import $ from 'jquery';
//Import Swiper assets
import Swiper from 'swiper/bundle';

window.addEventListener('DOMContentLoaded', () => {
  if ($('#editor').length === 0) {
	  initSwiperSliders();
  }
});

const sliderBlockSelector = ".wp-block-mm-bradentongulfislands-slider .swiper";
const swiperInstances = [];
const adminSlider = null;

const wrapperClass = null;
const slideClass = null;

let sliders;

export const initSwiperSliders = (adminSlider = null, wrapperClass, slideClass) => {


  if (!adminSlider) {
    sliders = document.querySelectorAll(sliderBlockSelector);
  } else {
    let currentSlider = document.querySelector(adminSlider);
    if (currentSlider.swiper) {
      currentSlider.swiper.destroy();
    }
    sliders = document.querySelectorAll(adminSlider);
  }

  const createSwiper = (slider, index) => {
    const effect = slider.dataset.effect ?? 'slide';
    const screenWidth = window.innerWidth;
    let initialSlide = 0;

    const MIN_SLIDES = 8;
    const slideWrapper = slider.querySelector('.swiper-wrapper');
    const originalSlides = slider.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)');
    let neededClones = 0;

    if (originalSlides.length > 0 && originalSlides.length < MIN_SLIDES && slider.dataset.loop && effect === 'cards') {
     
      neededClones = MIN_SLIDES - originalSlides.length;
      for (let i = 0; i < neededClones; i++) {
        const clone = originalSlides[i % originalSlides.length].cloneNode(true);
        clone.classList.add('swiper-slide-duplicate');
        slideWrapper.appendChild(clone);
      }
    }

    let cardsEffect;
    if (effect === 'cards') {
      cardsEffect = {
        perSlideOffset: screenWidth >= 769 ? 65 : 8,
        perSlideRotate: screenWidth >= 769 ? 6 : 2,
        slideShadows: screenWidth >= 769 ? false : true
      };
      if (screenWidth > 769) {
        initialSlide = 1;
      }
    }

    let paginationType = clonedSlides ? {
      el: '.slider-' + index + ' .swiper-pagination',
      type: 'bullets',
      clickable: true,
      renderBullet: function (index, className) {
        const slide = slider.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)')[index];
        if (slide) {
          return `<span class="${className}"></span>`;
        }
        return ''; // Exclude cloned slides
      }
    } : {
      el: '.slider-' + index + ' .swiper-pagination',
      type: 'bullets',
      clickable: true
    };

    return new Swiper(slider, {
      effect: effect,
      cardsEffect: cardsEffect,
      initialSlide: initialSlide,

      simulateTouch: adminSlider ? false : true,
      autoHeight: slider.dataset.enablegridrows ? false : true,
      mousewheel: slider.dataset.enablemousescroll ? true : false,

      autoplay: slider.dataset.enableautoplay ? true : false,
      centeredSlides: slider.dataset.centeredslides ? true : false,
      loop: slider.dataset.loop ? true : false,
      loopPreventsSliding: false,
      loopAdditionalSlides: 1,
      loopAddBlankSlides: neededClones ? true : false,
      freeMode: {
        enabled: slider.dataset.freemode ? true : false,
      },

      navigation: slider.dataset.enablearrownavigation ? {
        nextEl: '.slider-' + index + ' .swiper-button-next',
        prevEl: '.slider-' + index + ' .swiper-button-prev',
      } : false,

      pagination: slider.dataset.enablepagination ? paginationType : false,

      scrollbar: slider.dataset.enablescrollbar ? {
        el: '.slider-' + index + ' .swiper-scrollbar',
        draggable: true,
        dragSize: 70
      } : false,

      direction: slider.dataset.sliderdirectionmobile,
      parallax: {
        enabled: true
      },

      grid: {
        rows: slider.dataset.enablegridrows ? Number(slider.dataset.gridrowsmobile) : 1,
        fill: 'row'
      },
      spaceBetween: slider.dataset.enablespacebetween ? Number(slider.dataset.spacebetweenmobile) : 0,
      slidesPerGroup: slider.dataset.enableslidespergroup ? Number(slider.dataset.slidespergroupmobile) : 1,
      slidesPerGroupAuto: slider.dataset.enableslidespergroupauto ? true : false,
      slidesPerView: slider.dataset.enableslidesperview ?
        (slider.dataset.enableslidesperviewauto ? 'auto' : Number(slider.dataset.slidesperviewmobile)) : 1,

      breakpoints: {
        769: {
          direction: slider.dataset.sliderdirectiontablet,
          grid: {
            rows: slider.dataset.enablegridrows ? Number(slider.dataset.gridrowstablet) : 1
          },
          spaceBetween: slider.dataset.enablespacebetween ? Number(slider.dataset.spacebetweentablet) : 0,
          slidesPerGroup: slider.dataset.enableslidespergroup ? Number(slider.dataset.slidespergrouptablet) : 1,
          slidesPerView: slider.dataset.enableslidesperview ?
            (slider.dataset.enableslidesperviewauto ? 'auto' : Number(slider.dataset.slidesperviewtablet)) : 1
        },
        1200: {
          direction: slider.dataset.sliderdirectiondesktop,
          grid: {
            rows: slider.dataset.enablegridrows ? Number(slider.dataset.gridrowsdesktop) : 1
          },
          spaceBetween: slider.dataset.enablespacebetween ? Number(slider.dataset.spacebetweendesktop) : 0,
          slidesPerGroup: slider.dataset.enableslidespergroup ? Number(slider.dataset.slidespergroupdesktop) : 1,
          slidesPerView: slider.dataset.enableslidesperview ?
            (slider.dataset.enableslidesperviewauto ? 'auto' : Number(slider.dataset.slidesperviewdesktop)) : 1
        }
      },

      slideClass: slideClass ?? 'swiper-slide',
      wrapperClass: wrapperClass ?? 'swiper-wrapper',
    });
  };

  sliders.forEach((slider, index) => {
    if (!slider) return;

    slider.parentElement.classList.add('slider-' + index);

    swiperInstances[index] = createSwiper(slider, index);
  });

  // Reinitialize on resize
  let resizeTimer;
  // window.addEventListener('resize', function () {
  //   clearTimeout(resizeTimer);
  //   resizeTimer = setTimeout(() => {
  //     sliders.forEach((slider, index) => {
  //       if (swiperInstances[index]) {
  //         swiperInstances[index].destroy(true, true);
  //       }
  //       swiperInstances[index] = createSwiper(slider, index);
  //     });
  //     // Reinitialize the info block on resize
  //     changeInfoBlock();
  //   }, 300);
  // });

  
  // Update info block on slide change
  sliders.forEach((slider, index) => {
    if (swiperInstances[index]) {
    swiperInstances[index].on('slideChangeTransitionEnd', () => {
      changeInfoBlock();

      const stickers = document.querySelectorAll('.sticker');

      stickers.forEach(sticker => {
        // Reset animation if already applied
        sticker.classList.remove('animate-wiggle');
        void sticker.offsetWidth; // Force reflow
        sticker.classList.add('animate-wiggle');

        // Remove class after animation ends to allow retrigger
        setTimeout(() => {
          sticker.classList.remove('animate-wiggle');
        }, 1600); 
      });
    });
    }
  });

  // Initial call to set the info block
  changeInfoBlock();

  /**
    * Changes content of slideshow 
    */
  function changeInfoBlock() {

    const infoItems = ['title', 'excerpt', 'buttontext'];

    infoItems.map((item)=>{

      let activeItem = $(".wp-block-mm-bradentongulfislands-slider .swiper-slide-active");
      let itemText = $('.wp-block-mm-bradentongulfislands-slider .swiper-wrapper').find('.swiper-slide-active article').data(item) || '';

      if(itemText.length > 0) {
        
        $(`.slider-info-box #infoblock-${item}`).removeClass('infoblock__item--hide').removeClass('infoblock__item--hide-notransition').text(itemText);
      }
      else {

        $(`.slider-info-box #infoblock-${item}`).addClass('infoblock__item--hide-notransition').text(item);
      }
    });

    let buttonurl = $('.wp-block-mm-bradentongulfislands-slider .swiper-wrapper').find('.swiper-slide-active article').data('link') || '';
    let titleText = $('.wp-block-mm-bradentongulfislands-slider .swiper-wrapper').find('.swiper-slide-active article').data(infoItems[0]);

    if(buttonurl == '#' || buttonurl == ' ' || buttonurl.length < 1) {
      $(`.slider-info-box #infoblock-buttonurl`).addClass('infoblock__item--hide');
      $(`.slider-info-box #infoblock-buttonurl`).attr('href', '#');
      $(`.slider-info-box #infoblock-buttonurl`).attr('aria-label', 'click here to read more');
    }
    else if(buttonurl.length > 0) {  
      $(`.slider-info-box #infoblock-buttonurl`).removeClass('infoblock__item--hide');
      $(`.slider-info-box #infoblock-buttonurl`).attr('href', buttonurl);
      $(`.slider-info-box #infoblock-buttonurl`).attr('aria-label', titleText);
    }
  }
}
