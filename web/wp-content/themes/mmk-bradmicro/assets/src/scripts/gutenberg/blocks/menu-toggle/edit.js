import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl, SelectControl } from "@wordpress/components";
import { useState, Fragment } from "@wordpress/element";

const hamburgerTypes = [
	"3dx",
	"3dx-r",
	"3dy",
	"3dy-r",
	"3dxy",
	"3dxy-r",
	"arrow",
	"arrow-r",
	"arrowalt",
	"arrowalt-r",
	"arrowturn",
	"arrowturn-r",
	"boring",
	"collapse",
	"collapse-r",
	"elastic",
	"elastic-r",
	"emphatic",
	"emphatic-r",
	"minus",
	"slider",
	"slider-r",
	"spin",
	"spin-r",
	"spring",
	"spring-r",
	"stand",
	"stand-r",
	"squeeze",
	"vortex",
	"vortex-r",
];

export default function Edit({ attributes, setAttributes }) {
	const { hamburgerType, label, target } = attributes;

	const [isActive, setIsActive] = useState(false);
	const blockProps = useBlockProps();

	const toggleClass = () => {
		setIsActive(!isActive);
	};

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={__("Menu Toggle Settings", "madden-theme")}>
					<SelectControl
						label={__("Hamburger Type", "madden-theme")}
						value={hamburgerType}
						options={hamburgerTypes.map((type) => ({
							label: type,
							value: `hamburger--${type}`,
						}))}
						onChange={(value) => setAttributes({ hamburgerType: value })}
					/>
					<TextControl
						label={__("Label", "madden-theme")}
						value={label}
						onChange={(value) => setAttributes({ label: value })}
					/>
					<TextControl
						label={__("Target", "madden-theme")}
						help={__(
							"Add a target element to add .is-toggled to when active.",
							"madden-theme"
						)}
						value={target}
						onChange={(value) => setAttributes({ target: value })}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<a
					className={`hamburger ${hamburgerType} hamburger--accessible js-hamburger${
						isActive ? " is-active" : ""
					}`}
					type="button"
					onClick={toggleClass}
				>
					<span className="hamburger-box">
						<span className="hamburger-inner"></span>
					</span>
					{label && <span className="hamburger-label">{label}</span>}
				</a>
			</div>
		</Fragment>
	);
}
