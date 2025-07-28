// this is the front-end script for the block hero
import $ from "jquery";
import { getIsSmall, getIsLarge } from "../../../../inc/utilities";

$(window).on("load", () => {
	initHero();
});

export const initHero = () => {
	console.log("hero block loaded");
	// set the bg position
	$(".hero").each(function () {
		if ($(this).data("lg-background-position")) {
			let thisStyle = $(this).attr("style");
			if (getIsSmall()) {
				$(this).attr(
					"style",
					thisStyle +
						"background-position: " +
						$(this).data("sm-background-position")
				);
			} else {
				$(this).attr(
					"style",
					thisStyle +
						"background-position: " +
						$(this).data("lg-background-position")
				);
			}
		}
	});

	// Video controls
	$(".hero-video-play").on("click", function () {
		if ($(this).hasClass("pause")) {
			$(this).removeClass("pause").addClass("play");
			$(".video video").get(0).pause();
		} else {
			$(this).removeClass("play").addClass("pause");
			$(".video video").get(0).play();
		}
	});

	// Logo Url Variable
	var $title = $(".title");
	var logoUrl = $title.data("logo-url");

	if (logoUrl) {
		$title.css("--logo-url", `url(${logoUrl})`);
	}
};
