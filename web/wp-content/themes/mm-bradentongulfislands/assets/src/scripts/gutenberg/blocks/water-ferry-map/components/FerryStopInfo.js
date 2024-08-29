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

    return (
        <PanelBody title="City Info" initialOpen={true}>
            <Media mediaId={localAttributes.amiMediaId} placeholderText={"Ami Image"} setAttributes={setAttributes} mediaAttribute="amiMediaId" />
            <TextareaControl
                label="Ami Description"
                onChange={(value) => updateAttributes({ amiDescription: value })}
                value={localAttributes.amiDescription}
            />
            <TextControl
            label="Ami Url"
            value={localAttributes.amiUrl}
            onChange={(value) => updateAttributes({amiUrl : value })}
            />

            <Media mediaId={localAttributes.bridgeStreetMediaId} placeholderText={"Bridge Street Image"} setAttributes={setAttributes} mediaAttribute="bridgeStreetMediaId" />
            <TextareaControl
                label="Bridge Street Description"
                onChange={(value) => updateAttributes({ bridgeStreetDescription: value })}
                value={localAttributes.bridgeStreetDescription}
            />
            <TextControl
            label="Bridge Street Url"
            value={localAttributes.bridgeStreetUrl}
            onChange={(value) => updateAttributes({bridgeStreetUrl : value })}
            />

            <Media mediaId={localAttributes.bradentonRiverwalkMediaId} placeholderText={"Bradenton Riverwalk Image"} setAttributes={setAttributes} mediaAttribute="bradentonRiverwalkId" />
            <TextareaControl
                label="Bradenton Riverwalk Description"
                onChange={(value) => updateAttributes({ bradentonRiverwalkDescription: value })}
                value={localAttributes.bradentonRiverwalkDescription}
            />
            <TextControl
            label="Bradenton Riverwalk Url"
            value={localAttributes.bradentonRiverwalkUrl}
            onChange={(value) => updateAttributes({bradentonRiverwalkUrl : value })}
            />
        </PanelBody>
    );
};

export default FerryStop;