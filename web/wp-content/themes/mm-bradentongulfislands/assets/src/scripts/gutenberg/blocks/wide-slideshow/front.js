
jQuery(document).ready(function ($) {

  let images = [];
 
  $('.swiper-wideslideshow .swiper-wrapper .swiper-slide').each(function() {
      // Find the img element within the current div
      let imgElement = $(this).find('.item-slide-img');

      // Get the src attribute of the img element
      let imgSrc = imgElement.data('load-sm');

      // Check if imgSrc is not undefined and not already in the images array
      if (imgSrc && images.indexOf(imgSrc) === -1) {

          images.push(imgSrc);
      }
  });

      const thumbnailCarousel = new Swiper(".swiper-thumbnail-preview-slider--thumbnails", {
        slidesPerView: 4,
        spaceBetween: 10,
        freeMode: true,
        watchSlidesProgress: true
      }); 


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
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        },
        thumbs: {
          swiper: thumbnailCarousel,
        }
      }); 



      console.log(images);

      images.forEach(function(imageUrl) {
        let slide = $('<div class="swiper-slide"><img src="' + imageUrl + '" alt=""></div>');
        $('.swiper-thumbnail-preview-slider--thumbnails .swiper-wrapper').append(slide);
        });
      changeInfoBlock();
      console.log(heroBannerCarousel);
      
      heroBannerCarousel.on('slideChangeTransitionEnd', ()=> {
        
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