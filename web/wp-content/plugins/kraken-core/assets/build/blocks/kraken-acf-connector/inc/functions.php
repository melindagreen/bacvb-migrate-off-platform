<?php
namespace MaddenMedia\KrakenCore\Blocks\KrakenACF;

/**
 * Outputs the address formatted as:
 * 123 Street Name, apt 2
 * City, State 12345
 *
 * @param array $address Address data array
 * @param int $id Post ID
 * @param array $attrs Block attributes
 * @return string Formatted address HTML
 */
function formatted_address_output($address, $id, $attrs) {
	if (!is_array($address)) { return; }

	$output = "";

	$location   = isset($address['multi']) ? sanitize_text_field($address['multi']) : '';
	$street     = isset($address['street']) ? sanitize_text_field($address['street']) : '';
	$street2    = isset($address['street2']) ? sanitize_text_field($address['street2']) : '';
	$city       = isset($address['city']) ? sanitize_text_field($address['city']) : '';
	$state      = isset($address['state']) ? sanitize_text_field($address['state']) : '';
	$zip        = isset($address['zip']) ? sanitize_text_field($address['zip']) : '';

	// Build the location output as a single line
	$location_output = '';

	if ($location) {
		$location_output .= $location . '<br>';
	}

	if ($street) {
		$location_output .= $street;
		if ($street2) {
			$location_output .= ', ' . $street2;
		}
		$location_output .= ' ';
	}

	if ($city || $state || $zip) {
		$location_output .= '<br>';
		$location_output .= ($city ? $city: '');
		$location_output .= ($state ? ', ' . $state: '');
		$location_output .= ($zip ? ' ' . $zip: '');
	}

	if (!empty($location_output)) {

		$label = false;
		if ($attrs['displayLabel']) {
			$label = $attrs['customLabelText'] !== "" ? $attrs['customLabelText'] : 'Location:';
		}

		$location_title = "";
		if ($label) {
			$location_title = apply_filters('kraken-core/kraken-acf-connector/location_title_html', '<strong>'.$label.' </strong><br>', $id, $attrs);
		}


		if ($attrs['displayIcon']) {
			$output .= apply_filters( 'kraken-core/kraken-acf-connector/location_icon', file_get_contents( __DIR__ . '/../icons/location.php'));
		}

		$output .= '<!-- wp:paragraph --><p>'.$location_title.''.$location_output. '</p><!-- /wp:paragraph -->';
	}

	return $output;
}

/**
 * Outputs the address as a single line string
 * Intended for use in Google Map direction links
 *
 * @param array $address Address data array
 * @param int $id Post ID
 * @param array $attrs Block attributes
 * @return string Formatted address string
 */
function address_string_output($address, $id, $attrs) {
	if (!is_array($address)) { return; }

	$street     = isset($address['street']) ? sanitize_text_field($address['street']) : '';
	$street2    = isset($address['street2']) ? sanitize_text_field($address['street2']) : '';
	$city       = isset($address['city']) ? sanitize_text_field($address['city']) : '';
	$state      = isset($address['state']) ? sanitize_text_field($address['state']) : '';
	$zip        = isset($address['zip']) ? sanitize_text_field($address['zip']) : '';

	//include the title for better results (except for kraken-events)
	$location_output = '';
	if (!isset($attrs['contentType']) || $attrs['contentType'] !== 'kraken-events') {
		$title = get_the_title($id);
		if ($title) {
			$location_output = sanitize_text_field($title) . ' ';
		}
	}

	if ($street) {
		$location_output .= $street;
		if ($street2) {
			$location_output .= ', ' . $street2;
		}
		$location_output .= ' ';
	}

	if ($city || $state || $zip) {
		$location_output .= ($city ? $city: '');
		$location_output .= ($state ? ', ' . $state: '');
		$location_output .= ($zip ? ' ' . $zip: '');
	}

	if (!empty(trim($location_output))) {
		$link_text = isset($attrs['customLinkText']) && $attrs['customLinkText'] !== "" ? sanitize_text_field($attrs['customLinkText']) : 'Directions';
		$directions_link_text = apply_filters('kraken-core/kraken-acf-connector/directions_link_text', $link_text, $id, $attrs);

		$base_url = 'https://www.google.com/maps/search/?api=1&query=';
		$directions_link = esc_url($base_url . urlencode($location_output));

		// Add accessibility attributes
		$aria_label = sprintf(__('Get directions to %s', 'kraken-core'), $location_output);
		$block_output = '<!-- wp:paragraph --><p><a href="'.$directions_link.'" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($aria_label) . '">' . esc_html($directions_link_text) . '</a></p><!-- /wp:paragraph -->';
	}

	return $block_output;
}

/**
 * Outputs an embedded Google Map for the given address
 *
 * @param array $address Address data array
 * @param int $id Post ID
 * @param array $attrs Block attributes
 * @return string Map embed HTML
 */
function address_map_embed_output($address, $id, $attrs) {
	if (!is_array($address)) { return; }

	$output = "";

	$street     = isset($address['street']) ? sanitize_text_field($address['street']) : '';
	$street2    = isset($address['street2']) ? sanitize_text_field($address['street2']) : '';
	$city       = isset($address['city']) ? sanitize_text_field($address['city']) : '';
	$state      = isset($address['state']) ? sanitize_text_field($address['state']) : '';
	$zip        = isset($address['zip']) ? sanitize_text_field($address['zip']) : '';

	$title = get_the_title($id);
	if (!$title) {
		$title = __('Location', 'kraken-core');
	}
	$address_string = sanitize_text_field($title) . ' ';

	if ($street) {
		$address_string .= $street;
		if ($street2) {
			$address_string .= ', ' . $street2;
		}
		$address_string .= ' ';
	}

	if ($city || $state || $zip) {
		$address_string .= ($city ? $city: '');
		$address_string .= ($state ? ', ' . $state: '');
		$address_string .= ($zip ? ' ' . $zip: '');
	}

	if (!empty(trim($address_string))) {
		$map_title = sprintf(__('Google Map showing location of %s', 'kraken-core'), $title);
		$encoded_address = urlencode(str_replace(",", "", str_replace(" ", "+", $address_string)));
		$map_url = esc_url("https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=" . $encoded_address . "&z=14&output=embed");

		$output = '<iframe title="' . esc_attr($map_title) . '" src="' . $map_url . '" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen="" aria-label="' . esc_attr($map_title) . '"></iframe>';
	}

	return $output;
}

/**
 * Outputs the core social link block based on passed $socials array
 *
 * @param array $socials Array of social media configurations
 * @param int $id Post ID
 * @param array $attrs Block attributes
 * @return string Social links HTML
 */
function socials_block_output($socials, $id, $attrs) {
	$output = "";

	if (!is_array($socials) || empty($socials)) {
		return;
	}

	foreach($socials as $social) {
		if (!isset($social['acf_field']) || !isset($social['service']) || !isset($social['label'])) {
			continue;
		}

		$social_link = get_field($social['acf_field'], $id);
		if ($social_link && filter_var($social_link, FILTER_VALIDATE_URL)) {
			$aria_label = sprintf(__('Visit our %s page', 'kraken-core'), $social['label']);
			$output .= '<!-- wp:social-link {"url":"'. esc_url($social_link) .'","service":"'.esc_attr($social['service']).'","label":"'.esc_attr($social['label']).'"} /-->';
		}
	}

	if ($output) {
		$output = '<!-- wp:social-links {"openInNewTab":true,"className":"is-style-logos-only","style":{"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"flex","justifyContent":"flex-start"}} --><ul class="wp-block-social-links is-style-logos-only" role="list" aria-label="' . esc_attr(__('Social media links', 'kraken-core')) . '">'.$output.'</ul><!-- /wp:social-links -->';
	}

	return $output;
}

/**
 * Gets address data from ACF fields with error handling
 *
 * @param array $keys Address field keys
 * @param int $id Post ID
 * @param array $attrs Block attributes
 * @return array Address data array
 */
function get_address_data($keys, $id, $attrs) {
	$address = [
		'multi'		=> get_field($keys['multi'], $id),
		'street' 	=> get_field($keys['addr1'], $id),
		'street2' 	=> get_field($keys['addr2'], $id),
		'city' 		=> get_field($keys['city'], $id),
		'state' 	=> get_field($keys['state'], $id),
		'zip' 		=> get_field($keys['zip'], $id),
	];

	// Sanitize all address fields
	foreach ($address as $key => $value) {
		if (is_string($value)) {
			$address[$key] = sanitize_text_field($value);
		}
	}

	return $address;
}

