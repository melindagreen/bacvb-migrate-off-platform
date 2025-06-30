(function($) {
    $(document).ready(function() {
        console.log('Document is ready.');

        $('.wp-block-mm-bradentongulfislands-water-ferry-map svg .water-ferry-stop').on('click', function(event) {
            console.log('Water ferry stop clicked.');

            var stopId = $(this).attr('id');
            console.log('Stop ID:', stopId);

            // Hide all city cards 
            $('.ferry-stop-card').each(function() {
                console.log('Hiding card:', $(this).attr('class'));
                $(this).removeClass('pop-in').addClass('pop-out');
            });

            // Show the city card 
            let stopCard = $('.ferry-stop-card.' + stopId);
            console.log('Showing card for stop ID:', stopId);
            stopCard.removeClass('pop-out').addClass('pop-in');
            $('.ferry-stop-lightbox').addClass('ferry-stop-lightbox--on');
            console.log('Lightbox turned on.');
        });

        // Close on click
        $('.ferry-stop-lightbox .close').on('click', function() {
            console.log('Close button clicked.');

            var $cityCard = $(this).closest('.ferry-stop-card');
            console.log('Closing card:', $cityCard.attr('class'));
            $cityCard.removeClass('pop-in').addClass('pop-out');
            $('.ferry-stop-lightbox').removeClass('ferry-stop-lightbox--on');
            console.log('Lightbox turned off.');
        });
    });
})(jQuery);