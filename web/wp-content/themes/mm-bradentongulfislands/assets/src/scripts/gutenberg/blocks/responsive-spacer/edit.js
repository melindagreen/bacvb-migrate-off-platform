import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { useEffect } from "@wordpress/element";

const Edit = ({ attributes, setAttributes, clientId}) => {
  const { heightDesktop, heightMobile, unit, spacerId } = attributes;

  const desktopHeight = `${heightDesktop}${unit}`;
  const mobileHeight = `${heightMobile}${unit}`;

  //set unique id for each block
  const uniqueId = `custom-spacer-block-${clientId}`;


  const blockProps = useBlockProps( {
    className: `custom-spacer-block ${spacerId}`,
    style: {
      height: desktopHeight,
    }
  } );

  useEffect( () => {
    setAttributes({ spacerId: uniqueId });
  }, [] );

  return (
    <div {...blockProps}>
      <InspectorControls>
        <PanelBody title="Spacer Settings">
          <RangeControl
            label="Height (Desktop)"
            value={heightDesktop}
            min={0}
            step={0.25}
            max={10}
            onChange={(value) => setAttributes({ heightDesktop: value })}
          />
          <RangeControl
            label="Height (Mobile)"
            value={heightMobile}
            min={0}
            step={0.25}
            max={10}
            onChange={(value) => setAttributes({ heightMobile: value })}
          />
          <SelectControl
            label="Unit"
            value={unit}
            options={[
              { value: 'rem', label: 'rem' },
              { value: 'px', label: 'px' },
              { value: 'vh', label: 'vh' },
            ]}
            onChange={(value) => setAttributes({ unit: value })}
          />
        </PanelBody>
      </InspectorControls>
      <style>
        {`@media (max-width: 776px) {
          .${spacerId} {
            height: ${mobileHeight} !important;
          }
        }`}
      </style>
    </div>
  );
};

export default Edit;