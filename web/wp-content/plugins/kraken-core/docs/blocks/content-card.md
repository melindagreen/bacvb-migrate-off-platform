# Content Card Block

_Last Updated: October 2025_

## Overview

The Content Card block displays selected posts or custom content in a customizable card format. It's designed to work seamlessly with the Search & Filter block and can be used standalone or in sliders.

## Block Attributes

### Content Selection

| Attribute      | Type   | Default | Description                     |
| -------------- | ------ | ------- | ------------------------------- |
| `contentId`    | number | 0       | ID of the selected post/content |
| `postType`     | string | "post"  | Post type to display            |
| `contentTitle` | string | ""      | Custom title override           |

### Card Styling

| Attribute         | Type   | Default   | Description           |
| ----------------- | ------ | --------- | --------------------- |
| `cardStyle`       | string | "default" | Card style variant    |
| `backgroundColor` | string | ""        | Card background color |
| `textColor`       | string | ""        | Card text color       |
| `customImage`     | object | {}        | Custom image override |

### Content Display Options

| Attribute                  | Type    | Default     | Description                    |
| -------------------------- | ------- | ----------- | ------------------------------ |
| `displayAdditionalContent` | boolean | false       | Show additional content fields |
| `displayExcerpt`           | boolean | false       | Show post excerpt              |
| `excerptLength`            | number  | 12          | Excerpt word length            |
| `displayCustomExcerpt`     | boolean | false       | Use custom excerpt             |
| `customExcerpt`            | string  | ""          | Custom excerpt text            |
| `displayCustomTitle`       | boolean | false       | Use custom title               |
| `customTitle`              | string  | ""          | Custom title text              |
| `displayReadMore`          | boolean | false       | Show read more link            |
| `readMoreText`             | string  | "Read More" | Read more link text            |

### Event-Specific Options

| Attribute                  | Type    | Default | Description                 |
| -------------------------- | ------- | ------- | --------------------------- |
| `displayEventDate`         | boolean | false   | Show event date             |
| `displayEventTime`         | boolean | false   | Show event time             |
| `displayAddress`           | boolean | false   | Show event address          |
| `displayWebsiteLink`       | boolean | false   | Show website link           |
| `eventDateBackgroundColor` | string  | ""      | Event date background color |
| `eventDateTextColor`       | string  | ""      | Event date text color       |

### Mindtrip Integration

| Attribute            | Type    | Default  | Description            |
| -------------------- | ------- | -------- | ---------------------- |
| `displayMindtripCta` | boolean | false    | Show Mindtrip CTA      |
| `mindtripCtaType`    | string  | "button" | CTA type (button/icon) |
| `mindtripCtaText`    | string  | ""       | CTA button text        |
| `mindtripPrompt`     | string  | ""       | Mindtrip prompt text   |

### Custom Actions

| Attribute                 | Type   | Default | Description               |
| ------------------------- | ------ | ------- | ------------------------- |
| `customCtaText`           | string | ""      | Custom CTA text           |
| `customCtaUrl`            | object | {}      | Custom CTA URL object     |
| `customAdditionalContent` | object | {}      | Additional custom content |

## Block Supports

The Content Card block supports:

- **Background**: Background image and size controls
- **Color**: Background and text color with contrast checker

## Integration with Other Blocks

### Search & Filter Integration

When used with the Search & Filter block, the Content Card automatically:

- Receives query parameters
- Applies consistent styling

### Slider Integration

The Content Card can be used in sliders by:

- Set up the slider block and select automatic or recent post mode
- The card styles and options will be imported automatically

## PHP Hooks

### Core Hooks

| Hook                                          | Parameters                  | Description                                      |
| --------------------------------------------- | --------------------------- | ------------------------------------------------ |
| `kraken-core/content-card/classes`            | `$classes`, `$attrs`        | Modify classes array                             |
| `kraken-core/content-card/styles`             | `$date`, `$post`            | Modify inline styles                             |
| `kraken-core/content-card/wrapper_attributes` | `$wrapper_attrs`, `$attrs`  | Modify wrapper attributes                        |
| `kraken-core/content-card/title`              | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/link`               | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/link_title`         | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/link_target`        | `$target`, `$id`, `$attrs`  |                                                  |
| `kraken-core/content-card/image`              | `$image`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/website_link`       | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/website_link_text`  | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/content_elements`   | `$el = []`, `$id`, `$attrs` | Reorder or modify the card elements              |
| `kraken-core/content-card/event_elements`     | `$el = []`, `$id`, `$attrs` | Reorder or modify the event details              |
| `kraken-core/content-card/address`            | `$address`, `$id`, `$attrs` |                                                  |
| `kraken-core/content-card/excerpt`            | `$excerpt`, `$id`, `$attrs` |                                                  |
| `kraken-core/content-card/custom_excerpt`     | `$excerpt`, `$id`, `$attrs` |                                                  |
| `kraken-core/content-card/custom_cta_text`    | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/read_more_text`     | `$value`, `$id`, `$attrs`   |                                                  |
| `kraken-core/content-card/event_date_format`  | `$format`, `$id`, `$attrs`  | Modify the date format                           |
| `kraken-core/content-card/event_address_keys` | `$keys = []`                | Modify the ACF field names for the event address |

### Mindtrip Hooks

| Hook                                         | Parameters                | Description |
| -------------------------------------------- | ------------------------- | ----------- |
| `kraken-core/content-card/mindtrip_cta_text` | `$value`, `$id`, `$attrs` |             |
| `kraken-core/content-card/mindtrip_prompt`   | `$value`, `$id`, `$attrs` |             |
| `kraken-core/content-card/mindtrip_icon`     | `$icon`                   |             |

## Theme Customization

### Theme File Structure

Files are optional; only add the ones you need.

```
theme/assets/src/kraken-core/content-card/
├── common.scss          # Shared styles
├── common.js           # Shared scripts
├── editor.scss         # Editor-only styles
├── editor.js           # Editor-only scripts
├── view.scss           # Frontend-only styles
├── view.js             # Frontend-only scripts
├── hooks.php           # PHP hooks
└── card-styles/        # Card style variants
    ├── default.scss
    ├── event.scss
    └── overlay.scss
```

### Card Styles

Card styles are defined in separate stylesheets. Each style should be added to the theme in:

```
theme/assets/src/kraken-core/content-card/
├── card-styles/
│   ├── _default.scss
│   ├── _overlay-border.scss
│   ├── _events.scss
├── common.scss   # Import the card-styles stylesheets here
```

### Creating Custom Card Styles

```
theme/assets/src/kraken-core/kraken-core.json
{
	"blockData": {
		"cardStyles": {
			"add": [
				{ "label": "Border", "value": "border" },
				{ "label": "Overlay Title", "value": "overlay-title" },
				{ "label": "Icon Box", "value": "icon-box" },
			],
			"remove": ["overlay-partial"]
		},
	}
}

```

## Related Documentation

- [Search & Filter Block](search-and-filter.md) - Using cards in search results
- [Theme Customization](../theme-customization.md) - Extending from themes
- [Custom Attributes](../custom-card-attributes.md) - Adding custom controls
