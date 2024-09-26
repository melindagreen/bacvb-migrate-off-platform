// this is the front-end script for the block example-dynamic

(function($) {
  $(document).ready(function() {
      


    const element = $('.wp-block-mm-bradentongulfislands-quick-links');
    let isTucked = false;
    let lastScrollY = $(window).scrollTop();


    if($(window).width() > 768) {
      $(window).scroll(function() {
        const currentScrollY = $(window).scrollTop();

        if (currentScrollY > lastScrollY && !isTucked) {
          // Scrolling down, hide the element
          element.addClass('tuck');
          isTucked = true;
        } else if (currentScrollY < lastScrollY && isTucked) {
          // Scrolling up, show the element
          element.removeClass('tuck');
          isTucked = false;
        }

        lastScrollY = currentScrollY;
      });

    } else {

      element.find('.quick-links-heading').click(function(){
        element.find('.quick-link-items').slideToggle();
      });

      var stickyTop = element.offset().top - 88;

      $(window).scroll(function() {
        var windowTop = $(window).scrollTop();
        if (stickyTop < windowTop) {
          element.addClass('fixed-scroll');
        } else {
          element.removeClass('fixed-scroll');
        }
      });
    }

  });
})(jQuery);