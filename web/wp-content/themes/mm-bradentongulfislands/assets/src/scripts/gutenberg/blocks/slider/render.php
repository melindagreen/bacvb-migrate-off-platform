<?php

namespace MaddenNino\Blocks\Slider;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  $anchor = $attrs['anchor'] ?? '';  
  $className = $attrs['className'] ?? '';
  $arrowPosition = ($attrs['enableArrowNavigation'] && $attrs['arrowsBelowSlider']) ? 'slider-arrows-below' : '';

  $classes = [
    Constants::BLOCK_CLASS.'-slider',
    'slider-type-'.$attrs['contentType'],
    'card-style-'.$attrs['cardStyle'],
    $className,
    $arrowPosition
  ];

  ob_start(); 
  //print_r($attrs);
  ?>

<section id="<?php echo $anchor; ?>" class="<?php echo implode(' ', $classes); ?>">
  <div class="swiper" data-enableAutoplay="<?php echo $attrs['enableAutoplay'] ? true : false; ?>" data-centeredSlides="<?php echo $attrs['centeredSlides'] ? true : false; ?>" data-effect="<?php echo $attrs['effect']; ?>" data-loop="<?php echo $attrs['loop'] ? true : false; ?>" data-freeMode="<?php echo $attrs['freeMode'] ? true : false; ?>" data-enableMouseScroll="<?php echo $attrs['enableMouseScroll'] ? true : false; ?>" data-enableArrowNavigation="<?php echo $attrs['enableArrowNavigation'] ? true : false; ?>" data-enablePagination="<?php echo $attrs['enablePagination'] ? true : false; ?>" data-enableScrollbar="<?php echo $attrs['enableScrollbar'] ? true : false; ?>" data-sliderDirectionDesktop="<?php echo $attrs['sliderDirectionDesktop']; ?>" data-sliderDirectionTablet="<?php echo $attrs['sliderDirectionTablet']; ?>" data-sliderDirectionMobile="<?php echo $attrs['sliderDirectionMobile']; ?>" data-enableGridRows="<?php echo $attrs['enableGridRows'] ? true : false; ?>" data-gridRowsDesktop="<?php echo $attrs['gridRowsDesktop']; ?>" data-gridRowsTablet="<?php echo $attrs['gridRowsTablet']; ?>" data-gridRowsMobile="<?php echo $attrs['gridRowsMobile']; ?>" data-enableSpaceBetween="<?php echo $attrs['enableSpaceBetween'] ? true : false; ?>" data-spaceBetweenDesktop="<?php echo $attrs['spaceBetweenDesktop']; ?>" data-spaceBetweenTablet="<?php echo $attrs['spaceBetweenTablet']; ?>" data-spaceBetweenMobile="<?php echo $attrs['spaceBetweenMobile']; ?>" data-enableSlidesPerView="<?php echo $attrs['enableSlidesPerView'] ? true : false; ?>" data-enableSlidesPerViewAuto="<?php echo $attrs['enableSlidesPerViewAuto'] ? true : false; ?>" data-slidesPerViewDesktop="<?php echo $attrs['slidesPerViewDesktop']; ?>" data-slidesPerViewTablet="<?php echo $attrs['slidesPerViewTablet']; ?>" data-slidesPerViewMobile="<?php echo $attrs['slidesPerViewMobile']; ?>" data-enableSlidesPerGroup="<?php echo $attrs['enableSlidesPerGroup'] ? true : false; ?>" data-enableSlidesPerGroupAuto="<?php echo $attrs['enableSlidesPerGroupAuto'] ? true : false; ?>" data-slidesPerGroupDesktop="<?php echo $attrs['slidesPerGroupDesktop']; ?>" data-slidesPerGroupTablet="<?php echo $attrs['slidesPerGroupTablet']; ?>" data-slidesPerGroupMobile="<?php echo $attrs['slidesPerGroupMobile']; ?>">

    <div class="swiper-wrapper">
      <?php
        //if using innerblocks, show the content
        if ($attrs['contentType'] === 'custom') {

          echo $content;

        } elseif ($attrs['contentType'] === 'gallery') {

          //Image gallery
          if ($attrs['galleryImages']) {

            if ($attrs['shuffleSlides']) {
              shuffle($attrs['galleryImages']);
            }

            if (isset($attrs['galleryMaxHeight'])) {
              $imageHeight = $attrs['galleryMaxHeight'];
            } else {
              $imageHeight = '100%';
            }

            foreach($attrs['galleryImages'] as $image) { ?>
      <div class="swiper-slide">
        <figure class="wp-block-image size-full" data-fancybox="gallery" data-caption="<?php echo $image['caption']; ?>" data-src="<?php echo $image['url']; ?>" style="height: <?php echo $imageHeight; ?>;">
          <?php
                    echo wp_get_attachment_image($image['id'], 'full');
                    if ($image['caption']) { ?>
          <figcaption>
            <?php echo $image['caption']; ?>
          </figcaption>
          <?php } ?>
        </figure>
      </div>
      <?php
            }
          }

        } elseif ($attrs['contentType'] === 'automatic') {

			//Automatic recent posts
			$queryArgs = array(
			  'post_type'       => $attrs['postType'] ?? 'adventure',
			  'posts_per_page'  => $attrs['numberOfPosts'] ?? 5,
			  'post_status'     => 'publish',
			  'orderby'         => 'date',
			  'order'           => 'desc'
			);
  
      if ($attrs['enableTaxFilter']) {
        $termIds = array();
        foreach ($attrs['taxonomyTerms'] as $term) {
        $termIds[] = $term['id'];
        }
  
        $queryArgs['tax_query'] = array(
        array(
          'taxonomy'  => $attrs['taxonomyFilter'],
          'field'     => 'id',
          'terms'     => $termIds
        )
        );
      }

      // Exclude specific taxonomy terms for post type 'event'
      if ($attrs['postType'] === 'event' && $attrs['contentType'] === 'automatic') {
   

        // Add query argument to sort by start_date
        $queryArgs['meta_key'] = 'eventastic_start_date';
        $queryArgs['orderby'] = 'meta_value';
        $queryArgs['order'] = 'ASC';

        // Filter to show only upcoming events
        $queryArgs['meta_query'] = array(
          'relation' => 'AND',
          array(
            'key'     => 'eventastic_start_date',
            'value'   => date('Y-m-d'),
            'compare' => '>=',
            'type'    => 'DATE'
          )
        );
      }
  
			$posts = new \WP_Query($queryArgs);

          if ($posts->have_posts()) {
            $posts = $posts->get_posts();

            if ($attrs['shuffleSlides']) {
              shuffle($posts);
            }

            foreach($posts as $post) {
              echo '<div class="swiper-slide">';
              echo do_blocks('<!-- wp:mm-bradentongulfislands/content-card {
                "contentId":'.$post->ID.',
                "contentType":"'.$attrs['postType'].'", 
                "cardStyle": "'.$attrs['cardStyle'].'",
                "displayAdditionalContent": "'.$attrs['displayAdditionalContent'].'",
                "displayExcerpt": "'.$attrs['displayExcerpt'].'",
                "excerptLength": '.$attrs['excerptLength'].',
                "displayReadMore": "'.$attrs['displayReadMore'].'",
                "readMoreText": "'.$attrs['readMoreText'].'"
              } /-->');
              echo '</div>';
            }
          }
          wp_reset_postdata();

        } elseif ($attrs['contentType'] === 'manual') {

          if ($attrs['shuffleSlides']) {
            shuffle($attrs['manualPosts']);
          }

          foreach($attrs['manualPosts'] as $post) {
            echo '<div class="swiper-slide">';
            echo do_blocks('<!-- wp:mm-bradentongulfislands/content-card {
              "contentId":'.$post['id'].',
              "contentType":"'.$attrs['postType'].'", 
              "cardStyle": "'.$attrs['cardStyle'].'",
              "displayAdditionalContent": "'.$attrs['displayAdditionalContent'].'",
              "displayExcerpt": "'.$attrs['displayExcerpt'].'",
              "excerptLength": '.$attrs['excerptLength'].',
              "displayReadMore": "'.$attrs['displayReadMore'].'",
              "readMoreText": "'.$attrs['readMoreText'].'"
            } /-->');
            echo '</div>';
          }
        }

        if (($attrs['contentType'] === 'automatic' || $attrs['contentType'] === 'manual') && $attrs['enableCtaSlide']) {

          if ($attrs['ctaSlideBtnUrl']) {
            $ctaLink = $attrs['ctaSlideBtnUrl'];
          } else {
            $ctaLink = '';
          }

          $customCardAttrs = [
            'contentType'        => 'custom',
            'cardStyle'          => $attrs['cardStyle'],
            'contentTitle'       => $attrs['ctaSlideTitle'],
            'contentExcerpt'       => $attrs['ctaSlideExcerpt'],
            'customImage'        => $attrs['ctaSlideImage'],
            'customCtaText'      => $attrs['ctaSlideBtnText'],
            'customCtaUrl'       => $ctaLink,
          ];

          echo '<div class="swiper-slide">';
          echo do_blocks('<!-- wp:mm-bradentongulfislands/content-card '.json_encode($customCardAttrs).' /-->');
          echo '</div>';
        }
        ?>
    </div>
  </div>

  <?php 
  $dot_color =  $attrs['dotColor'] ? $attrs['dotColor']['name'] : "";
  $dot_color_active =  $attrs['dotColorActive'] ? $attrs['dotColorActive']['name'] : "";
  $arrow_color =  $attrs['arrowColor'] ? $attrs['arrowColor']['name'] : "";
  $arrow_color_bg =  $attrs['arrowBackgroundColor'] ? $attrs['arrowBackgroundColor']['name'] : "";
  $scrollbar_color =  $attrs['scrollbarColor'] ? $attrs['scrollbarColor']['name'] : "";

  if ($attrs['enableScrollbar'] || $attrs['enablePagination'] || $attrs['enableArrowNavigation']) {
    echo '<div class="swiper-navigation-wrapper">';
    if ($attrs['enableScrollbar']) {
      echo '<div class="swiper-scrollbar" data-color="'.$scrollbar_color.'"></div>';
    }
    if ($attrs['enableArrowNavigation']) {
      echo '<div class="swiper-button-prev" data-color="'.$arrow_color.'" data-color-background="'.$arrow_color_bg.'"></div>';
    }
    if ($attrs['enablePagination']) {
      echo '<div class="swiper-pagination" data-color="'.$dot_color.'" data-color-active="'.$dot_color_active.'"></div>';
    }
    if ($attrs['enableArrowNavigation']) {
      echo '<div class="swiper-button-next" data-color="'.$arrow_color.'" data-color-background="'.$arrow_color_bg.'"></div>';
    }
    echo '</div>';
  }
  if ($attrs['effect'] === 'cards') {
    echo '<div class="slider-info-box is-style-collage-square">';
    echo '<h3 id="infoblock-title"></h3>';
    echo '<p id="infoblock-excerpt"></p>';
    echo '<a id="infoblock-buttonurl" href="#">See Details</a>';
    echo '</div>';
  }
  ?>
</section>

<?php 
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}