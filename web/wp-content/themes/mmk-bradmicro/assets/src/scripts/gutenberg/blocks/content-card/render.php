<?php

namespace MaddenTheme\Blocks\ContentCard;
use MaddenTheme\Library\Constants as C;

//only include if Kraken Events is installed
//include_once('inc/kraken-events.php');

/**
 * Render function for the dynamic example block
 * @param array $attributes        all block attributes
 * @param string $content
 */

$attrs = $attributes;

$className = $attrs['className'] ?? '';
$backgroundColor = $attrs['backgroundColor'];
$textColor = $attrs['textColor'];

$classes = [
  	$attrs['postType'].'-card',
  	'card-style-'.$attrs['cardStyle'],
  	$className
];

//Colors from the block supports are output as variables
//Use the variables on each card style as needed
$styles = [
	$backgroundColor ? '--background-color: var(--wp--preset--color--'.$backgroundColor.');' : '',
	$textColor ? '--color: var(--wp--preset--color--'.$textColor.');' : ''
];

//Use get_block_wrapper_attributes to output block supports
$wrapper_attributes = get_block_wrapper_attributes([
	'class' => implode(' ', $classes),
	'style'	=> implode('', $styles)
]);

global $post;

$id         = 'queried_post' == $attrs['postType'] ? $post->ID : $attrs['contentId'];
$title      = '';
$link       = '';
$linkTitle  = '';
$linkTarget = '';
$image      = '';

if ($attrs['postType'] !== 'custom') {
	$title    	= get_the_title($id);
	$link       = get_the_permalink($id);
	$linkTitle  = get_the_title($id);
	$image      = get_the_post_thumbnail($id, 'large');
} else {

  $attrs['displayAdditionalContent'] = false;
  $title = $attrs['contentTitle'];
  $contentExcerpt = $attrs['contentExcerpt'];

  if ($attrs['customCtaUrl'] !== '') {
    $link       = isset($attrs['customCtaUrl']['url']) ? $attrs['customCtaUrl']['url'] : '';
    $linkTitle  = isset($attrs['customCtaUrl']['title']) ? $attrs['customCtaUrl']['title'] : $attrs['contentTitle'];

    $linkTarget = '';
    if (isset($attrs['customCtaUrl']['opensInNewTab']) && $attrs['customCtaUrl']['opensInNewTab']) {
      $linkTarget = 'target="_blank"';
    }
  } else {
    $link = '';
    $linkTitle = $attrs['contentTitle'];
    $linkTarget = '';
  }

  $image = wp_get_attachment_image($attrs['customImage'], 'large');
}
?>

<article <?php echo $wrapper_attributes; ?>>
	<?php
	if ( $link ) {
		echo '<a class="card-contents" href="' . $link . '" title="' . $linkTitle . '" ' . $linkTarget . '>';
	} else {
		echo '<span class="card-contents">';
	}
	?>

		<div class="featured-media">
			<?php echo $image; ?>
		</div>

		<?php echo do_action( 'madden-theme/content-card/after-image', $id, $attrs ); ?>

		<div class="content">

			<?php echo do_action( 'madden-theme/content-card/content-top', $id, $attrs ); ?>

			<h3 class="post-title"><?php echo $title; ?></h3>

			<?php echo do_action( 'madden-theme/content-card/after-title', $id, $attrs ); ?>

			<?php if ($attrs['displayAdditionalContent']) { ?>

				<?php
				if ($attrs['postType'] === 'event' && $attrs['displayEventDate']) {
					//be sure to uncomment the kraken-events.php file above
					//echo displayEventDates($id);
				}
				?>

				<?php
				if ($attrs['postType'] === 'event' && $attrs['displayEventTime']) {
					//be sure to uncomment the kraken-events.php file above
					//echo displayEventTimes($id);
				}
				?>

				<?php if ($attrs['displayExcerpt']) { ?>
				<div class="excerpt">
					<?php echo wp_trim_words(get_the_excerpt($id), $attrs['excerptLength'], '…'); ?>
				</div>
				<?php } ?>

				<?php if ($attrs['displayCustomExcerpt']) { ?>
				<div class="excerpt">
					<?php echo $attrs['customExcerpt']; ?>
				</div>
				<?php } ?>

				<?php if ($attrs['displayReadMore']) { ?>
				<div class="read-more"><?php echo $attrs['readMoreText']; ?></div>
				<?php } ?>

			<?php } ?>


			<?php if ($attrs['postType'] === 'custom') { ?>
			<?php if ($attrs['contentExcerpt']) { ?>
				<div class="excerpt">
					<?php echo $attrs['contentExcerpt']; ?>
				</div>
			<?php } ?>
				<?php if( isset( $attrs['customCtaText'] ) && '' != $attrs['customCtaText'] ) { ?>
					<div class="read-more"><?php echo $attrs['customCtaText']; ?></div>
				<?php } ?>
			<?php } ?>

			<?php echo do_action( 'madden-theme/content-card/content-bottom', $id, $attrs ); ?>

		</div>
		<?php
		echo $link ? '</a>' : '</span>';
		?>
</article>
