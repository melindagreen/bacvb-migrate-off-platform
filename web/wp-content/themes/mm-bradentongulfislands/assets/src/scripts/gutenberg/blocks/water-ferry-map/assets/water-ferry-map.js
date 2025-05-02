import $ from 'jquery';

$(window).on("load", () => {
    initInteractiveMap();
});

export const initInteractiveMap = () => {

    console.log("Initializing interactive map...");

    $('.wp-block-mm-bradentongulfislands-water-ferry-map svg .water-ferry-stop').on('click', function(event) {
        console.log("Water ferry stop clicked.");

        var stopId = $(this).attr('id');
        console.log("Stop ID:", stopId);

        // Hide all city cards 
        $('.ferry-stop-card').each(function() {
            console.log("Hiding city card:", $(this).attr('class'));
            $(this).removeClass('pop-in').addClass('pop-out');
        });

        // Show the city card 
        let stopCard = $('.ferry-stop-card.' + stopId);
        console.log("Showing city card for stop ID:", stopId);
        stopCard.removeClass('pop-out').addClass('pop-in');
    });

    // Close on click
    $('.wp-block-mm-bradentongulfislands-water-ferry-map .ferry-stop-card .close').on('click', function() {
        console.log("Close button clicked.");

        var $cityCard = $(this).closest('.ferry-stop-card');
        console.log("Hiding city card:", $cityCard.attr('class'));
        $cityCard.removeClass('pop-in').addClass('pop-out');
    });

    console.log("Interactive map initialized.");
}