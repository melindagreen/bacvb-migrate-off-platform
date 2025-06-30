import $ from "jquery";

$(window).on("load", () => {
    initInteractiveMap();
});

export const initInteractiveMap = () => {
    const $container = $('.mapViewArea');
    const $img = $container.find('img');
    const $svg = $container.find('svg');

    let isDragging = false;
    let startX = 0;
    let startLeft = 0;

    let minLeft; // Declare minLeft here
    const maxLeft = 0;    // Max pan limit (px)

    // Function to calculate and set minLeft based on current dimensions
    const setMinLeft = () => {
        const containerWidth = $container.width();
        const imgWidth = $img.width(); // Assuming img and svg have the same effective width for panning
        minLeft = -(imgWidth - containerWidth);
        // Ensure minLeft doesn't become positive if imgWidth is less than containerWidth
        if (minLeft > 0) {
            minLeft = 0;
        }
    };

    // Initial calculation of minLeft
    setMinLeft();

    // Recalculate minLeft on window resize
    $(window).on('resize', setMinLeft);


    function setLeft(x) {
        const clampedX = Math.min(maxLeft, Math.max(minLeft, x));
        $img.css('left', clampedX + 'px');
        $svg.css('left', clampedX + 'px');
    }

    function getLeft() {
        return parseFloat($img.css('left')) || 0;
    }

    // Mouse drag
    $container.on('mousedown', (e) => {
        isDragging = true;
        startX = e.pageX;
        startLeft = getLeft();
        e.preventDefault();
    });

    $(document).on('mouseup', () => {
        isDragging = false;
    });

    $(document).on('mousemove', (e) => {
        if (!isDragging) return;
        const deltaX = e.pageX - startX;
        setLeft(startLeft + deltaX);
    });

    // Scroll (wheel) — allow horizontal scroll only
    $container.on('wheel', (e) => {
        // Use deltaX only, block if vertical scroll
        const isMostlyHorizontal = Math.abs(e.originalEvent.deltaX) > Math.abs(e.originalEvent.deltaY);
        if (!isMostlyHorizontal) return;

        const deltaX = e.originalEvent.deltaX;
        const currentLeft = getLeft();
        setLeft(currentLeft - deltaX);

        e.preventDefault();
    });

    let hasScrolledToIcons = false;

    const $iconsG = $(
        ".wp-block-mm-bradentongulfislands-bradenton-map svg #ICONS g"
    );

    // Scroll handler to detect when #ICONS enters the viewport
    $(window).on("scroll", function () {
        if (hasScrolledToIcons) return;

        const $icons = $(
            ".wp-block-mm-bradentongulfislands-bradenton-map svg #ICONS"
        );
        const windowBottom = $(window).scrollTop() + $(window).height();
        const iconsTop = $icons.offset().top;

        if (windowBottom > iconsTop) {
            hasScrolledToIcons = true;

            // Add bounce animation
            $iconsG.addClass("bounce-scale");

            // Remove the animation class after it finishes to allow retrigger if needed
            setTimeout(() => {
                $iconsG.removeClass("bounce-scale");
            }, 1000); // match the animation duration
        }
    });

    // Handle click on icon
    $iconsG.on("click", function () {
        var stopId = $(this).attr("id");
        console.log(stopId);

        $(".bradenton-card").each(function () {
            $(this).removeClass("pop-in").addClass("pop-out");
        });

        let stopCard = $(".bradenton-card." + stopId);
        stopCard.removeClass("pop-out").addClass("pop-in");
        $(".bradenton-lightbox").addClass("bradenton-lightbox--on");
    });

    // Handle close click
    $(".bradenton-lightbox .close").on("click", function () {
        var $cityCard = $(this).closest(".bradenton-card");
        $cityCard.removeClass("pop-in").addClass("pop-out");
        $(".bradenton-lightbox").removeClass("bradenton-lightbox--on");

        // Add bounce animation
        $iconsG.addClass("bounce-scale");

        // Remove the animation class after it finishes to allow retrigger if needed
        setTimeout(() => {
            $iconsG.removeClass("bounce-scale");
        }, 1000); // match the animation duration
    });
};