/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";

// Local Dependencies
import ContentOptions from "./content-options";
import SliderOptions from "./slider-options";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	return (
		<>
			<InspectorControls group="settings">
				<ContentOptions {...props} />
			</InspectorControls>
			<InspectorControls group="styles">
				<SliderOptions {...props} />
			</InspectorControls>
		</>
	);
};

export default Inspector;
