
jQuery(document).ready(function ($) {

  $('.mm-content-slider').each(function() {
    $(this).find('.swiper-wrapper > div').each(function() {
      $(this).wrap('<div class="swiper-slide"></div>');
    });
    new Swiper($(this)[0], {
      slidesPerView: 1,
      loop: true,
      effect: 'fade',
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
    });
  });
 
});