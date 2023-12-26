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
          nextEl: ".ue-swiper-button-next",
          prevEl: ".ue-swiper-button-prev",
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        }
    }); 

    changeUEInfoBlock();

    eventsCarousel.on('slideChangeTransitionEnd', ()=> {
        changeUEInfoBlock();
    });

      //Initialize Lazy load for duplicate slides
      MMLazyLoad.init({ loadElements: document.querySelectorAll("[data-load-type]") })

      function changeUEInfoBlock() {

        const infoItems = ['title', 'info', 'buttontext'];
    
        infoItems.map((item)=>{
          
          let activeItem = $(".swiper-upcomingevents .swiper-slide-active");
          let itemText = $('.swiper-upcomingevents .swiper-wrapper').find('.swiper-slide-active').data(item);

          if (activeItem.index() % 2 === 0) {
            $('.ue-wrapper .bc-infoblock').addClass('bc-infoblock--purple');
          }
          else {
            $('.ue-wrapper .bc-infoblock').removeClass('bc-infoblock--purple');
          }
    
          if(itemText.length > 0) {
            
            $(`.ue-wrapper #infoblock-${item}`).removeClass('infoblock__item--hide').removeClass('infoblock__item--hide-notransition').text(itemText);
          }
          else {
    
            $(`.ue-wrapper #infoblock-${item}`).addClass('infoblock__item--hide-notransition').text(item);
          }
        });
    
        let buttonurl = $('.swiper-upcomingevents .swiper-wrapper').find('.swiper-slide-active').data('buttonurl');
        let titleText = $('.swiper-upcomingevents .swiper-wrapper').find('.swiper-slide-active').data(infoItems[0]);

        if(buttonurl == '#' || buttonurl == ' ' || buttonurl.length < 1) {
          $(`.ue-wrapper #infoblock-buttonurl`).addClass('infoblock__item--hide');
          $(`.ue-wrapper #infoblock-buttonurl a`).attr('href', '#');
          $(`.ue-wrapper #infoblock-buttonurl a`).attr('aria-label', 'click here to read more');
        }
        else if(buttonurl.length > 0) {
    
          $(`.ue-wrapper #infoblock-buttonurl`).removeClass('infoblock__item--hide');
          $(`.ue-wrapper #infoblock-buttonurl a`).attr('href', buttonurl);
          $(`.ue-wrapper #infoblock-buttonurl a`).attr('aria-label', titleText);
        }
      }

    });
})(jQuery);