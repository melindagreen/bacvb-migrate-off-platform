(function ($) {
    // both functions from https://css-tricks.com/using-css-transitions-auto-dimensions/
    function collapseSection(element) {
        // get the height of the element's inner content, regardless of its actual size
        var sectionHeight = element.scrollHeight;

        // temporarily disable all css transitions
        var elementTransition = element.style.transition;
        element.style.transition = '';

        // on the next frame (as soon as the previous style change has taken effect),
        // explicitly set the element's height to its current pixel height, so we 
        // aren't transitioning out of 'auto'
        requestAnimationFrame(function () {
            element.style.height = sectionHeight + 'px';
            element.style.transition = elementTransition;

            // on the next frame (as soon as the previous style change has taken effect),
            // have the element transition to height: 0
            requestAnimationFrame(function () {
                element.style.height = 0 + 'px';
            });
        });

        // mark the section as "currently collapsed"
        $(element).removeClass('open');
    }

    // both functions from https://css-tricks.com/using-css-transitions-auto-dimensions/
    function expandSection(element) {
        // get the height of the element's inner content, regardless of its actual size
        var sectionHeight = element.scrollHeight;

        // have the element transition to the height of its inner content
        element.style.height = sectionHeight + 'px';

        // when the next css transition finishes (which should be the one we just triggered)
        element.addEventListener('transitionend', function (e) {
            // remove this event listener so it only gets triggered once
            element.removeEventListener('transitionend', arguments.callee);
        });

        // mark the section as "currently not collapsed"
        $(element).addClass('open');
    }

    $(document).ready(function () {
            

        $('.accordion__header').on('click', function () {
            var $click = $(this),
                $section = $click.closest('.wp-block-mm-bradentongulfislands-accordion-section'),
                $header = $click.find('.accordion-section__title'),
                content = $click.next('.accordion__body');

                $click.toggleClass('open');

            if (content.hasClass('open')) {
                if( 'Read Less' == $header.html() ){
                    $header.html('Read More');
                }
                collapseSection( content.get(0) );
            }
            else {
                if( 'Read More' == $header.html() ){
                    $header.html('Read Less');
                }
                expandSection(content.get(0) );
            }
        });
    });
})(jQuery);
