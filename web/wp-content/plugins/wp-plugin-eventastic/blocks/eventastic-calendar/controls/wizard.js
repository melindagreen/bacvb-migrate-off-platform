/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
    PanelRow,
	ComboboxControl,
	SelectControl,
	Button,
	ResponsiveWrapper,
    Dropdown,
	TextControl,
	ToggleControl,	
	TextareaControl,
    DateTimePicker,
  PanelBody,  
	Spinner,
} from "@wordpress/components";
import { useSelect , withSelect} from '@wordpress/data';
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { date } from '@wordpress/date';

import { useState } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";
import TokenMultiSelectControl from './token-multiselect-control';


/*** CONSTANTS **************************************************************/
/*** FUNCTIONS **************************************************************/

const Wizard = (props) => {
	const { attributes, setAttributes, terms } = props;


	return (
		<div className={`content-selector eventastic-calendar-block ${props.className}`}>
            <PanelBody title="Content"> 
                <PanelRow>
                    <ToggleControl
                        label={__("Limit the Categories to Show?")}
                        checked={attributes.contentConfig_useAllCategories}
                        onChange={() => {
                            setAttributes({ 
                                contentConfig_useAllCategories: !attributes.contentConfig_useAllCategories
                            });
                        }}
                    />                
                </PanelRow>
                {attributes.contentConfig_useAllCategories && (

                    <PanelRow>
                        <TokenMultiSelectControl 
                          value={ attributes.contentConfig_categories } 
                          label='Only Show the Following Categories'
                          options={[
                            // Map terms to option objects
                            ...terms.map(term => {
                              return {
                                value: term.slug,
                                label: term.name.replace('&amp;', '&'),
                              }
                            })
                          ]} 
                          onChange={ value => setAttributes({ contentConfig_categories: value })}
                        />
                    </PanelRow>
                )}
                <PanelRow>
                    <TextControl
                        label={__("Message to show when no events found")}
                        value={attributes.contentConfig_failureMessage}
                        onChange={(val) => {
                            setAttributes({ contentConfig_failureMessage: val });
                        }}
                    />
                </PanelRow>                
            </PanelBody>           
            <PanelBody title="Layout">           
                <PanelRow>

        			<SelectControl
        				label={__("Layout Format")}
        				value={attributes.layoutStyle}
        				options={
                            [
                                { label: "List Only", value: "list" }, 
                                { label: "Calendar Only", value: "calendar" }, 
                                { label: "Calendar and List (Integrated)", value: "integrated" }, 
                                { label: "Calendar and List (Toggled)", value: "toggled" }, 
                            ]
                        }
        				onChange={(val) => {
        					setAttributes({ layoutStyle: val });
        				}}
        			/>
                </PanelRow>

                {attributes.layoutStyle === "integrated" && (
                    <PanelRow className='sub-row'>
                        <SelectControl
                            label={__("Calendar Location")}
                            value={attributes.integratedCalendarLocation}
                            options={
                                [
                                    { label: "Calendar in Sidebar", value: "sidebar" }, 
                                    { label: "Calendar on top", value: "top" }
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ integratedCalendarLocation: val });
                            }}
                        />
                    </PanelRow>
                )}
                {attributes.layoutStyle !== "calendar" && (
                    <PanelRow >
                        <SelectControl
                            label={__("Cards Per Row ")}
                            value={attributes.listConfig_cardsPerRow}
                            options={
                                [
                                    { label: "1", value: "1" }, 
                                    { label: "2", value: "2" },
                                    { label: "3", value: "3" },
                                    { label: "4", value: "4" }                                    
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ listConfig_cardsPerRow: val });
                            }}
                        />
                    </PanelRow>            
                )}
                <PanelRow>
                    <NumberControl
                        label={__("Max # Events to Show in Grid")}
                        value={attributes.maxNumberOfGridEventsToShow}
                        onChange={(val) => {
                            setAttributes({ maxNumberOfGridEventsToShow: val });
                        }}

                    />                
                </PanelRow>
            </PanelBody>
            <PanelBody title="Style">
                <PanelRow>
                    <SelectControl
                        label={__("Stylesheet Options")}
                        value={attributes.styleSheet}
                        options={
                            [
                                { label: "Default", value: "default" }, 
                                { label: "Option One", value: "style-one" },
                                { label: "None", value: "none" }
                            ]
                        }
                        onChange={(val) => {
                            setAttributes({ styleSheet: val });
                        }}
                    />
                </PanelRow>

        </PanelBody>
        <PanelBody title="Filter & Search">
            <PanelRow>
                <ToggleControl
                    label={__("Use Filters?")}
                    checked={attributes.useFilters}
                    onChange={() => {
                        setAttributes({ 
                            useFilters: !attributes.useFilters
                        });
                    }}
                />                
            </PanelRow>
            {attributes.useFilters && (
                <PanelRow className='sub-row'>
                    <SelectControl
                        label={__("Filters Location")}
                        value={attributes.filterLocation}
                        options={
                            [
                                { label: "Left Sidebar", value: "sidebar" }, 
                                { label: "Above", value: "above" }
                            ]
                        }
                        onChange={(val) => {
                            setAttributes({ filterLocation: val });
                        }}
                    />
                </PanelRow>
            )}                        
            {attributes.useFilters && (
                <PanelRow className='sub-row'>
                    <ToggleControl
                        label={__("Use Date Filters?")}
                        checked={attributes.filterConfig_useDates}
                        onChange={() => {
                            setAttributes({ 
                                filterConfig_useDates: !attributes.filterConfig_useDates
                            });
                        }}
                    />
                </PanelRow>
            )}
            {attributes.filterConfig_useDates && (
                <PanelRow className='sub-row'>
                    <SelectControl
                        label={__("End Date Default For Queries?")}
                        value={attributes.filterConfig_endDateDefault}
                        options={
                            [
                                { label: "Same as Start Date", value: "start_date" },
                                { label: "Through Current Month", value: "one_month" }, 
                                { label: "Through the Next Month", value: "two_month" },
                                { label: "Through Two More Months", value: "three_month" }
                            ]
                        }
                        onChange={(val) => {
                            setAttributes({ filterConfig_endDateDefault: val });
                        }}
                    />
                </PanelRow>
            )}
            {attributes.filterConfig_useDates && (
                <PanelRow className='sub-row'>
                    <ToggleControl
                        label={__("Prepopulate End Date Input?")}
                        checked={attributes.filterConfig_fillEndDateInput}
                        onChange={() => {
                            setAttributes({ 
                                filterConfig_fillEndDateInput: !attributes.filterConfig_fillEndDateInput
                            });
                        }}
                    />
                </PanelRow>
            )}            
            {attributes.useFilters && (
                <PanelRow className='sub-row'>
                    <ToggleControl
                        label={__("Use Keyword Filter?")}
                        checked={attributes.filterConfig_useKeyword}
                        onChange={() => {
                            setAttributes({ 
                                filterConfig_useKeyword: !attributes.filterConfig_useKeyword
                            });
                        }}
                    />                    
                </PanelRow>
            )}
            {attributes.useFilters && (
                <PanelRow className='sub-row'>
                    <ToggleControl
                        label={__("Use Category Filter?")}
                        checked={attributes.filterConfig_useCategories}
                        onChange={() => {
                            setAttributes({ 
                                filterConfig_useCategories: !attributes.filterConfig_useCategories
                            });
                        }}
                    />    
                </PanelRow>  
            )}
            {attributes.useFilters && attributes.filterConfig_useCategories && (
                <PanelRow className='sub-sub-row'>
                    <SelectControl
                        label={__("Category Filter Type")}
                        value={attributes.filterConfig_categoryElementType}
                        options={
                            [
                                { label: "Buttons", value: "buttons" }, 
                                { label: "Dropdown Select", value: "select" },
                                { label: "Checkboxes", value: "checkboxes" }
                            ]
                        }
                        onChange={(val) => {
                            setAttributes({ filterConfig_categoryElementType: val });
                        }}
                    />
                </PanelRow>
            )}      
            {attributes.useFilters && (
                <PanelRow className='sub-row'>
                    <ToggleControl
                        label={__("Use Filter Reset?")}
                        checked={attributes.filterConfig_useFilterReset}
                        onChange={() => {
                            setAttributes({ 
                                filterConfig_useFilterReset: !attributes.filterConfig_useFilterReset
                            });
                        }}
                    />    
                </PanelRow>  
            )}
            {attributes.useFilters && (
                <PanelRow className='sub-row'>
                    <ToggleControl
                        label={__("Use Search Submit?")}
                        checked={attributes.filterConfig_useFilterSubmit}
                        onChange={() => {
                            setAttributes({ 
                                filterConfig_useFilterSubmit: !attributes.filterConfig_useFilterSubmit
                            });
                        }}
                    />    
                </PanelRow>  
            )}            

        </PanelBody>
        {attributes.layoutStyle !== "list" && (

            <PanelBody title="Calendar Configurations">

                <PanelRow>
                        <SelectControl
                            label={__("How to Display Events on Calendar")}
                            value={attributes.calendarConfig_eventRenderType}
                            options={
                                [
                                    { label: "Empty Circle", value: "emptyCircle" },
                                    { label: "Circle with Number of Events", value: "numberedCircle" },
                                    { label: "List of events", value: "list" }
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ calendarConfig_eventRenderType: val });
                            }}
                        />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__("Calendar Header - Left")}
                        value={attributes.calendarConfig_headerToolbarStart}
                        onChange={(val) => {
                            setAttributes({ calendarConfig_headerToolbarStart: val });
                        }}
                    />
                </PanelRow>                                
                <PanelRow>
                    <TextControl
                        label={__("Calendar Header - Center")}
                        value={attributes.calendarConfig_headerToolbarCenter}
                        onChange={(val) => {
                            setAttributes({ calendarConfig_headerToolbarCenter: val });
                        }}
                    />
                </PanelRow>                                                
                <PanelRow>
                    <TextControl
                        label={__("Calendar Header - Right")}
                        value={attributes.calendarConfig_headerToolbarEnd}
                        onChange={(val) => {
                            setAttributes({ calendarConfig_headerToolbarEnd: val });
                        }}
                    />
                </PanelRow>                                
            </PanelBody>
            )}
            {attributes.layoutStyle !== "calendar" && (

                <PanelBody title="List/Grid Configurations">
                    <PanelRow>
                        <ToggleControl
                            label={__("Show List Title?")}
                            checked={attributes.listConfig_showTitle}
                            onChange={() => {
                                setAttributes({ 
                                    listConfig_showTitle: !attributes.listConfig_showTitle
                                });
                            }}
                        />                
                    </PanelRow>
                    <PanelRow>
                        <SelectControl
                            label={__("How to Display Recurring Events in List")}
                            value={attributes.listConfig_recurringDateHandler}
                            options={
                                [
                                    { label: "Show Event Card Once", value: "showOnce" }, 
                                    { label: "Show Event Card for Each Date", value: "showEachDate" }
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ listConfig_recurringDateHandler: val });
                            }}
                        />
                    </PanelRow>            
                </PanelBody>
            )}
            <PanelBody title="Event Card Content">
                <PanelRow>
                    <ToggleControl
                        label={__("Show Categories?")}
                        checked={attributes.cardConfig_showCategories}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showCategories: !attributes.cardConfig_showCategories
                            });
                        }}
                    />                
                </PanelRow>
                <PanelRow>
                    <ToggleControl
                        label={__("Show Address?")}
                        checked={attributes.cardConfig_showAddress}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showAddress: !attributes.cardConfig_showAddress
                            });
                        }}
                    />                
                </PanelRow>
                {attributes.cardConfig_showAddress && (
                    <PanelRow className='sub-row'>
                        <SelectControl
                            label={__("Address Format")}
                            value={attributes.cardConfig_addressFormat}
                            options={
                                [
                                    { label: "Street", value: "street" }, 
                                    { label: "Street & City", value: "street_city" }, 
                                    { label: "Street, City & State", value: "street_city_state" }, 
                                    { label: "Street, City, State & Zip", value: "street_city_state_zip" }, 
                                    { label: "City & State", value: "city_state" } 
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ cardConfig_addressFormat: val });
                            }}
                        />
                    </PanelRow>
                )}
                <PanelRow>
                    <ToggleControl
                        label={__("Show Date?")}
                        checked={attributes.cardConfig_showDate}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showDate: !attributes.cardConfig_showDate
                            });
                        }}
                    />                
                </PanelRow>
                {attributes.cardConfig_showDate && (
                    <PanelRow className='sub-row'>
                        <SelectControl
                            label={__("Date Style")}
                            value={attributes.cardConfig_dateStyle}
                            options={
                                [
                                    { label: "Start Date", value: "startDate" }, 
                                    { label: "Next Date", value: "nextDate" }, 
                                    { label: "Start Date through End Date", value: "range" } 
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ cardConfig_dateStyle: val });
                            }}
                        />
                    </PanelRow>
                )}
                {attributes.cardConfig_showDate && (
                    <PanelRow className='sub-row'>
                        <SelectControl
                            label={__("Date Format")}
                            value={attributes.cardConfig_dateFormat}
                            options={
                                [
                                    { label: "2024-03-21", value: "Y-M-D" }, 
                                    { label: "03-21", value: "M-D" },
                                    { label: "03/21", value: "M/D" },
                                    { label: "Mar 3", value: "m D" }
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ cardConfig_dateFormat: val });
                            }}
                        />
                    </PanelRow>
                )}                
                <PanelRow>
                    <ToggleControl
                        label={__("Show Time?")}
                        checked={attributes.cardConfig_showTime}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showTime: !attributes.cardConfig_showTime
                            });
                        }}
                    />                
                </PanelRow>
                <PanelRow>
                    <ToggleControl
                        label={__("Describe Reccurence Pattern?")}
                        checked={attributes.cardConfig_showPatternString}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showPatternString: !attributes.cardConfig_showPatternString
                            });
                        }}
                    />                
                </PanelRow>
                <PanelRow>
                    <ToggleControl
                        label={__("Show Upcoming Dates?")}
                        checked={attributes.cardConfig_showUpcomingDates}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showUpcomingDates: !attributes.cardConfig_showUpcomingDates
                            });
                        }}
                    />                
                </PanelRow>
                {attributes.cardConfig_showUpcomingDates && (
                    <PanelRow>
                        <SelectControl
                            label={__("Date Format for Upcoming Dates")}
                            value={attributes.cardConfig_upcomingDateFormat}
                            options={
                                [
                                    { label: "2024-03-21", value: "Y-M-D" }, 
                                    { label: "03-21", value: "M-D" },
                                    { label: "03/21", value: "M/D" },
                                    { label: "Mar 3", value: "m D" }
                                ]
                            }
                            onChange={(val) => {
                                setAttributes({ cardConfig_upcomingDateFormat: val });
                            }}
                        />
                    </PanelRow>
                )}

                <PanelRow>
                    <ToggleControl
                        label={__("Show Thumbnail?")}
                        checked={attributes.cardConfig_showThumbnail}
                        onChange={() => {
                            setAttributes({ 
                                cardConfig_showThumbnail: !attributes.cardConfig_showThumbnail
                            });
                        }}
                    />                
                </PanelRow>
                {attributes.cardConfig_showThumbnail && (
                    <PanelRow>
                        <TextControl
                            label={__("Image Size")}
                            value={attributes.cardConfig_imageSize}
                            onChange={(val) => {
                                setAttributes({ cardConfig_imageSize: val });
                            }}
                        />
                    </PanelRow>                
                )}
            </PanelBody>
            <PanelBody title="Functionality">
                <PanelRow>
                    <SelectControl
                        label={__("Initial Load Date Range")}
                        value={attributes.listConfig_initialLoadDateRange}
                        options={
                            [
                                { label: "1 Month", value: "1_month" }, 
                                { label: "2 Months", value: "2_months" }, 
                                { label: "3 Months", value: "3_months" }, 
                                { label: "1 Week", value: "1_week" }, 
                                { label: "This Weekend", value: "this_weekend" }, 
                                { label: "Today", value: "today" }, 
                                { label: "Custom Length", value: "custom_length" }, 
                                { label: "Custom Range", value: "custom_range" }, 
                            ]
                        }
                        onChange={(val) => {
                            setAttributes({ listConfig_initialLoadDateRange: val });
                        }}
                    />            
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__("Initial Load List Title")}
                        value={attributes.listConfig_initialTitle}
                        onChange={(val) => {
                            setAttributes({ listConfig_initialTitle: val });
                        }}
                    />
                </PanelRow>
                {attributes.listConfig_initialLoadDateRange == "custom_range" && (
                    <div>
                        <label class='mm-gb-label'>Start Date</label>
                        <PanelRow>
                            <DateTimePicker
                                currentDate={ attributes.listConfig_startDate }
                                is12Hour={ true }
                                onChange={(val) => {
                                    setAttributes({ listConfig_startDate: val });
                                }}
                            />
                        </PanelRow>
                        <label class='mm-gb-label'>End Date</label>
                        <PanelRow>
                            <DateTimePicker
                                currentDate={ attributes.listConfig_endDate }
                                is12Hour={ true }
                                onChange={(val) => {
                                    setAttributes({ listConfig_endDate: val });
                                }}
                            />
                        </PanelRow>                    
                    </div>
                )}
                <PanelRow>
                    <ToggleControl
                        label={__("Show Past Events?")}
                        checked={attributes.showPastEvents}
                        onChange={() => {
                            setAttributes({ 
                                showPastEvents: !attributes.showPastEvents
                            });
                        }}
                    />
                </PanelRow>
                {attributes.layoutStyle !== "list" && (
                    <PanelRow>
                        <ToggleControl
                            label={__("Preload Events From Server?")}
                            checked={attributes.preloadEvents}
                            onChange={() => {
                                setAttributes({ 
                                    preloadEvents: !attributes.preloadEvents
                                });
                            }}
                        />
                    </PanelRow>                
                )}
                <PanelRow>
                    <NumberControl
                        isShiftStepEnabled={ true }
                        shiftStep={ 1 }
                        label={__("Max # Events to Preload")}
                        value={attributes.maxNumberOfGridEventsToPreLoad}
                        onChange={(val) => {
                            setAttributes({ maxNumberOfGridEventsToPreLoad: val });
                        }}

                    />                
                </PanelRow>                
            </PanelBody>
            <PanelBody title="Developer Configurations">
                <PanelRow>
                <TextControl
                    label={__("Block Namespace for JS Overrides")}
                    value={attributes.blockConfig_jsOverrideNamespace}
                    onChange={(val) => {
                        setAttributes({ blockConfig_jsOverrideNamespace: val });
                    }}
                />
                </PanelRow>   
                <PanelRow>
                    <ToggleControl
                        label={__("Enable Developer Mode?")}
                        checked={attributes.blockConfig_developerMode}
                        onChange={() => {
                            setAttributes({ 
                                blockConfig_developerMode: !attributes.blockConfig_developerMode
                            });
                        }}
                    />                
                </PanelRow>

            </PanelBody>            
		</div>
	);
};

const edit = withSelect( ( select ) => {
  const data = {};
    // Select all terms for given taxonomy
  data.terms = select('core')
    .getEntityRecords(
        'taxonomy',
        'eventastic_categories',
        { per_page: -1 }
    );
  if(!data.terms || !data.terms.length ){
    data.terms = [];
  }
  return data;    
} )( Wizard )

/*** EXPORTS ****************************************************************/

export default edit;
