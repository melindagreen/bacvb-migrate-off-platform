// this is the front-end script for the block example-static

(function($) {

        // Move lightbox element to be a direct child of <body>
        $('.wp-block-mm-bradentongulfislands-lightbox-card__lightbox').each(function () {
            $(this).appendTo('body');
        });
        

        // Function to open the lightbox
  $('.wp-block-mm-bradentongulfislands-lightbox-card__card').on('click', function () {
    const $card = $(this).closest('.wp-block-mm-bradentongulfislands-lightbox-card__card');
    const lightboxSelector = $card.data('lightbox-selector');
    const $lightbox = $(`.${lightboxSelector}`);



    // Show the lightbox
    $lightbox.removeClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox--hide')
             .addClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox--show');
  });

  // Close the lightbox
  $('.lightbox-card-overlay__close').on('click', function () {
    $(this).closest('.wp-block-mm-bradentongulfislands-lightbox-card__lightbox')
           .removeClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox--show')
           .addClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox--hide');
  });

  // Close the lightbox when clicking outside the content area
  $('.wp-block-mm-bradentongulfislands-lightbox-card__lightbox').on('click', function (e) {
    if ($(e.target).hasClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox')) {
      $(this).removeClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox--show')
             .addClass('wp-block-mm-bradentongulfislands-lightbox-card__lightbox--hide');
    }
  });

})(jQuery);