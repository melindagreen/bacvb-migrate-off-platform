import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import {
	PanelBody,
	__experimentalUnitControl as UnitControl,
} from "@wordpress/components";
import { useState } from "@wordpress/element";
import { useEffect } from "@wordpress/element";
const { select } = wp.data;

export default function Edit(props) {
	const { attributes, setAttributes, clientId } = props;
	const { heightDesktop, heightTablet, heightMobile } = attributes;

	const selectedBlock = select("core/block-editor").isBlockSelected(
		clientId,
		true
	);
	const [focusedControl, setFocusedControl] = useState("desktop");
	const [previewHeight, setPreviewHeight] = useState(heightDesktop);

	const blockProps = useBlockProps({
		style: {
			"--responsive-spacer--desktop": previewHeight,
		},
	});

	useEffect(() => {
		if (focusedControl === "mobile") {
			setPreviewHeight(
				heightMobile
					? heightMobile
					: heightTablet
					? heightTablet
					: heightDesktop
			);
		} else if (focusedControl === "tablet") {
			setPreviewHeight(heightTablet ? heightTablet : heightDesktop);
		} else {
			setPreviewHeight(heightDesktop);
		}
	}, [focusedControl]);

	return (
		<>
			{/* Inspector Controls */}
			<InspectorControls>
				<PanelBody title={__("Responsive Spacer Settings", "mtphr-theme")}>
					<UnitControl
						label={__("Desktop Height", "mtphr-theme")}
						value={heightDesktop}
						min={0}
						step={0.25}
						onChange={(value) => {
							setAttributes({ heightDesktop: value });
							setPreviewHeight(value);
						}}
						onFocus={() => setFocusedControl("desktop")}
						onBlur={() => setFocusedControl("desktop")}
					/>
					<UnitControl
						label={__("Tablet Height", "mtphr-theme")}
						value={heightTablet}
						min={0}
						step={0.25}
						onChange={(value) => {
							setAttributes({ heightTablet: value });
							setPreviewHeight(value);
						}}
						onFocus={() => setFocusedControl("tablet")}
						onBlur={() => setFocusedControl("desktop")}
					/>
					<UnitControl
						label={__("Mobile Height", "mtphr-theme")}
						value={heightMobile}
						min={0}
						step={0.25}
						onChange={(value) => {
							setAttributes({ heightMobile: value });
							setPreviewHeight(value);
						}}
						onFocus={() => setFocusedControl("mobile")}
						onBlur={() => setFocusedControl("desktop")}
					/>
				</PanelBody>
			</InspectorControls>

			{/* Block Rendering */}
			<div {...blockProps}>
				{selectedBlock && (
					<span className="preview-info">
						{focusedControl} - {previewHeight}
					</span>
				)}
			</div>
		</>
	);
}
