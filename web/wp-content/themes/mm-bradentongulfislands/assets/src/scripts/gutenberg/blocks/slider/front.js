(function ($) {
  $(document).ready(function () {
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

      return new Swiper(slider, {
        effect: effect,
        coverflowEffect: {
          rotate: 0,
          stretch: 0,
          depth: 800,
          modifier: 1,
          slideShadows: false,
        },
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
        loopAddBlankSlides: true,
        freeMode: {
          enabled: slider.dataset.freemode ? true : false,
        },

        navigation: slider.dataset.enablearrownavigation ? {
          nextEl: '.slider-' + index + ' .swiper-button-next',
          prevEl: '.slider-' + index + ' .swiper-button-prev',
        } : false,

        pagination: slider.dataset.enablepagination ? {
          el: '.slider-' + index + ' .swiper-pagination',
          type: 'bullets',
          clickable: true
        } : false,

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
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        sliders.forEach((slider, index) => {
          if (swiperInstances[index]) {
            swiperInstances[index].destroy(true, true);
          }
          swiperInstances[index] = createSwiper(slider, index);
        });
      }, 300);
    });

    // Update info block on slide change
    sliders.forEach((slider, index) => {
      if (swiperInstances[index]) {
      swiperInstances[index].on('slideChangeTransitionEnd', () => {
        changeInfoBlock();
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
  
      let buttonurl = $('.wp-block-mm-bradentongulfislands-slider .swiper-wrapper').find('.swiper-slide-active').data('link') || '';
      let titleText = $('.wp-block-mm-bradentongulfislands-slider .swiper-wrapper').find('.swiper-slide-active').data(infoItems[0]);

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
  });
})(jQuery);
