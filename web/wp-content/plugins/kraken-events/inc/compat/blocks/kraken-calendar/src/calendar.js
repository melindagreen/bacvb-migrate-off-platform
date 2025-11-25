/**
 * Eventastic Calendar Block v2.0
 *
 * This script handles the frontend functionality for the Eventastic Calendar block.
 * It uses a pre-loaded `window.preLoadData` object and fetches subsequent months'
 * data on demand. It displays events in a sidebar list when a user clicks on a
 * day in the FullCalendar instance.
 */

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

/**
 * Parses a date string in YYYYMMDD format into a valid JavaScript Date object.
 * @param {string} dateString The date string (e.g., "20250709").
 * @returns {Date} A new Date object in the local timezone.
 */
function parseYMDString(dateString) {
    if (!dateString || typeof dateString !== 'string' || dateString.length !== 8) {
        return null;
    }
    const year = parseInt(dateString.substring(0, 4), 10);
    const month = parseInt(dateString.substring(4, 6), 10) - 1; // Month is 0-indexed
    const day = parseInt(dateString.substring(6, 8), 10);
    return new Date(year, month, day);
}

/**
 * Formats a Date object or a date string into various string representations.
 * @param {object} args - Arguments for formatting.
 * @param {Date|string} args.input - The date to format.
 * @param {string} [args.dateFormat="Y-M-D"] - The target format.
 * @returns {string} The formatted date string.
 */
function formatDate({ input, dateFormat = "Y-M-D" }) {
    const date = (input instanceof Date) ? input : new Date(input);
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const year = date.getFullYear();
    const monthIndex = date.getMonth();
    const day = date.getDate();

    const zeroMonth = ('0' + (monthIndex + 1)).slice(-2);
    const zeroDay = ('0' + day).slice(-2);

    switch (dateFormat) {
        case "Y-M-D": return `${year}-${zeroMonth}-${zeroDay}`;
        case "Ymd": return `${year}${zeroMonth}${zeroDay}`;
        case "Y-M": return `${year}-${zeroMonth}`;
        case "M D": return `${months[monthIndex]} ${day}`;
        case "m D": return `${shortMonths[monthIndex]} ${day}`;
        case "monthName": return months[monthIndex];
        case "fullDate": return `${months[monthIndex]} ${day}, ${year}`;
        default: return `${year}-${zeroMonth}-${zeroDay}`;
    }
}

/**
 * Parses a time string (e.g., "07:00 pm") into a Date object for time-based sorting.
 * @param {string} timeStr The time string to parse.
 * @returns {Date|false} A Date object, or false if input is invalid.
 */
function parseTime(timeStr) {
    if (typeof timeStr !== 'string' || timeStr.trim() === '') return false;
    const [time, period] = timeStr.toLowerCase().split(' ');
    const [hours, minutes] = time.split(':');
    let h = parseInt(hours, 10);
    if (period === 'pm' && h !== 12) h += 12;
    if (period === 'am' && h === 12) h = 0;
    const date = new Date();
    date.setHours(h, parseInt(minutes, 10), 0, 0);
    return date;
}

// =============================================================================
// EVENT DATA PROCESSING
// =============================================================================

/**
 * Processes raw event objects and expands recurring ones into individual instances.
 * @param {Array<object>} eventObjects - The array of raw event objects from the API.
 * @returns {Array<object>} A new array of events formatted for FullCalendar.
 */
function processAndExpandEvents(eventObjects) {
    const processedEvents = [];
    if (!Array.isArray(eventObjects)) return processedEvents;

    eventObjects.forEach(event => {
        const startDate = parseYMDString(event.events_meta.event_start_date);
        if (!startDate) return; // Skip if start date is invalid

        const endDate = event.events_meta.event_end_date ? parseYMDString(event.events_meta.event_end_date) : startDate;
        const isDaily = event.events_meta.events_recurrence_options === 'daily';

        // For daily events, create an instance for each day in the range
        if (isDaily && endDate) {
            let loopDate = new Date(startDate);
            while (loopDate <= endDate) {
                processedEvents.push({
                    id: `${event.id}_${formatDate({ input: loopDate, dateFormat: 'Y-M-D' })}`,
                    title: event.title.rendered,
                    start: new Date(loopDate), // Use a new Date object
                    allDay: event.events_meta.events_event_all_day === 'true',
                    extendedProps: { ...event }
                });
                loopDate.setDate(loopDate.getDate() + 1);
            }
        } else {
            // Handle one-day and specific-date events
            processedEvents.push({
                id: event.id,
                title: event.title.rendered,
                start: startDate,
                end: endDate && endDate > startDate ? new Date(endDate.setDate(endDate.getDate() + 1)) : null, // Set end date for multi-day non-daily events
                allDay: event.events_meta.events_event_all_day === 'true',
                extendedProps: { ...event }
            });
        }
    });
    return processedEvents;
}


// =============================================================================
// DOM & UI FUNCTIONS
// =============================================================================

/**
 * Builds and renders the grid of event cards in the sidebar.
 * @param {Array} events - An array of event objects to display.
 * @param {object} args - Display options.
 */
function buildEventsGrid(events, args = {}) {
    const $target = jQuery('#calendarList');
    const config = jQuery('#calendar-container').data();
    const maxEvents = config.maxnumberofgrideventstoshow || 10;
    let output = "";

    events.sort((a, b) => {
        if (a.start < b.start) return -1;
        if (a.start > b.start) return 1;
        const timeA = parseTime(a.extendedProps.events_meta.event_start_time);
        const timeB = parseTime(b.extendedProps.events_meta.event_start_time);
        if (!timeA || !timeB) return 0;
        return timeA - timeB;
    });

    if (events.length > 0) {
        events.forEach((event, index) => {
            const props = event.extendedProps;

            // If a specific day was clicked, use that date for display. Otherwise, use the event's start date.
            const dateToDisplay = args.clickedDate ? args.clickedDate : event.start;
            const eventDate = formatDate({ input: dateToDisplay, dateFormat: config.cardconfig_dateformat || 'M D' });

            const eventTime = props.events_meta.event_start_time ? props.events_meta.event_start_time : '';
            const categories = Array.isArray(props.categories) ? props.categories.map(c => c.name).join(', ') : '';
            const location = props.events_meta.events_addr_multi || '';

            const template = `
                <div class="events-card ${index >= maxEvents ? 'overflow-card' : ''}">
                    <a href="${props.permalink}" target="_blank">
                        <div class="wrapper">
                            ${config.cardconfig_showthumbnail && props.featured_image ? `<div class="image-wrapper" style="background-image:url(${props.featured_image});"></div>` : ''}
                            <div class="content">
                                ${config.cardconfig_showcategories && categories ? `<div class="categories">${categories}</div>` : ''}
                                <div class="date">${eventDate} ${eventTime}</div>
                                <div class="title">${props.title.rendered}</div>
                                ${location ? `<div class="location">${location}</div>` : ''}
                            </div>
                        </div>
                    </a>
                </div>
            `;
            output += template;
        });

        if (events.length > maxEvents) {
            output += "<button id='eventastic-calendar-view-more'>View More</button>";
        }
    } else {
        const mssg = config.contentconfig_failuremessage || "There are no events for this day.";
        output = `<div class='ajax-message'>${mssg}</div>`;
    }
    $target.html(output);
}

/**
 * Toggles the visibility of overflow event cards.
 */
function toggleViewMore() {
    const $button = jQuery('#eventastic-calendar-view-more');
    $button.toggleClass('active');
    jQuery('.overflow-card').toggleClass('active');
    $button.text($button.hasClass('active') ? 'Show Less' : 'View More');
}


// =============================================================================
// FILTERING & DATA RETRIEVAL
// =============================================================================

/**
 * Fetches and processes events for a given month and adds them to the calendar.
 * @param {Date} date - A date within the target month.
 * @param {object} calendar - The FullCalendar instance.
 * @param {Array} loadedMonths - An array tracking which months have been loaded.
 */
async function fetchEventsForMonth(date, calendar, loadedMonths) {
    const targetMonth = formatDate({ input: date, dateFormat: 'Y-M' });
    if (loadedMonths.includes(targetMonth)) return;

    // Get a reference to the loader element
    const $loader = jQuery('.eventastic-sidebar .eventastic-loader');

    try {
        // Show the loader before starting the fetch
        $loader.addClass('is-loading');

        const startDate = formatDate({ input: new Date(date.getFullYear(), date.getMonth(), 1), dateFormat: 'Ymd' });
        const endDate = formatDate({ input: new Date(date.getFullYear(), date.getMonth() + 1, 0), dateFormat: 'Ymd' });
        const apiBase = window.preLoadData.rest_url;
        const apiUrl = `${apiBase}kraken/v1/events?date_filter=true&start_date=${startDate}&end_date=${endDate}`;

        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        loadedMonths.push(targetMonth);

        if (data && Array.isArray(data.events) && data.events.length > 0) {
            const newEvents = processAndExpandEvents(data.events);
            calendar.addEventSource(newEvents);

            newEvents.forEach(event => {
                const dateStr = formatDate({ input: event.start, dateFormat: 'Y-M-D' });
                const $dayCell = jQuery(`.fc-daygrid-day[data-date="${dateStr}"]`);
                if ($dayCell.length && !$dayCell.find('.event-dot').length) {
                    const dotEl = document.createElement('div');
                    dotEl.className = 'event-dot';
                    $dayCell.find('.fc-daygrid-day-frame').append(dotEl);
                }
            });
        }
    } catch (error) {
        console.error('Failed to fetch events for month:', error);
    } finally {
        // Always hide the loader after the operation is complete
        $loader.removeClass('is-loading');
    }
}

/**
 * Retrieves the currently selected category slugs from the filter controls.
 * @returns {Array<string>} An array of selected category slugs.
 */
function getSelectedCategories() {
    const config = jQuery('#calendar-container').data();
    const slugs = [];
    const filterType = config.filterconfig_categoryelementtype || 'buttons';

    if (filterType === "select") {
        jQuery(".category-filter > option:selected").each((_, el) => {
            if (jQuery(el).val()) slugs.push(jQuery(el).val());
        });
    } else if (filterType === "checkboxes") {
        jQuery('.category-checkbox:checked').each((_, el) => slugs.push(jQuery(el).val()));
    } else {
        jQuery(".event-category-filter.button.active").each((_, el) => slugs.push(jQuery(el).data('category')));
    }
    return slugs;
}

/**
 * Filters all loaded events based on date, category, and keyword.
 * @param {object} args - Filtering criteria.
 * @returns {Array} The filtered array of event objects.
 */
function getFilteredEvents(args) {
    const allEvents = window.eventasticCalendar.getEvents();
    const { targetDate, categories, keyword } = args;
    const targetDateStr = formatDate({ input: targetDate, dateFormat: 'Y-M-D' });

    return allEvents.filter(event => {
        // Date filter
        const eventDateStr = formatDate({ input: event.start, dateFormat: 'Y-M-D' });
        if (eventDateStr !== targetDateStr) return false;

        // Keyword filter
        if (keyword) {
            const searchPattern = new RegExp(keyword, "i");
            const title = event.title;
            const content = event.extendedProps.content || '';
            if (!searchPattern.test(title) && !searchPattern.test(content)) return false;
        }

        // Category filter
        if (categories && categories.length > 0) {
            if (!event.extendedProps.categories || event.extendedProps.categories.length === 0) return false;
            const eventCategories = event.extendedProps.categories.map(c => c.slug);
            if (!categories.some(slug => eventCategories.includes(slug))) return false;
        }

        return true;
    });
}

// =============================================================================
// MAIN INITIALIZATION
// =============================================================================
export function initializeEventasticCalendarBlock() {
    const $calendarContainer = jQuery('#calendar-container');
    if (!$calendarContainer.length) {
        console.error("Eventastic Error: Missing #calendar-container element.");
        return;
    }

    const config = $calendarContainer.data() || {};
    const loadedMonths = []; // Track fetched months

    // 1. Process initial pre-loaded data
    const initialEvents = processAndExpandEvents(window.preLoadData.event_objects || []);
    const initialMonth = formatDate({ input: new Date(), dateFormat: 'Y-M' });
    loadedMonths.push(initialMonth);

    // 2. Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const eventasticCalendar = new FullCalendar.Calendar(calendarEl, {
        events: initialEvents,
        height: 'auto',
        headerToolbar: {
            start: 'title',
            center: null,
            end: 'prev,next'
        },
        eventDisplay: 'none', // HIDE events from the calendar grid
        datesSet: async (dateInfo) => { // 1. Make the function async
            // This fires on initial load and when the view changes.
            // 2. Await the fetch to ensure events are loaded before proceeding.
            await fetchEventsForMonth(dateInfo.view.currentStart, eventasticCalendar, loadedMonths);

            // Set the title for the whole month
            const monthName = formatDate({ input: dateInfo.view.currentStart, dateFormat: 'monthName' });
            jQuery('#events-list-title').html(`<h3>Upcoming ${monthName} Events</h3>`);

            // 3. Get all events from the calendar and filter them for the current month.
            const allEvents = window.eventasticCalendar.getEvents();
            const viewStart = dateInfo.view.currentStart;
            const viewMonth = viewStart.getMonth();
            const viewYear = viewStart.getFullYear();

            const eventsForMonth = allEvents.filter(event => {
                const eventDate = event.start;
                return eventDate.getMonth() === viewMonth && eventDate.getFullYear() === viewYear;
            });

            // 4. Build the grid with all of this month's events.
            buildEventsGrid(eventsForMonth);
        },
        dayCellDidMount: (arg) => {
            // Get all events currently loaded in the calendar instance
            const allEvents = window.eventasticCalendar.getEvents();
            const dateStr = formatDate({ input: arg.date, dateFormat: 'Y-M-D' });

            // Check all events to see if any fall on the current day
            const eventsOnDay = allEvents.filter(e => formatDate({ input: e.start, dateFormat: 'Y-M-D' }) === dateStr);

            if (eventsOnDay.length > 0) {
                const dotEl = document.createElement('div');
                dotEl.className = 'event-dot';
                // Check if a dot already exists to prevent duplicates
                if (!arg.el.querySelector('.event-dot')) {
                    arg.el.querySelector('.fc-daygrid-day-frame').appendChild(dotEl);
                }
            }

            // Inside the dayCellDidMount function...
            arg.el.addEventListener('click', () => {
                jQuery('.fc-daygrid-day').removeClass('active');
                arg.el.classList.add('active');

                const eventsForDay = getFilteredEvents({
                    targetDate: arg.date,
                    categories: getSelectedCategories(),
                    keyword: jQuery("#Keyword").val(),
                });

                jQuery('#events-list-title').html(`<h3>Events for ${formatDate({ input: arg.date, dateFormat: 'fullDate' })}</h3>`);

                // Pass the clicked date into buildEventsGrid
                buildEventsGrid(eventsForDay, { clickedDate: arg.date });
            });
        },
    });

    window.eventasticCalendar = eventasticCalendar;
    eventasticCalendar.render();

    // 3. Bind Global Event Handlers
    function reRenderGrid() {
        const activeDayEl = jQuery('.fc-daygrid-day.active');
        const activeDate = activeDayEl.length ? new Date(activeDayEl.data('date') + 'T00:00:00') : null;
        if (!activeDate) return;

        const eventsForDay = getFilteredEvents({
            targetDate: activeDate,
            categories: getSelectedCategories(),
            keyword: jQuery("#Keyword").val(),
        });
        buildEventsGrid(eventsForDay);
    }

    jQuery('.event-category-filter, .category-filter, .category-checkbox').on('change click', function (e) {
        if (e.type === 'click' && jQuery(this).is('.event-category-filter')) {
            jQuery(this).toggleClass('active');
        }
        reRenderGrid();
    });

    jQuery(".eventFilterSubmit").on('click', reRenderGrid);

    jQuery(".resetFilters").on('click', () => {
        jQuery('.event-category-filter.button.active').removeClass('active');
        jQuery('.category-checkbox:checked').prop('checked', false);
        jQuery('.category-filter').val('');
        jQuery("#Keyword").val('');
        reRenderGrid();
    });

    jQuery('body').on('click', '#eventastic-calendar-view-more', (e) => {
        e.preventDefault();
        toggleViewMore();
    });
}