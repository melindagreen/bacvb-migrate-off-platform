
(function($) {
    $(document).ready(function() {
        
    $('.wp-block-mm-bradentongulfislands-bradenton-map svg .bradenton-stop').on('click', function(event) {

        var stopId = $(this).attr('id');
        console.log(stopId);

        // Hide all city cards 
        $('.ferry-stop-card').each(function() {
            $(this).removeClass('pop-in').addClass('pop-out');
        });

        // Show the city card 
        let stopCard = $('.ferry-stop-card.' + stopId);
        stopCard.removeClass('pop-out').addClass('pop-in');
        $('.ferry-stop-lightbox').addClass('ferry-stop-lightbox--on');
    });

    // Close on click
    $('.ferry-stop-lightbox .close').on('click', function() {
        var $cityCard = $(this).closest('.ferry-stop-card');
        $cityCard.removeClass('pop-in').addClass('pop-out');
        $('.ferry-stop-lightbox').removeClass('ferry-stop-lightbox--on');
    });
    })
})(jQuery);