<?php

namespace MaddenTheme\Blocks\Slider;

/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content
 */

$attrs = $attributes;

$anchor = $attrs['anchor'] ?? '';
$className = $attrs['className'] ?? '';
$arrowPosition = ($attrs['enableArrowNavigation'] && $attrs['arrowsBelowSlider']) ? 'slider-arrows-below' : 'slider-arrows-sides';

$classes = [
  'slider-type-'.$attrs['contentType'],
  $className,
  $attrs['enableArrowNavigation'] ? 'nav-arrows-enabled' : '',
  $attrs['enableEqualHeightSlides'] ? 'force-equal-height-slides' : '',
  $arrowPosition
];

$wrapper_attributes = get_block_wrapper_attributes([
  'class' => implode(' ', $classes),
  ' data-uid' => $attrs['sliderId']
]);
?>

<section id="<?php echo $anchor; ?>" <?php echo $wrapper_attributes; ?>>
	<div class="swiper" data-enableautoplay="<?php echo $attrs['enableAutoplay'] ? true : false; ?>" data-centeredslides="<?php echo $attrs['centeredSlides'] ? true : false; ?>" data-effect="<?php echo $attrs['effect']; ?>" data-loop="<?php echo $attrs['loop'] ? true : false; ?>" data-freemode="<?php echo $attrs['freeMode'] ? true : false; ?>" data-enablemousescroll="<?php echo $attrs['enableMouseScroll'] ? true : false; ?>" data-enablearrownavigation="<?php echo $attrs['enableArrowNavigation'] ? true : false; ?>" data-enablepagination="<?php echo $attrs['enablePagination'] ? true : false; ?>" data-enablescrollbar="<?php echo $attrs['enableScrollbar'] ? true : false; ?>" data-sliderdirectiondesktop="<?php echo $attrs['sliderDirectionDesktop']; ?>" data-sliderdirectiontablet="<?php echo $attrs['sliderDirectionTablet']; ?>" data-sliderdirectionmobile="<?php echo $attrs['sliderDirectionMobile']; ?>" data-enablegridrows="<?php echo $attrs['enableGridRows'] ? true : false; ?>" data-gridrowsdesktop="<?php echo $attrs['gridRowsDesktop']; ?>" data-gridrowstablet="<?php echo $attrs['gridRowsTablet']; ?>" data-gridrowsmobile="<?php echo $attrs['gridRowsMobile']; ?>" data-enablespacebetween="<?php echo $attrs['enableSpaceBetween'] ? true : false; ?>" data-spacebetweendesktop="<?php echo $attrs['spaceBetweenDesktop']; ?>" data-spacebetweentablet="<?php echo $attrs['spaceBetweenTablet']; ?>" data-spacebetweenmobile="<?php echo $attrs['spaceBetweenMobile']; ?>" data-enableslidesperview="<?php echo $attrs['enableSlidesPerView'] ? true : false; ?>" data-enableslidesperviewauto="<?php echo $attrs['enableSlidesPerViewAuto'] ? true : false; ?>" data-slidesperviewdesktop="<?php echo $attrs['slidesPerViewDesktop']; ?>" data-slidesperviewtablet="<?php echo $attrs['slidesPerViewTablet']; ?>" data-slidesperviewmobile="<?php echo $attrs['slidesPerViewMobile']; ?>" data-enableslidespergroup="<?php echo $attrs['enableSlidesPerGroup'] ? true : false; ?>" data-enableslidespergroupauto="<?php echo $attrs['enableSlidesPerGroupAuto'] ? true : false; ?>" data-slidespergroupdesktop="<?php echo $attrs['slidesPerGroupDesktop']; ?>" data-slidespergrouptablet="<?php echo $attrs['slidesPerGroupTablet']; ?>" data-slidespergroupmobile="<?php echo $attrs['slidesPerGroupMobile']; ?>" data-initialslide="<?php echo $attrs['initialSlide']; ?>">

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
                <figure class="wp-block-image size-full" data-fancybox="gallery" data-caption="<?php echo isset($image['caption']) ? $image['caption'] : ''; ?>" data-src="<?php echo isset($image['url']) ? $image['url'] : ''; ?>" style="height: <?php echo $imageHeight; ?>;">
                  <?php
                  echo wp_get_attachment_image($image['id'], 'full');
                  if (isset($image['caption'])) { ?>
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
            'post_type'       => $attrs['postType'] ?? 'post',
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

          if ($attrs['postOrder'] !== 'date') {
            $queryArgs['orderby'] = $attrs['postOrder'];
            $queryArgs['order'] = 'asc';
          }

          if ($attrs['postType'] === 'event') {
            $today = new \DateTime();
            //order by next occurrence date
            if ($attrs['postOrder'] === 'date') {
              $queryArgs['order']     = 'asc';
              $queryArgs['orderby']   = 'meta_value';
              $queryArgs['meta_key']  = 'event_next_occurrence';
            }
            //only include events that have not ended
            $queryArgs['meta_query'] = array(
              'relation' => 'OR',
              array(
                'key'     => "event_start_date",
                'value'   => $today->format('Ymd'),
                'compare' => '>=',
                'type'    => 'DATE',
              ),
              array(
                'key'     => "event_end_date",
                'value'   => $today->format('Ymd'),
                'compare' => '>=',
                'type'    => 'DATE',
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

              $cardAttrs = array_merge($attrs,  array(
                "contentId" => $post->ID
              ));

              echo do_blocks('<!-- wp:madden-theme/content-card '.json_encode($cardAttrs).' /-->');

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

            $cardAttrs = array_merge($attrs,  array(
              "contentId" => $post['id']
            ));

            echo do_blocks('<!-- wp:madden-theme/content-card '.json_encode($cardAttrs).' /-->');

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
            'postType'          => 'custom',
            'customCtaUrl'      => $ctaLink,
          ];

          $cardAttrs = array_merge($attrs, $customCardAttrs);

          echo '<div class="swiper-slide">';
          echo do_blocks('<!-- wp:madden-theme/content-card '.json_encode($cardAttrs).' /-->');
          echo '</div>';
        }
        ?>
		</div>
	</div>

	<?php
    $dot_color =  $attrs['dotColor'] ? $attrs['dotColor'] : "";
    $dot_color_active =  $attrs['dotColorActive'] ? $attrs['dotColorActive'] : "";
    $arrow_color =  $attrs['arrowColor'] ? $attrs['arrowColor'] : "";
    $arrow_color_bg =  $attrs['arrowBackgroundColor'] ? $attrs['arrowBackgroundColor'] : "";
    $scrollbar_color =  $attrs['scrollbarColor'] ? $attrs['scrollbarColor'] : "";

    if ($attrs['enableScrollbar'] || $attrs['enablePagination'] || $attrs['enableArrowNavigation']) {
      echo '<div class="swiper-navigation-wrapper">';
      if ($attrs['enableScrollbar']) {
        echo '<div class="swiper-scrollbar" data-color="'.$scrollbar_color.'"></div>';
      }
      if ($attrs['enablePagination']) {
        echo '<div class="swiper-pagination" data-color="'.$dot_color.'" data-color-active="'.$dot_color_active.'"></div>';
      }
      if ($attrs['enableArrowNavigation']) {
        if ( isset( $attributes['className'] ) && strpos( $attributes['className'], 'is-style-stacked' ) !== false && strpos( $attributes['className'], 'is-style-full-wdith' ) !== false ) {
          echo '<div class="swiper-button-prev has-'.$arrow_color.'-color" data-color="'.$arrow_color.'" data-color-background="'.$arrow_color_bg.'"><svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.18 28.91"><path data-name="Path 2" d="m14.45 0 3.73 3.73L7.46 14.45l10.72 10.72-3.73 3.73L0 14.45 14.45 0Z" style="fill:#8fd8f9"/></svg></div>';
          echo '<div class="swiper-button-next has-'.$arrow_color.'-color" data-color="'.$arrow_color.'" data-color-background="'.$arrow_color_bg.'"><svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.18 28.91"><path data-name="Path 2" d="M3.73 28.91 0 25.18l10.72-10.72L0 3.73 3.73 0l14.45 14.45L3.73 28.91Z" style="fill:#8fd8f9"/></svg></div>';
        } else {
          echo '<div class="swiper-button-prev has-'.$arrow_color.'-color" data-color="'.$arrow_color.'" data-color-background="'.$arrow_color_bg.'"><svg class="desktop-arrow" xmlns="http://www.w3.org/2000/svg" width="22" height="25" viewBox="0 0 22 25"><path id="Path_84491" data-name="Path 84491" d="M12.5,0,25,22H0Z" transform="translate(0 25) rotate(-90)" fill="currentColor"/></svg><svg class="mobile-arrow" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 69.32 67.52"><path style="fill:none" d="M0 0h69.32v67.51H0z"/><path data-name="Path 21" d="M.15 36.8C1.62 52.84 16.52 65.56 32.1 67.35c6.97.82 13.4-1.38 19.51-4.39 6.49-4.46 12.77-10.62 15.55-18.13.16-1.44-.86.16-.94.78-.91 2.29-2.19 4.41-3.78 6.29 2.1-5.13 6.06-9.98 6.29-15.82 0-.32.14-.35.45-.43.49-.29-.48-.87-.2-1.25 1.05-12.63-8.12-27.46-20.55-30.25.04.04.09.07.14.1-.21-.13-.43-.23-.65-.31.54-.45-2.83-1.56-3.2-1.48.39.45.92.75 1.51.86.17.03.46.1.53.26-3.65-.82-8.03-2.31-11.83-1.37-1.03-.29-2.08-.51-3.15-.65 3.27-.99 4.9-1.65-.13-1.23-1.73.44-3.56.33-5.22-.32-1.27.41-2.57.57-3.84.89-5.1 1.86-8.74 5.87-12.78 9.19-.65.3-.16 1.16-.95 1.28-2.37 2.4-3.47 5.96-4.41 9.21C.58 31.9 1.83 44.83 8.98 54.46c-3.34-3.06-5.74-9.3-6.86-13.82-1.4-4.95-.23-10.28-.51-15.25-.56 1.02-.48 2.48-.83 3.65-1.04.88-.75 6.28-.61 7.76m40.54 25.06c.1-.07.19-.19.3-.22 1.74-.57 3.61-.63 5.31-1.25.58-.54 1.31-.89 2.1-.98 1.17-.22 2.25-1.13 3.36-1.33a16.725 16.725 0 0 1-11.06 3.78m23.6-40.92c2.59 4.65 4.13 10.43 2.58 15.64.2-6.33-1.49-12.58-4.86-17.95a.593.593 0 0 1-.05-.23c1.37-.14 1.66 1.65 2.32 2.54m-3.53-4.34C56.66 11.07 50.37 8.15 44.1 5.7c5.01.99 15.16 5.84 16.68 10.9" style="fill:#2b7b7c"/><path data-name="Path 22" d="M26.22 24.23c2.24-2.72-5.06 3.95-5.68 3.99-.61.22-1.15-.51-1.8-.18-.27.04-.72-.45-.96-.65-.44-.22-.92-.36-1.41-.4-.11.21-.17.48-.49.25-.74-.65-1.35.47-.73 1.03.47.5.04 1.03.05 1.64-.39.04-1.57-1.05-1.45-.3.05.12.19.26.16.33-.29.77.42 1.06.85 1.47.06.59-.49.05-.71-.03-1.77.09 1.17 3.28 1.98 3.36.46.22.9.47 1.32.76 9.34 5.85 19.41 10.45 29.95 13.69.85-.29 1.66-1.73 1.07-2.56-.1-.07-.23.04-.32.19-.42.56-.91-.04-1.1-.47-.23-.41.42-.27.5-.57.1-.68-.99-.6-1.27-1.14-.08-.58.57-.21.88-.15-5.35-4.62-13.21-6.78-19.18-11.03 2.75-3.99 8.86-7.1 13.18-10.16 2.43-1.36 4.29-3.56 6.55-5.15.49-.24.32-.83.62-1.18.52-.5-.59-.64-.8-1.01-.41-.5-.97-.39-1.5-.58.53-.84-.62-.96-1.14-1.32-.63.2-1.34 0-1.91.52-.29.3-.75.36-1.1.14a.641.641 0 0 0-.76.13c-3.14 2.72-6.8 4.41-10.22 6.68-.91.19-7.6 5.78-4.57 2.72" style="fill:#fff"/></svg></div>';
          echo '<div class="swiper-button-next has-'.$arrow_color.'-color" data-color="'.$arrow_color.'" data-color-background="'.$arrow_color_bg.'"><svg class="desktop-arrow" xmlns="http://www.w3.org/2000/svg" width="22" height="25" viewBox="0 0 22 25"><path id="Polygon_9" data-name="Polygon 9" d="M12.5,0,25,22H0Z" transform="translate(22) rotate(90)" fill="currentColor"/></svg><svg class="mobile-arrow" data-name="Group 3398" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 69.32 67.51"><path style="fill:none" d="M0 0h69.32v67.51H0z"/><g data-name="Group 6"><path data-name="Path 21" d="M69.17 36.8C67.7 52.83 52.8 65.56 37.22 67.34c-6.97.82-13.4-1.38-19.51-4.39-6.5-4.46-12.77-10.62-15.55-18.13-.16-1.44.86.16.94.78.91 2.29 2.19 4.41 3.78 6.29C4.78 46.76.82 41.92.59 36.07c0-.32-.14-.35-.45-.43-.49-.29.48-.87.2-1.25C-.71 21.77 8.46 6.95 20.89 4.15c-.04.04-.09.07-.14.1.21-.13.43-.23.65-.31-.54-.45 2.83-1.56 3.2-1.48-.39.45-.92.75-1.5.86-.17.03-.46.1-.53.26 3.66-.82 8.03-2.31 11.83-1.37 1.03-.29 2.08-.51 3.15-.65-3.27-.99-4.9-1.65.13-1.23 1.73.44 3.55.33 5.22-.32 1.27.41 2.57.57 3.84.89 5.1 1.86 8.74 5.87 12.78 9.19.65.3.17 1.16.95 1.28 2.37 2.4 3.47 5.96 4.41 9.21 3.87 11.31 2.62 24.25-4.53 33.88 3.34-3.06 5.74-9.3 6.86-13.82 1.4-4.95.23-10.28.51-15.25.56 1.02.48 2.48.83 3.65 1.04.88.75 6.28.61 7.76M28.61 61.86c-.1-.07-.19-.19-.3-.22-1.74-.57-3.61-.63-5.31-1.25-.58-.54-1.31-.89-2.1-.98-1.17-.22-2.25-1.13-3.36-1.33 3.11 2.55 7.04 3.89 11.07 3.78M5 20.94C2.41 25.6.87 31.37 2.41 36.58 2.21 30.25 3.9 24 7.27 18.63c.03-.07.04-.15.05-.23-1.36-.14-1.66 1.65-2.32 2.54m3.54-4.34c4.12-5.53 10.41-8.45 16.68-10.9-5.01.99-15.16 5.84-16.68 10.9" style="fill:#2b7b7c"/><path data-name="Path 22" d="M43.1 24.23c-2.24-2.72 5.06 3.95 5.68 3.99.61.22 1.15-.51 1.8-.18.27.04.72-.45.96-.65.44-.22.92-.36 1.41-.4.11.21.17.48.49.25.74-.65 1.35.47.73 1.03-.47.5-.04 1.03-.05 1.64.39.04 1.57-1.05 1.45-.3-.05.12-.19.26-.16.33.29.77-.42 1.06-.85 1.47-.06.59.49.05.71-.03 1.77.09-1.17 3.28-1.98 3.36-.46.22-.9.47-1.32.76a126.372 126.372 0 0 1-29.95 13.69c-.85-.29-1.66-1.73-1.06-2.56.1-.07.23.04.33.19.42.56.91-.04 1.1-.47.23-.41-.42-.27-.5-.57-.1-.68.99-.6 1.27-1.14.08-.58-.57-.21-.88-.15 5.35-4.62 13.21-6.78 19.17-11.03-2.75-3.99-8.86-7.1-13.18-10.16-2.43-1.36-4.29-3.56-6.55-5.15-.49-.24-.32-.83-.62-1.18-.52-.5.59-.64.8-1.01.41-.5.97-.39 1.5-.58-.53-.84.62-.96 1.14-1.32.63.2 1.34 0 1.91.52.29.3.75.36 1.1.14.25-.13.56-.08.76.13 3.14 2.72 6.8 4.41 10.22 6.68.91.19 7.6 5.78 4.57 2.72" style="fill:#fff"/></g></svg></div>';
        }
      }
      echo '</div>';
    }

    if ($attrs['enableSlidesPerView'] && $attrs['enableSlidesPerViewAuto']) { ?>
	<style>
		[data-uid="<?php echo $attrs['sliderId']; ?>"] .swiper .swiper-slide {
			width: <?php echo $attrs['slidesPerViewWidth'];
			?>;
		}

	</style>
	<?php } ?>
</section>
