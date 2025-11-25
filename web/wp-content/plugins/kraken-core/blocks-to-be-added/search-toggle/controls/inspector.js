/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl, ToggleControl, SelectControl } from "@wordpress/components";

// Local Dependencies
import ColorControls from "./color-controls";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	const { attributes, setAttributes } = props;

	return (
		<>
			<InspectorControls group="settings">
				<PanelBody title={__("Search Toggle Settings", "madden-theme")}>
					<SelectControl
						label={__("Mobile Breakpoint", "madden-theme")}
						help={__("Toggle will be hidden at selected breakpoint", "madden-theme")}
						value={attributes.mobileBreakpoint}
						options={[
							{ label: __("Small", "madden-theme"), value: "sm" },
							{ label: __("Medium", "madden-theme"), value: "md" },
							{ label: __("Large", "madden-theme"), value: "lg" },
							{ label: __("X-Large", "madden-theme"), value: "xl" },
							{ label: __("Disabled", "madden-theme"), value: "disabled" },
						]}
						onChange={(value) => setAttributes({ mobileBreakpoint: value })}
					/>
					<TextControl
						label={__("Search Label", "madden-theme")}
						value={attributes.searchLabel}
						onChange={(value) => setAttributes({ searchLabel: value })}
					/>
					<TextControl
						label={__("Search Target", "madden-theme")}
						help={__(
							"Add a target element to add .is-toggled to when active.",
							"madden-theme"
						)}
						value={attributes.searchTarget}
						onChange={(value) => setAttributes({ searchTarget: value })}
					/>
				</PanelBody>
			</InspectorControls>
			<InspectorControls group="styles">
				<ColorControls {...props} />
			</InspectorControls>
		</>
	);
};

export default Inspector;
