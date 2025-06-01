import React, { useState, useEffect } from 'react';
import { PanelBody, TextareaControl, TextControl } from '@wordpress/components';
import Media from './Media'; // Adjust the path as necessary

const FerryStop = ({ attributes, setAttributes }) => {

    const [localAttributes, setLocalAttributes] = useState(attributes);

    // Update local state when props change
    useEffect(() => {
        setLocalAttributes(attributes);
    }, [attributes]);

    
    const updateAttributes = (newAttributes) => {
        const updatedAttributes = { ...localAttributes, ...newAttributes };
        setLocalAttributes(updatedAttributes);
        setAttributes(updatedAttributes);
    };

    const ferryStops = {
        BRIDGE_STREET_FERRY_STOP: 'Bridge Street Ferry Stop',
        LAKEWOOD_RANCH: 'Lakewood Ranch',
        LAKE_MANATEE_STATE_PARK: 'Lake Manatee State Park',
        ANNA_MARIE_ISLAND: 'Anna Maria Island',
        LONGBOAT_KEY: 'Longboat Key',
        ROBINSON_PRESERVE: 'Robinson Preserve',
        CORTEZ: 'Cortez',
        DESOTO: 'DeSoto National Memorial',
        BISHOP_MUSEUM: 'Bishop Museum',
        BRADENTON_RIVER_WALK: 'Bradenton Riverwalk',
        ANNA_MARIE_CITY_PIER: 'Anna Maria City Pier',
        GULF_ISLANDS_FERRY: 'Gulf Islands Ferry',
        HERRIG_CENTER: 'HERRIG Center',
        MANATEE_PERFORMING_ARTS: 'Manatee Performing Arts',
        VILLAGE_OF_THE_ARTS: 'Village of the Arts',
      };

    return (
        <PanelBody title="City Info" initialOpen={true}>
        {Object.entries(ferryStops).map(([key, label]) => (
          <div key={key} style={{ marginBottom: '2rem' }}>
            <TextareaControl
              label={`${label} Description`}
              value={localAttributes[`${key}Description`] || ''}
              onChange={(value) =>
                updateAttributes({ [`${key}Description`]: value })
              }
            />
            <TextControl
              label={`${label} Url`}
              value={localAttributes[`${key}Url`] || ''}
              onChange={(value) =>
                updateAttributes({ [`${key}Url`]: value })
              }
            />
            <Media
              mediaId={localAttributes[`${key}MediaId`] || 0}
              placeholderText={`${label} Image`}
              setAttributes={setAttributes}
              mediaAttribute={`${key}MediaId`}
            />
          </div>
        ))}
      </PanelBody>
    );
};

export default FerryStop;