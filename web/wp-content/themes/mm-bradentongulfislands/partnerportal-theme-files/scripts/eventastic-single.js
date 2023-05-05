jQuery(document).ready(function($) {
    $(".images .arrowNext").click(function(){
        var active = $(".images .active");
        if (active.is(":last-of-type")) {
            var next = $(".images .image:first-child");
        } else var next = active.next();
        $(".images .image").removeClass('active');
        next.addClass('active');
    });
    $(".images .arrowPrev").click(function(){
        var active = $(".images .active");
        if (active.is(":first")) {
            var next = $(".images .image:last-of-type");
        } else var next = active.prev();
        $(".images .image").removeClass('active');
        next.addClass('active');
    });
});
