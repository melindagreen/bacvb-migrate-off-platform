// this is the front-end script for the block example-static

(function($) {

    const $ctaBubble = $('.wp-block-mm-bradentongulfislands-cta-bubble');
    const $closeButton = $('.wp-block-mm-bradentongulfislands-cta-bubble .close');

    const scrollOffset = 150; // Start animation 150px later than the viewport

  function isInViewport($element) {
    const elementTop = $element.offset().top + scrollOffset;
    const elementBottom = elementTop + $element.outerHeight();

    const viewportTop = $(window).scrollTop();
    const viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
  }

  // On scroll, check if the element is in the viewport
  $(window).on('scroll', function () {
    if (isInViewport($ctaBubble)) {
      $ctaBubble.addClass('visible');
    }
  });

  // Close button handler
  $closeButton.on('click', function () {
    $ctaBubble.removeClass('visible').addClass('hidden');
  });
})(jQuery);