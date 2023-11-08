// this is the front-end script for the block example-dynamic

(function($) {
    $(document).ready(function() {
        


            var contentHeight = $(this).height();
            var containerHeight = $(".grid-item-body").height();
          
            if (contentHeight > containerHeight) {
              var newTop = contentHeight - containerHeight;
              $(this).css("top", -newTop + "px");
            } else {
              $(this).css("top", "0");
            }
    });
})(jQuery);