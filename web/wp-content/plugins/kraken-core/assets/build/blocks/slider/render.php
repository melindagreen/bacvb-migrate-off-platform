<?php
namespace MaddenMedia\Blocks\Slider;
use MaddenMedia\KrakenCore\Utilities as U;

/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content
 */

$attrs = $attributes;
$unique_id = uniqid('kraken-slider-');

$anchor = $attrs['anchor'] ?? '';
$className = $attrs['className'] ?? '';
$arrowPosition = '';

if ($attrs['enableArrowNavigation']) {
	if ($attrs['arrowsBelowSlider']) {
		$arrowPosition = 'slider-arrows-below';
	} elseif ($attrs['arrowsOutsideSlider']) {
		$arrowPosition = 'slider-arrows-outside';
	} else {
		$arrowPosition = '';
	}
}

$classes = [
	$unique_id,
	'slider-type-'.$attrs['contentType'],
	$className,
	$attrs['enableArrowNavigation'] ? 'nav-arrows-enabled' : '',
	$attrs['enableEqualHeightSlides'] ? 'force-equal-height-slides' : '',
	$arrowPosition
];

$styles = [];

if ($attrs['enableSlidesPerView'] && $attrs['enableSlidesPerViewAuto']) {
  $styles[] = '--swiper-slide-width-desktop: '.$attrs['slidesPerViewWidthDesktop'].';';
  $styles[] = '--swiper-slide-width-tablet: '.$attrs['slidesPerViewWidthTablet'].';';
  $styles[] = '--swiper-slide-width-mobile: '.$attrs['slidesPerViewWidthMobile'].';';
}

//Generate inline styles
$color_styles = '';
$color_attributes = [
    "arrowColor", "arrowBackgroundColor", "arrowBackgroundHoverColor", "paginationColor", "paginationActiveColor", "scrollbarColor"
];

$color_attributes = apply_filters('kraken-core/slider/color-options', $color_attributes, $attrs);

foreach ($color_attributes as $attr) {
	if (!empty($attrs[$attr])) {
		$css_var = '--' . U::to_kebab_case($attr);
		$color_styles .= "{$css_var}: var(--wp--preset--color--{$attrs[$attr]});";
	}
}

if (!empty($color_styles)) {
	// Attempting to make this more specific than any plugin or theme stylesheets since the editor settings should have top priority
	$style_rules = "body .{$unique_id}.wp-block-kraken-core-slider { {$color_styles} }";
	wp_add_inline_style('kraken-core-slider-style', $style_rules);
}

$wrapper_attributes = get_block_wrapper_attributes([
	'id'	=> $anchor,
  'class' => implode(' ', $classes),
	'style'	=> implode(';', $styles),
  ' data-uid' => $attrs['sliderId']
]);
?>

<section <?php echo $wrapper_attributes; ?>>
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

        } elseif ($attrs['contentType'] === 'automatic' || $attrs['contentType'] === 'related') {

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

					if ($attrs['contentType'] === 'related' && is_single()) {
    				$current_post_id = get_the_ID();
    				$categories = get_the_category($current_post_id);
						if (!empty($categories)) {
							$category_ids = wp_list_pluck($categories, 'term_id');
							$queryArgs['tax_query'][] = [
								'taxonomy'	=> 'category',
								'field'			=> 'term_id',
								'terms'			=> $category_ids
							];
						}
						$queryArgs['post__not_in'] = [$current_post_id];
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

              echo do_blocks('<!-- wp:kraken-core/content-card '.json_encode($cardAttrs).' /-->');

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

            echo do_blocks('<!-- wp:kraken-core/content-card '.json_encode($cardAttrs).' /-->');

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
          echo do_blocks('<!-- wp:kraken-core/content-card '.json_encode($cardAttrs).' /-->');
          echo '</div>';
        }
        ?>
		</div>
	</div>

	<?php
	if ($attrs['enableScrollbar'] || $attrs['enablePagination'] || $attrs['enableArrowNavigation']) {
		echo '<div class="swiper-navigation-wrapper">';
			if ($attrs['enableScrollbar']) {
				echo '<div class="swiper-scrollbar"></div>';
			}
			if ($attrs['enablePagination']) {
				echo '<div class="swiper-pagination"></div>';
			}
			if ($attrs['enableArrowNavigation']) {
				echo '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
			}
		echo '</div>';
	}
	?>

</section>
