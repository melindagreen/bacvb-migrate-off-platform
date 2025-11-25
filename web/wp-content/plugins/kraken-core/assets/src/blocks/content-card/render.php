<?php
namespace MaddenMedia\KrakenCore\Blocks\ContentCard;
use MaddenMedia\KrakenCore\Helpers as Helpers;

// only include if Kraken Events is installed
$events_plugin = Helpers::get_events_plugin();
$events_slug = Helpers::get_events_slug();
if ( $events_plugin ) {
	switch( $events_plugin ) {
		case 'kraken-events':
			include_once( 'inc/kraken-events.php' );
			break;

		case 'eventastic':
			break;

		case 'the-events-calendar':
			include_once( 'inc/the-events-calendar.php' );
			break;
	}
}

$attrs = $attributes;
$className = $attrs['className'] ?? '';
$backgroundColor = $attrs['backgroundColor'];
$textColor = $attrs['textColor'];

$classes = [
    $className,
    $attrs['postType'] . '-card',
    'card-style-' . $attrs['cardStyle'],
    $attrs['displayAdditionalContent'] ? 'has-additional-content' : 'has-title-only',
];

// Filter the classes array to allow themes to add/remove classes
$classes = apply_filters('kraken-core/content-card/classes', $classes, $attrs);

$styles = [
    $backgroundColor ? '--background-color: var(--wp--preset--color--' . $backgroundColor . ');' : '',
    $textColor ? '--color: var(--wp--preset--color--' . $textColor . ');' : ''
];

if ( $attrs['postType'] == $events_slug ) {
  $styles[] = ($attrs['eventDateBackgroundColor']) ? '--event-date-background-color:var(--wp--preset--color--' . $attrs['eventDateBackgroundColor'] . ');' : '';
  $styles[] = ($attrs['eventDateTextColor']) ? '--event-date-text-color:var(--wp--preset--color--' . $attrs['eventDateTextColor'] . ');' : '';
}

// Filter the inline styles to allow themes to add/remove styles
$styles = apply_filters('kraken-core/content-card/styles', $styles, $attrs);

global $post;
$id = 'queried_post' == $attrs['postType'] ? $post->ID : $attrs['contentId'];
$title = '';
$link = '';
$linkTarget = '';
$image = '';

if ($attrs['postType'] !== 'custom') {

	//title
    $base_title = get_the_title($id);
    if ($attrs['displayCustomTitle'] && $attrs['customTitle'] !== "") {
        $title = $attrs['customTitle'];
    } else {
        $title = $base_title;
    }

	//link
    $link = get_the_permalink($id);

	//image
	if ($attrs['displayCustomImage'] && isset($attrs['customImage']) && isset($attrs['customImage']['id'])) {
    	$image = wp_get_attachment_image($attrs['customImage']['id'], 'large');
	} else {
    	$image = get_the_post_thumbnail($id, 'large');
	}

} else {
    $attrs['displayAdditionalContent'] = false;
    $title = $attrs['contentTitle'];

    if ($attrs['customCtaUrl'] !== '') {
        $link = isset($attrs['customCtaUrl']['url']) ? $attrs['customCtaUrl']['url'] : '';
        if (isset($attrs['customCtaUrl']['opensInNewTab']) && $attrs['customCtaUrl']['opensInNewTab']) {
            $linkTarget = 'target="_blank"';
        }
    }

	if (isset($attrs['customImage']) && is_array($attrs['customImage']) && isset($attrs['customImage']['id'])) {
		$image = wp_get_attachment_image($attrs['customImage']['id'], 'large');
	} elseif (isset($attrs['customImage']) && is_numeric($attrs['customImage'])) {
		//fallback as old value was a number and not an object
		$image = wp_get_attachment_image($attrs['customImage'], 'large');
	}
}

//determine if the card actions bar should be below the content or not
//we are only displaying the website button IF the primary "read more" button is also displayed
$display_card_actions = false;
$separate_card_actions = false;
$secondary_action = false;

if ($attrs['displayWebsiteLink'] || ($attrs['displayMindtripCta'] && $attrs['mindtripCtaType'] === "button")) {
	$secondary_action = true;
}

//A secondary action is any link that is NOT the selected post/listing link
//Secondary actions always need to be output outside of the primary <a> element
//Otherwise the "read more" link is fake since the entire card links to the post
//When there are secondary actions, the read more is output as a real link after the primary <a> element but the second instance is hidden for screen readers
if ($attrs['displayAdditionalContent'] && $secondary_action) {
	//Check if a website link actually exists; otherwise only show the read more action
	if ($attrs['displayWebsiteLink']) {
		$website_link = apply_filters('kraken-core/content-card/website_link', get_field("website_link", $id), $id, $attrs);
		if ($website_link) {
			$separate_card_actions = true;
		}
	} elseif ($attrs['displayMindtripCta']) {
		$separate_card_actions = true;
	}
} elseif ($attrs['displayAdditionalContent'] && $attrs['displayReadMore']) {
	$display_card_actions = true;
}

if ($separate_card_actions) {
	$classes[] = 'has-separate-card-actions';
}

// Add filters for core content elements, passing the post ID and attributes for context.
$title = apply_filters('kraken-core/content-card/title', $title, $id, $attrs);
$link = apply_filters('kraken-core/content-card/link', $link, $id, $attrs);
$link_title = apply_filters('kraken-core/content-card/link_title', "View details about {$title}", $id, $attrs);
$image = apply_filters('kraken-core/content-card/image', $image, $id, $attrs);
$linkTarget = apply_filters('kraken-core/content-card/link_target', $linkTarget, $id, $attrs);

//mindtrip info
if ($attrs['displayMindtripCta']) {
	$mindtrip_default_text = $attrs['mindtripCtaText'] !== "" ? $attrs['mindtripCtaText'] : 'Explore';
	$mindtrip_default_prompt = $attrs['mindtripPrompt'] !== "" ? $attrs['mindtripPrompt'] : 'Explore things to do near %postname%';

	$mindtrip_cta_text = apply_filters('kraken-core/content-card/mindtrip_cta_text', $mindtrip_default_text, $id, $attrs);
	$mindtrip_prompt = apply_filters('kraken-core/content-card/mindtrip_prompt', $mindtrip_default_prompt, $id, $attrs);
	$mindtrip_icon = apply_filters('kraken-core/content-card/mindtrip_icon', file_get_contents(__DIR__ . '/icons/mindtrip.php'));

	//replace %postname% with the actual post name
	$mindtrip_prompt = str_replace('%postname%', $title, $mindtrip_prompt);

	$mindtrip_output = '<a href="#mindtrip.ai/chat/new?q='.urlencode($mindtrip_prompt).'" class="mindtrip-prompt" title="'.$mindtrip_prompt.'">';
	$mindtrip_output .= $mindtrip_icon;
	if ($attrs['mindtripCtaType'] === 'button') {
		$mindtrip_output .= '<span class="mindtrip-label">'.$mindtrip_cta_text.'</span>';
	} else {
		$mindtrip_output .= '<span class="sr-only">'.$mindtrip_prompt.'</span>';
	}
	$mindtrip_output .= '</a>';

	$classes[] = 'has-mindtrip-prompt';
	if ($attrs['mindtripCtaType'] === "icon-only") {
		$classes[] = 'has-mindtrip-overlay';
	}
}

// Use get_block_wrapper_attributes to output block supports
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => implode(' ', $classes),
    'style' => implode('', $styles)
]);

// Filter the wrapper attributes string to allow for advanced modifications
$wrapper_attributes = apply_filters('kraken-core/content-card/wrapper_attributes', $wrapper_attributes, $attrs);

// Action hook before the entire card article is rendered.
do_action('kraken-core/content-card/before_card', $id, $attrs);
?>

<article <?php echo $wrapper_attributes; ?>>

    <?php
    // Action hook at the card top
    do_action('kraken-core/content-card/card_top', $id, $attrs);
    ?>

    <a href="<?php echo esc_url($link); ?>" <?php echo $linkTarget; ?> class="primary-card-link">

		<?php if ($attrs['cardStyle'] !== "text-only") { ?>
        <div class="featured-media">
            <?php
            // Action hook right before the image is output.
            do_action('kraken-core/content-card/before_image', $id, $attrs);

            echo $image; // The filtered image variable

            // Action hook right after the image is output.
            do_action('kraken-core/content-card/after_image', $id, $attrs);
            ?>
        </div>
		<?php } ?>

        <div class="content">
            <?php
            $content_elements = apply_filters('kraken-core/content-card/content_elements', [
              'title',
              'event_details',
              'address',
              'excerpt',
              'actions',
            ], $id, $attrs);

            $event_elements = apply_filters('kraken-core/content-card/event_elements', [
              'date',
              'time',
              'address',
            ], $id, $attrs);

            // Action hook at the top of the content area, before the title.
            do_action('kraken-core/content-card/content_top', $id, $attrs);

            if ( is_array( $content_elements ) && ! empty( $content_elements ) ) {
              foreach ( $content_elements as $element ) {
                switch( $element ) {

                  case 'title':
                    echo '<h3 class="post-title">' . esc_html($title) . '</h3>';
                    do_action('kraken-core/content-card/after_title', $id, $attrs);
                    break;

                  case 'event_details':
                    if ($attrs['displayAdditionalContent'] && $attrs['postType'] === $events_slug) {
                      // Action hook for adding content before event details.
                      do_action('kraken-core/content-card/before_event_details', $id, $attrs);

                      if ( is_array( $event_elements ) && ! empty( $event_elements ) ) {
                        foreach ( $event_elements as $el ) {

                          switch( $el ) {
                            case 'date':
                              if ($attrs['displayEventDate']) {
    							$filtered_start_date = isset($attrs['customAdditionalContent']['filtered_start_date']) ? $attrs['customAdditionalContent']['filtered_start_date'] : null;
    							$filtered_end_date = isset($attrs['customAdditionalContent']['filtered_end_date']) ? $attrs['customAdditionalContent']['filtered_end_date'] : null;
                                echo displayEventDates($id, $filtered_start_date, $filtered_end_date, $attrs);
                              }
                              break;

                            case 'time':
                              if ($attrs['displayEventTime']) {
                                echo displayEventTimes($id);
                              }
                              break;

                            case 'address':
                              if ($attrs['displayAddress']) {
                                echo displayEventLocation($id);
                              }
                              break;

                            default:
                              if ( str_starts_with( $el, 'action_' ) ) {
                                do_action("kraken-core/content-card/{$el}", $id, $attrs);
                              }
                              break;
                          }
                        }
                      }

                      // Action hook for adding content after event details.
                      do_action('kraken-core/content-card/after_event_details', $id, $attrs);
                    }
                    break;

                  case 'address':
                    if ($attrs['displayAdditionalContent'] && $attrs['postType'] !== $events_slug && $attrs['displayAddress']) {
                      $address_output = '';
                      $address 	= get_field('street_address', $id);
                      $city 		= get_field('city', $id);
                      $state 		= get_field('state', $id);
                      $zip 		  = get_field('zip', $id);
                      if ($address) {
                        $address_output .= $address;
                      }
                      if ($city) {
                        if ($address) { $address_output .= '<br>'; }
                        $address_output .= $city;
                      }
                      if ($state) {
                        $address_output .= ', '.$state;
                      }
                      if ($zip) {
                        $address_output .= ' '.$zip;
                      }

                      $address_output = apply_filters('kraken-core/content-card/address', $address_output, $id, $attrs);
                      if ($address_output !== '') {
                        echo '<div class="address">';
                        include __DIR__ . '/icons/location.php';
                        echo $address_output.'</div>';
                      }
                    }
                    break;

                  case 'excerpt':
                    if ($attrs['displayAdditionalContent']) {
                      if ($attrs['displayExcerpt']) {
                        $excerpt = wp_trim_words(get_the_excerpt($id), $attrs['excerptLength'], '…');
                        $excerpt = apply_filters('kraken-core/content-card/excerpt', $excerpt, $id, $attrs);
                        echo '<div class="excerpt">' . $excerpt . '</div>';
                      }
                      if ($attrs['displayCustomExcerpt']) {
                        $custom_excerpt = apply_filters('kraken-core/content-card/custom_excerpt', $attrs['customExcerpt'], $id, $attrs);
                        echo '<div class="excerpt">' . $custom_excerpt . '</div>';
                      }
                    }
                    break;

                  case 'actions':
                    if ($attrs['displayAdditionalContent'] && ($attrs['postType'] === 'custom' || (!$separate_card_actions && $display_card_actions))) {
                      ?>
                      <div class="card-actions">
                        <?php
						// do not add extra link elements to this section!
                        // Action hook inside the actions wrapper, before the buttons/links.
                        do_action('kraken-core/content-card/before_actions', $id, $attrs);

                        if ($attrs['postType'] === 'custom') {
                          if(!empty($attrs['customCtaText'])) {
                            $custom_cta_text = apply_filters('kraken-core/content-card/custom_cta_text', $attrs['customCtaText'], $id, $attrs);
                            echo '<a href="'.esc_url($link).'" '.$linkTarget.' class="read-more-link">' . esc_html($custom_cta_text) . '</a>';
                          }
                        } else {
                          if ($attrs['displayReadMore']) {
                            $read_more_text = apply_filters('kraken-core/content-card/read_more_text', $attrs['readMoreText'], $id, $attrs);
                            echo '<span class="read-more-link">' . esc_html($read_more_text) . '</span>';
                          }
                        }

                        // Action hook inside the actions wrapper, after the buttons/links.
                        do_action('kraken-core/content-card/after_actions', $id, $attrs);
                        ?>
                      </div>
                      <?php
                    }
                    break;

                  default:
                    if ( str_starts_with( $element, 'action_' ) ) {
                      do_action("kraken-core/content-card/{$element}", $id, $attrs);
                    }
                    break;
                }
              }
            }
            ?>

            <?php
            // Action hook at the bottom of the content area.
            do_action('kraken-core/content-card/content_bottom', $id, $attrs);
            ?>
        </div>
    </a>

	<?php if ($separate_card_actions) { ?>
		<div class="card-actions with-secondary-link">
			<?php
			// Action hook inside the actions wrapper, before the buttons/links.
			do_action('kraken-core/content-card/before_actions', $id, $attrs);

			if ($attrs['displayReadMore']) {
				$read_more_text = apply_filters('kraken-core/content-card/read_more_text', $attrs['readMoreText'], $id, $attrs);
				echo '<a href="'.esc_url($link).'" '.$linkTarget.' class="read-more-link" aria-hidden="true" tabindex="-1" title="'.$link_title.'">' . esc_html($read_more_text) . '</a>';
			}

			if ($attrs['displayWebsiteLink']) {
				$website_link_text = apply_filters('kraken-core/content-card/website_link_text', 'Visit Website', $id, $attrs);
				echo '<a href="'.$website_link.'" class="website-link" target="_blank" title="Visit the website for '.$title.'">' . esc_html($website_link_text) . '</a>';
			}

			if ($attrs['displayMindtripCta'] && $attrs['mindtripCtaType'] === 'button') {
				echo $mindtrip_output;
			}

			// Action hook inside the actions wrapper, after the buttons/links.
			do_action('kraken-core/content-card/after_actions', $id, $attrs);
			?>
		</div>
	<?php } ?>

	<?php
	if ($attrs['displayMindtripCta'] && $attrs['mindtripCtaType'] === "icon-only") {
		echo $mindtrip_output;
	}
	?>

  <?php
  // Action hook at the card bottom
  do_action('kraken-core/content-card/card_bottom', $id, $attrs);
  ?>

</article>

<?php
// Action hook after the entire card article is rendered.
do_action('kraken-core/content-card/after_card', $id, $attrs);
?>
