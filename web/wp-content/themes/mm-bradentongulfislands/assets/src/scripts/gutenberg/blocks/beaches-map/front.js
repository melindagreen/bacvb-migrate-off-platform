
(function($) {
    $(document).ready(function() {

        // $('.location_pin').click(function(){
        //     $('.beach_content_overlay').fadeIn();
        //     $('body').css({
        //         'overflow': 'hidden',
        //         'height': '180%'
        //     });


        //     // show the matching beach content selected
        //     var beach = $(this).attr('id');

        //     $('.selectBeach').removeClass('show');
        //     $('.selectBeach').each(function(){
        //         if($(this).hasClass(beach)) {
        //             $(this).addClass('show');
        //         }
        //     });
        // });
        $('.location_pin').click(function(){
            // Record the current scroll position
            var scrollPosition = window.scrollY;

            $('.beach_content_overlay').fadeIn('fast');

            // Add CSS rule to set the body height to 100%
            $('body').css({
                'overflow': 'hidden',
                'height': '100%'
            });

            // show the matching beach content selected
            var beach = $(this).attr('id');

            $('.selectBeach').removeClass('show');
            $('.selectBeach').each(function(){
                if($(this).hasClass(beach)) {
                    $(this).addClass('show');
                }
            });

            // Set the scroll position back after hiding the overlay
            $('.beach_content_overlay').on('click', function(){
                $(this).fadeOut();
                $('body').css({
                    'overflow': 'auto',
                    'height': 'auto'
                });
                
                window.scrollTo(0, scrollPosition);
            });
        });


        $('.beach_content_overlay').click(function(event){
            if ($(event.target).hasClass('beach_content_overlay')) {
                $('.beach_content_overlay').fadeOut('fast');
                $('body').css({
                    'overflow': 'hidden',
                    'height': 'auto'
                });
                $('.selectBeach').removeClass('show');
            }
        });

    });
})(jQuery);