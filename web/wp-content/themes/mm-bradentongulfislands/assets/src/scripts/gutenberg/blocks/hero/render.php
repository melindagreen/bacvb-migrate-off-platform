<?php

namespace MaddenNino\Blocks\Hero;
use MaddenNino\Library\Constants as Constants;

/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attrs
 * @param string $content
 */
function render_block( $attrs, $content ) {

    if (!isset($attrs['videoHero']) || !$attrs['videoHero']) {
        $mobileImage = isset($attrs['mobileImage']) ? $attrs['mobileImage'] : $attrs['image'];
        $caption = wp_get_attachment_caption($attrs['image']['id']);
    }

    ob_start();
    $classes = isset($attrs['className']) ? $attrs['className'] : '';

?>

    <?php if (!isset($attrs['smallHero']) || !$attrs['smallHero']) { ?>
    
    <section class="<?php echo Constants::BLOCK_CLASS; ?>-hero<?php if (isset($attrs['videoHero']) && $attrs['videoHero']) echo ' videoHero'; ?> <?php echo $classes; ?><?php echo $attrs['className'] !== 'is-style-small-font-header' && ($attrs['logoId'] || $attrs['ctaBannerTitle'] !== "") ? ' is-style-small-font-header' : ''; ?> <?php echo $attrs['ctaBannerTitle'] !== "" ? ' '. Constants::BLOCK_CLASS . '-hero--banner' : ''; ?>">
    <?php if (!$attrs['title']) { ?> <h1> <?php } ?>

    <?php if (is_front_page()) { ?>
        <!-- <div class="badge">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-the-original-one-only.svg">
        </div> -->
    <?php } ?>

        <?php if (!isset($attrs['videoHero']) || !$attrs['videoHero']) { ?>
            <div <?php if (!$attrs['title']) { ?> title="<?php echo get_the_title($post->ID); ?>" <?php } ?>  class="hero<?php if (isset($attrs["doParallax"]) && $attrs["doParallax"]) echo " has-parallax" ?><?php if (!isset($attrs['showBottomWave']) || !$attrs['showBottomWave']) echo " full" ?>"
                data-load-type="bg"    
                data-load-onload="true"
                data-load-lg="<?php echo wp_get_attachment_image_src($attrs['image']['id'], 'full')[0]; ?>"
                data-load-md="<?php echo wp_get_attachment_image_src($attrs['image']['id'], 'full')[0]; ?>"
                data-load-sm="<?php echo wp_get_attachment_image_src($attrs['mobileImage']['id'], 'large')[0]; ?>"
                <?php
                // if no parallax, check for focal point - if it's not 50/50, employ it
                if ((!isset($attrs["doParallax"]) || !$attrs["doParallax"]) && isset($attrs["focalPoint"])) {
                    $focal_point_x = floatval($attrs["focalPoint"]["x"]) * 100;
                    $focal_point_y = floatval($attrs["focalPoint"]["y"]) * 100;
                    if (($focal_point_x != 50) || ($focal_point_y != 50)) {
                        echo 'data-load-lg-bg-position="' . $focal_point_x . '% ' . $focal_point_y . '%" '.PHP_EOL;
                        echo 'data-load-md-bg-position="' . $focal_point_x . '% ' . $focal_point_y . '%" '.PHP_EOL;    
                    }
                }
                if ((!isset($attrs["doParallax"]) || !$attrs["doParallax"]) && isset($attrs["focalPointMobile"])) {
                    $focal_point_mobile_x = floatval($attrs["focalPointMobile"]["x"]) * 100;
                    $focal_point_mobile_y = floatval($attrs["focalPointMobile"]["y"]) * 100;
                    if (($focal_point_mobile_x != 50) || ($focal_point_mobile_y != 50)) {
                        echo 'data-load-sm-bg-position="' . $focal_point_mobile_x . '% ' . $focal_point_mobile_y . '%" '.PHP_EOL;
                    }
                }
                ?>
            >
                <div class="fade"></div>
            </div>
        <?php } ?>


        <?php if (isset($attrs['videoHero']) && $attrs['videoHero']) { ?>
            <button type='button' class='hero-video-play pause'><span class='sr-only'>Pause Video</span></button>
            <?php if ($attrs['videoHero'] && (!isset($attrs['videoForMobile']) || !$attrs['videoForMobile'])) : ?>
            <!-- Display video for desktop only when there's no mobile video -->
            <div class="video no--mobile">
                <video poster="<?php if (isset($attrs['videoPoster'])) echo wp_get_attachment_image_src($attrs['videoPoster']['id'], 'full')[0]; ?>" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url($attrs['video']['id']); ?>" type="video/mp4">
                </video>
                <div class="fade"></div>
            </div>
        <?php endif; ?>
        <?php if (isset($attrs['videoForMobile']) && $attrs['videoHero']) : ?>
            <!-- Display both desktop and mobile videos when both hero video and mobile video are set -->
            <div class="video video--desktop">
                <video poster="<?php if (isset($attrs['videoPoster'])) echo wp_get_attachment_image_src($attrs['videoPoster']['id'], 'full')[0]; ?>" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url($attrs['video']['id']); ?>" type="video/mp4">
                </video>
                <div class="fade"></div>
            </div>

            <div class="video video--mobile">
                <video poster="<?php if (isset($attrs['videoPoster'])) echo wp_get_attachment_image_src($attrs['videoPoster']['id'], 'full')[0]; ?>" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url($attrs['videoForMobile']['id']); ?>" type="video/mp4">
                </video>
                <div class="fade"></div>
            </div>    
                <?php endif; ?>
            <!--if no video--mobile keep showing main--->
            <?php }  ?>
                <?php if (isset($caption) && $caption) { ?>
                    <span class="description"><?php echo $caption; ?></span>
                <?php } ?>
                <?php if (isset($attrs['title']) && $attrs['title']) { ?>
                    <h1 class="title <?php if (!is_front_page()) { echo "alt-title"; } ?>"
                    <?php if (isset($attrs['logoId']) && $attrs['logoId'] && $attrs['ctaBannerTitle'] === "") { ?>
                        data-logo-url="<?php echo wp_get_attachment_url($attrs['logoId']); ?>"
                    <?php } ?>
                    >
                    <?php if (isset($attrs['subtitle']) && $attrs['subtitle']) echo '<span>'.$attrs['subtitle'].'</span>'; ?><?php echo $attrs['title']; ?></h1>
                <?php } ?>

                <?php if(is_page('gulf-islands-ferry') && (get_field('herobanner_toggle', 'option') || get_field('herobanner_toggle', 'option'))) { ?>
                <div class="hero-banner">
                    <h3 class="hero-banner__title"><?php echo get_field('herobanner_title', 'option'); ?></h3>
                    <p class="hero-banner__description">
                        <?php echo get_field('herobanner_description', 'option'); ?>
                    </p>
                    <img class="hero-banner__crab" src="<?php echo get_theme_file_uri()?>/assets/images/crab.png" alt="Crab Illustration">
                    
                </div>
            <?php } ?>
            <?php if($attrs['ctaBannerTitle'] !== "") { ?>
                <div class="cta-hero-banner">
                    <h3 class="cta-hero-banner__title"><?php echo $attrs['ctaBannerTitle']; ?></h3>
                    <?php if($attrs['bannerUrl'] !== "") { ?>
                            <div class="cta-hero-banner__cta">
                                <img class="cta-hero-banner__logo" src="<?php echo wp_get_attachment_url($attrs['logoId']); ?>" alt="">
                                <a class="wp-block-button__link has-white-color has-bradenton-white-background-color has-text-color has-background has-link-color has-text-align-center wp-element-button" href="<?php echo $attrs['ctaBannerUrl'] ?>"><?php echo $attrs['ctaBannerText'] ?></a>
                            </div>
                    <?php } ?>
                </div>
            <?php } ?>
    
            <?php if (!$attrs['title']) { ?> </h1> <?php } ?>
        </section>

    <?php } ?>
    <?php 
    $output = ob_get_contents();
    ob_end_clean();
    return $output;

    return $html;
}
