/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import {
	Panel,
	PanelBody,
	SelectControl,
	ToggleControl,
	__experimentalUnitControl as UnitControl,
} from "@wordpress/components";

// Local Dependencies
import ColorControls from "./color-controls";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	const { attributes, setAttributes } = props;
	return (
		<>
			<InspectorControls group="styles">
				<ColorControls {...props} />
			</InspectorControls>
			<InspectorControls group="settings">
				<Panel>
					<PanelBody title="Settings">
						{/* add other border options here & in render.php as needed */}
						<SelectControl
							label={__("Border Style")}
							value={attributes.borderStyle}
							options={[
								{ label: "None", value: "" },
								{ label: "Torn Border Top", value: "torn-border-top" },
								{ label: "Torn Border Bottom", value: "torn-border-bottom" },
								{ label: "Paper Tear Top", value: "paper-tear-top" },
								{ label: "Paper Tear Bottom", value: "paper-tear-bottom" },
								{ label: "Wave Bottom", value: "wave-bottom" },
							]}
							onChange={(val) => {
								setAttributes({ borderStyle: val });
							}}
						/>
						<ToggleControl
							label={__("Flip Horizontal")}
							checked={attributes.flipped}
							onChange={() => {
								setAttributes({ flipped: !attributes.flipped });
							}}
						/>
						<ToggleControl
							label={__("Flip Vertical")}
							checked={attributes.flippedVertical}
							onChange={() => {
								setAttributes({ flippedVertical: !attributes.flippedVertical });
							}}
						/>
						<UnitControl
							label={__("Minimum Width")}
							help={__("Set a minimum width of the svg")}
							value={attributes.minWidth}
							min={1}
							onChange={(val) => {
								setAttributes({ minWidth: val });
							}}
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
};

export default Inspector;
