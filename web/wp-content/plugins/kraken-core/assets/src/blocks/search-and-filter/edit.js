/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import ServerSideRender from "@wordpress/server-side-render";
import { useBlockProps } from "@wordpress/block-editor";
import { useEffect } from "@wordpress/element";

// Controls - add block/inspector controls here
import Controls from "./controls";

// Helper functions
import { cleanupDynamicAttributes } from "../../filters/helpers";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const Editor = (props) => {
  const blockProps = useBlockProps();
  return (
    <div {...blockProps}>
      <ServerSideRender block={props.name} {...props} />
    </div>
  );
};

const edit = (props) => {
  const { attributes, setAttributes } = props;

  // Clean up old dynamic attributes on component mount and when settings change
  useEffect(() => {
    // Get current available settings from theme configuration
    const currentSettings = KrakenThemeSettings?.blockData?.cardAttributes || {};

    // Clean up customAdditionalContent if it exists
    if (
      attributes.customAdditionalContent &&
      Object.keys(attributes.customAdditionalContent).length > 0
    ) {
      const cleanedAttributes = cleanupDynamicAttributes(
        currentSettings,
        attributes,
        "customAdditionalContent",
      );

      // Only update if cleanup actually removed something
      if (
        Object.keys(cleanedAttributes).length !==
        Object.keys(attributes.customAdditionalContent).length
      ) {
        setAttributes({
          customAdditionalContent: cleanedAttributes,
        });
      }
    }
  }, []); // Only run on mount

  return (
    <>
      <Controls {...props} />
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
