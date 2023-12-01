jQuery(document).ready(function($) {

    const swiper = new Swiper('.listingImgSwiper', {
      loop: true,
      pagination: {
        el: '.swiper-pagination',
      },
      autoHeight: true,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      }
    });
});
