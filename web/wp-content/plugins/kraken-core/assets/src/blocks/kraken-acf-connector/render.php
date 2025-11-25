<?php
/**
 * Kraken ACF Connector Block Render
 *
 * This file handles the rendering of the Kraken ACF Connector block,
 * which allows users to display ACF field data in various formats.
 *
 * @package MaddenMedia\KrakenCore\Blocks\KrakenACF
 * @since 1.0.0
 */

namespace MaddenMedia\KrakenCore\Blocks\KrakenACF;
use MaddenMedia\KrakenCore\Helpers as Helpers;
include_once __DIR__ . '/inc/functions.php';

global $post;

$attrs = $attributes;
$post_type_slug = 'post';

$in_editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST && !empty($_REQUEST['context']) && $_REQUEST['context'] === 'edit';

// Validate that we have a valid post ID
if (!$in_editor_preview && (!$post || !isset($post->ID))) {
	echo '<div class="kraken-acf-connector-error">Error: No valid post context available.</div>';
	return;
}

//Make sure this is actually a Kraken Events website
if ($attrs['contentType'] === 'kraken-events') {
	$events_plugin 		= Helpers::get_events_plugin();
	$post_type_slug 	= Helpers::get_events_slug();
	//Stop here if we aren't using Kraken Events
	if ($events_plugin !== "kraken-events") {
		echo 'This setting requires Kraken Events.';
		return;
	} else {
		include_once('inc/kraken-events.php');
	}
} elseif ($attrs['contentType'] === 'kraken-crm') {
	$post_type_slug 	= Helpers::get_kraken_crm_listing_slug();
}

if ($in_editor_preview) {
    $query = new \WP_Query([
        'post_type'      	=> $post_type_slug,
        'posts_per_page' 	=> 1,
		'post_status'		=> 'publish',
		'orderby'			=> 'date',
		'order'				=> 'desc',
        'fields'         	=> 'ids'
    ]);

    if ($query->have_posts()) {
        $id = $query->posts[0];
    }

    wp_reset_postdata();
} else {
    $id = $post->ID;
}

//add the preset field name or the custom field name to the class list
$data_class_name = $attrs['contentType'] === 'custom' ? $attrs['customField'] : $attrs['presetField'];
$icon_class_name = $attrs['displayIcon'] ? 'has-icon': '';

$classes = [
    $attrs['className'] ?? '',
    $attrs['contentType'].'-'.$data_class_name,
	$icon_class_name
];

$wrapper_attributes = get_block_wrapper_attributes([
	'class' => implode(' ', $classes)
]);

$block_output = '';

/**
 * Handle preset field types for Kraken CRM and Kraken Events content
 * Each case handles a specific field type with appropriate formatting and accessibility
 */
if ($attrs['contentType'] !== "custom") {
	switch ($attrs["presetField"]):
		case 'crm_hours':
			//This needs some work, the current fields are a bit odd.
			//The default hours field in Kraken CRM should be updated:
			//dotw should be multiselect
			//time needs to have start & end time
			$hours = get_field('hours', $id);
			if ($hours) {
				$list = "";
				foreach($hours as $row) {
					$dotw = $row['dotw'];
					$time = $row['time'];
					$list .= '<strong>' . ucfirst($dotw) . ':</strong> ' . $time . '<br>';
				}
				if ($list) {
					$block_output = '<!-- wp:paragraph --><p>'.$list.'</p><!-- /wp:paragraph -->';
				}
			}
			break;
		case 'crm_website':
			$website_field = apply_filters('kraken-core/kraken-acf-connector/crm_website_field_name', 'website_link', $id, $attrs);
			$website_link = get_field($website_field, $id);

			if ($website_link && filter_var($website_link, FILTER_VALIDATE_URL)) {
				//see if this field exists first
				$website_text_field = apply_filters('kraken-core/kraken-acf-connector/crm_website_text_field_name', 'website_link_text', $id, $attrs);
				$website_link_text = get_field($website_text_field, $id);

				//use the customlinktext field if text is set or fallback to website_link_text field
				$website_link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $website_link_text;

				if (!$website_link_text) {
					//if nothing else is set, fallback to this
					$website_link_text = 'Visit Website';
				}

				//if for some reason we need to customize it before output...
				$link_text = apply_filters('kraken-core/kraken-acf-connector/crm_website_link_text', $website_link_text, $id, $attrs);

				// Add accessibility attributes
				$aria_label = sprintf(__('Visit website: %s', 'kraken-core'), $website_link);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/crm_website_aria_label', $aria_label, $id, $attrs);

				$block_output = '<!-- wp:paragraph --><p><a href="' . esc_url($website_link) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'crm_phone':
			$phone_field = apply_filters('kraken-core/kraken-acf-connector/crm_phone_field_name', 'phone', $id, $attrs);
			$phone = get_field($phone_field, $id);

			if ($phone && !empty(trim($phone))) {
				// Sanitize phone number for tel: link
				$phone_clean = preg_replace('/[^0-9+]/', '', $phone);

				//use the customlinktext field if text is set or fallback to the phone number
				$phone_link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $phone;

				//customize it before output if needed
				$link_text = apply_filters('kraken-core/kraken-acf-connector/crm_phone_link_text', $phone_link_text, $id, $attrs);

				if ($attrs['displayIcon']) {
					$block_output .= apply_filters( 'kraken-core/kraken-acf-connector/phone_icon', file_get_contents( __DIR__ . '/icons/phone.php'));
				}

				// Add accessibility attributes
				$aria_label = sprintf(__('Call %s', 'kraken-core'), $phone);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/crm_phone_aria_label', $aria_label, $id, $attrs);
				$block_output .= '<!-- wp:paragraph --><p><a href="tel:' . esc_attr($phone_clean) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'crm_email':
			$email_field = apply_filters('kraken-core/kraken-acf-connector/crm_email_field_name', 'email', $id, $attrs);
			$email = get_field($email_field, $id);

			if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
				//use the customlinktext field if text is set or fallback to the email address
				$email_link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $email;

				//customize it before output if needed
				$link_text = apply_filters('kraken-core/kraken-acf-connector/crm_email_link_text', $email_link_text, $id, $attrs);

				if ($attrs['displayIcon']) {
					$block_output .= apply_filters( 'kraken-core/kraken-acf-connector/email_icon', file_get_contents( __DIR__ . '/icons/email.php'));
				}

				// Add accessibility attributes
				$aria_label = sprintf(__('Send email to %s', 'kraken-core'), $email);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/crm_email_aria_label', $aria_label, $id, $attrs);
				$block_output .= '<!-- wp:paragraph --><p><a href="mailto:' . esc_attr($email) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'crm_address':
			//default is a google_map field
			$map_field_name = apply_filters('kraken-core/kraken-acf-connector/crm_map_field_name', 'address', $id, $attrs);
			$map_field 	= get_field($map_field_name, $id);

			$address 	= [];

			if (is_array($map_field)) {
				$address = [
					'multi'		=> '',
					'street' 	=> isset($map_field['street_number']) ? sanitize_text_field($map_field['street_number']) : '',
					'street2' 	=> isset($map_field['street_name']) ? sanitize_text_field($map_field['street_name']) : '',
					'city' 		=> isset($map_field['city']) ? sanitize_text_field($map_field['city']) : '',
					'state' 	=> isset($map_field['state']) ? sanitize_text_field($map_field['state']) : '',
					'zip' 		=> isset($map_field['post_code']) ? sanitize_text_field($map_field['post_code']) : '',
				];
			} else {
				//fallback to individual fields w/ filter for customizations
				$keys = apply_filters('kraken-core/kraken-acf-connector/crm_address_keys', [
					'multi' => '',
					'addr1'	=> 'address',
					'addr2' => 'address2',
					'city'	=> 'city',
					'state'	=> 'state',
					'zip'	=> 'zip'
				]);

				$address = get_address_data($keys, $id, $attrs);
			}

			$block_output = formatted_address_output($address, $id, $attrs);
			break;
		case 'crm_directions':
			$keys = apply_filters('kraken-core/kraken-acf-connector/crm_address_keys', [
				'multi' => '',
				'addr1'	=> 'address',
				'addr2' => 'address2',
				'city'	=> 'city',
				'state'	=> 'state',
				'zip'	=> 'zip'
			]);

			$address = get_address_data($keys, $id, $attrs);
			$block_output = address_string_output($address, $id, $attrs);
			break;
		case 'crm_map_embed':
			$keys = apply_filters('kraken-core/kraken-acf-connector/crm_address_keys', [
				'multi' => '',
				'addr1'	=> 'address',
				'addr2' => 'address2',
				'city'	=> 'city',
				'state'	=> 'state',
				'zip'	=> 'zip'
			]);

			$address = get_address_data($keys, $id, $attrs);
			$block_output = address_map_embed_output($address, $id, $attrs);
			break;
		case 'crm_social':

			$socials = apply_filters('kraken-core/kraken-acf-connector/listing_socials', [
				[
					'acf_field' => 'facebook',
					'label' 	=> 'Facebook',
					'service'	=> 'facebook'
				],
				[
					'acf_field' => 'instagram',
					'label' 	=> 'Instagram',
					'service'	=> 'instagram'
				],
				[
					'acf_field' => 'x',
					'label' 	=> 'X',
					'service'	=> 'x'
				],
				[
					'acf_field' => 'youtube',
					'label' 	=> 'YouTube',
					'service'	=> 'youtube'
				]
			]);

			$block_output = socials_block_output($socials, $id, $attrs);

			break;
		case 'event_dates':
			$block_output = getEventDates($id, $attrs);
			break;
		case 'event_recurring_dates':
			$block_output = getAllUpcomingDates($id, $attrs);
			break;
		case 'event_times':
			$block_output = getEventTimes($id, $attrs);
			break;
		case 'event_website':
			$website_field = apply_filters('kraken-core/kraken-acf-connector/event_website_link_field_name', 'events_url', $id, $attrs);
			$website_link = get_field($website_field, $id);

			if ($website_link && filter_var($website_link, FILTER_VALIDATE_URL)) {
				//use the customlinktext field if text is set or fallback
				$website_link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : 'Visit Website';

				//customize it before output if needed
				$link_text = apply_filters('kraken-core/kraken-acf-connector/event_website_link_text', $website_link_text, $id, $attrs);

				// Add accessibility attributes
				$aria_label = sprintf(__('Visit event website: %s', 'kraken-core'), $website_link);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/event_website_aria_label', $aria_label, $id, $attrs);
				$block_output = '<!-- wp:paragraph --><p><a href="' . esc_url($website_link) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'event_phone':
			$phone_field = apply_filters('kraken-core/kraken-acf-connector/event_phone_field_name', 'events_phone', $id, $attrs);
			$phone = get_field($phone_field, $id);

			if ($phone && !empty(trim($phone))) {
				// Sanitize phone number for tel: link
				$phone_clean = preg_replace('/[^0-9+]/', '', $phone);

				//use the customlinktext field if text is set or fallback to the phone number
				$phone_link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $phone;

				//customize it before output if needed
				$link_text = apply_filters('kraken-core/kraken-acf-connector/event_phone_link_text', $phone_link_text, $id, $attrs);

				if ($attrs['displayIcon']) {
					$block_output .= apply_filters( 'kraken-core/kraken-acf-connector/phone_icon', file_get_contents( __DIR__ . '/icons/phone.php'));
				}

				// Add accessibility attributes
				$aria_label = sprintf(__('Call event organizer: %s', 'kraken-core'), $phone);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/event_phone_aria_label', $aria_label, $id, $attrs);
				$block_output .= '<!-- wp:paragraph --><p><a href="tel:' . esc_attr($phone_clean) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'event_email':
			$email_field = apply_filters('kraken-core/kraken-acf-connector/event_email_field_name', 'events_email', $id, $attrs);
			$email = get_field($email_field, $id);

			if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
				//use the customlinktext field if text is set or fallback to the email address
				$email_link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $email;

				//customize it before output if needed
				$link_text = apply_filters('kraken-core/kraken-acf-connector/event_email_link_text', $email_link_text, $id, $attrs);

				if ($attrs['displayIcon']) {
					$block_output .= apply_filters( 'kraken-core/kraken-acf-connector/email_icon', file_get_contents( __DIR__ . '/icons/email.php'));
				}

				// Add accessibility attributes
				$aria_label = sprintf(__('Send email to event organizer: %s', 'kraken-core'), $email);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/event_email_aria_label', $aria_label, $id, $attrs);
				$block_output .= '<!-- wp:paragraph --><p><a href="mailto:' . esc_attr($email) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'event_location':
			$keys = apply_filters('kraken-core/kraken-acf-connector/event_address_keys', [
				'multi' => 'events_addr_multi',
				'addr1'	=> 'events_addr1',
				'addr2' => 'events_addr2',
				'city'	=> 'events_city',
				'state'	=> 'events_state',
				'zip'	=> 'events_zip'
			]);

			$address = get_address_data($keys, $id, $attrs);
			$block_output = formatted_address_output($address, $id, $attrs);
			break;
		case 'event_directions':
			$keys = apply_filters('kraken-core/kraken-acf-connector/event_address_keys', [
				'multi' => 'events_addr_multi',
				'addr1'	=> 'events_addr1',
				'addr2' => 'events_addr2',
				'city'	=> 'events_city',
				'state'	=> 'events_state',
				'zip'	=> 'events_zip'
			]);

			$address = get_address_data($keys, $id, $attrs);
			$block_output = address_string_output($address, $id, $attrs);
			break;
		case 'event_map_embed':
			$keys = apply_filters('kraken-core/kraken-acf-connector/event_address_keys', [
				'multi' => '',
				'addr1'	=> 'events_addr1',
				'addr2' => 'events_addr2',
				'city'	=> 'events_city',
				'state'	=> 'events_state',
				'zip'	=> 'events_zip'
			]);

			$address = get_address_data($keys, $id, $attrs);
			$block_output = address_map_embed_output($address, $id, $attrs);
			break;
		case 'event_social':

			$socials = apply_filters('kraken-core/kraken-acf-connector/event_socials', [
				[
					'acf_field' => 'events_facebook',
					'label' 	=> 'Facebook',
					'service'	=> 'facebook'
				],
				[
					'acf_field' => 'events_instagram',
					'label' 	=> 'Instagram',
					'service'	=> 'instagram'
				],
				[
					'acf_field' => 'events_twitter',
					'label' 	=> 'X',
					'service'	=> 'x'
				]
			]);

			$block_output = socials_block_output($socials, $id, $attrs);

			break;
		case 'event_ticket_price':
			$price          = get_field('events_price', $id);
			$priceVaries    = get_field('events_price_varies', $id);
			$price_output = '';

			if ($priceVaries) {
				$price_output = apply_filters('kraken-core/kraken-acf-connector/event_price_varies_text', 'Price varies');
			} elseif ($price) {
				$price_output = '$' . $price;
			}

			if (!empty($price_output)) {
				$block_output = '<!-- wp:paragraph --><p>'.$price_output. '</p><!-- /wp:paragraph -->';
			}
			break;
		case 'event_ticket_link':
			$ticketUrl = get_field('events_ticket_link', $id);
			if ($ticketUrl && filter_var($ticketUrl, FILTER_VALIDATE_URL)) {
				$link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : 'Register Here';
				$ticket_link_text = apply_filters('kraken-core/kraken-acf-connector/event_ticket_link_text', $link_text);

				// Add accessibility attributes
				$aria_label = sprintf(__('Purchase tickets for this event: %s', 'kraken-core'), $ticketUrl);
				$aria_label = apply_filters('kraken-core/kraken-acf-connector/event_ticket_link_aria_label', $aria_label, $id, $attrs);
				$block_output = '<!-- wp:paragraph --><p><a href="' . esc_url($ticketUrl) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($aria_label) . '">' . esc_html($ticket_link_text) . '</a></p><!-- /wp:paragraph -->';
			}
			break;
		case 'mindtrip':
			$title = get_the_title($id);

			$link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : 'Explore';
			$link_text = apply_filters('kraken-core/kraken-acf-connector/mindtrip_link_text', $link_text, $id, $attrs);

			$mindtrip_icon = apply_filters( 'kraken-core/kraken-acf-connector/mindtrip_icon', file_get_contents( __DIR__ . '/icons/mindtrip.php'));

			$mindtrip_prompt = apply_filters( 'kraken-core/kraken-acf-connector/mindtrip_prompt', 'Explore things to do near '.$title, $id, $attrs);

			// Add accessibility attributes
			$aria_label = sprintf(__('Explore things to do near %s using Mindtrip AI', 'kraken-core'), $title);
			$aria_label = apply_filters('kraken-core/kraken-acf-connector/mindtrip_aria_label', $aria_label, $id, $attrs);
			$block_output = '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#mindtrip.ai/chat/new?q='.urlencode($mindtrip_prompt).'" title="'.$mindtrip_prompt.'" aria-label="' . esc_attr($aria_label) . '">'.$mindtrip_icon.'<span class="wp-block-button__text">' . esc_html($link_text) . '</span></a></div><!-- /wp:button --></div><!-- /wp:buttons -->';
			break;
		default:
			break;
	endswitch;
} elseif ($attrs['contentType'] === 'hook-only') {
	/**
	 * Hook Only mode - provides field data via filters for custom theme handling
	 * No default output, relies entirely on theme hooks
	 * This is to avoid the user needing to create their own block
	 */

	// Validate custom field name
	if (empty($attrs['customField'])) { return;	}

	$custom_field = get_field_object($attrs['customField'], $id);

	$field_type  = $custom_field['type'];
	$field_value = $custom_field['value'];
	$field_label = $custom_field['label'];
	$field_name  = $custom_field['name'];

	// Use standardized hook naming
	$hook_name = sprintf(
		'kraken-core/kraken-acf-connector/hook-only/%s',
		$field_name
	);

	$block_output = apply_filters($hook_name, '', $id, $attrs, $custom_field);
} else {
	/**
	 * Handle custom ACF field types
	 * Supports various field types with appropriate output formatting
	 * Includes validation, sanitization, and accessibility features
	 */

	$custom_field = get_field_object($attrs['customField'], $id);

	if ($custom_field && is_array($custom_field)) {

		$field_type  = $custom_field['type'];
		$field_value = $custom_field['value'];
		$field_label = $custom_field['label'];
		$field_name  = $custom_field['name'];

		$link_prefix = "";
		$link_target = "";

		if ($attrs['outputAsLink']) {
			if ($attrs['customLinkText'] !== "") {
				$field_label = $attrs['customLinkText'];
			}
			if ($attrs['customLinkType'] !== "") {
				$link_prefix = $attrs['customLinkType'];
			}
			if ($attrs['customLinkTarget']) {
				$link_target = 'target="_blank"';
			}
		}

		//Used as link text and/or extra label if enabled
		$label_filter_name = sprintf(
			'kraken-core/kraken-acf-connector/%s_field_label',
			$field_name
		);
		$field_label = apply_filters($label_filter_name, $field_label);

		//Outputs optional extra label
		$extra_label = false;
		if ($attrs['displayLabel']) {
			$extra_label = $attrs['customLabelText'] !== "" ? $attrs['customLabelText'] : $field_label;
		}

		$custom_title = "";
		if ($extra_label) {
			$title_filter_name = sprintf(
				'kraken-core/kraken-acf-connector/%s_title',
				$field_name
			);
			$custom_title = apply_filters($title_filter_name, '<strong>'.$extra_label.' </strong>', $id, $attrs);
		}

		/**
		 * Handle different ACF field types with appropriate output formatting
		 * Each case includes validation, sanitization, and accessibility features
		 */
		switch($field_type):
			case 'email':
				// Validate email format
				if (filter_var($field_value, FILTER_VALIDATE_EMAIL)) {
					$aria_label = sprintf(__('Send email to %s', 'kraken-core'), $field_value);

					$aria_label_filter_name = sprintf(
						'kraken-core/kraken-acf-connector/%s_aria_label',
						$field_name
					);

					$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'<a href="mailto:' . esc_attr($field_value) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($field_label) . '</a></p><!-- /wp:paragraph -->';
				}
				break;
			case 'url':
				// Validate URL format
				if (filter_var($field_value, FILTER_VALIDATE_URL)) {
					$aria_label = sprintf(__('Visit external website', 'kraken-core'));

					$aria_label_filter_name = sprintf(
						'kraken-core/kraken-acf-connector/%s_aria_label',
						$field_name
					);

					$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'<a href="' . esc_url($field_value) . '" '.$link_target.' rel="noopener noreferrer" aria-label="' . esc_attr($aria_label) . '">' . esc_html($field_label) . '</a></p><!-- /wp:paragraph -->';
				}
				break;
			case 'text':
			case 'textarea':
			case 'number':
			case 'select':
			case 'radio':
				if ($attrs['outputAsLink']) {
					$aria_label = sprintf(__('Visit external link', 'kraken-core'));

					$aria_label_filter_name = sprintf(
						'kraken-core/kraken-acf-connector/%s_aria_label',
						$field_name
					);
					$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'<a href="'.$link_prefix.'' . esc_url($field_value) . '" '.$link_target.' rel="noopener noreferrer" aria-label="' . esc_attr($aria_label) . '">' . esc_html($field_label) . '</a></p><!-- /wp:paragraph -->';
				} else {
					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'' . esc_html($field_value) . '</p><!-- /wp:paragraph -->';
				}
				break;
			case 'image':
				if (is_int($field_value) && $field_value !== 0) {
					$image = wp_get_attachment_image($field_value, 'full');
					if ($image) {
						$block_output = '<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image">'.$image.'</figure><!-- /wp:image -->';
					}
				} elseif (is_array($field_value) && isset($field_value['url']) && filter_var($field_value['url'], FILTER_VALIDATE_URL)) {
					$alt_text = isset($field_value['alt']) ? $field_value['alt'] : '';
					$block_output = '<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image"><img src="' . esc_url($field_value['url']) . '" alt="' . esc_attr($alt_text) . '" /></figure><!-- /wp:image -->';
				} elseif (is_string($field_value) && $field_value !== "" && filter_var($field_value, FILTER_VALIDATE_URL)) {
					$block_output = '<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image"><img src="' . esc_url($field_value) . '" alt="" /></figure><!-- /wp:image -->';
				}
				if ($block_output && $custom_title) {
					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'</p><!-- /wp:paragraph -->'.$block_output;
				}
				break;
			case 'wysiwyg':
				if ($custom_title) {
					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'</p><!-- /wp:paragraph -->';
				}
				$block_output .= $field_value;
				break;
			case 'date_picker':
				if ($field_value) {
					// Format date as month/day/year with leading zeroes
					$default_format = 'm/d/Y';
					$date_format = apply_filters('kraken-core/kraken-acf-connector/date_picker_format', $default_format, $field_name, $id, $attrs);
					$formatted_date = date($date_format, strtotime($field_value));
					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'' . esc_html($formatted_date) . '</p><!-- /wp:paragraph -->';
				}
				break;
			case 'time_picker':
				if ($field_value) {
					// Format time as hours:minutes am/pm
					$default_format = 'g:ia';
					$time_format = apply_filters('kraken-core/kraken-acf-connector/time_picker_format', $default_format, $field_name, $id, $attrs);
					$formatted_time = date($time_format, strtotime($field_value));
					$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'' . esc_html($formatted_time) . '</p><!-- /wp:paragraph -->';
				}
				break;
			case 'post_object':
				$post_output = '';

				if ($custom_field['multiple'] && is_array($field_value)) {
					$i = 0;
					foreach($field_value as $item) {
						// Validate item exists
						if (!$item) continue;

						//get the link & title
						if ($custom_field['return_format'] == 'object' && isset($item->ID)) {
							$link = get_permalink($item->ID);
							$title = $item->post_title;
						} else {
							$link = get_permalink($item);
							$title = get_the_title($item);
						}

						// Validate link and title exist
						if (!$link || !$title) continue;

						//add to the final output
						if ($i === 0) {
							$post_output .= '<!-- wp:list --><ul class="wp-block-list">';
						}

						$post_output .= '<!-- wp:list-item --><li>';
						if ($attrs['outputAsLink']) {
							$link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $title;
							$aria_label = sprintf(__('View %s', 'kraken-core'), $title);

							$aria_label_filter_name = sprintf(
								'kraken-core/kraken-acf-connector/%s_aria_label',
								$field_name
							);
							$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

							$post_output .= '<a href="' . esc_url($link) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a>';
						} else {
							$post_output .= esc_html($title);
						}
						$post_output .= '</li><!-- /wp:list-item -->';

						if ($i === 0) {
							$post_output .= '</ul><!-- /wp:list -->';
						}
						$i++;
					}
				} else {
					$item = $field_value;
					if ($item) {
						if ($custom_field['return_format'] == 'object' && isset($item->ID)) {
							$link = get_permalink($item->ID);
							$title = $item->post_title;
						} else {
							$link = get_permalink($item);
							$title = get_the_title($item);
						}

						if ($link && $title) {
							if ($attrs['outputAsLink']) {
								$link_text = $attrs['customLinkText'] !== "" ? $attrs['customLinkText'] : $title;
								$aria_label = sprintf(__('View %s', 'kraken-core'), $title);

								$aria_label_filter_name = sprintf(
									'kraken-core/kraken-acf-connector/%s_aria_label',
									$field_name
								);
								$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

								$post_output .= '<!-- wp:paragraph --><p><a href="' . esc_url($link) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($link_text) . '</a></p><!-- /wp:paragraph -->';
							} else {
								$post_output .= '<!-- wp:paragraph --><p>' . esc_html($title) . '</p><!-- /wp:paragraph -->';
							}
						}
					}
				}

				if ($post_output) {
					if ($custom_title) {
						$post_output = '<!-- wp:paragraph --><p>'.$custom_title.'</p><!-- /wp:paragraph -->'.$post_output;
					}
					$block_output = $post_output;
				}

				break;
				case 'taxonomy':
					if (is_array($field_value)) {
						$terms = get_the_terms($id, $custom_field['taxonomy']);
						if ($terms && ! is_wp_error($terms)) {
							if ($attrs['outputAsList']) {
								// Output as core list block
								$list_items = '';
								foreach ($terms as $term) {
									if ($attrs['outputAsLink']) {
										$term_link = get_term_link($term);
										if (!is_wp_error($term_link)) {
											$aria_label = sprintf(__('View posts in %s category', 'kraken-core'), $term->name);

											$aria_label_filter_name = sprintf(
												'kraken-core/kraken-acf-connector/%s_aria_label',
												$field_name
											);
											$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

											$list_items .= '<!-- wp:list-item --><li><a href="' . esc_url($term_link) . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html($term->name) . '</a></li><!-- /wp:list-item -->';
										}
									} else {
										$list_items .= '<!-- wp:list-item --><li>' . esc_html($term->name) . '</li><!-- /wp:list-item -->';
									}
								}
								if (!empty($list_items)) {
									$block_output = '<!-- wp:list --><ul class="wp-block-list">' . $list_items . '</ul><!-- /wp:list -->';
									if ($custom_title) {
										$block_output = '<!-- wp:paragraph --><p>'.$custom_title.'</p><!-- /wp:paragraph -->' . $block_output;
									}
								}
							} elseif ($attrs['outputAsLink']) {
								$term_links = [];
								foreach ($terms as $term) {
									$term_link = get_term_link($term);
									if (!is_wp_error($term_link)) {
										$aria_label = sprintf(__('View posts in %s category', 'kraken-core'), $term->name);

										$aria_label_filter_name = sprintf(
											'kraken-core/kraken-acf-connector/%s_aria_label',
											$field_name
										);
										$aria_label = apply_filters($aria_label_filter_name, $aria_label, $id, $attrs);

										$term_links[] = sprintf(
											'<a href="%1$s" class="my-term-link" aria-label="%3$s">%2$s</a>',
											esc_url($term_link),
											esc_html($term->name),
											esc_attr($aria_label)
										);
									}
								}
								if (!empty($term_links)) {
									$block_output = '<!-- wp:paragraph --><p>'.$custom_title.''.implode(', ', $term_links).'</p><!-- /wp:paragraph -->';
								}
							} else {
								$term_names = wp_list_pluck($terms, 'name');
								$block_output = '<!-- wp:paragraph --><p>'.$custom_title.''.esc_html(implode(', ', $term_names)).'</p><!-- /wp:paragraph -->';
							}
						}
					}
					break;
				default:
					//Don't output anything unless the user hooks into this filter
					//Not going to begin to attempt to process a repeater field
					$fallback_filter_name = sprintf(
						'kraken-core/kraken-acf-connector/hook-only/%s',
						$field_name
					);
					$block_output = apply_filters($fallback_filter_name, '', $id, $attrs, $custom_field);
					break;
		endswitch;
	}
}

/**
 * Output the final block content
 * Renders the processed field data or shows preview in editor
 */
if ($block_output) {
	?>
	<div <?php echo $wrapper_attributes; ?>>
		<?php echo do_blocks($block_output); ?>
	</div>
	<?php
} elseif ($in_editor_preview) {
	?>
	<div <?php echo $wrapper_attributes; ?>>
		Preview: <?php echo $data_class_name; ?>
	</div>
	<?php
}
?>
