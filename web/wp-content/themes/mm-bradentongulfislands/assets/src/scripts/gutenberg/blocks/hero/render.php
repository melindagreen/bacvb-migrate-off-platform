<?php

namespace MaddenNino\Blocks\Hero;
use MaddenNino\Library\Constants as Constants;

/**
* Render function for the dynamic example block
* @param array $attrs        all block attrs
* @param string $content
*/
function render_block( $attrs, $content ) {
	if (!$attrs['videoHero']) {
		$mobileImage = (isset($attrs['mobileImage'])) ? $attrs['mobileImage'] : $attrs['image'];
		$caption = wp_get_attachment_caption( $attrs['image']['id'] );
	}
	ob_start();
	$classes = "";
	if($attrs['className'] !== "") {
		$classes = $attrs['className'];
	}

?>

	<?php  if(!$attrs['smallHero']) { ?>

	<section class="<?php echo Constants::BLOCK_CLASS; ?>-hero<?php if ($attrs['videoHero']) echo ' videoHero'; ?> <?php echo $classes; ?>">

	<?php if(is_front_page()) { ?>
		<!-- <div class="badge">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-the-original-one-only.svg">
		</div> -->
	<?php } ?>

		<?php if (!$attrs['videoHero']) { ?>
			<div class="hero<?php if ($attrs["doParallax"]) echo " has-parallax" ?><?php if (!$attrs['showBottomWave']) echo " full" ?>"
				data-load-type="bg"	
				data-load-onload="true"
				data-load-lg="<?php echo wp_get_attachment_image_src($attrs['image']['id'], 'full')[0]; ?>"
				data-load-md="<?php echo wp_get_attachment_image_src($attrs['image']['id'], 'full')[0]; ?>"
				data-load-sm="<?php echo wp_get_attachment_image_src($attrs['image']['id'], 'large')[0]; ?>"
				<?php
				// if no parallax, check for focal point - if it's not 50/50, employ it
				if( (! $attrs["doParallax"]) && (isset( $attrs["focalPoint"] )) ) {
					$focal_point_x = floatval( $attrs["focalPoint"]["x"] ) * 100;
					$focal_point_y = floatval( $attrs["focalPoint"]["y"] ) * 100;
					if( ($focal_point_x != 50) || ($focal_point_y != 50) ) {
						// echo 'style="background-size: auto;"'.PHP_EOL;
						echo 'data-load-lg-bg-position="' . $focal_point_x . '% ' . $focal_point_y . '%" '.PHP_EOL;
						echo 'data-load-md-bg-position="' . $focal_point_x . '% ' . $focal_point_y . '%" '.PHP_EOL;	
					}
				}
				if( (! $attrs["doParallax"]) && (isset( $attrs["focalPointMobile"] )) ) {
					$focal_point_mobile_x = floatval( $attrs["focalPointMobile"]["x"] ) * 100;
					$focal_point_mobile_y = floatval( $attrs["focalPointMobile"]["y"] ) * 100;
					if( ($focal_point_mobile_x != 50) || ($focal_point_mobile_y != 50) ) {
						// echo 'style="background-size: auto;"'.PHP_EOL;
						echo 'data-load-sm-bg-position="' . $focal_point_mobile_x . '% ' . $focal_point_mobile_y . '%" '.PHP_EOL;
					}
				}
				?>
			></div>
		<?php } ?>


		<?php if ($attrs['videoHero']) { ?>
			<button type='none' class='hero-video-play pause'><span class='sr-only'>Pause Video</span></button>
			<?php if ($attrs['videoHero'] && !$attrs['videoForMobile']) : ?>
            <!-- Display video for desktop only when there's no mobile video -->
            <div class="video no--mobile">
                <video poster="<?php if ($attrs['videoPoster']) echo wp_get_attachment_image_src($attrs['videoPoster']['id'], 'full')[0]; ?>" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url($attrs['video']['id']); ?>" type="video/mp4">
                </video>
            </div>
        <?php endif; ?>
        <?php if (isset($attrs['videoForMobile']) && $attrs['videoHero']) : ?>
            <!-- Display both desktop and mobile videos when both hero video and mobile video are set -->
            <div class="video video--desktop">
                <video poster="<?php if ($attrs['videoPoster']) echo wp_get_attachment_image_src($attrs['videoPoster']['id'], 'full')[0]; ?>" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url($attrs['video']['id']); ?>" type="video/mp4">
                </video>
            </div>

            <div class="video video--mobile">
                <video poster="<?php if ($attrs['videoPoster']) echo wp_get_attachment_image_src($attrs['videoPoster']['id'], 'full')[0]; ?>" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url($attrs['videoForMobile']['id']); ?>" type="video/mp4">
                </video>
            </div>	
				<?php endif; ?>
			<!--if no video--mobile keep showing main--->
			<?php }  ?>
				<?php if ($caption) { ?>
					<span class="description"><?php echo $caption; ?></span>
				<?php } ?>
				<?php if (isset($attrs['title']) && $attrs['title']) { ?>
					<h1 class="title 	<?php if(!is_front_page()) { echo "alt-title"; } ?>"><?php if ($attrs['subtitle']) echo '<span>'.$attrs['subtitle'].'</span>'; ?><?php echo $attrs['title']; ?></h1>
				<?php } ?>

			<div class="fade"></div>
		</section>

		<?php } ?>
		<?php $output = ob_get_contents();
		ob_end_clean();
		return $output;

		return $html;
	}
