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
	__experimentalSpacingSizesControl as SpacingSizesControl
} from "@wordpress/block-editor";
import {
	Panel,
	PanelBody,
	PanelRow,
	RangeControl,
	ToggleControl,
	__experimentalUnitControl as UnitControl
} from "@wordpress/components";

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** FUNCTIONS ****************************************************************/
/**
 * Add custom controls to editor
 */
const withCustomControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { name, attributes, setAttributes } = props;

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
									CUSTOMIZE_BLOCKS[name].map((customization) => {
										switch (customization) {
											case "justify-content":
												return (
													<JustifyContentControl
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
								<Panel>
									<PanelBody title="[MM] Options">
										{
											// parse through matching customizations and add new inspector controls
											CUSTOMIZE_BLOCKS[name].map((customization) => {
												switch (customization) {
													case "content-width-settings":
														return (
															<>
																<PanelRow>
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
																		<PanelRow>
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
																			<PanelRow>
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
														break;

													case "reverse-order":
														return (
															<PanelRow>
																<ToggleControl
																	label="Reverse order on mobile"
																	checked={attributes.reverseOrder}
																	onChange={(reverseOrder) =>
																		setAttributes({ reverseOrder })
																	}
																/>
															</PanelRow>
														);
														break;

													case "z-index":
														return (
															<PanelRow>
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
														break;

													case "center-on-mobile":
														return (
															<PanelRow>
																<ToggleControl
																	label="Center on mobile"
																	checked={attributes.centerOnMobile}
																	onChange={(centerOnMobile) =>
																		setAttributes({ centerOnMobile })
																	}
																/>
															</PanelRow>
														);
														break;

													case "hide-on-mobile":
														return (
															<PanelRow>
																<ToggleControl
																	label="Hide on mobile"
																	checked={attributes.hideOnMobile}
																	onChange={(hideOnMobile) =>
																		setAttributes({ hideOnMobile })
																	}
																/>
															</PanelRow>
														);
														break;

													case "responsive-sizes":
														return (
															<>
																<PanelRow>
																	<ToggleControl
																		label="Enable responsive sizes"
																		help={__("Leave values blank for auto")}
																		checked={attributes.enableResponsiveSizes}
																		onChange={(enableResponsiveSizes) =>
																			setAttributes({enableResponsiveSizes})
																		}
																	/>
																</PanelRow>
																{attributes?.enableResponsiveSizes && (
																	<>
																		<PanelRow>
																			<UnitControl
																				label={__("Tablet")}
																				value={attributes.tabletWidth}
																				onChange={(tabletWidth) => {
																					setAttributes({tabletWidth});
																				}}
																			/>
																			<UnitControl
																				label={__("Mobile")}
																				value={attributes.mobileWidth}
																				onChange={(mobileWidth) => {
																					setAttributes({mobileWidth});
																				}}
																			/>
																		</PanelRow>
																	</>
																)}
															</>
														);
														break;
												
													case "mobile-padding":
														return (
															<>
																<ToggleControl
																	label="Add Mobile Padding"
																	checked={attributes.enableMobilePadding}
																	onChange={(enableMobilePadding) =>
																		setAttributes({ enableMobilePadding,
																			...(enableMobilePadding ? {} : { mobilePadding: {} }) // clear mobilePadding when toggled off
																		})
																	}
																/>
																{attributes.enableMobilePadding && (
																	<SpacingSizesControl
																		label={ "Mobile Padding" }
																		onChange={ mobilePadding => setAttributes({ mobilePadding }) }
																		values={ attributes.mobilePadding }
																	/>
																)}
															</>
														);
														break;
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
