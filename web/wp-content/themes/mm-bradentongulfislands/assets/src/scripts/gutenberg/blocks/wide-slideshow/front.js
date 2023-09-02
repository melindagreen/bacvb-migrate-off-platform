
jQuery(document).ready(function ($) {

      const heroBannerCarousel = new Swiper(".swiper-wideslideshow", {
        slidesPerView: 1,
        loop: true,
        autoplay: {
          delay: 5500,
          disableOnInteraction: true,
          pauseOnMouseEnter: true
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        }
      }); 
      changeInfoBlock();
      
      heroBannerCarousel.on('slideChangeTransitionEnd', ()=> {
        
        changeInfoBlock();
      });

      //Initialize Lazy load for duplicate slides
      MMLazyLoad.init({ loadElements: document.querySelectorAll("[data-load-type]") })

      function changeInfoBlock() {

        const infoItems = ['title', 'info'];
    
        infoItems.map((item)=>{
    
          let itemText = $('.swiper-wideslideshow .swiper-wrapper').find('.swiper-slide-active').data(item);
    
          if(itemText.length > 0) {
            
            $(`.hc-wrapper #infoblock-${item}`).removeClass('infoblock__item--hide').removeClass('infoblock__item--hide-notransition').text(itemText);
          }
          else {
    
            $(`.hc-wrapper #infoblock-${item}`).addClass('infoblock__item--hide-notransition').text(item);
          }
        });
    
        let buttonurl = $('.swiper-wideslideshow .swiper-wrapper').find('.swiper-slide-active').data('buttonurl');
        let titleText = $('.swiper-wideslideshow .swiper-wrapper').find('.swiper-slide-active').data(infoItems[0]);

        if(buttonurl == '#' || buttonurl == ' ' || buttonurl.length < 1) {
          $(`.hc-wrapper #infoblock-buttonurl`).addClass('infoblock__item--hide');
          $(`.hc-wrapper #infoblock-buttonurl a`).attr('href', '#');
          $(`.hc-wrapper #infoblock-buttonurl a`).attr('aria-label', 'click here to read more');
        }
        else if(buttonurl.length > 0) {
    
          $(`.hc-wrapper #infoblock-buttonurl`).removeClass('infoblock__item--hide');
          $(`.hc-wrapper #infoblock-buttonurl a`).attr('href', buttonurl);
          $(`.hc-wrapper #infoblock-buttonurl a`).attr('aria-label', titleText);
        }
      }
});