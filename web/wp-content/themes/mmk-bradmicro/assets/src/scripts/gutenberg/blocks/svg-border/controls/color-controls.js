/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	PanelColorSettings,
	getColorObjectByColorValue,
} from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const ColorControls = (props) => {
	const { attributes, setAttributes } = props;

	const handleColorChange = (color, key) => {
		let colorName = "";
		if (color) {
			const settings = wp.data.select("core/editor").getEditorSettings();
			const colorObject = getColorObjectByColorValue(settings.colors, color);

			if (colorObject) {
				colorName = colorObject.slug;
			}
		}

		setAttributes({ [key]: colorName });
	};

	const formatValue = (val) => {
		let newVal = "var(--wp--preset--color--" + val + ")";
		return newVal;
	};

	const setColorPanel = () => {
		let colorPanelSettings = [];

		// colorPanelSettings.push({
		// 	value: formatValue(attributes.backgroundColor),
		// 	onChange: (value) => handleColorChange(value, "backgroundColor"),
		// 	label: __("Background Color"),
		// });

		colorPanelSettings.push({
			value: formatValue(attributes.secondaryColor),
			onChange: (value) => handleColorChange(value, "secondaryColor"),
			label: __("Secondary Color"),
		});

		return colorPanelSettings;
	};

	return (
		<PanelColorSettings
			title={"Additional Colors"}
			colorSettings={setColorPanel()}
			disableCustomColors={true}
		/>
	);
};

/*** EXPORTS ****************************************************************/
export default ColorControls;
