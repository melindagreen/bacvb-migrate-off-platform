/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	PanelColorSettings,
	withColors
} from '@wordpress/block-editor';

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const ColorControls = (props) => {
	const {
		primaryColor,
		secondaryColor,
		tertiaryColor,
		setPrimaryColor,
		setSecondaryColor,
		setTertiaryColor
	} = props;

	const setColorPanel = () => {
		let colorPanelSettings = [];
		
		colorPanelSettings.push({
			value: primaryColor.color,
			onChange: setPrimaryColor,
			label: __("Primary"),
		});

		colorPanelSettings.push({
			value: secondaryColor.color,
			onChange: setSecondaryColor,
			label: __("Secondary"),
		});

		colorPanelSettings.push({
			value: tertiaryColor.color,
			onChange: setTertiaryColor,
			label: __("Tertiary"),
		});

		return colorPanelSettings;
	};

	return (
		<PanelColorSettings 
			__experimentalIsRenderedInSidebar
			title={"SVG Colors"} 
			colorSettings={setColorPanel()}
			className={`madden-theme-color-panel`}
		/>
	);
};

/*** EXPORTS ****************************************************************/
export default withColors({
	'primaryColor': 'primary-color',
	'secondaryColor': 'secondary-color',
	'tertiaryColor': 'tertiary-color'
})(ColorControls);
