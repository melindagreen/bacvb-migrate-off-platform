(function ($) {
	$(document).ready(function () {
		// $('.location_pin').on('click', function(){
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
		$(".location_pin").on("click", function () {
			$(".beach_content_overlay").fadeIn("fast");

			// Add CSS rule to set the body height to 100%
			$("body").css({
				overflow: "hidden",
				height: "100%",
			});

			// show the matching beach content selected
			var beach = $(this).attr("id");

			$(".selectBeach").removeClass("show");
			$(".selectBeach").each(function () {
				if ($(this).hasClass(beach)) {
					$(this).addClass("show");
				}
			});
		});

		// Set the scroll position back after hiding the overlay
		$(".beach_content_overlay").on("click", function () {
			if ($(event.target).hasClass("beach_content_overlay")) {
				$(this).fadeOut();
				$("body").css({
					overflow: "auto",
					height: "auto",
				});

				// Scroll down to #beachMap
				$("html, body").animate(
					{
						scrollTop: $("#beachMap").offset().top,
					},
					0
				);
			}
		});

		$(".close").on("click", function () {
			$(".beach_content_overlay").fadeOut();
			$("body").css({
				overflow: "auto",
				height: "auto",
			});
			// Scroll down to #beachMap
			$("html, body").animate(
				{
					scrollTop: $("#beachMap").offset().top,
				},
				0
			);
		});
	});
})(jQuery);
