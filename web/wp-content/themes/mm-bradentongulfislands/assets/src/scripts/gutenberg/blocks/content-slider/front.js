
jQuery(document).ready(function ($) {

  $('.mm-content-slider').each(function() {
    $(this).find('.swiper-wrapper > div').each(function() {
      $(this).wrap('<div class="swiper-slide"></div>');
    });

    if ($('.main').hasClass('main--bradensota')) {
      // Change the image sources for the buttons
      $('.content-slider-swiper-button-prev img').attr('src', '/wp-content/themes/mm-bradentongulfislands/assets/images/bradensota-prev.png');
      $('.content-slider-swiper-button-next img').attr('src', '/wp-content/themes/mm-bradentongulfislands/assets/images/bradensota-next.png');
    }

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
        nextEl: ".content-slider-swiper-button-next",
        prevEl: ".content-slider-swiper-button-prev",
        clickable: true
      },
      pagination: {
        el: '.content-sliderswiper-pagination',
        type: "fraction",
        clickable: true
      },
    });

  });
 
});