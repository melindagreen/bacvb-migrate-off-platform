# Kraken ACF Connector Block

_Last Updated: October 2025_

## Overview

The Kraken ACF Connector block displays Advanced Custom Fields (ACF) data with customizable formatting and layout options. It supports various field types and provides hooks for extensive customization.

## Block Attributes

### Content Configuration

| Attribute     | Type   | Default         | Description                |
| ------------- | ------ | --------------- | -------------------------- |
| `contentType` | string | "kraken-events" | Type of content to display |
| `presetField` | string | ""              | Preset field selection     |
| `customField` | string | ""              | Custom field name          |

### Display Options

| Attribute          | Type    | Default | Description                   |
| ------------------ | ------- | ------- | ----------------------------- |
| `displayIcon`      | boolean | false   | Show field icon               |
| `displayLabel`     | boolean | false   | Show field label              |
| `customLabelText`  | string  | ""      | Custom label text             |
| `outputAsLink`     | boolean | false   | Output as clickable link      |
| `customLinkText`   | string  | ""      | Custom link text              |
| `customLinkType`   | string  | ""      | Link type (internal/external) |
| `customLinkTarget` | boolean | false   | Open link in new tab          |
| `outputAsList`     | boolean | false   | Output as list format         |

### Block Supports

The block supports:

- **Anchor**: Add custom anchor ID
- **Align**: Block alignment options
- **Color**: Background and text color with contrast checker
- **Spacing**: Margin and padding controls
- **Typography**: Font size, line height, and text alignment

## Supported Field Types

### Text Fields

- Text
- Textarea
- Number
- Email
- URL

### Choice Fields

- Select
- Radio Button

### Content Fields

- WYSIWYG
- Image

### Relational Fields

- Post Object
- Taxonomy

### Date/Time Fields

- Date Picker
- Time Picker

## PHP Hooks

### Field Display Hooks

| Hook                                                         | Parameters                                | Description                                      |
| ------------------------------------------------------------ | ----------------------------------------- | ------------------------------------------------ |
| `kraken-core/kraken-acf-connector/{$field_name}_field_name`  | `$field_name`, `$id`, `$attrs`            | Modify default ACF field name for presets        |
| `kraken-core/kraken-acf-connector/{$field_name}_field_label` | `$label`                                  | Modify default ACF field label                   |
| `kraken-core/kraken-acf-connector/{$field_name}_link_text`   | `$field_value`, `$id`, `$attrs`           | Modify link text value                           |
| `kraken-core/kraken-acf-connector/{$field_name}_title`       | `$title`, `$id`, `$attrs`                 | Modify title value                               |
| `kraken-core/kraken-acf-connector/crm_address_keys`          | `$keys = []`                              | Modify default ACF field keys for address output |
| `kraken-core/kraken-acf-connector/event_address_keys`        | `$keys = []`                              | Modify default ACF field keys for address output |
| `kraken-core/kraken-acf-connector/listing_socials`           | `$socials = []`                           | Modify default ACF field keys for social media   |
| `kraken-core/kraken-acf-connector/event_socials`             | `$socials = []`                           | Modify default ACF field keys for social media   |
| `kraken-core/kraken-acf-connector/event_price_varies_text`   | `$text`                                   |                                                  |
| `kraken-core/kraken-acf-connector/date_picker_format`        | `$format`, `$field_name`, `$id`, `$attrs` | Modify date output format                        |
| `kraken-core/kraken-acf-connector/time_picker_format`        | `$format`, `$field_name`, `$id`, `$attrs` | Modify time output format                        |
| `kraken-core/kraken-acf-connector/event_date_format`         | `$format`, `$id`, `$attrs`                | Modify event date format                         |

### Aria Labels

| Hook                                                            | Parameters                     | Description |
| --------------------------------------------------------------- | ------------------------------ | ----------- |
| `kraken-core/kraken-acf-connector/{$field_name}_aria_label`     | `$aria_label`, `$id`, `$attrs` | For custom  |
| `kraken-core/kraken-acf-connector/crm_website_aria_label`       | `$aria_label`, `$id`, `$attrs` | For presets |
| `kraken-core/kraken-acf-connector/crm_phone_aria_label`         | `$aria_label`, `$id`, `$attrs` | For presets |
| `kraken-core/kraken-acf-connector/crm_email_aria_label`         | `$aria_label`, `$id`, `$attrs` | For presets |
| `kraken-core/kraken-acf-connector/event_website_aria_label`     | `$aria_label`, `$id`, `$attrs` | For presets |
| `kraken-core/kraken-acf-connector/event_phone_aria_label`       | `$aria_label`, `$id`, `$attrs` | For presets |
| `kraken-core/kraken-acf-connector/event_email_aria_label`       | `$aria_label`, `$id`, `$attrs` | For presets |
| `kraken-core/kraken-acf-connector/event_ticket_link_aria_label` | `$aria_label`, `$id`, `$attrs` | For presets |

### Icons

| Hook                                          | Parameters | Description     |
| --------------------------------------------- | ---------- | --------------- |
| `kraken-core/kraken-acf-connector/phone_icon` | `$icon`    | Modify icon SVG |
| `kraken-core/kraken-acf-connector/email_icon` | `$icon`    | Modify icon SVG |

### Mindtrip

| Hook                                                   | Parameters               | Description               |
| ------------------------------------------------------ | ------------------------ | ------------------------- |
| `kraken-core/kraken-acf-connector/mindtrip_link_text`  | `$text`, `$id`, `$attrs` | Link text                 |
| `kraken-core/kraken-acf-connector/mindtrip_icon`       | `$icon`                  | Modify icon SVG           |
| `kraken-core/kraken-acf-connector/mindtrip_prompt`     | `$text`, `$id`, `$attrs` | Prompt passed to Mindtrip |
| `kraken-core/kraken-acf-connector/mindtrip_aria_label` | `$text`, `$id`, `$attrs` | Aria label for link       |

### Hook Only Mode

When using the block in hook only mode, the output must be added with the following hook.

| Hook                                                       | Parameters                    | Description          |
| ---------------------------------------------------------- | ----------------------------- | -------------------- |
| `kraken-core/kraken-acf-connector/hook-only/{$field_name}` | ``, `$id`, `$attrs`, `$field` | For hook only output |

## Theme Customization

### File Structure

```
theme/assets/src/kraken-core/kraken-acf-connector/
├── common.scss          # Shared styles
├── common.js           # Shared scripts
├── editor.scss         # Editor-only styles
├── editor.js           # Editor-only scripts
├── view.scss           # Frontend-only styles
├── view.js             # Frontend-only scripts
└── hooks.php           # PHP hooks
```

## Event Integration

The block includes special handling for event-related fields for Kraken Events

## Troubleshooting

### Common Issues

**Field not displaying?**

- Check that the field name is correct
- Verify the field exists on the post
- Ensure the field has a value

## Related Documentation

- [Content Card Block](content-card.md) - Using ACF data in cards
- [Search & Filter Block](search-and-filter.md) - Displaying ACF data in search results
- [Theme Customization](../theme-customization.md) - Extending from themes
