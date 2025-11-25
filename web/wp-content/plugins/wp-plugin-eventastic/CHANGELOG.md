# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.3] - 2024-12-19
### Fixed
- FullCalendar HTML entities

## [2.0.2] - 2024-12-13
### Fixed
- Addresses a common request to display the date in which a recurring event happens instead of the range of dates that it happens.

## [2.0.1] - 2024-11-25
### Removed
- Removed Font Awesome kit

## [2.0.0] - 2024-09-12
### Added
- Major feature addition of Recurring events. 
- Adds new schema/data structure for "v2" of recurring events. 
- Updates the FullCalendar library to latest release
- Adds a comprehensive Events Page calendar block
- More details here: http://demo.maddenmedia.com/2024/content/eventastic/deployment/install.html

### Fixed
- More PHP 8 cleanup

## [1.8.97] - 2024-08-01
### Fixed
- Added checks for meta output to fix PHP warnings

## [1.8.96] - 2024-06-18
### Fixed
- hiding v2 options in order to permit a release for robots follow.

## [1.8.95] - 2024-05-14
### Fixed
- corrected robots meta tag to output "follow".
- adds sql check on the existence of a start date (apparently neceessary with some combination of newer php and mysql versions)
- query outputs list of event's venues

## [1.8.9] - 

## [1.8.8] - 2023-09-01
### Fixed
- corrected get_events_date_ordered() to include non-recurring events; also set default end date for recurring events to one year if event has no end date.

## [1.8.7] - 2023-08-31
### Fixed
- Updated plugin ver number.

## [1.8.6] - 2023-08-31
### Fixed
- consequent to 1.8.5, the function get_events_date_ordered broke for recurring events. This update creates all instances of a recurring event that has no end date through the end date of the query.

## [1.8.5] - 2023-08-31
### Added
- main get_events query is revised to return events with no end date

## [1.8.3/1.8.4] - 2023-08-14
### Fixed
- PHP 8, fixing statically called function declartions in the main php file.
- check if $post is set, if not continuing. Issue is from the login page.

## [1.8.2] - 2023-08-14
### Added
- `noindex, nofollow` is added to robots meta tag on single page for past events
- "Date(s)" column in admin Events table is changes to two sortable columns, Start Date and End Date 

## [1.8.1] - 2023-06-28
### Fixed
- Revised the moment.js enqueue script handle to 'mm-moment' to avoid script defer issues with wp core's 'moment' enqueue
  
## [1.8.0] - 2023-06-04
### Added
- Add excerpt to calendar events output 

## [Unreleased]
### Changed
- This changelog file is updated with all missing releases. Roll it into the next point release!

## [1.7.9] - 2023-03-16
### Added
- Tweaked ajax call to allow for single-date events

## [1.7.7] - 2023-02-23
### Added
- Added URL validation notice for event URLs
- Converted social media fields into URL fields

## [1.7.4] - 2023-01-18
### Added
- New ajax function to display JSON code for event calendar

## [1.7.3] - 2023-01-05
### Added
- Missing image tag in ajax function

## [1.7.2] - 2022-11-23
### Changed
- Show years in admin list
- Update MetaBoxContactInformation.php
- Double check recurring is actually recurring
- Check values set in theme template file

## [1.7.1] - 2022-08-12
### Changed
- In the Calendar Render, a JS array is created of event objects. Update adds two properties to an event object, featured_image and images (gallery images).
- Images can be called from eventastic_render_event_calendar by adding a third argument, an array of size names ['Full', 'thumbnail'].
- Dropping the argument, no images are returned.


## [1.7.0] - 2022-08-09
### Added
- Config options for (1) hiding venues, organizers and featured images and (2) moving categories to main from sidebar
### Fixed
- Fixed gallery image issues:
-- Default value of empty array
-- Checks if image exists, purges from array if not
-- Bug fix: new images could not be deleted; now working
- Query issue where events with no end date were not returned; on event save, if there is no end-date, the end-date is set to the start-date
- Admin UI where toggling the recurring option mistakenly hid the time option
- Day-of-week lookup for recurring events (off-by-one error)

## [1.6.3.1] - 2022-07-08
### Fixed
Re-added missing  tag to example code

## [1.6.3] - 2022-07-08
### Fixed
- Fixed bug with iteration on the event output in cases of recurring events
- Fixed filter/search bug where it would ignore the date rage

## [1.6.2] - 2022-06-08
### Fixed
- Squashed minor alerts in library/FormControlLayout.php and admin/MetaBoxGallery.php
### Changed
- Did some code standard updates to reflect WP style guide in files w/ bug fixes
- Reversed Changelog

## [1.6.1] - 2022-05-19
### Added
- Date filters will now change the other date filter to match itself if the other date is in the past
- This CHANGELOG.md file
### Removed
- The legacy readme.txt file in this plugin

## [1.6.0] - 2022-05-17
### Changed
- Updated date range query to better find partial overlaps in recurring events vs date range.

## [1.5.6]
### Changed
- Meta Query Update to capture multi day events

## [1.5.5]
### Changed
- Query meta modified (without extensive testing) to capture multi-day events.
- Query now looks if start date OR end date falls within the searched dates.

## [1.5.4]
### Changed
- Tax Query param now takes string or array, allowing for one or multiple category terms per taxonomy

## [1.5.3]
### Changed
- [bugfix] Template query was not returning single template

## [1.5.2]
### Changed
- Additional bugfix for archive check in enqueus

## [1.5.1]
### Changed
- [bugfix] Template query was not returning archive template
- Modified logic within view_project_template

## [1.5.0]
### Added
- We have a mascot now!

## [1.4.3]
### Changed
- Fix to correctly factor in meta key prefix for date queries

## [1.4.0]
### Added
- Added in example template files

## [1.0.0]
### Initial Release
- Hello, world!
