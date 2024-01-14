
(function($) {
    $(document).ready(function() {

        $('.location_pin').click(function(){
            $('.beach_content_overlay').fadeIn();
            $('body').css('overflow', 'hidden');


            // show the matching beach content selected
            var beach = $(this).attr('id');

            $('.selectBeach').removeClass('show');
            $('.selectBeach').each(function(){
                if($(this).hasClass(beach)) {
                    $(this).addClass('show');
                }
            });
        });

        $('.beach_content_overlay').click(function(event){
            if ($(event.target).hasClass('beach_content_overlay')) {
                $('.beach_content_overlay').fadeOut('fast');
                $('body').css('overflow', 'auto');
                $('.selectBeach').removeClass('show');
            }
        });

    });
})(jQuery);