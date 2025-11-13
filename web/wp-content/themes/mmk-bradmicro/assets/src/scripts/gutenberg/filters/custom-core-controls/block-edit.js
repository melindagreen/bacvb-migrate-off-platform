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
	__experimentalLinkControl as LinkControl,
	__experimentalSpacingSizesControl as SpacingSizesControl,
} from "@wordpress/block-editor";
import {
	AnglePickerControl,
	BoxControl,
	Flex,
	FlexItem,
	Panel,
	PanelBody,
	Popover,
	PanelRow,
	RangeControl,
	ToggleControl,
	ToolbarButton,
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

											case "justify-content":
												return (
													<JustifyContentControl
														key={`${customization}-${index}`}
														value={attributes.justifyContent}
														onChange={(justifyContent) =>
															setAttributes({ justifyContent })
														}
													/>
												);
										}
									})
								}
							</BlockControls>
							<InspectorControls>
								<Panel key="mm-options-panel">
									<PanelBody title="[MM] Options" key="mm-options">
										{
											// parse through matching customizations and add new inspector controls
											CUSTOMIZE_BLOCKS[name].map((customization, index) => {
												switch (customization) {
													case "content-width-settings":
														return (
															<>
																<PanelRow key="enableMaxWidth">
																	<ToggleControl
																		label={__("Restrict Width?")}
																		help={
																			attributes.enableMaxWidth
																				? __("Yes")
																				: __("No")
																		}
																		checked={!!attributes.enableMaxWidth}
																		onChange={(enableMaxWidth) =>
																			setAttributes({
																				enableMaxWidth: !!enableMaxWidth,
																			})
																		}
																		onLabel={__("Yes")}
																		offLabel={__("No")}
																	/>
																</PanelRow>
																{attributes.enableMaxWidth && (
																	<>
																		<PanelRow key="defaultMaxWidth">
																			<ToggleControl
																				label={__("Use Default Max Width?")}
																				help={__(
																					"Enable this to use the default max width for the website (80rem)"
																				)}
																				checked={!!attributes.defaultMaxWidth}
																				onChange={(defaultMaxWidth) =>
																					setAttributes({
																						defaultMaxWidth: !!defaultMaxWidth,
																					})
																				}
																				onLabel={__("Yes")}
																				offLabel={__("No")}
																			/>
																		</PanelRow>
																		{!attributes.defaultMaxWidth && (
																			<PanelRow key="customMaxWidth">
																				<RangeControl
																					help={__("Value set uses rem units")}
																					label="Custom Max Width"
																					value={attributes.customMaxWidth}
																					onChange={(customMaxWidth) => {
																						setAttributes({ customMaxWidth });
																					}}
																					min={0}
																					max={200}
																				/>
																			</PanelRow>
																		)}
																	</>
																)}
															</>
														);

													case "reverse-order":
														return (
															<PanelRow key="reverseOrder">
																<ToggleControl
																	label="Reverse order on mobile"
																	checked={attributes.reverseOrder}
																	onChange={(reverseOrder) =>
																		setAttributes({ reverseOrder })
																	}
																/>
															</PanelRow>
														);

													case "rotate-element":
														return (
															<PanelRow key="rotateElement">
																<AnglePickerControl
																	label="Rotate"
																	help={__("Rotate the element")}
																	value={attributes.rotateElement}
																	onChange={(rotateElement) => {
																		setAttributes({ rotateElement });
																	}}
																/>
															</PanelRow>
														);

													case "disable-pointer-events":
														return (
															<PanelRow>
																<ToggleControl
																	label="Disable pointer events"
																	checked={attributes.disablePointerEvents}
																	onChange={(disablePointerEvents) =>
																		setAttributes({ disablePointerEvents })
																	}
																/>
															</PanelRow>
														);

													case "responsive-grid-columns":
														return (
															<>
																<PanelRow>
																	<ToggleControl
																		label="Enable responsive grid columns"
																		checked={
																			attributes.enableResponsiveGridCols
																		}
																		onChange={(enableResponsiveGridCols) =>
																			setAttributes({
																				enableResponsiveGridCols,
																			})
																		}
																	/>
																</PanelRow>
																{attributes?.enableResponsiveGridCols && (
																	<>
																		<PanelRow>
																			<NumberControl
																				label={__("Tablet")}
																				value={attributes.tabletGridCols}
																				onChange={(val) => {
																					setAttributes({
																						tabletGridCols: Number(val),
																					});
																				}}
																			/>
																			<NumberControl
																				label={__("Mobile")}
																				value={attributes.mobileGridCols}
																				onChange={(val) => {
																					setAttributes({
																						mobileGridCols: Number(val),
																					});
																				}}
																			/>
																		</PanelRow>
																	</>
																)}
															</>
														);

													case "z-index":
														return (
															<PanelRow key="zIndex">
																<RangeControl
																	help={__(
																		"Controls the visibility level of this element"
																	)}
																	label="Z Index"
																	value={attributes.zIndex}
																	onChange={(zIndex) => {
																		setAttributes({ zIndex });
																	}}
																/>
															</PanelRow>
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

													case "hide-on-mobile":
														return (
															<PanelRow key="hideOnMobile">
																<ToggleControl
																	label="Hide on mobile"
																	checked={attributes.hideOnMobile}
																	onChange={(hideOnMobile) =>
																		setAttributes({ hideOnMobile })
																	}
																/>
															</PanelRow>
														);

													case "responsive-sizes":
														return (
															<Fragment key="enableResponsiveSizesFrag">
																<PanelRow key="enableResponsiveSizes">
																	<ToggleControl
																		label="Enable responsive sizes"
																		help={__("Leave values blank for auto")}
																		checked={attributes.enableResponsiveSizes}
																		onChange={(enableResponsiveSizes) =>
																			setAttributes({ enableResponsiveSizes })
																		}
																	/>
																</PanelRow>
																{attributes?.enableResponsiveSizes && (
																	<Fragment key="responsiveSizesFrag">
																		<PanelRow key="responsiveSizes">
																			<UnitControl
																				key="tabletWidth"
																				label={__("Tablet")}
																				value={attributes.tabletWidth}
																				onChange={(tabletWidth) => {
																					setAttributes({ tabletWidth });
																				}}
																			/>
																			<UnitControl
																				key="mobileWidth"
																				label={__("Mobile")}
																				value={attributes.mobileWidth}
																				onChange={(mobileWidth) => {
																					setAttributes({ mobileWidth });
																				}}
																			/>
																		</PanelRow>
																	</Fragment>
																)}
															</Fragment>
														);

													case "mobile-padding":
														return (
															<Fragment key="enableMobilePaddingFrag">
																<ToggleControl
																	key="enableMobilePadding"
																	label="Add Mobile Padding"
																	checked={attributes.enableMobilePadding}
																	onChange={(enableMobilePadding) =>
																		setAttributes({
																			enableMobilePadding,
																			...(enableMobilePadding
																				? {}
																				: { mobilePadding: {} }), // clear mobilePadding when toggled off
																		})
																	}
																/>
																{attributes.enableMobilePadding && (
																	<SpacingSizesControl
																		key="mobilePadding"
																		label={"Mobile Padding"}
																		onChange={(mobilePadding) =>
																			setAttributes({ mobilePadding })
																		}
																		values={attributes.mobilePadding}
																	/>
																)}
															</Fragment>
														);

													case "absolute-position":
														return (
															<Fragment key="absolutePositionControls">
																<ToggleControl
																	key="enableAbsolutePosition"
																	label="Absolute Position?"
																	checked={attributes.enableAbsolutePosition}
																	onChange={(enableAbsolutePosition) =>
																		setAttributes({ enableAbsolutePosition })
																	}
																/>
																{attributes.enableAbsolutePosition && (
																	<Flex direction="column" gap={2}>
																		<Flex gap={2}>
																			<FlexItem style={{ flex: 1 }}>
																				<UnitControl
																					label="Top"
																					value={
																						attributes.absolutePositions?.top
																					}
																					onChange={(val) =>
																						setAttributes({
																							absolutePositions: {
																								...attributes.absolutePositions,
																								top: val,
																							},
																						})
																					}
																					isResetValueOnUnitChange
																					allowReset
																				/>
																			</FlexItem>
																			<FlexItem style={{ flex: 1 }}>
																				<UnitControl
																					label="Right"
																					value={
																						attributes.absolutePositions?.right
																					}
																					onChange={(val) =>
																						setAttributes({
																							absolutePositions: {
																								...attributes.absolutePositions,
																								right: val,
																							},
																						})
																					}
																					allowReset
																				/>
																			</FlexItem>
																		</Flex>

																		<Flex gap={2}>
																			<FlexItem style={{ flex: 1 }}>
																				<UnitControl
																					label="Bottom"
																					value={
																						attributes.absolutePositions?.bottom
																					}
																					onChange={(val) =>
																						setAttributes({
																							absolutePositions: {
																								...attributes.absolutePositions,
																								bottom: val,
																							},
																						})
																					}
																					allowReset
																				/>
																			</FlexItem>
																			<FlexItem style={{ flex: 1 }}>
																				<UnitControl
																					label="Left"
																					value={
																						attributes.absolutePositions?.left
																					}
																					onChange={(val) =>
																						setAttributes({
																							absolutePositions: {
																								...attributes.absolutePositions,
																								left: val,
																							},
																						})
																					}
																					allowReset
																				/>
																			</FlexItem>
																		</Flex>
																	</Flex>
																)}
															</Fragment>
														);

													case "mobile-font-settings":
														return (
															<Fragment key="mobileFontSettingsControls">
																<PanelRow>
																	<UnitControl
																		label={__("Mobile Font Size")}
																		value={attributes.mobileFontSize}
																		onChange={(mobileFontSize) => {
																			setAttributes({ mobileFontSize });
																		}}
																		isResetValueOnUnitChange={false}
																	/>
																</PanelRow>
																<PanelRow>
																	<NumberControl
																		label={__("Mobile Line Height")}
																		value={attributes.mobileLineHeight}
																		step="0.1"
																		min="0"
																		onChange={(mobileLineHeight) => {
																			setAttributes({ mobileLineHeight });
																		}}
																	/>
																</PanelRow>
																<PanelRow>
																	<UnitControl
																		label={__("Mobile Letter Spacing")}
																		value={attributes.mobileLetterSpacing}
																		onChange={(mobileLetterSpacing) => {
																			setAttributes({ mobileLetterSpacing });
																		}}
																		isResetValueOnUnitChange={false}
																	/>
																</PanelRow>
															</Fragment>
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
