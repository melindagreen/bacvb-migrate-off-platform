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
		searchIconColor,
		searchIconActiveColor,
		setSearchIconColor,
		setSearchIconActiveColor,
	} = props;

	const setColorPanel = () => {
		let colorPanelSettings = [];
		
		colorPanelSettings.push({
			value: searchIconColor.color,
			onChange: setSearchIconColor,
			label: __("Search Color"),
		});
		
		colorPanelSettings.push({
			value: searchIconActiveColor.color,
			onChange: setSearchIconActiveColor,
			label: __("Search Color - Active"),
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
	'searchIconColor': 'search-icon-color',
	'searchIconActiveColor': 'search-icon-active-color'
})(ColorControls);
