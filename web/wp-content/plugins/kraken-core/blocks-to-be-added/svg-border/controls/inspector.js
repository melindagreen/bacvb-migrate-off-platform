/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import {
	Panel,
	PanelBody,
	SelectControl,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
} from "@wordpress/components";

// Local Dependencies
import ColorControls from "./color-controls";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	const { attributes, setAttributes } = props;
	return (
		<>
			<InspectorControls group="settings">
				<Panel>
					<PanelBody title="Settings">
						{/* add other border options here & in render.php as needed */}
						<SelectControl
							label={__("Border Style")}
							value={attributes.borderStyle}
							options={[
								{ label: "Mountain Range", value: "mountain-range" },
								{ label: "Small Waves", value: "small-waves" }
							]}
							onChange={(val) => {
								setAttributes({ borderStyle: val });
							}}
						/>
						<ToggleControl
							label={__("Flip Y Axis")}
							checked={attributes.flipY}
							onChange={() => {
								setAttributes({
									flipY: !attributes.flipY,
								});
							}}
						/>
						<ToggleControl
							label={__("Flip X Axis")}
							checked={attributes.flipX}
							onChange={() => {
								setAttributes({
									flipX: !attributes.flipX,
								});
							}}
						/>
						<NumberControl
							label={__("Z Index")}
							value={attributes.positionZIndex}
							onChange={(val) => {
								setAttributes({ positionZIndex: Number(val) });
							}}
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>
			<InspectorControls group="color">
				<ColorControls {...props} />
			</InspectorControls>
		</>
	);
};

export default Inspector;
