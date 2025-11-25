/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { __experimentalNumberControl as NumberControl, TextControl, TextareaControl, ToggleControl } from "@wordpress/components";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const CardContent = (props) => {
  const { attributes, setAttributes } = props;

	return (
		<>
      <ToggleControl
        label={__("Display Additional Content?")}
        checked={attributes.displayAdditionalContent}
        onChange={() => {
          setAttributes({ displayAdditionalContent: !attributes.displayAdditionalContent });
        }}
      />
      {attributes.displayAdditionalContent &&
      <>
        {attributes.postType === 'listing' &&
          <ToggleControl
            label={__("Listing Categories")}
            checked={attributes.displayCategories}
            onChange={() => {
              setAttributes({ 
                displayCategories: !attributes.displayCategories
              });
            }}
          />
        }
        <ToggleControl
          label={__("Address")}
          checked={attributes.displayAddress}
          onChange={() => {
            setAttributes({ 
              displayAddress: !attributes.displayAddress
            });
          }}
        />
        <ToggleControl
          label={__("Phone Number")}
          checked={attributes.displayPhoneNumber}
          onChange={() => {
            setAttributes({ 
              displayPhoneNumber: !attributes.displayPhoneNumber
            });
          }}
        />
        <ToggleControl
          label={__("Description")}
          checked={attributes.displayDescription}
          onChange={() => {
            setAttributes({ 
              displayDescription: !attributes.displayDescription
            });
          }}
        />
        <hr/>
        <ToggleControl
          label={__("Use Weddings Info")}
          checked={attributes.displayWeddingInfo}
          onChange={() => {
            setAttributes({ 
              displayWeddingInfo: !attributes.displayWeddingInfo,
              displayMeetingInfo: false
            });
          }}
        />
        <ToggleControl
          label={__("Use Meetings/Events Info")}
          checked={attributes.displayMeetingInfo}
          onChange={() => {
            setAttributes({ 
              displayMeetingInfo: !attributes.displayMeetingInfo,
              displayWeddingInfo: false
            });
          }}
        />
        <hr/>
        <ToggleControl
          label={__("Skynav Link")}
          checked={attributes.displaySkynavLink}
          onChange={() => {
            setAttributes({ 
              displaySkynavLink: !attributes.displaySkynavLink
            });
          }}
        />
        <ToggleControl
          label={__("Internal Link")}
          checked={attributes.displayInternalLink}
          onChange={() => {
            setAttributes({ 
              displayInternalLink: !attributes.displayInternalLink,
              displayExternalLink: false
            });
          }}
        />
        {attributes.displayInternalLink && 
          <TextControl
            label={__('Customize Link Text')}
            value={attributes.internalLinkText}
            onChange={(val) => {
              setAttributes({ internalLinkText: val });
            }}
          />
        }
        <ToggleControl
          label={__("External Link")}
          checked={attributes.displayExternalLink}
          onChange={() => {
            setAttributes({ 
              displayExternalLink: !attributes.displayExternalLink,
              displayInternalLink: false
            });
          }}
        />
        {attributes.displayExternalLink && 
          <TextControl
            label={__('Customize Link Text')}
            value={attributes.externalLinkText}
            onChange={(val) => {
              setAttributes({ externalLinkText: val });
            }}
          />
        }
      </>
      }
    </>
  )
};

/*** EXPORTS ****************************************************************/

export default CardContent;