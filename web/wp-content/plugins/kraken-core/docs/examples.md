# Examples

_Last Updated: October 2025_

## Overview

This collection of examples and recipes demonstrates common patterns and real-world implementations using Kraken Core. Each example includes complete code and explanations.

## Content Card Examples

#### Set a fallback placeholder image

```php
add_filter('kraken-core/content-card/image', function($image, $id, $attrs) {
	if ($image === "") {
		return '<img src="/wp-content/themes/mmk-grandcounty/assets/images/placeholder.svg" alt="" class="placeholder">';
	}
	return $image;
}, 10, 3);
```

#### Move the title to be under the image wrapper instead of the content wrapper when using a specific card style

```php
add_filter('kraken-core/content-card/after_image', function($id, $attrs) {
    if ($attrs['cardStyle'] === 'overlay-title' || $attrs['cardStyle'] === 'overlay-title-only') {
		  $base_title = get_the_title($id);
      if ($attrs['displayCustomTitle'] && $attrs['customTitle'] !== "") {
        $title = $attrs['customTitle'];
      } else {
        $title = $base_title;
      }
      echo '<h3 class="post-title">'.$title.'</h3>';
    }
}, 10, 3);

add_filter('kraken-core/content-card/content_elements', function($items, $id, $attrs) {
    if ($attrs['cardStyle'] === 'overlay-title' || $attrs['cardStyle'] === 'overlay-title-only') {
    	return array_diff($items, ['title']);
	} else {
		return $items;
	}
}, 10, 3);
```

#### Output a custom attribute

```php
add_filter('kraken-core/content-card/link', function($link, $id, $attrs) {
	if (isset($attrs['customAdditionalContent']['linkQueryString'])) {
		$link = $link . $attrs['customAdditionalContent']['linkQueryString'];
	}
	return $link;
}, 10, 3);
```

#### Modify default event address keys

```php
add_filter('kraken-core/content-card/event_address_keys', function($keys) {
    $keys['venue'] 	= 'venue';
	$keys['addr1'] 	= 'address';
	$keys['addr2'] 	= 'address2';
	$keys['city'] 	= 'city';
	$keys['state'] 	= 'state';
	$keys['zip'] 	= 'zip';
	return $keys;
}, 10, 1);
```

#### Add a wrapper to event information

```php
add_action('kraken-core/content-card/before_event_details', function($id, $attrs) {
	echo '<div class="event-info-wrapper">';
}, 10, 2);

add_action('kraken-core/content-card/after_event_details', function($id, $attrs) {
	echo '</div>';
}, 10, 2);
```

## Kraken ACF Connector Examples

#### Modify a field name

```php
add_filter('kraken-core/kraken-acf-connector/crm_website_field_name', function() {
	return 'business_url';
}, 10);
```

#### Modify a field title

```php
add_filter('kraken-core/kraken-acf-connector/end_date_title', function($title, $id, $attrs) {
	$end_date = get_field('end_date', $id);
	if ($end_date) {
		$end_date = date('m/d/Y', strtotime($end_date));
		if (strtotime($end_date) < time()) {
			return 'Offer ended on ';
		}
	}
	return 'Offer ends on ';
}, 10, 3);
```

#### Modify link text

```php
add_filter('kraken-core/kraken-acf-connector/directions_link_text', function($title) {
	return 'Directions >';
}, 10, 1);
```

#### Add custom output for a hook only field

```php
add_filter('kraken-core/kraken-acf-connector/hook-only/social_media', function($content, $id, $attrs) {
	$socials = get_field('social_media', $id);
	$output = '';

	if ($socials) {
		foreach($socials as $social) {
			$label = $social['website'];
			$url = $social['url'];
			$service = '';

			if ($url) {
				//check if the url contains a common social media service
				if (strpos($url, 'facebook.com') !== false) {
					$service = 'facebook';
				} elseif (strpos($url, 'twitter.com') !== false) {
					$service = 'x';
				} elseif (strpos($url, 'instagram.com') !== false) {
					$service = 'instagram';
				} elseif (strpos($url, 'youtube.com') !== false) {
					$service = 'youtube';
				} elseif (strpos($url, 'pinterest.com') !== false) {
					$service = 'pinterest';
				}
				$output .= '<!-- wp:social-link {"url":"'. esc_url($url) .'","service":"'.$service.'","label":"'.$label.'"} /-->';
			}
		}
	}

	if ($output) {
		$output = '<!-- wp:social-links {"openInNewTab":true,"className":"is-style-logos-only","style":{"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"flex","justifyContent":"flex-start"}} --><ul class="wp-block-social-links is-style-logos-only">'.$output.'</ul><!-- /wp:social-links -->';
	}

	return $output;
}, 10, 3);
```
