// this is the front-end script for the block example-static

(function($) {

    /**
       * Toggles Ligthbox
       */
    function toggleLightbox() {

        const overlay = $('.wideslideshow-ligthbox-overlay');
        const activeSlide = $('.swiper-wideslideshow .swiper-wrapper').find('.swiper-slide-active');
        const title = activeSlide.data('lightboxtitle');
        const buttontext = activeSlide.data('lightboxbuttontext');
        const subtitle = activeSlide.data('lightboxsubtitle');
        const embedSrc = activeSlide.data('lightboxembedsrc');
        const buttonUrl = activeSlide.data('buttonurl')

        overlay.removeClass('wideslideshow-ligthbox-overlay--hide');
      }
})(jQuery);