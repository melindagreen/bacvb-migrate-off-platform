// this is the front-end script for the block example-dynamic

(function($) {
    $(document).ready(function() {
        


      const element = $('.wp-block-mm-bradentongulfislands-quick-links');
      let isTucked = false;
      let lastScrollY = $(window).scrollTop();

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

    });
})(jQuery);