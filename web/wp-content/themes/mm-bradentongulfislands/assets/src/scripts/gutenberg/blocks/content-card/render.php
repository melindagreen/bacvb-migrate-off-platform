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

    $mime_type = get_post_mime_type($attrs['customImage']);
    $attachment_url = wp_get_attachment_url($attrs['customImage']);

    if (strpos($mime_type, 'video/') === 0) {
      $image = '<video class="content-card-video" autoplay muted loop playsinline>
                  <source src="' . esc_url($attachment_url) . '" type="' . esc_attr($mime_type) . '">
                  Your browser does not support the video tag.
                </video>';
    } else {
      $image = wp_get_attachment_image($attrs['customImage'], 'large');
    }
  }

  ob_start(); 
  ?>

<article id="<?php echo $anchor; ?>" class="<?php echo implode(' ', $classes); ?>" data-title="<?php echo esc_attr($title); ?>" data-excerpt="<?php echo esc_attr($excerpt); ?>" data-link="<?php echo esc_attr($link); ?>">

    <?php /*
    <a href="<?php echo $link; ?>" title="<?php echo $linkTitle; ?>" <?php echo $linkTarget; ?> class="overlay-link">
    </a> */ ?>

    <!-- Open inner container for pic-left card -->
    <?php if($attrs['cardStyle'] === 'pic-left'){ ?>
      <div class="inner">
    <?php } ?>

    <div class="featured-media">
      <?php echo $image; ?>
    </div>

		<div class="content">

      <?php if($attrs['cardStyle'] === 'signature-card'){ ?>
        <div class="post-date">FEB 14 - MAR 21</div>
      <?php } ?>

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

      <?php if($attrs['cardStyle'] === 'content-overlay'){ ?>
        <hr class="overlay-hr" />
      <?php } ?>

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

      <?php if ($attrs['contentType'] === 'custom' && !empty($excerpt)) { ?>
        <div class="excerpt">
          <?php echo $excerpt; ?>
        </div>
			<?php } ?>

      <?php if ($attrs['contentType'] === 'custom' && !empty($link)) { ?>
        <a href="<?php echo $link; ?>" title="<?php echo $linkTitle; ?>" <?php echo $linkTarget; ?> class="read-more">
          <?php echo $attrs['customCtaText']; ?>
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="39.15" height="38.13" viewBox="0 0 39.15 38.13">
            <defs>
              <clipPath id="clip-path">
                <rect id="Rectangle_4" data-name="Rectangle 4" width="39.15" height="38.13" fill="#77c3c6"/>
              </clipPath>
            </defs>
            <g id="Group_1993" data-name="Group 1993" transform="translate(-1658 -4105.231)">
              <g id="Group_6" data-name="Group 6" transform="translate(1658 4105.231)" clip-path="url(#clip-path)">
                <path id="Path_21" data-name="Path 21" d="M39.067,20.782c-.829,9.058-9.246,16.243-18.043,17.251-3.935.462-7.568-.781-11.02-2.479-3.668-2.518-7.211-6-8.783-10.24-.088-.811.486.091.532.439a12.914,12.914,0,0,0,2.135,3.554C2.7,26.41.465,23.674.332,20.373c0-.179-.077-.2-.253-.242-.276-.165.269-.49.112-.7C-.4,12.3,4.776,3.923,11.8,2.346a.962.962,0,0,1-.079.056,2.07,2.07,0,0,1,.369-.177c-.3-.253,1.6-.88,1.807-.839a1.48,1.48,0,0,1-.85.483c-.1.016-.258.058-.3.146,2.065-.465,4.537-1.3,6.681-.776A12.56,12.56,0,0,1,21.2.871c-1.844-.56-2.767-.932.072-.695A4.823,4.823,0,0,0,24.223,0c.718.23,1.45.321,2.17.5,2.881,1.048,4.936,3.317,7.22,5.192.365.17.093.655.539.725a11.977,11.977,0,0,1,2.488,5.2c2.186,6.391,1.48,13.694-2.558,19.137,1.884-1.731,3.241-5.25,3.877-7.805.79-2.8.13-5.805.29-8.614a11.167,11.167,0,0,1,.472,2.061c.59.5.425,3.545.346,4.381M16.157,34.936c-.056-.042-.109-.109-.17-.125-.985-.321-2.04-.355-3-.7a2.1,2.1,0,0,0-1.185-.555c-.662-.125-1.268-.637-1.9-.75a9.438,9.438,0,0,0,6.249,2.135M2.823,11.829C1.359,14.456.49,17.72,1.364,20.663a18.025,18.025,0,0,1,2.744-10.14.37.37,0,0,0,.026-.13c-.771-.077-.936.934-1.31,1.436m2-2.453C7.151,6.253,10.7,4.6,14.243,3.22c-2.827.56-8.56,3.3-9.42,6.156" transform="translate(0 0)" fill="#77c3c6"/>
                <path id="Path_22" data-name="Path 22" d="M63.1,50.326c-1.265-1.533,2.859,2.234,3.21,2.251.347.127.647-.286,1.015-.1.152.021.408-.256.543-.37a2.125,2.125,0,0,1,.8-.223c.062.118.1.273.276.14.417-.365.765.268.415.579-.266.285-.023.584-.027.928.222.025.889-.592.819-.17-.026.066-.1.148-.089.186.165.434-.236.6-.479.828-.033.331.279.029.4-.015,1,.052-.659,1.853-1.117,1.9a5.282,5.282,0,0,0-.746.429,71.139,71.139,0,0,1-16.912,7.731c-.479-.165-.94-.977-.6-1.445.059-.041.128.02.184.109.24.314.513-.02.623-.263.13-.229-.234-.151-.285-.322-.055-.386.558-.339.717-.642.046-.326-.32-.118-.5-.085,3.024-2.612,7.458-3.831,10.83-6.232-1.553-2.253-5-4.01-7.445-5.738a45.966,45.966,0,0,1-3.7-2.911c-.275-.136-.181-.472-.353-.667-.294-.283.334-.362.451-.571.233-.28.55-.22.85-.326-.3-.475.349-.543.646-.748.354.115.755,0,1.076.292a.494.494,0,0,0,.62.078.354.354,0,0,1,.427.072,52.533,52.533,0,0,0,5.771,3.775c.515.11,4.293,3.263,2.583,1.534" transform="translate(-38.76 -36.641)" fill="#fff"/>
              </g>
            </g>
          </svg>
        </a>

			<?php } ?>
		</div>
    
    <!-- Close inner container for pic left card -->
    <?php if($attrs['cardStyle'] === 'pic-left'){ ?>
      </div>
    <?php } ?>
</article>

<?php
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}
