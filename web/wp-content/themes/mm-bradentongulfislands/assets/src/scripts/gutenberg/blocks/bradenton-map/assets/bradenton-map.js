import $ from 'jquery';

$(window).on("load", () => {
    initInteractiveMap();
});

export const initInteractiveMap = () => {
    let hasScrolledToIcons = false;

    const $iconsG = $('.wp-block-mm-bradentongulfislands-bradenton-map svg #ICONS g');

    // Scroll handler to detect when #ICONS enters the viewport
    $(window).on('scroll', function () {
        if (hasScrolledToIcons) return; // only do once

        const $icons = $('.wp-block-mm-bradentongulfislands-bradenton-map svg #ICONS');
        const windowBottom = $(window).scrollTop() + $(window).height();
        const iconsTop = $icons.offset().top;

        if (windowBottom > iconsTop) {
            hasScrolledToIcons = true;
            // Scale all #ICONS g elements
            $iconsG.css('transform', 'scale(1.05)');
            $iconsG.css('transition', 'transform 0.3s ease');
        }
    });

    // Handle click on icon
    $iconsG.on('click', function () {
        var stopId = $(this).attr('id');
        console.log(stopId);

        // Hide all city cards 
        $('.bradenton-card').each(function () {
            $(this).removeClass('pop-in').addClass('pop-out');
        });

        // Show the city card 
        let stopCard = $('.bradenton-card.' + stopId);
        stopCard.removeClass('pop-out').addClass('pop-in');
        $('.bradenton-lightbox').addClass('bradenton-lightbox--on');
    });

    // Handle close click
    $('.bradenton-lightbox .close').on('click', function () {
        var $cityCard = $(this).closest('.bradenton-card');
        $cityCard.removeClass('pop-in').addClass('pop-out');
        $('.bradenton-lightbox').removeClass('bradenton-lightbox--on');

        // Reset scale
        $iconsG.css('transform', 'scale(1)');
    });
};
