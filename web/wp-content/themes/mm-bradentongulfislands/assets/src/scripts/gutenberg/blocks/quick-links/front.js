(function($) {
  $(document).ready(function() {

    function quickLinks() {

      const element = $('.wp-block-mm-bradentongulfislands-quick-links');
      let isTucked = false;
      let lastScrollY = $(window).scrollTop();

      // Clear previous scroll and click event handlers to avoid conflicts
      $(window).off('scroll');
      element.find('.quick-links-heading').off('click');

      // Desktop behavior
      if ($(window).width() > 768) {

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
        // Mobile behavior

        element.find('.quick-links-heading').click(function() {
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

    }

    quickLinks();

    // Listen for window resize and reset the behavior accordingly
    $(window).resize(function() {
      quickLinks();
    });
  });
})(jQuery);
