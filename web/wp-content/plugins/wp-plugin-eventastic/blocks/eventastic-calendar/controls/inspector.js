/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { Panel, PanelBody } from "@wordpress/components";

// Local Dependencies
// Controls - add block/inspector controls here
import Wizard from "./wizard";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	return (
		<InspectorControls>
			<Wizard {...props} />
		</InspectorControls>
	);
};

export default Inspector;
