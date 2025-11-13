/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { Panel, PanelBody, ToggleControl } from "@wordpress/components";

// Local dependencies

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
  const { attributes, setAttributes } = props;

  return (
    <InspectorControls>
      <Panel>
        <PanelBody title="Options">
          <ToggleControl
            label={__("Open first item on page load?")}
            help={__(attributes.openFirstItem ? "ON" : "OFF")}
            checked={attributes.openFirstItem}
            onChange={() => {
              setAttributes({ openFirstItem: !attributes.openFirstItem });
            }}
          />
          <ToggleControl
            label={__("Limit to one item open at a time?")}
            help={__(attributes.autoCloseOthers ? "ON" : "OFF")}
            checked={attributes.autoCloseOthers}
            onChange={() => {
              setAttributes({ autoCloseOthers: !attributes.autoCloseOthers });
            }}
          />
        </PanelBody>
      </Panel>
    </InspectorControls>
  );
};

export default Inspector;
