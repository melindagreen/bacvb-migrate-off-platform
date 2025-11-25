# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.6.2] - 2025-11-19

### Fixed
- Reset end date to match the start date for single day events

## [1.6.1] - 2025-09-24

### Fixed
- Activation function error fix 

## [1.6.0] - 2025-09-18

### Added
- Added kraken_event_occurrences database table for efficient event querying
- Added a custom event querying clause for kraken_event_start_date for ordering events
- Added RRULE support and library php-rrule library for managing recurrence
- Added two new utility functions for get_events_between_dates and get_event_recurrence_pattern
- Added plugin activation hook for creating the new database table
- Added WP CLI command `wp kraken-events upgrade_database_and_meta` create the database table, update all existing events with the new recurrence meta, and update the table with all occurrences
- Added a button on the settings page to do the same thing as the CLI command ^

### Updates
- Updated recurringDatesList utility function to optionally accept a date range
- Reworked the recurrence logic to primarily use RRULE structure for event recurrence. The event_repeat_dates meta still exists with the same structure but is now populated by fetching all occurrences with the RRULE library

## [1.5.0] - 2025-09-12

### Expanded Event API Updates

## [1.4.3] - 2025-09-02

### Added additional meta fields for event_next_occurrence for better sorting & display
- event_next_occurrence_all_day
- event_next_occurrence_start_time
- event_next_occurrence_end_time
- event_next_occurrence is unchanged and still contains the next occurrence date

## [1.4.2] - 2025-08-27

### Fixed an oversight in class-cron.php with a hard coded event slug

## [1.3.6] - 2025-07-15

### Fixed an issue raised in 1.3.2 where class aliases were not functioning as intended causing PHP fatal errors.
### Also, further expanded upon displaying recurrence event data properly for all types of Kraken Events recurrence types.

## [1.3.3] - 2025-07-11

### Fixing release version numbers for 1.3.2

## [1.3.2] - 2025-07-11

### Fixing class aliases to prevent fatal errors before & after Eventastic deactivation

## [1.3.1] - 2025-07-11

### Fatal error upon activation fix

## [1.3.0] - 2025-07-11

### Eventastic Template Conversion
- Added the necessary Eventastic templates to support sites that utilize Eventastic's page/post templates
- Updated legacy functions to be backwards compatabile so that templates using Eventastic-formatted meta data won't break.
- Added the Kraken Calendar block to replace the Eventastic Calendar block utilized on some sites.
- Added toggle settings to turn on & off the Kraken Calendar block if it is not needed.
- Added toggle seetings to turn on & off Eventastic template backwards compatibility. All new builds will always have this unchecked.
- Added a conditional to prevent Eventastic templates from overriding even Kraken-ready event templates.

## [1.2.0] - 2025-07-01

### Eventastic 2.0 data conversion
- Added the ability to convert Eventastic (2.0 or greater) event data to work with Kraken Events.
- Repair recurrence data from Eventastic to work seamlessly in Kraken Events.
- Automatically deactivates Eventastic after data conversion.
- Added backwards compatibility with Eventastic functions that are used in templates.

This should ideally be utilized in either new builds or MDW/sprint projects.

### Future Update
- Ability to convert Eventastic templates to work within Kraken Events.


## [1.0.7] - 2025-06-05

### Bug Fixes
- Added 'with_front' => false inside the rewrite array to event registration to stop single event URLs from having the /blog/ segment.

## [1.0.6] - 2025-05-29

### Updated
- Added logs to the event_next_occurrence cron

### Bug Fixes
- Fixed a bug that prevented the event_next_occurrence cron from running on multiple pages of events

## [1.0.5] - 2025-05-23

### Bug Fixes
- Added fallback for existing taxonomies

## [1.0.4] - 2025-05-22

### Bug Fixes
- Removed stray comma in utilities file

## [1.0.3] - 2025-03-05

### Added event_next_occurrence meta key
- Added event_next_occurrence meta key to store the closest future occuring event date.
- Meta value will be updated on post save
- Cron will run daily to find the next event date from event_repeat_dates and update the meta for querying and ordering by date

## [1.0.2] - 2025-03-04

### Added
- Partner portal class when CRM functionality is enabled

## [1.0.1] - 2025-03-04

### Added
- Utility functions for front-end templates and blocks


## [1.0.0] - 2025-02-19

- Release