# Helpers and Utilities

_Last Updated: October 2025_

## Overview

Kraken Core includes several helper classes and utility functions to simplify common tasks and provide consistent functionality across the plugin. This documentation covers all available helpers and utilities.

## PHP Helper Classes

### Helpers Class

The `Helpers` class provides utility functions for plugin management and integration checks.

#### Plugin Status Checks

##### `check_required_plugins()`

```php
public static function check_required_plugins(): bool
```

**Description**: Checks if all required plugins are active.

**Returns**: `true` if all required plugins are active, `false` otherwise.

**Required Plugins**:

- Advanced Custom Fields (ACF)

**Example**:

```php
if (Helpers::check_required_plugins()) {
    // Required plugins are active
    echo 'All required plugins are installed and active.';
} else {
    // Missing required plugins
    echo 'Please install and activate the required plugins.';
}
```

##### `check_kraken_crm_status()`

```php
public static function check_kraken_crm_status(): bool
```

**Description**: Checks if Kraken CRM plugin is active.

**Returns**: `true` if Kraken CRM is active, `false` otherwise.

**Example**:

```php
if (Helpers::check_kraken_crm_status()) {
    // Kraken CRM is active
    $listings = get_posts(['post_type' => 'listing']);
}
```

##### `check_kraken_events_status()`

```php
public static function check_kraken_events_status(): bool
```

**Description**: Checks if Kraken Events plugin is active.

**Returns**: `true` if Kraken Events is active, `false` otherwise.

**Example**:

```php
if (Helpers::check_kraken_events_status()) {
    // Kraken Events is active
    $events = get_posts(['post_type' => 'event']);
}
```

#### Event Plugin Detection

##### `get_events_plugin()`

```php
public static function get_events_plugin(): string|false
```

**Description**: Returns the currently active events plugin.

**Returns**: Plugin name or `false` if none active.

**Supported Plugins**:

- `kraken-events` - Kraken Events plugin
- `eventastic` - Eventastic plugin
- `the-events-calendar` - The Events Calendar (TEC)

**Example**:

```php
$events_plugin = Helpers::get_events_plugin();

switch ($events_plugin) {
    case 'kraken-events':
        // Handle Kraken Events
        break;
    case 'eventastic':
        // Handle Eventastic
        break;
    case 'the-events-calendar':
        // Handle TEC
        break;
    default:
        // No events plugin active
        break;
}
```

##### `get_events_slug()`

```php
public static function get_events_slug(): string|false
```

**Description**: Returns the post type slug for the active events plugin.

**Returns**: Post type slug or `false` if no events plugin active.

**Post Type Slugs**:

- Kraken Events: Customizable (default: `event`)
- Eventastic: `event`
- TEC: `tribe_events`

**Example**:

```php
$events_slug = Helpers::get_events_slug();

if ($events_slug) {
    $events = get_posts(['post_type' => $events_slug]);
}
```

#### CRM Integration

##### `get_kraken_crm_listing_slug()`

```php
public static function get_kraken_crm_listing_slug(): string|false
```

**Description**: Returns the listing post type slug for Kraken CRM.

**Returns**: Listing slug or `false` if Kraken CRM not active.

**Example**:

```php
$listing_slug = Helpers::get_kraken_crm_listing_slug();

if ($listing_slug) {
    $listings = get_posts(['post_type' => $listing_slug]);
}
```

#### Error Handling

##### `log_error($message)`

```php
public static function log_error(string $message): void
```

**Description**: Logs an error message to the debug.log file.

**Parameters**:

- `$message` (string) - The message to log

**Example**:

```php
try {
    // Some operation that might fail
    $result = risky_operation();
} catch (Exception $e) {
    Helpers::log_error('Risky operation failed: ' . $e->getMessage());
}
```

##### `notify_missing_plugins()`

```php
public static function notify_missing_plugins(): void
```

**Description**: Displays admin notice if required plugins are missing.

**Example**:

```php
// This is automatically called, but you can call it manually if needed
Helpers::notify_missing_plugins();
```

### Utilities Class

The `Utilities` class provides frontend utility functions.

#### String Utilities

##### `to_kebab_case($string)`

```php
public static function to_kebab_case(string $string): string
```

**Description**: Converts a string to kebab-case format.

**Parameters**:

- `$string` (string) - The string to convert

**Returns**: Kebab-case formatted string

**Example**:

```php
$camelCase = 'myCamelCaseString';
$kebabCase = Utilities::to_kebab_case($camelCase);
// Result: 'my-camel-case-string'

$pascalCase = 'MyPascalCaseString';
$kebabCase = Utilities::to_kebab_case($pascalCase);
// Result: 'my-pascal-case-string'
```

**Use Cases**:

- Converting class names to CSS classes
- Creating HTML IDs from variable names
- Generating URL-friendly slugs

## Common Use Cases

### Plugin Integration

#### Check for Events Plugin

```php
// Check if any events plugin is active
$events_plugin = Helpers::get_events_plugin();
if ($events_plugin) {
    $events_slug = Helpers::get_events_slug();
    $events = get_posts(['post_type' => $events_slug]);
}
```

#### CRM Integration

```php
// Check for Kraken CRM
if (Helpers::check_kraken_crm_status()) {
    $listing_slug = Helpers::get_kraken_crm_listing_slug();
    $listings = get_posts(['post_type' => $listing_slug]);
}
```

### Error Handling

```php
// Wrap risky operations in try-catch
try {
    $result = some_risky_operation();
} catch (Exception $e) {
    Helpers::log_error('Operation failed: ' . $e->getMessage());
    // Handle error gracefully
}
```

### String Formatting

```php
// Convert class names to CSS classes
$className = 'myCustomClassName';
$cssClass = Utilities::to_kebab_case($className);
// Result: 'my-custom-class-name'

// Use in HTML
echo '<div class="' . $cssClass . '">Content</div>';
```

## Related Documentation

- [Architecture](architecture.md) - Plugin structure and organization
- [Custom Attributes](custom-attributes.md) - Dynamic attribute system
- [Examples](examples.md) - Common usage patterns
