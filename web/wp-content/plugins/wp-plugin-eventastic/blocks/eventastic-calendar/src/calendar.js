/*
NEW REFACTOR OBJECTIVE
dates need to be string and for comparison, timestamps. where timestamps are UTC midnight (stored in database as such)
the date() function should ONLY be called in a conversion function. EVERYTHING ELSE should be passed as string or timestamp.

While there are several ways to compensate for the local timezone, it isnt necessary as these are local single timezone events. 
The calendar is for events within the DMO. These are not virtual events. In order to minimize the confusion of developers handdling timezone offsets differently,
let us avoid the issue and assume UTC represents midnight of the day of interest for the DMO. 

*/

function makeTimestamp( date ){
    if( date ){
        if( 'object' == typeof date ){

            // NEED TO GET TIMESTAMP AS UTC
            var zeroMonth = ('0' + (date.getUTCMonth() + 1)).slice(-2);
            var zeroDay = ('0' + date.getUTCDate()).slice(-2);
            var datestring = date.getFullYear() + "-" + zeroMonth + "-" + zeroDay + "T00:00:00.000+00:00";
            var newDate = new Date(datestring);
            var timestamp = newDate.getTime();
        }
        else if( Number.isInteger( date ) ){
            var timestamp = date;
        }
        else{
            var datestring = date + "T00:00:00.000+00:00";
            var newDate = new Date(datestring);
            var timestamp = newDate.getTime();
        }
        return timestamp;
    }
    else{
        return false;
    }
}
function addMonth( dateParameter ){
    var timestamp = makeTimestamp( dateParameter );
    var date = new Date( timestamp );  
    var addMonthDate = new Date( date.getFullYear(), date.getMonth() + 1, date.getDate() );
    return  makeTimestamp( addMonthDate);
}
function lessMonth( dateParameter ){
    var timestamp = makeTimestamp( dateParameter );
    var date = new Date( timestamp );  
    var addMonthDate = new Date( date.getFullYear(), date.getMonth() - 1, date.getDate() );
    return  makeTimestamp( addMonthDate);
}

function makeDate( dateParameter ){
    var timestamp = makeTimestamp( dateParameter );
    var date = new Date( timestamp );
    var offsetDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
return offsetDate;
}
function makeDateString( dateParameter ){
    var timestamp = makeTimestamp( dateParameter );
    var date = makeDate(timestamp);
    return (date.getMonth()+1) + "-" + date.getDate()  + "-" + date.getFullYear() + " GMT";
}

function formatDate( args ){
    var passedDate = makeDate( args.input );
    //correct for timezone
    var date = new Date(passedDate.getTime() + passedDate.getTimezoneOffset() * 60000);
    var dateFormat = ( "undefined" != typeof args.dateFormat ) ? args.dateFormat : "Y-M-D";
    var datestring = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate();

    var zeroMonth = ('0' + (date.getUTCMonth() + 1)).slice(-2);
    var zeroDay = ('0' + date.getUTCDate()).slice(-2);
    if( "Y-M-D" == dateFormat ){
        var datestring = date.getFullYear() + "-" + zeroMonth + "-" + zeroDay;
    }
    if( "Y/M/D" == dateFormat ){
        var datestring = date.getFullYear() + "/" + zeroMonth + "/" + zeroDay;
    }    
    if( "Y-M" == dateFormat ){
        var datestring = date.getFullYear() + "-" + zeroMonth;
    }
    if( "M-D-Y" == dateFormat ){
        var datestring = (date.getMonth() + 1) + "-" + date.getDate() + "-" + date.getFullYear();
    }
    if( "M/D/Y" == dateFormat ){
        var datestring = zeroMonth + "/" + zeroDay + "/" + date.getFullYear();
    }    
    if( "M-D" == dateFormat ){
        var datestring = (date.getMonth() + 1) + "-" + date.getDate();
    }    
    if( "M/D" == dateFormat ){
        var datestring = (date.getMonth() + 1) + "/" + date.getDate();
    }     
    if( "m D" === dateFormat ){
        var datestring = getShortMonth(date.getMonth()) + " " + date.getDate();
    }    
    if( "M D" === dateFormat ){
        var datestring = getMonth(date.getMonth()) + " " + date.getDate();
    }    

    return datestring;
}
function getUpcomingDates( args ){
    // check for start date, if none, is today
    if( "undefined" != typeof args.start_date && args.start_date ){
        var start_date = args.start_date;
    }
    else{
        var start_date = makeTimestamp( new Date() );
    }
    if( "undefined" != typeof args.event.meta.recurrence_options && args.event.meta.recurrence_options ){
        if( "pattern" == args.event.meta.recurrence_options ){
            var testDates = args.event.meta.pattern_dates;
        }
        if( "specific_days" == args.event.meta.recurrence_options ){
            var testDates = args.event.meta.repeat_dates;
        }        
    }
    var upcomingDates = [];
    if( 'object' === typeof testDates  && testDates !== null ){
        for (var [key, testDate ] of Object.entries( testDates )) {
            var testDateTimestamp = makeTimestamp(testDate);
            if( testDateTimestamp > start_date ){
                var returnType = ( "undefined" != typeof args.returnType ) ? args.returnType : "arrayOfDates";
                var dateFormat = ( "undefined" != typeof args.dateFormat ) ? args.dateFormat : "Y-m-d";
                if( "arrayOfStamps" == returnType ){
                    upcomingDates.push( testDateTimestamp );
                }                
                else{
                    if( "Y-m-d" != dateFormat ){
                        testDate = formatDate( {'input' : testDateTimestamp, dateFormat : dateFormat } );
                    }                
                    if( "arrayOfDates" == returnType ){
                        upcomingDates.push( testDate );
                    }
                    if( "arrayOfObjects" == returnType ){
                        upcomingDates.push( {testDateTimestamp : testDate} );
                    }
                }
            }
        }
    }
    return upcomingDates;
}
function getGridEvents( data , args ){
    var start_date_stamp =   'undefined' != typeof args.start_date ? makeTimestamp( args.start_date ) : makeTimestamp( Date.now() );
    var grid_start_date_stamp =   'undefined' != typeof args.grid_start_date ? makeTimestamp( args.grid_start_date ) : null;
    var start_date = grid_start_date_stamp ? grid_start_date_stamp : start_date_stamp;

    var end_date_stamp =   'undefined' != typeof args.end_date ? makeTimestamp( args.end_date ) : null;
    var grid_end_date_stamp =   'undefined' != typeof args.grid_end_date ? makeTimestamp( args.grid_end_date ) : null;
    var end_date = (grid_end_date_stamp ? grid_end_date_stamp : end_date_stamp) ; 

    var maxEvents = ( 'undefined' != typeof args.maxEvents ) ? args.maxEvents : null;
    var config = jQuery('#calendar-container').data();
    var loadArgs = getLoadArguments();
    
    let eventsForDate = [];
    var eventsShown = [],
        eventObject = {};
    var i = 0;
    //iterate over all days 
    for (const [timestampSeconds, dayObject ] of Object.entries( data.days )) {
        //timestamp is PHP UTC in seconds; convert to ms
        var timestamp = timestampSeconds * 1000;
        var test_date_stamp = timestamp;


        var startDifference = ( test_date_stamp - start_date ) / ( 1000 * 60 * 60 * 24 );            
        var addEventsBasedOnDates = false;
        // check event is after startDate
        if( startDifference >= 0 ){
            addEventsBasedOnDates = true;
        }
        // check if event is in range
        if( addEventsBasedOnDates && 'undefined' != typeof end_date ){
            var endDifference = ( end_date - test_date_stamp ) / ( 1000 * 60 * 60 * 24 );  
            if( endDifference <= 0 ){
                addEventsBasedOnDates = false;
            }          
            if( ( !startDifference || startDifference > 0 )&& ( !endDifference || endDifference > 0 ) ){
                addEventsBasedOnDates = true;            
            }
        }
        // if day is within range, check its events
        if( addEventsBasedOnDates ){
            for (const [key, eventId ] of Object.entries( data.days[timestamp/1000]['events'] )) {
                // final check: reject if maxEvents is reached
                var excludeByKeyword = false;
                if( 'undefined' != typeof args.keyword && args.keyword ){
                    var keywords = args.keyword.split(" ");
                    excludeByKeyword = true;
                    for (const [k, keyword ] of Object.entries( keywords )) {
                        let regex = new RegExp(keyword, "i");                        
                        var post_content = data.event_objects[ eventId ].post_content;
                        if( post_content.match( regex ) ){
                            excludeByKeyword = false;
                        }
                        var post_title = data.event_objects[ eventId ].post_title;
                        if( post_title.match( regex ) ){
                            excludeByKeyword = false;
                        }
                       
                    }
                }        
                if( !excludeByKeyword && ( ( maxEvents &&  eventsForDate.length < maxEvents) || !maxEvents )){
                    // CHECK IF HAS CATEGORY
                    if( doesEventMatchCategory(data.event_objects[ eventId ], loadArgs ) ){
                        var dateFormat = ( "undefined" != typeof config.cardconfig_dateformat && config.cardconfig_dateformat ) ? config.cardconfig_dateformat : 'M D';
                        if( 'undefined' != typeof config.listconfig_recurringdatehandler && 'showOnce' == config.listconfig_recurringdatehandler){
                            if( !eventsShown.includes(eventId) ){
                                var eventObject = structuredClone( data.event_objects[ eventId ] );
                                //eventObject.forGridDate = format_date( makeDate(test_date_stamp), 'fullMonth');
                                eventObject.forGridDate = formatDate({input : makeDate(test_date_stamp), dateFormat: dateFormat });

                                eventsForDate.push(eventObject);
                                eventsShown.push(eventId);
                            }
                        }
                        else{
                            var eventObject = structuredClone( data.event_objects[ eventId ] );
                            eventObject.forGridDate = format_date( makeDate(test_date_stamp), 'fullMonth');
                            eventsForDate.push(eventObject);                            
                        }
                    }
                    i++;
                }
                else{
                    //break;
                }
            }
        }
    }
    return eventsForDate;
}
/*
    args.start_date // string "2024-08-16";
*/
function buildAsyncArgs( args ){
    var config = jQuery('#calendar-container').data(),
        imageSize = 'undefined' != typeof config.cardconfig_imagesize ? config.cardconfig_imagesize : 'thumbnail';

    //var start_date = format_date( args.start_date,'fullCalendar'),
      //  end_date = format_date(args.end_date,'fullCalendar');            
    let body = new URLSearchParams();
    body.append('action', 'get_events_date_ordered');
    body.append('use_categories', true);
    body.append('layoutStyle', 'integrated');
    body.append('start_date', args.start_date);
    body.append('end_date', args.end_date);
    body.append('image_size', imageSize);
    if( args.categories ){
        body.append('category_slug', args.categories);
    }    
    var built_args = {
        'bodyData' : body,
        'image_size':'large',
        'start_date' : args.start_date,
        'targetMonth' : formatDate( {'input' : args.start_date, dateFormat : "Y-M" } ),
        'end_date' : args.end_date,
        'calendarConfig_eventRenderType' : ('undefined' != typeof args.calendarConfig_eventRenderType ? args.calendarConfig_eventRenderType : ('undefined' != typeof config.calendarConfig_eventRenderType ? config.calendarConfig_eventRenderType : 'emptyCircle')),
        'layoutStyle' : 'integrated',
        'renderCalendarEvents' : ( ('undefined' != typeof args.renderCalendarEvents && args.renderCalendarEvents ) ? true : false )            
    };
    return built_args;
}
function getCategories( args ){
    var config = jQuery('#calendar-container').data();    
    var term_slugs = [];
    var callOrigin = ( "undefined" != typeof args.callOrigin && args.callOrigin ) ? args.callOrigin : null;
    if( "initial-load" == callOrigin ){
        term_slugs = ( "undefined" != typeof config.contentconfig_categories && config.contentconfig_categories) ? config.contentconfig_categories : [];
    }
    else{
        if( "undefined" != typeof config.filterconfig_categoryelementtype ){
            if( "select" == config.filterconfig_categoryelementtype){
                var activeCategories = jQuery(".category-filter > option:selected");
                jQuery.each( activeCategories , function(k,v){
                    if( jQuery(v).val() ){
                        term_slugs.push( jQuery(v).val() );
                    }
                });
            }
            else if( "checkboxes" == config.filterconfig_categoryelementtype ){
                jQuery('.category-checkbox:checkbox:checked').each(function () {
                    term_slugs.push( jQuery(this).val() );
                });
            }
            else{
                var activeCategories = jQuery(".event-category-filter.button.active");
                jQuery.each( activeCategories , function(k,v){
                    term_slugs.push( jQuery(v).data('category') );
                });
            }
        }
    }
    return term_slugs;

}

function initialLoad( args ){
    var config = jQuery('#calendar-container').data();

    /*
        The current month was already loaded to the js window.preLoadData via php
        This function will load (if configured) the previous month and the subsequent month 
    */

    var now = new Date(),
        start_date = formatDate( {'input' : now, dateFormat : "Y-M-D" } ),
        nextMonthEnd = new Date(now.getFullYear(), now.getMonth() + 2, 0),
        end_date = formatDate( {'input' : nextMonthEnd, dateFormat : "Y-M-D" } );

    if( 'undefined' != typeof config && 'undefined' != typeof config.showpastevents){
        if( config.showpastevents ){
            var prevMonthStart = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            var start_date = formatDate( {'input' : prevMonthStart, dateFormat : "Y-M-D" } );
        }
    }
    var config_args = {
        'callOrigin' : 'initial-load',
        'start_date' : start_date,
        'end_date' : end_date,
        'renderCalendarEvents' : false,
        'categories' : config.categories ? config.categories : [],
        'maxNumberOfGridEventsToPreLoad' : ( "undefined" != typeof config.maxnumberofgrideventstopreload && config.maxnumberofgrideventstopreload > 0) ? config.maxnumberofgrideventstopreload : 1000        
    };
    loadEvents( config_args );            
}
function cloneArgs( args ){
    var clonedArgs = {};
    for (const [key, value] of Object.entries(args)) {
        if( key != 'bodyData'){
            clonedArgs[key] = value;
        }
    }
    return clonedArgs;
}
function bindDayClick(){
    jQuery(".fc-day, .fc-day-top").on("click", function(e) {
        var $target = jQuery(e.currentTarget);
        jQuery(".fc-day-top, .fc-day").removeClass('active');
        $target.addClass('active');
        var date = $target.data('date'),
        start_date_stamp = makeTimestamp(date),
        end_date_stamp = start_date_stamp + (1000*24*60*60 - 1);
        var args = {
            'start_date' : start_date_stamp,
            'end_date' : end_date_stamp
        }
        let eventsForDate = getGridEvents( window.preLoadData, args  );
        var gridArgs = {
            'title' : format_date( new Date(date) , 'fullMonth'),
            'start_date' : start_date_stamp,
            'end_date' : end_date_stamp            
        }
        buildEventsGrid( eventsForDate, gridArgs );
    });              
}
/*
args.start_date : JS Date Object
*/
function loadEvents( args ){
    var targetStartMonth = formatDate( {'input' : args.start_date, dateFormat : "Y-M" } ),
        targetTestMonth = lessMonth(args.start_date),
        targetTestEndMonth = addMonth(args.end_date);
    var loadMonths = [];

    loadMonths.push( targetStartMonth );

    while( ( targetTestEndMonth - targetTestMonth ) > 0  ){
        var targetCurrentMonth = formatDate( {'input' : targetTestMonth, dateFormat : "Y-M" } );
        if(  "undefined" != typeof window.preLoadData && "undefined" != typeof window.preLoadData.monthsLoaded && !window.preLoadData.monthsLoaded.includes( targetCurrentMonth ) && !loadMonths.includes( targetCurrentMonth ) ){
            loadMonths.push( targetCurrentMonth );
        }
        targetTestMonth = addMonth( targetTestMonth );
    }
    args['categories'] = getCategories( {callOrigin:'initial-load'} );
    if( loadMonths.length > 0){
        for (var i = 0; i < loadMonths.length; i++) { 
            var targetMonth = loadMonths[i];
            if(  "undefined" != typeof window.preLoadData && "undefined" != typeof window.preLoadData.monthsLoaded && window.preLoadData.monthsLoaded.includes( targetMonth ) ){

                args.renderCalendarEvents = true;
                args.maxEvents = ( "undefined" != typeof args.maxNumberOfGridEventsToPreLoad && args.maxNumberOfGridEventsToPreLoad > 0) ? args.maxNumberOfGridEventsToPreLoad : 1000;                
                if( 'initial-load' != args.callOrigin ){
                    buildOutput(window.preLoadData, args);
                }
                
                // check if do month has been loaded; preload if not
                var monthStart = new Date( makeDate(args.start_date).getFullYear(), makeDate(args.start_date).getMonth() - 1, 1);
                var month = format_date( monthStart, "monthsLoaded");
                var monthEnd = new Date(monthStart.getFullYear(), monthStart.getMonth() + 1, 1);

                var monthStartStr = formatDate( {'input' : monthStart, dateFormat : "Y-M-D" } );
                var monthEndStr = formatDate( {'input' : monthEnd, dateFormat : "Y-M-D" } );

                if( !window.preLoadData.monthsLoaded.includes( month ) ){
                    var reducedArgs = cloneArgs( args )
                    var  monthArgsClone = structuredClone( reducedArgs );
                    monthArgsClone.renderCalendarEvents = false;
                    monthArgsClone.start_date = monthStartStr;
                    monthArgsClone.end_date = monthEndStr;
                    var monthArgs = buildAsyncArgs( monthArgsClone );
                    apiFetchEvents( monthArgs );
                } 
                else{
                }
            }
            else{

                var startDateObj = makeDate(targetMonth + "-01"),
                start_date = formatDate( {'input' : startDateObj, dateFormat : "Y-M-D" } ),
                nextMonthEnd = new Date(startDateObj.getFullYear(), startDateObj.getMonth() + 1, 0),
                end_date = formatDate( {'input' : nextMonthEnd, dateFormat : "Y-M-D" } );

                var reducedArgs = cloneArgs( args )
                var  monthArgsClone = structuredClone( reducedArgs );
                monthArgsClone.renderCalendarEvents = false;
                monthArgsClone.start_date = start_date;
                monthArgsClone.end_date = end_date;
                var monthArgs = buildAsyncArgs( monthArgsClone );
                apiFetchEvents( monthArgs );
            }
        }
    }
    else{
        console.log(' alert:: removed line 405 conditional. if you get this, add it back');
    }    
}
    function buildOutput( data, args ){

            var config = jQuery('#calendar-container').data();
            var renderCalendarEvents = ('undefined' != typeof args.renderCalendarEvents &&  args.renderCalendarEvents ) ? true : false;
            var targetMonth = formatDate( {'input' : args.start_date, dateFormat : "Y-M" } );          
            if( 'undefined' != typeof FullCalendar ){
                var events = data.fullCalendarEventsSource;
                if( 'list' == config.calendarconfig_eventrendertype ){
                    //alert('is not list: ' + targetMonth);
                    if( !window.preLoadData.monthsEventSourceAdded.includes(targetMonth) ){
                        window.calendar.addEventSource( events );
                        window.preLoadData.monthsEventSourceAdded.push(targetMonth);
                    }
                }
                else{
                    if( renderCalendarEvents ){
                        args.categories = getCategories({'callOrigin': 'category-filter' });
                        calendarEventCountRender( data, args );
                    }
                }
            }
            if(  renderCalendarEvents  ){
                if( args.start_date ){
                    var startDateOrig = makeDate( args.start_date );
                }   
                else{
                    var startDateOrig = new Date();
                }
                if( 'undefined' != typeof args.callOrigin && 'category-filter' == args.callOrigin){
                    var $target = jQuery('#calendarList');
                    var firstDay = $target.data('start_date'),
                    gridEndDay = $target.data('end_date');
                    var title = jQuery('#events-list-title h3').html();
                }       
                else{
                    var firstDay = new Date(startDateOrig.getTime() + startDateOrig.getTimezoneOffset() * 60000);
                    let gridEndDay = new Date(startDateOrig.getFullYear(), startDateOrig.getMonth(), 1);
                    gridEndDay.setDate(gridEndDay.getDate() + 1);
                    // if date is is not in the past

                    var title = ("undefined" != typeof args.title && args.title ) ? args.title : getMonth( startDateOrig.getMonth() ) + " Events";
                }  
                let lastDay = new Date(startDateOrig.getFullYear(), (startDateOrig.getMonth() + 1), 1);

                let maxNumberofEvents = args.maxNumberofEvents ? args.maxNumberofEvents : 1000;

                var args = {
                    'start_date' : args.start_date,
                    'end_date' : lastDay,
                    'maxEvents' : maxNumberofEvents,
                    'categories' : args.categories,
                    'grid_start_date' : args.start_date,
                    'grid_end_date' : args.end_date,
                    'keyword' : args.keyword,
                    'callOrigin' : args.callOrigin              
                }

                let eventsForDate = getGridEvents( data , args);
                var gridArgs = {
                    'title' : title,
                    'start_date' : firstDay,
                    'end_date' : gridEndDay                      
                }
                buildEventsGrid( eventsForDate , gridArgs);
                bindDayClick();
            }
    }
    function apiFetchEvents( args ){
        fetch(Eventastic_Variables.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: new Headers({'Content-Type': 'application/x-www-form-urlencoded'}),
            body:  args.bodyData
        })
        .then((response) => response.json())
        .then(function(data) {
            if( "undefined" != typeof window.preLoadData && "undefined" != typeof window.preLoadData.monthsLoaded && !window.preLoadData.monthsLoaded.includes( args.targetMonth ) ){
                window.preLoadData.days  = {...window.preLoadData.days,  ...data.days};
                window.preLoadData.event_objects  = {...window.preLoadData.event_objects,  ...data.event_objects};
                window.preLoadData.monthsLoaded.push( args.targetMonth );
            }       
            buildOutput( data, args );
        })
        .catch(function(error) {
            console.log('error');
          console.log(JSON.stringify(error));
        });
    }
    function replace_card_content(template, key, value) {
        key = "{{" + key + "}}";
        const re = new RegExp(`${key}`, 'g');
        template = template.replace(re, value);
        return template;
    }    
    function getMonth( monthNumber ){
        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return months[monthNumber];        
    }
    function getShortMonth( monthNumber ){
        const months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'
        ];
        return months[monthNumber];        
    }
    function format_date(date, format) {
        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        const days = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];
        var dayName = days[date.getUTCDay()];
        var monthName = months[date.getUTCMonth()];

        if (format === 'calendar') {
            var output = ('0' + (date.getUTCMonth() + 1)).slice(-2) + '/'
                + ('0' + date.getUTCDate()).slice(-2) + '/'
                + date.getUTCFullYear();
        } else if (format === 'fullMonth') {
            var output = monthName + ' ' + ('0' + date.getUTCDate()).slice(-2) + ',  '
                + date.getUTCFullYear();
        }else if (format === 'fullCalendar') {
            var output = date.getUTCFullYear() + "-" + ('0' + (date.getUTCMonth() + 1)).slice(-2) + '-'
                + ('0' + date.getUTCDate()).slice(-2);
        }else if (format === 'monthsLoaded') {
            var output = date.getUTCFullYear() + "-" + ('0' + (date.getUTCMonth() + 1)).slice(-2);
        }                
         else {
            var output = dayName + ', ' + monthName + ' ' + ('0' + date.getUTCDate()).slice(-2) + ' '
                + date.getUTCFullYear();
        }

        return output;
    }
    function parseTime(timeStr) {
        if( "undefined" != typeof timeStr ){
            const [time, period] = timeStr.split(' ');
            const [hours, minutes] = time.split(':');
            let date = new Date();
            date.setHours(parseInt(hours, 10));

            // Adjust hours for PM times
            if (period == 'PM' || period == 'pm' && date.getHours() !== 12) {
                date.setHours(date.getHours() + 12);
            }

            date.setMinutes(parseInt(minutes, 10));
            date.setSeconds(0);
            return date;
        }
        else{
            return false;
        }

    }
    function toggleCalendar( $target ){
        jQuery('#calendar-container .toggle-buttons button').removeClass('active');
        $target.addClass('active');
        var toggleTarget = $target.data('target');
        jQuery('.toggle-target').removeClass('active').removeClass('as-grid').removeClass('as-list');
        if( 'grid' == toggleTarget ){
            jQuery('.toggle-target.grid-list').addClass('active').addClass('as-grid');
        }
        if( 'list' == toggleTarget ){
            jQuery('.toggle-target.grid-list').addClass('active').addClass('as-list');
        }
        if( 'calendar' == toggleTarget ){
            jQuery('.toggle-target[data-target="calendar"]').addClass('active');
        }
    }

    function buildEventsGrid(events , args) {
        var output = "",
            config = jQuery('#calendar-container').data(),
            showDate = ( 'undefined' != typeof args.cardconfig_showdate ? args.cardconfig_showdate : config.cardconfig_showdate),
            maxEvents = config.maxnumberofgrideventstoshow,
            $target = jQuery('#calendarList');
            $target.data('start_date', args.start_date);
            $target.data('end_date', args.end_date);
            var upcomingDateFormat = "Y-M-D"; // default
            if( "undefined" != typeof config.cardconfig_upcomingdateformat && config.cardconfig_upcomingdateformat ){
                upcomingDateFormat = config.cardconfig_upcomingdateformat;
            }

        if( "undefined" != typeof window.override && "undefined" != typeof window.override.template ){
            var template = window.override.template;
        }
        else{
            var template = `
                <div class="events-card {{card_classes}}">
                    <a href="{{event_url}}">
                        <div class="wrapper">
                            <div class="content">`;                            
                if( config.cardconfig_showthumbnail ){
                    template += `<div class="image-wrapper" style="background-image:url({{event_image}});"></div>`;
                }                        
                template += `
                                <div class="categories">{{event_categories}}</div>
                                <div class="date">{{event_date}} {{event_time}}</div>`;
                if( config.cardconfig_showpatternstring ){
                    template += `<div class="pattern-string">{{event_pattern}}</div>`;
                }                        
                template += `   <div class="upcoming-dates">{{event_upcoming_dates}}</div>
                                <div class="title">{{event_title}}</div>
                                <div class="location">{{event_address}}</div>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        }
        var step = 0;
        if (Object.keys(events).length > 0) {

            // Sort the events by meta.start_time
            Object.keys(events).sort((event1, event2) => {
                const time1 = parseTime(events[event1].meta.start_time);
                const time2 = parseTime(events[event2].meta.start_time);

                if (time1 < time2) {
                    return -1;
                } else if (time1 > time2) {
                    return 1;
                } else {
                    return 0;
                }
            });
            jQuery.each(events, function (k, event) {
                const default_image = 'images/default.jpg';
                if ( event.meta.pattern_dates && event.meta.pattern_dates.length > 1 ) {
                    event.meta.pattern_dates.forEach( function(date) {
                        date = makeTimestamp(date);
                        let gridStartDate = makeTimestamp(args.grid_start_date);
                        if ( date >= gridStartDate ) {
                            let recurringEvent = structuredClone(event);
                            recurringEvent.meta.start_date = date;
                            recurringEvent.forGridDate = formatDate({input : date, dateFormat: args.date_format });
                        }
                    });
                }



                var start = event.meta.start_date,
                    end = event.meta.end_date ? event.meta.end_date : null;
                var dateStyle = ( "undefined" != typeof config.cardconfig_datestyle && config.cardconfig_datestyle ) ? config.cardconfig_datestyle : "startDate";
                var event_date = "";
                var dateFormat = ( "undefined" != typeof config.cardconfig_dateformat && config.cardconfig_dateformat ) ? config.cardconfig_dateformat : 'M D';
                if( "undefined" != typeof config.cardconfig_showdate && config.cardconfig_showdate ){
                    if( showDate ){
                        var event_date = formatDate({input : start, dateFormat: dateFormat });

                        if( event.forGridDate ){
                            event_date = event.forGridDate;
                        }
                        else if( "range" == dateStyle && end ){
                            var start = formatDate({input : start, dateFormat: dateFormat });
                            event_date =  start;
                            var end = formatDate({input : end, dateFormat: dateFormat });
                            if( end != start){
                                event_date +=   " - " + end;
                            }

                        }
                    }

                }
                var this_card = replace_card_content(template, 'event_date', event_date);

                var post_patternString = "";
                if( config.cardconfig_showpatternstring ){
                    if (event.meta.patternString && event.meta.patternString.length > 1) {
                        post_patternString = event.meta.patternString;
                    }
                }
                this_card = replace_card_content(this_card, 'event_pattern', post_patternString);

                var event_time = '';
                if( config.cardconfig_showtime ){
                    if (event.meta.start_time && event.meta.start_time.length > 3) {
                        if( config.cardconfig_showdate ){
                            event_time = "</br>";
                        }
                        event_time += event.meta.start_time;
                    }
                    if (event.meta.end_time && event.meta.end_time.length > 3) {
                        event_time += "-" + event.meta.end_time
                    }
                }
                var this_card = replace_card_content(this_card, 'event_time', event_time);


                var upcoming_dates_string = '';
                if( config.cardconfig_showupcomingdates ){
                    var upcoming_dates = getUpcomingDates( {'event':event, dateFormat: upcomingDateFormat} );
                    if( upcoming_dates.length > 0 ){

                        upcoming_dates_string = "Upcoming Dates: " + upcoming_dates.join(', ');
                    }
                }
                var this_card = replace_card_content(this_card, 'event_upcoming_dates', upcoming_dates_string);                

                var post_title = "";
                if (event.post_title) {
                    post_title = event.post_title;
                }
                this_card = replace_card_content(this_card, 'event_title', post_title);

                var post_excerpt = "";
                if (event.post_excerpt) {
                    post_excerpt = event.post_excerpt;
                }
                else if(event.alternative_excerpt){
                    post_excerpt = event.alternative_excerpt;                    
                }
                this_card = replace_card_content(this_card, 'event_excerpt', post_excerpt);

                var event_categories = "";
                if( config.cardconfig_showcategories ){

                    if( 'undefined' != typeof event.meta.thisCategories && event.meta.thisCategories.length > 0 ){
                        var eventCategoriesArr = [];
                        for (var i = 0; i < event.meta.thisCategories.length; i++) {
                            var thisCat = event.meta.thisCategories[i];
                            if( 'undefined' != typeof config.categories ){
                                if( ( 1 == config.categories.length && 'all' == config.categories[0]) || ( config.categories.includes( thisCat.slug ) ) ){
                                    eventCategoriesArr.push( thisCat.name );
                                }
                            }
                            else{
                                eventCategoriesArr.push( thisCat.name );                                
                            }
                        }                                        
                        event_categories += eventCategoriesArr.join(", ");
                    }
                }
                this_card = replace_card_content(this_card, 'event_categories', event_categories);

                var event_address = "";
                if( config.cardconfig_showaddress ){
                    if( 'undefined' != typeof event.meta.thisVenues && event.meta.thisVenues.length > 0 ){
                        if( 'undefined' != typeof venues ){
                            var thisVenueId = event.meta.thisVenues[0].term_id;
                            var thisVenue = venues[thisVenueId];
                            if( 'undefined' != typeof thisVenue.address ){
                                event_address = thisVenue.address.loc_name + " at " + thisVenue.address.street;
                            }
                            else{
                                event_address = event.meta.thisVenues[0].name + "<br>" + event.meta.thisVenues[0].description;                            
                            }
                        }
                    } 
                    if( !event_address ){
                        if( "street" == config.cardconfig_addressformat ){
                            event_address = event.meta.addr1;
                        }
                        if( "street_city" == config.cardconfig_addressformat ){
                            event_address = event.meta.addr1;
                            event_address += (event.meta.city ? " " + event.meta.city : "" );                             
                        }
                        if( "street_city_state" == config.cardconfig_addressformat ){
                            event_address = event.meta.addr1;
                            event_address += (event.meta.city ? " " + event.meta.city : "" );                             
                            event_address += (event.meta.state ? " " + event.meta.state : "" );                             
                        }
                        if( "street_city_state_zip" == config.cardconfig_addressformat ){
                            event_address = event.meta.addr1;
                            event_address += (event.meta.city ? " " + event.meta.city : "" );                             
                            event_address += (event.meta.state ? " " + event.meta.state : "" );                             
                            event_address += (event.meta.zip ? " " + event.meta.zip : "" );                             
                        }
                        if( "city_state" == config.cardconfig_addressformat ){
                            event_address = (event.meta.city ? " " + event.meta.city : "" );                             
                            event_address += (event.meta.state ? " " + event.meta.state : "" );                             
                        }                        


                    }
                }
                this_card = replace_card_content( this_card, 'event_address', event_address );

                var post_image = default_image;
                if( config.cardconfig_showthumbnail ){
                    var post_image_url = null;
                    if (event.meta.thumbnail && event.meta.thumbnail !== null) {
                        post_image_url = event.meta.thumbnail[0];
                    }
                    this_card = replace_card_content(this_card, 'event_image', post_image_url);
                }

                var post_url = "";
                if (event.permalink) {
                    post_url = event.permalink;
                }
                this_card = replace_card_content(this_card, 'event_url', post_url);

                var card_classes = "";
                if( step >= maxEvents ){
                    card_classes = " overflow-card";
                }
                
                this_card = replace_card_content(this_card, 'card_classes', card_classes);                
                output += this_card;
                step++;

            });
        }
        else {
            var mssg = config.contentconfig_failuremessage ? config.contentconfig_failuremessage : "There are no Events matching that.";
            output = "<div class='ajax-message'>" + mssg + "</div>";
        }

        if( step > (maxEvents + 1) ){ 
            output += "<button id='eventastic-calendar-view-more'>View More</button>";
        }
        if( 'undefined' != typeof args.title || args.title ){" + + "
            jQuery('#events-list-title').html('<h3>' + args.title + '</h3>');
        }
        $target.html('');
        $target.append(output);
        jQuery('#eventastic-calendar-view-more').on('click', function(e){
            e.preventDefault();
            toggleViewMore();
        })        
    }   

    function toggleViewMore(){
        jQuery('#eventastic-calendar-view-more').toggleClass('active');
        if( jQuery('#eventastic-calendar-view-more').hasClass('active') ){
            jQuery('#eventastic-calendar-view-more').html('Show Less');
            jQuery('.overflow-card').addClass('active');
        }
        else{
            jQuery('#eventastic-calendar-view-more').html('View More');
            jQuery('.overflow-card').removeClass('active');
        }
    }
    function doesEventMatchCategory( event, config ){
        var matches = [],
            include = false;
            /*
        if( ( 'undefined' != typeof config.categories && config.categories.length > 0) ){
            if( 'undefined' != typeof event.meta && 'undefined' != typeof event.meta.thisCategories && event.meta.thisCategories.length > 0 ){
                for (const [catKey, catTerm ] of Object.entries( event.meta.thisCategories ) ) {
                    if( config.categories.includes( catTerm.slug ) ){
                        matches.push(  catTerm.slug );
                         include = true;
                    }
                }
            }
        }
        else{ // include ALL, no categories requested
            include = true;
        }
        */
        if( 'undefined' != typeof config.categories ){
            if( config.categories.length == 1 && config.categories[0] == '' ){
                var categories = [];
            }
            else{
                var categories = config.categories;
            }
        }
        if( ( 'undefined' != typeof categories  )  ){
            if( 'undefined' != typeof event.meta && 'undefined' != typeof event.meta.thisCategories && event.meta.thisCategories.length > 0 ){
                for (const [catKey, catTerm ] of Object.entries( event.meta.thisCategories ) ) {
                    if( categories.includes( catTerm.slug ) || categories.length < 1 ){
                        matches.push(  catTerm.slug );
                         include = true;
                    }
                }
            }

        }
        else{ // include ALL, no categories requested
            include = true;            
        }
        if( include ){
            return matches;
        }
        else{
            return false;
        }
    }
    function ISODateString(d){
        function pad(n){return n<10 ? '0'+n : n}
        return d.getUTCFullYear()+'-'
        + pad(d.getUTCMonth()+1)+'-'
        + pad(d.getUTCDate())
    }    
    function  calendarEventCountRender( data, args ) {
        var dateKey = "",
            config = jQuery('#calendar-container').data();

        jQuery('.event-count').remove();
        setTimeout(function () {
            Object.keys( data.days ).forEach(function (key) {
                // need to iterate over days events and check for categories (and other exlcusiions)
                var countEvents = 0;
                for (const [i, value] of Object.entries(data.days[key].events) ) {
                    var eventId = data.days[key].events[i];
                    var event = data.event_objects[eventId];
                    if( doesEventMatchCategory( event, args ) ){
                        countEvents++;
                    }
                };
                var date = new Date(key * 1000);
                var formattedDate = ISODateString(date);
                var $target = jQuery('td.fc-day[data-date="' + formattedDate + '"]');
                if (countEvents > 0) {
                    var countClass = "";
                    if( 'emptyCircle' == config.calendarconfig_eventrendertype ){
                        countClass += " hide-number";
                    }

                    $target.addClass('has-event').find('.fc-daygrid-day-frame').prepend('<div class="event-count ' + countClass + '"><span>' + countEvents + '</span></div>');
                }
            });

        }, 100);
    };
    function getNextDayOfWeek(date, dayOfWeek) {
        var resultDate = new Date(date.getTime());
        resultDate.setDate(date.getDate() + (7 + dayOfWeek - date.getDay()) % 7);
        return resultDate;
    }    
    function getInitialLoadDateRange(){
        var config = jQuery('#calendar-container').data(),
        endDateDefault = ( "undefined" != typeof config.filterconfig_enddatedefault && config.filterconfig_enddatedefault ) ? config.filterconfig_enddatedefault : 'start_date';
        if( 'undefined' != typeof config.listconfig_initialloaddaterange ){
            if( 'this_weekend' == config.listconfig_initialloaddaterange ){
                //
                var date = new Date(),
                    today = date.getDay(),
                    incr = 3;
                if( 5 == today || 6 == today || 7 == today ){
                    var start_date = new Date(); 
                    var end_date = new Date(); 
                    incr = (7 - today );
                }
                else{
                    var start_date = getNextDayOfWeek( new Date(), 5 );
                    var end_date = getNextDayOfWeek( new Date(), 5 );
                }
                end_date.setDate( end_date.getDate() + incr );
            }
            if( 'today' == config.listconfig_initialloaddaterange ){
                //
                var start_date = new Date();
                var end_date = new Date();
            }            
            if( '3_months' == config.listconfig_initialloaddaterange ){
                //
                var start_date = new Date();
                var end_date = new Date();
                end_date.setMonth(end_date.getMonth() + 3);
            }            


        }
        if( !start_date || !end_date ){
            var date = new Date(), 
            y = date.getFullYear(), 
            m = date.getMonth(),
            start_date = new Date(),
            end_date = new Date(y, m + 1, 0);
        }   
        start_date.setHours(0,0,0);                
        end_date.setHours(0,0,0);                
        if ( "undefined" != typeof config.filterconfig_usedates && config.filterconfig_usedates){
            var start_date_str = formatDate( {'input' : start_date, dateFormat : "Y-M-D" } );            
            jQuery('#StartDate').val( start_date_str ); 
            if ( "undefined" != typeof config.filterconfig_fillenddateinput && config.filterconfig_fillenddateinput){
                if( 'start_date' == endDateDefault ){
                    var end_date_val = formatDate( {'input' : start_date, dateFormat : "Y-M-D" } );
                }
                if( 'one_month' == endDateDefault ){
                    var end_date_val = new Date(start_date.getFullYear(), start_date.getMonth() + 1, 1);
                }                
                if( 'two_month' == endDateDefault ){
                    var end_date_val = new Date(start_date.getFullYear(), start_date.getMonth() + 2, 1);
                }                
                if( 'three_month' == endDateDefault ){
                    var end_date_val = new Date(start_date.getFullYear(), start_date.getMonth() + 3, 1);
                }
                var end_date_str = formatDate( {'input' : end_date_val, dateFormat : "Y-M-D" } );
                jQuery('#EndDate').val( end_date_str );   
            }
            else{
                jQuery('#EndDate').val( null ); 
            }

        }
        var returnObject = {'start_date': start_date, 'end_date':end_date};
        return returnObject;
    }
    function getLoadArguments( args = {} ){
        var config = jQuery('#calendar-container').data(),
            callOrigin = ( "undefined" != typeof args.callOrigin && args.callOrigin ) ? args.callOrigin : null,
            endDateDefault = ( "undefined" != typeof config.filterconfig_enddatedefault && config.filterconfig_enddatedefault ) ? config.filterconfig_enddatedefault : 'start_date',
            useDates = ( "undefined" != typeof config.filterconfig_usedates && config.filterconfig_usedates ) ? config.filterconfig_usedates : null,            
            term_slugs = getCategories({'callOrigin': callOrigin });
        if( 'initial-load' == callOrigin || !callOrigin ){
            var start_date = ("undefined" != typeof eventasticCalendar) ? eventasticCalendar.getDate() : new Date();
        }
        else{
            if( args.start_date ){

                var start_date_string = args.start_date;
            }
            else{
                // if is using start and end date inputs  in filters:
                if( useDates ){
                    var start_date_string = jQuery('#StartDate').val();
                }
                // if is NOT using dates in filter, and is using calendar, check current day of calendar
                else{
                    var $target = jQuery('#calendarList');
                    var start_date = makeDate($target.data('start_date')); //timestamp
                }

            }
            if( !start_date && start_date_string){
                var start_date = makeDate( start_date_string );
            }
        }
        if( 'initial-load' == callOrigin || !callOrigin ){
            let end_date = new Date(start_date.getFullYear(), start_date.getMonth() + 1, 1);
        }
        else{
            if( args.end_date ){
                var end_date_string = args.end_date;
            }
            else{
                if( useDates ){                
                    // check the config for the default end date (if set)
                    if( 'start_date' == endDateDefault ){
                        var end_date_string = jQuery('#StartDate').val();
                    }
                    if( 'one_month' == endDateDefault ){
                        var end_date_string = new Date(start_date.getFullYear(), start_date.getMonth() + 1, 1);
                    }                
                    if( 'two_month' == endDateDefault ){
                        var end_date_string = new Date(start_date.getFullYear(), start_date.getMonth() + 2, 1);
                    }                
                    if( 'three_month' == endDateDefault ){
                        var end_date_string = new Date(start_date.getFullYear(), start_date.getMonth() + 3, 1);
                    }                                                
                }
                else{
                    if( start_date_string ){
                        var end_date_string = start_date_string;
                    }
                    else{
                        var end_date_string = start_date;                        
                    }
                }
            }
            var end_date = makeDate( end_date_string );
        }        
        var args = {
            'start_date' : start_date,
            'end_date' : end_date,
            'calendarConfig_eventRenderType' : config.calendarConfig_eventRenderType,
            'layoutStyle' : 'integrated',
            'renderCalendarEvents' : true,
        };
        if( term_slugs.length > 0 ){
            args.categories = term_slugs;
        }
        return args;
    }
    function submitSearch( args ){
        args.start_date = jQuery("#StartDate").val();
        args.end_date = jQuery("#EndDate").val();
        var args = getLoadArguments( args );
        var async_args = buildAsyncArgs( args );
        var start = formatDate( {'input' : args.start_date, dateFormat : "m D" } );
        var end = formatDate( {'input' : args.end_date, dateFormat : "m D" } );
        async_args.callOrigin = 'submit-search';
        async_args.keyword = jQuery("#Keyword").val();
        async_args.title = start + " to " + end;
        loadEvents( async_args );
    }    
    function decodeHTMLEntities(str) {
        const textarea = document.createElement("textarea");
        textarea.innerHTML = str;
        return textarea.value;
    }
    export function initializeEventasticCalendarBlock() {    
        var config = jQuery('#calendar-container').data(),
        eventasticCalendar = {};


        // Load the override js
        var jsBlockNamespace = "";
        if( "undefined" != typeof config.blockconfig_jsoverridenamespace &&  config.blockconfig_jsoverridenamespace ){
            jsBlockNamespace = config.blockconfig_jsoverridenamespace;
        }
        if( "function" == typeof eventasticOverride){
            window.override = eventasticOverride( jsBlockNamespace );
        }
        var toolbarHeaderStart = ( "undefined" != typeof config.calendarconfig_headertoolbarstart &&  config.calendarconfig_headertoolbarstart ) ? config.calendarconfig_headertoolbarstart : 'title';
        var toolbarHeaderCenter = ( "undefined" != typeof config.calendarconfig_headertoolbarcenter &&  config.calendarconfig_headertoolbarcenter ) ? config.calendarconfig_headertoolbarcenter : null;
        var toolbarHeaderEnd = ( "undefined" != typeof config.calendarconfig_headertoolbarend &&  config.calendarconfig_headertoolbarend ) ? config.calendarconfig_headertoolbarend : 'prev,next';

        if( 'undefined' != typeof FullCalendar ){
            eventasticCalendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                dayMaxEventRows:3,
                height: 'auto',
                displayEventTime: true,
                headerToolbar: {
                    start: toolbarHeaderStart,
                    center: toolbarHeaderCenter,
                    end: toolbarHeaderEnd
                },
                eventDidMount: function(info) {
                  info.el.querySelector('.fc-event-title').textContent = decodeHTMLEntities(info.event.title);
                }
            });
            eventasticCalendar.render();
            window.calendar = eventasticCalendar;
        }
        /*
console.log('REFACTOR: (2): normalize all the args ');
console.log('REFACTOR: (3): consolidate the formatDate format_date funcs ');
        // build initial grid from php's 5 populated upcoming events
        */
        if( 'undefined' != typeof window.preLoadData ){
                var initialLoadDateRange = getInitialLoadDateRange(),
                start_date = initialLoadDateRange.start_date,
                end_date = initialLoadDateRange.end_date;

            var args = {
                'start_date' : start_date,
                'end_date' : end_date,
                'grid_start_date' : start_date,
                'grid_end_date' : end_date,
//                'maxEvents' : 20
            };
            // events are preloaded with a query that limits it to the configured category options 
            let eventsForDate = getGridEvents( window.preLoadData, args);
            if( "undefined" != typeof config.listconfig_initialtitle && config.listconfig_initialtitle ){
                var listTitle =  config.listconfig_initialtitle;
            }
            else{
                var listTitle =  "Upcoming " + getMonth( start_date.getMonth() ) + " Events";
            }
            var gridArgs = {
                'cardconfig_showdate' : true,
                'title' : listTitle,
                'start_date' : start_date,
                'end_date' : end_date,                
                'grid_start_date' : start_date,
                'grid_end_date' : end_date,
                'date_format' : config.cardconfig_dateformat
            }

            buildEventsGrid( eventsForDate, gridArgs );
            if( 'undefined' != typeof FullCalendar ){
                
                if( 'list' == config.calendarconfig_eventrendertype ){
                    var targetMonth = formatDate( {'input' : initialLoadDateRange.start_date, dateFormat : "Y-M" }  );
                    window.preLoadData.monthsEventSourceAdded.push(targetMonth);
                    eventasticCalendar.addEventSource( window.preLoadData.fullCalendarEventsSource );
                }
                else{
                    calendarEventCountRender( window.preLoadData, config );
                }
            }  
            jQuery('.fc-next-button, .fc-prev-button').on('click', {eventasticObject: eventasticCalendar}, function(e){
                var config = $('#calendar-container').data();

                jQuery('#calendarList, #events-list-title').html('');
                var useCategories = true;

                var firstDay = e.data.eventasticObject.getDate();
                let lastDay = new Date(firstDay.getFullYear(), firstDay.getMonth() + 1, 1);

                var layoutStyle = "integrated";
                var start_date = formatDate( {'input' : firstDay, dateFormat : "Y-M-D" } );
                var end_date = formatDate( {'input' : lastDay, dateFormat : "Y-M-D" } );

                /*           
                var activeCategories = jQuery(".event-category-filter.active"),
                    term_slugs = [];
                jQuery.each( jQuery(".event-category-filter.active") , function(k,v){
                    term_slugs.push( jQuery(v).data('category') );
                })
                */
                var config_args = {
                    'start_date' : start_date,
                    'end_date' : end_date,
                    'renderCalendarEvents' : true,
                    'grid_start_date' : start_date,
                    'grid_end_date' : end_date,                                    
                    'categories' : config.categories ? config.categories : [],
                    'maxNumberOfGridEventsToPreLoad' : ( "undefined" != typeof config.maxnumberofgrideventstopreload && config.maxnumberofgrideventstopreload > 0) ? config.maxnumberofgrideventstopreload : 1000                    
                };
                var async_args = buildAsyncArgs( config_args );
                loadEvents( async_args );
            })

            bindDayClick();

            jQuery('.eventastic-calendar-block .toggle-buttons button').on('click', function(e){
                toggleCalendar( jQuery(e.currentTarget) );
            })

            jQuery('.event-category-filter').on('click', function(e){
                var $target = jQuery(e.currentTarget);
                $target.toggleClass('active');

                var loadArgs = {};
                loadArgs.callOrigin = 'category-filter';
                var args = getLoadArguments( loadArgs );
                buildOutput( window.preLoadData, args );
            })

            var $ = jQuery;
            var datePickerDispFormat = 'MM/DD/YYYY';
            var datePickerEvalFormat = 'YYYY-MM-DD';

        //This is used to setup the date boxes
            $("input[type=date].eventasticDatePicker").each(function() {

                var options = {
                    showDropdowns: false,
                    opens: "right",
                    drops: "down",
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    autoApply: true,
                    locale: {
                        format: datePickerDispFormat,
                        firstDay: 0
                    }                    
                };

                // current value?
                if ($(this).val() != "") {
                    options.startDate = moment($(this).val()).format(datePickerDispFormat);
                }

                // init it
                $(this).daterangepicker( options );

                // act upon selection
                
                $(this).on('show.daterangepicker', function(ev, picker) {
                    if ($(this).val() != "") {
                        var sd = moment($(this).val()).format(datePickerDispFormat);
                        $(this).data('daterangepicker').setStartDate(sd);
                    }
                });
                
                $(this).on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    if ($(this).val() != "") {
                        // for comparison
                        var triggerEl = $(this);
                        var newDate = picker.startDate.format(datePickerEvalFormat);
                        var areStart = (picker.element.attr('id').toLowerCase().indexOf('start'))
                            ? true
                            : false;

                        // set our value
                        $(this).val(newDate).trigger('change');
                        // go through the other eventastic date pickers and set any that
                        //  has a date less than this date to this date
                        $("input[type=date].eventasticDatePicker").each(function() {
                            if ($(this).get(0) !== triggerEl.get(0)) {
                                var thisDate = moment($(this).val()).format(datePickerEvalFormat);
                                // if it's start, ensure end is after; opposite if not
                                if ( ( (areStart) && (thisDate > newDate) ) || ( (! areStart) && (thisDate < newDate) ) ) {
                                    $(this).val(moment(newDate).format(datePickerEvalFormat));
                                    // and change the related picker
                                    var updatedDate = moment(newDate).format(datePickerDispFormat);
                                    $(this).data('daterangepicker').setStartDate(updatedDate);
                                    $(this).data('daterangepicker').setEndDate(updatedDate);
                                }                        
                            }
                        });
                    }
                });
                $(this).on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('').trigger('change');
                });
            });

        } 
        else{
            console.log('1180 DO NOT buildEventsGrid');
        }

        jQuery(".eventFilterSubmit").click(function(){
            args.callOrigin = 'submit-search';
            submitSearch( args );
        })
        jQuery(".resetFilters").click(function () {

            /*
            const startDateInput = document.getElementById("StartDate");
            const endDateInput = document.getElementById("EndDate");
            startDateInput.value = "";
            endDateInput.value = "";
            // Loop through the input elements and clear their values
        */
            if( "undefined" != typeof config.filterconfig_categoryelementtype ){
                if( "select" == config.filterconfig_categoryelementtype){
                    var activeCategories = jQuery(".category-filter > option:selected");
                    jQuery.each( activeCategories , function(k,v){
                        jQuery(v).prop("selected", false);
                    });
                }
                else if( "checkboxes" == config.filterconfig_categoryelementtype ){
                    jQuery('.category-checkbox:checkbox:checked').each(function (k,v) {
                        jQuery(v).prop('checked', false);
                    });
                }
                else{
                    var activeCategories = jQuery(".event-category-filter.button.active");
                    jQuery.each( activeCategories , function(k,v){
                        jQuery(v).removeClass('active');
                    });
                }
            }        
            args = getLoadArguments();
            console.log('XXXXXXXXXXXXfilterargs ::::', args);

            buildOutput( window.preLoadData, args );
        })

        initialLoad();
    };