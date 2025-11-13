/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import {} from "@wordpress/components";
import { PanelColorSettings } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	const { textColor, setTextColor } = props;

	return (
		<InspectorControls>
			<PanelColorSettings
				title={__("Color settings", "madden-theme")}
				colorSettings={[
					{
						value: textColor.color,
						onChange: setTextColor,
						label: __("Title color", "madden-theme"),
					},
				]}
			/>
		</InspectorControls>
	);
};

export default Inspector;
