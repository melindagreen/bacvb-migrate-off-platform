jQuery(document).ready(function($) {
    $(".images .arrowNext").click(function() {
        var active = $(".images .active");
        var next;
        
        if (active.is(":last-of-type")) {
            next = $(".images .image:first-child");
        } else {
            next = active.next(); 
        }
        
        $(".images .image").removeClass('active');
        next.addClass('active');
    });

    $(".images .arrowPrev").click(function() {
        var active = $(".images .active");
        var next;
        
        if (active.is(":first-of-type")) {
            next = $(".images .image:last-of-type"); 
        } else {
            next = active.prev();
        }
        
        $(".images .image").removeClass('active');
        next.addClass('active');
    });
});
