(function($) {
    $(document).ready(function() {
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
            //Select all of our slider blocks
            sliders = document.querySelectorAll(sliderBlockSelector);
          } else {
            //in admin, we want to destroy and reinit the slider when the options/content changes.
            let currentSlider = document.querySelector(adminSlider);
            if (currentSlider.swiper) {
              currentSlider.swiper.destroy();
            }
            sliders = document.querySelectorAll(adminSlider);
          }
        
          sliders.forEach((slider, index) => {
        
            if (!slider) { return; }
        
            slider.parentElement.classList.add('slider-'+index);
            let setAutoHeight = slider.dataset.enablegridrows ? false : true;
        
            //autoheight hates the block editor
            if (adminSlider) {
              setAutoHeight = slider.dataset.sliderdirectiondesktop === 'vertical' ? true : false;
            }
            
            let swiperslider = new Swiper(slider, {

                cardsEffect: {
                    perSlideOffset: 65,
                    perSlideRotate: 6,
                    slideShadows: false,
                },

              loopAdditionalSlides: 2,
        
              simulateTouch: adminSlider ? false : true,
              autoHeight: setAutoHeight,
              mousewheel: slider.dataset.enablemousescroll ? true : false,
              
              //general settings
              autoplay: slider.dataset.enableautoplay ? true : false,
              centeredSlides: slider.dataset.centeredslides ? true : false,
              effect: slider.dataset.effect ?? 'slide',
              loop: slider.dataset.loop ? true : false,
            //   loopPreventsSliding: false,
            //   loopAdditionalSlides: 1,
            //   loopAddBlankSlides: true,
              freeMode: {
                enabled: slider.dataset.freemode ? true : false,
              },
        
              //navigation settings
              navigation: slider.dataset.enablearrownavigation ? {
                nextEl: '.slider-'+index+' .swiper-button-next',
                prevEl: '.slider-'+index+' .swiper-button-prev',
              } : false,
              pagination: slider.dataset.enablepagination ? {
                el: '.slider-'+index+' .swiper-pagination',
                type: 'bullets',
                clickable: true
              } : false,
              scrollbar: slider.dataset.enablescrollbar ? {
                el: '.slider-'+index+' .swiper-scrollbar',
                draggable: true,
                dragSize: 70
              } : false,
        
              direction: slider.dataset.sliderdirectionmobile,
              parallax: {
                enabled: true
              },
        
              //responsive settings
              grid: {
                rows: slider.dataset.enablegridrows ? Number(slider.dataset.gridrowsmobile) : 1,
                fill: 'row'
              },
              spaceBetween: slider.dataset.enablespacebetween ? Number(slider.dataset.spacebetweenmobile) : 0,
              slidesPerGroup: slider.dataset.enableslidespergroup ? Number(slider.dataset.slidespergroupmobile) : 1,
              slidesPerGroupAuto: slider.dataset.enableslidespergroupauto ? true : false,
              slidesPerView: slider.dataset.enableslidesperview ? slider.dataset.enableslidesperviewauto ? 'auto' : Number(slider.dataset.slidesperviewmobile) : 1,
              breakpoints: {
                769: {
                  direction: slider.dataset.sliderdirectiontablet,
                  grid: {
                    rows: slider.dataset.enablegridrows ? Number(slider.dataset.gridrowstablet) : 1
                  },
                  spaceBetween: slider.dataset.enablespacebetween ? Number(slider.dataset.spacebetweentablet) : 0,
                  slidesPerGroup: slider.dataset.enableslidespergroup ? Number(slider.dataset.slidespergrouptablet) : 1,
                  slidesPerView: slider.dataset.enableslidesperview ? slider.dataset.enableslidesperviewauto ? 'auto' : Number(slider.dataset.slidesperviewtablet) : 1,
                },
                1200: {
                  direction: slider.dataset.sliderdirectiondesktop,
                  grid: {
                    rows: slider.dataset.enablegridrows ? Number(slider.dataset.gridrowsdesktop) : 1
                  },
                  spaceBetween: slider.dataset.enablespacebetween ? Number(slider.dataset.spacebetweendesktop) : 0,
                  slidesPerGroup: slider.dataset.enableslidespergroup ? Number(slider.dataset.slidespergroupdesktop) : 1,
                  slidesPerView: slider.dataset.enableslidesperview ? slider.dataset.enableslidesperviewauto ? 'auto' : Number(slider.dataset.slidesperviewdesktop) : 1,
                }
              },
        
              //other settings
              //observer: true,
              //observeParents: true,
              //observeSlideChildren: true,
        
              //used in block editor
              slideClass: slideClass ?? 'swiper-slide',
              wrapperClass: wrapperClass ?? 'swiper-wrapper',
        
              //slider events
              //on: {
              //  'observerUpdate': function() {
              //    this.update();
              //  }
              //}
            });
        
            //console.log(swiperslider.passedParams);
            //console.log(swiperslider.slides);
            swiperInstances[index] = swiperslider;
          });
        
          //console.log(swiperInstances);
        
    });
})(jQuery);