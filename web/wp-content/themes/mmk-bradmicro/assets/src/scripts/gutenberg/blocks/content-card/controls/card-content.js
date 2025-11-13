/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { __experimentalNumberControl as NumberControl, TextControl, TextareaControl, ToggleControl } from "@wordpress/components";
import { THEME_PREFIX } from "../../../inc/constants";
const { useSelect } = wp.data;

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const CardContent = (props) => {
  const { attributes, setAttributes } = props;

	const blockName = useSelect((select) =>
		select("core/block-editor").getBlockName(props.clientId)
	);

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
        {attributes.postType.includes("event") &&
          <>
            <ToggleControl
              label={__("Event Date")}
              checked={attributes.displayEventDate}
              onChange={() => {
                setAttributes({ displayEventDate: !attributes.displayEventDate });
              }}
            />
            <ToggleControl
              label={__("Event Time")}
              checked={attributes.displayEventTime}
              onChange={() => {
                setAttributes({ displayEventTime: !attributes.displayEventTime });
              }}
            />
          </>
        }
        <ToggleControl
          label={__("Post Excerpt")}
          checked={attributes.displayExcerpt}
          onChange={() => {
            setAttributes({ 
              displayExcerpt: !attributes.displayExcerpt,
              displayCustomExcerpt: false
            });
          }}
        />
        {attributes.displayExcerpt &&							
          <NumberControl
            label={__("Excerpt Word Count")}
            value={attributes.excerptLength}
            min={0}
            onChange={(val) => {
              setAttributes({ excerptLength: Number(val) });
            }}
          />
        }
				{attributes.postType !== "queried_post" && blockName === THEME_PREFIX + "/content-card" && (
          <>
          <ToggleControl
            label={__("Custom Excerpt")}
            checked={attributes.displayCustomExcerpt}
            onChange={() => {
              setAttributes({ 
                displayExcerpt: false,
                displayCustomExcerpt: !attributes.displayCustomExcerpt 
              });
            }}
          />
          {attributes.displayCustomExcerpt &&
            <TextareaControl
              label={__('Customize Excerpt Text')}
              value={attributes.customExcerpt}
              onChange={(val) => {
                setAttributes({ customExcerpt: val });
              }}
            />
          }
          </>
        )}
        <hr/>
        <ToggleControl
          label={__("Read More Text")}
          checked={attributes.displayReadMore}
          onChange={() => {
            setAttributes({ displayReadMore: !attributes.displayReadMore });
          }}
        />
        {attributes.displayReadMore && 
          <TextControl
            label={__('Customize Read More Text')}
            value={attributes.readMoreText}
            onChange={(val) => {
              setAttributes({ readMoreText: val });
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