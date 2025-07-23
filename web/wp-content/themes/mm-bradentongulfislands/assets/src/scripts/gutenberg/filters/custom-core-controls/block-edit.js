/*
This file adds:
- custom inspector(sidebar) controls
- custom toolbar controls
*/

/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { createHigherOrderComponent } from "@wordpress/compose";
import {
	InspectorControls,
	BlockControls,
	JustifyContentControl,
	MediaUpload,
	MediaUploadCheck,
	__experimentalSpacingSizesControl as SpacingSizesControl,
	__experimentalLinkControl as LinkControl,
} from "@wordpress/block-editor";
import {
	Panel,
	PanelBody,
	PanelRow,
	Popover,
	TextControl,
	ToggleControl,
	ToolbarButton,
	SelectControl,
	__experimentalNumberControl as NumberControl,
	__experimentalUnitControl as UnitControl,
} from "@wordpress/components";
import { Fragment, useState } from "@wordpress/element";
import { link, linkOff } from "@wordpress/icons";

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** FUNCTIONS ****************************************************************/
/**
 * Add custom controls to editor
 */
const withCustomControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { name, attributes, setAttributes } = props;
		const [popoverOpen, setPopoverOpen] = useState(false);

		// check for matching customizations
		if (
			typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
			Array.isArray(CUSTOMIZE_BLOCKS[name])
		) {
			// add wrapper to style within editor, otherwise styles aren't visible in editor
			return (
				<>
					<BlockEdit {...props} />
					{/*only load these controls if the block is selected */}
					{props.isSelected && (
						<>
							<BlockControls group="block">
								{
									// parse through matching customizations and add new toolbar controls
									CUSTOMIZE_BLOCKS[name].map((customization, index) => {
										switch (customization) {
											case "justify-content":
												return (
													<JustifyContentControl
														key="justifyContent"
														value={attributes.justifyContent}
														onChange={(justifyContent) =>
															setAttributes({ justifyContent })
														}
													/>
												);
											case "wraparound-link":
												return (
													<Fragment key={index}>
														<ToolbarButton
															icon={
																attributes.wraparoundLink &&
																Object.keys(attributes.wraparoundLink).length >
																	0
																	? linkOff
																	: link
															}
															label="Wraparound Link"
															onClick={() => setPopoverOpen(!popoverOpen)}
														/>
														{popoverOpen && (
															<Popover>
																{CUSTOMIZE_BLOCKS[name].map(
																	(customization, subIndex) => {
																		switch (customization) {
																			case "wraparound-link":
																				return (
																					<LinkControl
																						key={subIndex}
																						value={attributes.wraparoundLink}
																						onChange={(wraparoundLink) => {
																							setAttributes({
																								wraparoundLink,
																							});
																						}}
																						onRemove={() => {
																							setAttributes({
																								wraparoundLink: false,
																							});
																						}}
																					/>
																				);
																		}
																	}
																)}
															</Popover>
														)}
													</Fragment>
												);
										}
									})
								}
							</BlockControls>
							<InspectorControls>
								<Panel key="mmOptions">
									<PanelBody title="[MM] Options">
										{
											// parse through matching customizations and add new inspector controls
											CUSTOMIZE_BLOCKS[name].map((customization, index) => {
												switch (customization) {
													case "lightbox-data":
														return (
															<PanelBody key="lightboxData">
																<TextControl
																	key="lbTitle"
																	label="Lightbox Title"
																	onChange={(lbTitle) =>
																		setAttributes({ lbTitle })
																	}
																	value={attributes.lbTitle}
																/>
																<TextControl
																	key="lbDescription"
																	label="Lightbox Description"
																	onChange={(lbDescription) =>
																		setAttributes({ lbDescription })
																	}
																	value={attributes.lbDescription}
																/>
																<MediaUploadCheck>
																	<MediaUpload
																		title={"Choose Images"}
																		allowedTypes={"image"}
																		gallery
																		multiple="add"
																		onSelect={(images) =>
																			setAttributes({
																				lbImageIds: images.map(
																					(image) => image.id
																				),
																				lbImageUrls: images.map(
																					(image) => image.url
																				),
																				lbImageAlts: images.map(
																					(image) => image.alt
																				),
																			})
																		}
																		value={attributes.lbImageIds}
																		render={({ open }) => (
																			<Button
																				onClick={open}
																				icon="format-gallery"
																			>
																				{"Choose Images"}
																			</Button>
																		)}
																	/>
																</MediaUploadCheck>
															</PanelBody>
														);
													case "hide-on-breakpoints":
														return (
															<PanelBody
																key="hideOnBreakpoints"
																title="Hide on breakpoints"
																initialOpen={false}
															>
																<PanelRow key="hideOnMobile">
																	<ToggleControl
																		label={__("Hide on mobile?")}
																		checked={attributes.hideOnMobile}
																		onChange={(hideOnMobile) =>
																			setAttributes({ hideOnMobile })
																		}
																	/>
																</PanelRow>

																<PanelRow key="hideOnDesktop">
																	<ToggleControl
																		label={__("Hide on desktop?")}
																		checked={attributes.hideOnDesktop}
																		onChange={(hideOnDesktop) =>
																			setAttributes({ hideOnDesktop })
																		}
																	/>
																</PanelRow>
															</PanelBody>
														);

													case "center-on-mobile":
														return (
															<PanelRow key="centerOnMobile">
																<ToggleControl
																	label="Center on mobile"
																	checked={attributes.centerOnMobile}
																	onChange={(centerOnMobile) =>
																		setAttributes({ centerOnMobile })
																	}
																/>
															</PanelRow>
														);

													case "reverse-mobile":
														return (
															<PanelBody key="reverseMobile">
																<ToggleControl
																	label="Reverse on Mobile"
																	help={
																		attributes.reverseMobile
																			? "Columns are reversed on mobile"
																			: "Columns are not reversed on mobile"
																	}
																	checked={attributes.reverseMobile}
																	onChange={(reverseMobile) => {
																		setAttributes({ reverseMobile });
																	}}
																/>
															</PanelBody>
														);

													case "overlap":
														return (
															<PanelBody key="overlap">
																<NumberControl
																	onChange={(overlap) => {
																		overlap = parseInt(overlap);
																		setAttributes({ overlap });
																	}}
																	isDragEnabled
																	isShiftStepEnabled
																	label={"Overlap"}
																	max={100}
																	min={-100}
																	shiftStep={1}
																	step={1}
																	value={attributes.overlap}
																/>
															</PanelBody>
														);
													case "layer":
														return (
															<PanelBody key="layer">
																<SelectControl
																	label="Layer"
																	value={attributes.layer}
																	options={[
																		{ label: "Middle", value: "middle" },
																		{ label: "Top", value: "top" },
																		{ label: "Bottom", value: "bottom" },
																	]}
																	onChange={(layer) => {
																		setAttributes({ layer });
																	}}
																/>
															</PanelBody>
														);
													case "photo-credit":
														return (
															<PanelBody key="photoCredit">
																<ToggleControl
																	label="Photo Credit"
																	help={
																		attributes.photoCredit
																			? "Photo Credit is enabled"
																			: "Photo Credit is disabled"
																	}
																	checked={attributes.photoCredit}
																	onChange={(photoCredit) => {
																		setAttributes({ photoCredit });
																	}}
																/>
															</PanelBody>
														);
												}
											})
										}
									</PanelBody>
								</Panel>
							</InspectorControls>
						</>
					)}
				</>
			);
		}

		return <BlockEdit {...props} />;
	};
});

/*** EXPORTS ***************************************************************/

export default {
	name: "customBlockEdit",
	hook: "editor.BlockEdit",
	action: withCustomControls,
};
