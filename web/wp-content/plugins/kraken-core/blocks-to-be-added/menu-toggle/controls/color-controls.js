/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	PanelColorSettings,
	withColors
} from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const ColorControls = (props) => {
	const {
		menuToggleColor,
		menuToggleActiveColor,
		setMenuToggleColor,
		setMenuToggleActiveColor,
	} = props;

	const setColorPanel = () => {
		let colorPanelSettings = [];
		
		colorPanelSettings.push({
			value: menuToggleColor.color,
			onChange: setMenuToggleColor,
			label: __("Menu Color"),
		});
		
		colorPanelSettings.push({
			value: menuToggleActiveColor.color,
			onChange: setMenuToggleActiveColor,
			label: __("Menu Color - Active"),
		});

		return colorPanelSettings;
	};

	return (
		<PanelColorSettings 
			__experimentalIsRenderedInSidebar
			title={"Colors"} 
			colorSettings={setColorPanel()}
			className={`madden-theme-color-panel`}
		/>
	);
};

/*** EXPORTS ****************************************************************/
export default withColors({
	'menuToggleColor': 'menu-toggle-color',
	'menuToggleActiveColor': 'menu-toggle-active-color'
})(ColorControls);
