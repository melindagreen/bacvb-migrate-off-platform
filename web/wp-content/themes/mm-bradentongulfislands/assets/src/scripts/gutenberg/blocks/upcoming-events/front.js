// this is the front-end script for the block upcomingevents

(function($) {
    $(document).ready(function() {

    const eventsCarousel = new Swiper(".swiper-upcomingevents", {
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
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        }
    }); 

    changeInfoBlock();

    eventsCarousel.on('slideChangeTransitionEnd', ()=> {
        changeInfoBlock();
    });

      //Initialize Lazy load for duplicate slides
      MMLazyLoad.init({ loadElements: document.querySelectorAll("[data-load-type]") })

      function changeInfoBlock() {

        const infoItems = ['title', 'info', 'buttontext'];
    
        infoItems.map((item)=>{
          
          let activeItem = $(".swiper-wrapper .swiper-slide-active");
          let itemText = $('.swiper-wideslideshow .swiper-wrapper').find('.swiper-slide-active').data(item);

          if (activeItem.index() % 2 === 0) {
            $('.bc-infoblock').addClass('bc-infoblock--purple');
          }
          else {
            $('.bc-infoblock').removeClass('bc-infoblock--purple');
          }
    
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
})(jQuery);