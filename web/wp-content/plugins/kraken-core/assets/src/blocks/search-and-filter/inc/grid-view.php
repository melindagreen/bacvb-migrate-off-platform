<?php
namespace MaddenMedia\KrakenCore\Blocks\SearchResultsFilter;

if ($gridView) {
?>
<div class="results-grid active <?php echo $attributes['enabledView']; ?>">
    <?php
    $i = 1;

    //Check if content card block exists; use that if so.
    $cardBlock = false;
    if (\WP_Block_Type_Registry::get_instance()->is_registered('kraken-core/content-card')) {
        $cardBlock = true;
    }

	$has_posts = true;

	// Don't show all posts if post__in exists and no posts
	if ( isset( $query->query['post__in'] ) && empty( $query->query['post__in'] ) ) {
		$has_posts = false;
	}

	if ( $has_posts && $query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();

			$id     = get_the_ID();

			if ($cardBlock) {
				$cardAttrs = array_merge($cardJson,  array(
					"contentId" => $id,
					"blockParent" => 'kraken-core/search-and-filter'
				));
				$cardAttrs = apply_filters( 'kraken-core/search-and-filter/card_attrs', $cardAttrs, $args );

				echo do_blocks('<!-- wp:kraken-core/content-card '.json_encode($cardAttrs).' /-->');
			} else {
				// Generic fallback card; add your own styles
				$title  = get_the_title();
				$link   = get_permalink();
				$image  = get_the_post_thumbnail($id, 'large');

				$listingHTML = '<article class="grid-item">';
				$listingHTML .= '<a href="' . $link . '" title="'.$title.'" class="overlay-link"></a>';
				$listingHTML .= '<div class="featured-media">' . $image . '</div>';
				$listingHTML .= '<div class="content">';
				$listingHTML .= '<h3 class="post-title">' . $title . '</h3>';
				$listingHTML .= '<div class="read-more">LEARN MORE →</div>';
				$listingHTML .= '</div></article>';

				echo $listingHTML;
			}

			$i++;
		}

		// Restore original post data
		wp_reset_postdata();
	} else {
		// No posts found
		echo  apply_filters( 'kraken-core/search-and-filter/no_results_text', '<p class="no-results-found">No results found</p>', $attributes, $query );
	}
    ?>
</div>
<?php } ?>
