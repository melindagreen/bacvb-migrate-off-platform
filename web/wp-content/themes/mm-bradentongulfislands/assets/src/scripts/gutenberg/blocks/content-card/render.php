<?php

namespace MaddenNino\Blocks\ContentCard;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  $anchor = $attrs['anchor'] ?? '';

  $classes = [
    Constants::BLOCK_CLASS.'-content-card',
    $attrs['contentType'].'-card',
    'card-style-'.$attrs['cardStyle']
  ];

  $id         = $attrs['contentId'];
  $title      = '';
  $link       = '';
  $linkTitle  = '';
  $linkTarget = '';
  $image      = '';

  if ($attrs['contentType'] !== 'custom') {
    $title      = get_the_title($id);
    $link       = get_the_permalink($id);
    $excerpt = wp_trim_words(get_the_excerpt($id), 20, '…');
    $linkTitle  = get_the_title($id);
    $image      = get_the_post_thumbnail($id, 'large');
  } else {
    $attrs['displayAdditionalContent'] = false;

    $title = $attrs['contentTitle'];
    $excerpt = $attrs['contentExcerpt'];

    if ($attrs['customCtaUrl'] !== '') {
      $link       = $attrs['customCtaUrl']['url'];
      $linkTitle  = $attrs['customCtaUrl']['title'] ? $attrs['customCtaUrl']['title'] : $attrs['contentTitle'];

      $linkTarget = '';
      if (isset($attrs['customCtaUrl']['opensInNewTab']) && $attrs['customCtaUrl']['opensInNewTab']) {
        $linkTarget = 'target="_blank"';
      }
    } else {
      $link = '';
      $linkTitle = $attrs['contentTitle'];
      $linkTarget = '';
    }

    $image      = wp_get_attachment_image($attrs['customImage'], 'large');
  }

  ob_start(); 
  ?>

<article id="<?php echo $anchor; ?>" class="<?php echo implode(' ', $classes); ?>" data-title="<?php echo esc_attr($title); ?>" data-excerpt="<?php echo esc_attr($excerpt); ?>" data-link="<?php echo esc_attr($link); ?>">

    <a href="<?php echo $link; ?>" title="<?php echo $linkTitle; ?>" <?php echo $linkTarget; ?> class="overlay-link">
    </a>

    <div class="featured-media">
      <?php echo $image; ?>
    </div>

		<div class="content">

			<?php if ($attrs['displayAdditionalContent'] && ($attrs['displayCategory'])) { ?>
			<div class="post-meta">
				<?php  
            if ($attrs['displayCategory']) {
              $categories = get_the_terms($id, 'category');
              if ($categories) {
                $i = 0;
                foreach($categories as $cat) {
                  if ($i > 0) { echo ', '; }
                  echo '<a href="/'.$cat->slug.'">'.$cat->name.'</a>';
                  $i++;
                }
              }
            }
            ?>
			</div>
			<?php } ?>

			<h3 class="post-title"><?php echo $title; ?></h3>

			<?php if ($attrs['displayAdditionalContent']) { ?>

        <?php if ($attrs['displayExcerpt']) { ?>
        <div class="excerpt">
          <?php echo wp_trim_words(get_the_excerpt($id), $attrs['excerptLength'], '…'); ?>
        </div>
        <?php } ?>

        <?php if ($attrs['displayReadMore']) { ?>
          <div class="read-more"><?php echo $attrs['readMoreText']; ?></div>
        <?php } ?>

      <?php } ?>

      <?php if ($attrs['contentType'] === 'custom') { ?>
        <div class="read-more"><?php echo $attrs['customCtaText']; ?></div>
			<?php } ?>

		</div>
    
	</a>
</article>

<?php
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}
