
// (function($) {
//     $(document).ready(function() {
        
//     $('.wp-block-mm-bradentongulfislands-bradenton-map svg .bradenton-stop').on('click', function(event) {

//         var stopId = $(this).attr('id');
//         console.log(stopId);

//         // Hide all city cards 
//         $('.bradenton-card').each(function() {
//             $(this).removeClass('pop-in').addClass('pop-out');
//         });

//         // Show the city card 
//         let stopCard = $('.bradenton-card.' + stopId);
//         stopCard.removeClass('pop-out').addClass('pop-in');
//         $('.bradenton-lightbox').addClass('bradenton-lightbox--on');
//     });

//     // Close on click
//     $('.bradenton-lightbox .close').on('click', function() {
//         var $cityCard = $(this).closest('.bradenton-card');
//         $cityCard.removeClass('pop-in').addClass('pop-out');
//         $('.bradenton-lightbox').removeClass('bradenton-lightbox--on');
//     });
//     })
// })(jQuery);