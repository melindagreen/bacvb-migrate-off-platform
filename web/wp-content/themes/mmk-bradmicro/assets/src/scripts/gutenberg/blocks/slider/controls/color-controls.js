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
        let newVal = 'var(--wp--preset--color--'+val+')';
        return newVal;
    }

	const setColorPanel = () => {
		let colorPanelSettings = [];

		colorPanelSettings.push({
			value: formatValue(attributes.backgroundColor),
			onChange: (value) =>
				handleColorChange(value, "backgroundColor"),
			label: __("Card Background Color"),
		});

		colorPanelSettings.push({
			value: formatValue(attributes.textColor),
			onChange: (value) =>
				handleColorChange(value, "textColor"),
			label: __("Card Text Color"),
		});

		if (attributes.enableArrowNavigation) {
			colorPanelSettings.push(
				{
					value: formatValue(attributes.arrowColor),
					onChange: (value) =>
						handleColorChange(value, "arrowColor"),
					label: __("Arrow Color"),
				}
			);
		}
		if (attributes.enablePagination) {
			colorPanelSettings.push(
				{
					value: formatValue(attributes.dotColor),
					onChange: (value) =>
						handleColorChange(value, "dotColor"),
					label: __("Dot Color"),
				},
				{
					value: formatValue(attributes.dotColorActive),
					onChange: (value) =>
						handleColorChange(value, "dotColorActive"),
					label: __("Active Dot Color"),
				}
			);
		}
		if (attributes.enableScrollbar) {
			colorPanelSettings.push({
				value: formatValue(attributes.scrollbarColor),
				onChange: (value) =>
					handleColorChange(value, "scrollbarColor"),
				label: __("Scrollbar Color"),
			});
		}
		return colorPanelSettings;
	};

	return (
        <PanelColorSettings
            title={"Color"}
            colorSettings={setColorPanel()}
			disableCustomColors
        />
	);
};

/*** EXPORTS ****************************************************************/
export default ColorControls;
