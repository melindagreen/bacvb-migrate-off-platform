// this is the front-end script for the block hero
import $ from 'jquery';
import { getIsSmall, getIsLarge } from '../../inc/utils';

$(window).on("load", () => {
    initHero();
});

export const initHero = () => {
        // set the bg position
        $('.hero').each(function () {
            if ($(this).data('lg-background-position')) {
                let thisStyle = $(this).attr('style');
                if (getIsSmall()) {
                    $(this).attr('style', thisStyle + "background-position: " + $(this).data('sm-background-position'));
                } else {
                    $(this).attr('style', thisStyle + "background-position: " + $(this).data('lg-background-position'));
                }
            }
        });

        // Video controls
        $('.hero-video-play').click(function () {
            if ($(this).hasClass('pause')) {
                $(this).removeClass('pause').addClass('play');
                $('.video video').get(0).pause();
            } else {
                $(this).removeClass('play').addClass('pause');
                $('.video video').get(0).play();
            }
        });

        // Logo Url Variable
        var $title = $('.title');
        var logoUrl = $title.data('logo-url');
    
        if (logoUrl) {
            $title.css('--logo-url', `url(${logoUrl})`);
        }

         // put the video source in the tag for the size we need
         const videoSize = (getIsSmall()) ? '.video-el.video--mobile' : '.video-el.video--desktop';
         $(videoSize).each(function () {
             const videoContainer = $(this);
             const sources = videoContainer.find('source');
             let foundSource = false;
             
             sources.each(function () {
                 const source = $(this);
                 const videoURL = source.data('video-url');
 
                 // set the source
                 source.attr('src', videoURL);
                 source.removeAttr('data-video-url');
                 foundSource = true;
             });
             // load and play the video
             if (foundSource) {
                 // large was just set to hidden to fill space, so logic depends here
                 if (getIsSmall()) {
                     $('.video.video--desktop').css('display', 'none');
                     $('.video.video--mobile').css('display', 'block');
                 }
                 videoEl = videoContainer;
                 videoContainer[0].load();
                 videoContainer[0].play();
                 $('.hero-video-play').css('visibility', 'visible');
             }
         
             // Show the control after loading and playing the video
             $('.hero-video-play').css('display', 'block');
         });
                 
         // video controls
         $('.hero-video-play').click(function () {
             if (videoEl != null) {
                 if ($(this).hasClass('pause')) {
                     $(this).removeClass('pause').addClass('play');
                     videoEl.get(0).pause();
                 } else {
                     $(this).removeClass('play').addClass('pause');
                     videoEl.get(0).play();
                 }
             }
         });
    }
