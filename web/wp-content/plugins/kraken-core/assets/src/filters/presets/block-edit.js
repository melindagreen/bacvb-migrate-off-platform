/*
Documentation: https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#editor-blockedit
- This file is for adding the inspector and toolbar controls in the editor to manage the new filter settings.
- console.log can be used in the fields onChange function and will print any time the setting has changed.
*/

/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { createHigherOrderComponent } from "@wordpress/compose";
import {
  InspectorControls,
  BlockControls,
  JustifyContentControl,
  __experimentalSpacingSizesControl as SpacingSizesControl,
} from "@wordpress/block-editor";
import {
  AnglePickerControl,
  Flex,
  FlexItem,
  Panel,
  PanelBody,
  PanelRow,
  RangeControl,
  TabPanel,
  ToggleControl,
  __experimentalNumberControl as NumberControl,
  __experimentalUnitControl as UnitControl,
} from "@wordpress/components";
import { Fragment } from "@wordpress/element";

// Get the preset values
const CUSTOMIZE_BLOCKS = KrakenThemeSettings.blockFilterPresets || {};

/*** FUNCTIONS ****************************************************************/
/**
 * Add custom controls to editor
 */
const withCustomControls = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    const { name, attributes, setAttributes } = props;

    // check for matching customizations
    if (typeof CUSTOMIZE_BLOCKS[name] !== "undefined" && Array.isArray(CUSTOMIZE_BLOCKS[name])) {
      // add wrapper to style within editor, otherwise styles aren't visible in editor
      return (
        <Fragment>
          <BlockEdit {...props} />
          {/*only load these controls if the block is selected */}
          {props.isSelected && (
            <Fragment>
              <BlockControls group="block">
                {
                  // parse through matching customizations and add new toolbar controls
                  CUSTOMIZE_BLOCKS[name].map((customization) => {
                    switch (customization) {
                      case "justify-content":
                        return (
                          <JustifyContentControl
                            key={customization}
                            value={attributes.justifyContent}
                            onChange={(justifyContent) => setAttributes({ justifyContent })}
                          />
                        );
                    }
                  })
                }
              </BlockControls>
              <InspectorControls>
                <Panel>
                  <PanelBody title="[Kraken] Options" className="kraken-options-panel">
                    {
                      // parse through matching customizations and add new inspector controls
                      CUSTOMIZE_BLOCKS[name].map((customization) => {
                        switch (customization) {
                          case "content-width-settings":
                            return (
                              <Fragment key={customization}>
                                <PanelRow>
                                  <ToggleControl
                                    label={__("Restrict Width?")}
                                    help={attributes.enableMaxWidth ? __("Yes") : __("No")}
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
                                  <Fragment>
                                    <PanelRow>
                                      <ToggleControl
                                        label={__("Use Default Max Width?")}
                                        help={__(
                                          "Enable this to use the default max width for the website (80rem)",
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
                                  </Fragment>
                                )}
                              </Fragment>
                            );

                          case "reverse-order":
                            return (
                              <PanelRow key={customization}>
                                <ToggleControl
                                  label="Reverse order on mobile"
                                  checked={attributes.reverseOrder || false}
                                  onChange={(reverseOrder) => setAttributes({ reverseOrder })}
                                />
                              </PanelRow>
                            );

                          case "stack-on-tablet":
                            return (
                              <PanelRow key={customization}>
                                <ToggleControl
                                  label="Stack on tablet"
                                  checked={attributes.stackOnTablet || false}
                                  onChange={(stackOnTablet) => setAttributes({ stackOnTablet })}
                                />
                              </PanelRow>
                            );

                          case "alignfull-on-mobile":
                            return (
                              <PanelRow key={customization}>
                                <ToggleControl
                                  label="Full width on mobile"
                                  checked={attributes.alignfullOnMobile || false}
                                  onChange={(alignfullOnMobile) =>
                                    setAttributes({ alignfullOnMobile })
                                  }
                                />
                              </PanelRow>
                            );

                          case "center-on-mobile":
                            return (
                              <PanelRow key={customization}>
                                <ToggleControl
                                  label="Center on mobile"
                                  checked={attributes.centerOnMobile || false}
                                  onChange={(centerOnMobile) => setAttributes({ centerOnMobile })}
                                />
                              </PanelRow>
                            );

                          case "position-absolute":
                            return (
                              <Fragment key={customization}>
                                <Flex
                                  direction="column"
                                  gap={0}
                                  className="kraken-core-tab-panel-wrapper"
                                >
                                  <PanelRow>
                                    <ToggleControl
                                      label="Enable absolute position"
                                      help={__("Leave position values blank for auto")}
                                      checked={attributes.positionAbsolute || false}
                                      onChange={(positionAbsolute) =>
                                        setAttributes({ positionAbsolute })
                                      }
                                    />
                                  </PanelRow>

                                  {attributes?.positionAbsolute && (
                                    <PanelRow>
                                      <TabPanel
                                        className="kraken-core-tab-panel"
                                        activeClass="is-active"
                                        tabs={[
                                          {
                                            icon: (
                                              <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                              >
                                                <path
                                                  fill="inherit"
                                                  d="M8 21v-2h2v-2H4q-.825 0-1.412-.587T2 15V5q0-.825.588-1.412T4 3h16q.825 0 1.413.588T22 5v10q0 .825-.587 1.413T20 17h-6v2h2v2zm-4-6h16V5H4zm0 0V5z"
                                                />
                                              </svg>
                                            ),
                                            name: "desktop",
                                            title: "Desktop",
                                          },
                                          {
                                            icon: (
                                              <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                              >
                                                <path
                                                  fill="inherit"
                                                  d="M12 20.5q.425 0 .713-.288T13 19.5t-.288-.712T12 18.5t-.712.288T11 19.5t.288.713t.712.287M5 23q-.825 0-1.412-.587T3 21V3q0-.825.588-1.412T5 1h14q.825 0 1.413.588T21 3v18q0 .825-.587 1.413T19 23zm0-5v3h14v-3zm0-2h14V6H5zM5 4h14V3H5zm0 0V3zm0 14v3z"
                                                />
                                              </svg>
                                            ),
                                            name: "tablet",
                                            title: "Tablet",
                                          },
                                          {
                                            icon: (
                                              <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                              >
                                                <path
                                                  fill="inherit"
                                                  d="M7 23q-.825 0-1.412-.587T5 21V3q0-.825.588-1.412T7 1h10q.825 0 1.413.588T19 3v3.1q.45.175.725.55T20 7.5v2q0 .475-.275.85T19 10.9V21q0 .825-.587 1.413T17 23zm0-2h10V3H7zm0 0V3zm5-1q.425 0 .713-.288T13 19t-.288-.712T12 18t-.712.288T11 19t.288.713T12 20"
                                                />
                                              </svg>
                                            ),
                                            name: "mobile",
                                            title: "Mobile",
                                          },
                                        ]}
                                      >
                                        {(tab) => (
                                          <Flex direction="column" gap={2}>
                                            {tab.name === "desktop" && (
                                              <>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Top")}
                                                      value={attributes.positionTop}
                                                      onChange={(positionTop) =>
                                                        setAttributes({ positionTop })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Bottom")}
                                                      value={attributes.positionBottom}
                                                      onChange={(positionBottom) =>
                                                        setAttributes({ positionBottom })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Left")}
                                                      value={attributes.positionLeft}
                                                      onChange={(positionLeft) =>
                                                        setAttributes({ positionLeft })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Right")}
                                                      value={attributes.positionRight}
                                                      onChange={(positionRight) =>
                                                        setAttributes({ positionRight })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Transform X")}
                                                      value={attributes.transformX}
                                                      onChange={(transformX) =>
                                                        setAttributes({ transformX })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Transform Y")}
                                                      value={attributes.transformY}
                                                      onChange={(transformY) =>
                                                        setAttributes({ transformY })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                              </>
                                            )}
                                            {tab.name === "tablet" && (
                                              <>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Top")}
                                                      value={attributes.positionTopTablet}
                                                      onChange={(positionTopTablet) =>
                                                        setAttributes({ positionTopTablet })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Bottom")}
                                                      value={attributes.positionBottomTablet}
                                                      onChange={(positionBottomTablet) =>
                                                        setAttributes({
                                                          positionBottomTablet,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Left")}
                                                      value={attributes.positionLeftTablet}
                                                      onChange={(positionLeftTablet) =>
                                                        setAttributes({
                                                          positionLeftTablet,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Right")}
                                                      value={attributes.positionRightTablet}
                                                      onChange={(positionRightTablet) =>
                                                        setAttributes({
                                                          positionRightTablet,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Transform X")}
                                                      value={attributes.transformXTablet}
                                                      onChange={(transformXTablet) =>
                                                        setAttributes({
                                                          transformXTablet,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Transform Y")}
                                                      value={attributes.transformYTablet}
                                                      onChange={(transformYTablet) =>
                                                        setAttributes({
                                                          transformYTablet,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                              </>
                                            )}
                                            {tab.name === "mobile" && (
                                              <>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Top")}
                                                      value={attributes.positionTopMobile}
                                                      onChange={(positionTopMobile) =>
                                                        setAttributes({
                                                          positionTopMobile,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Bottom")}
                                                      value={attributes.positionBottomMobile}
                                                      onChange={(positionBottomMobile) =>
                                                        setAttributes({
                                                          positionBottomMobile,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Left")}
                                                      value={attributes.positionLeftMobile}
                                                      onChange={(positionLeftMobile) =>
                                                        setAttributes({
                                                          positionLeftMobile,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Right")}
                                                      value={attributes.positionRightMobile}
                                                      onChange={(positionRightMobile) =>
                                                        setAttributes({
                                                          positionRightMobile,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Transform X")}
                                                      value={attributes.transformXMobile}
                                                      onChange={(transformXMobile) =>
                                                        setAttributes({
                                                          transformXMobile,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <UnitControl
                                                      label={__("Transform Y")}
                                                      value={attributes.transformYMobile}
                                                      onChange={(transformYMobile) =>
                                                        setAttributes({
                                                          transformYMobile,
                                                        })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                              </>
                                            )}
                                          </Flex>
                                        )}
                                      </TabPanel>
                                    </PanelRow>
                                  )}
                                </Flex>
                              </Fragment>
                            );

                          case "responsive-display":
                            return (
                              <Fragment key={customization}>
                                <Flex
                                  direction="column"
                                  gap={0}
                                  className="kraken-core-tab-panel-wrapper"
                                >
                                  <PanelRow>
                                    <ToggleControl
                                      label="Enable responsive display"
                                      help={__("Hide elements at certain breakpoints")}
                                      checked={attributes.responsiveDisplay || false}
                                      onChange={(responsiveDisplay) =>
                                        setAttributes({ responsiveDisplay })
                                      }
                                    />
                                  </PanelRow>

                                  {attributes?.responsiveDisplay && (
                                    <PanelRow>
                                      <TabPanel
                                        className="kraken-core-tab-panel"
                                        activeClass="is-active"
                                        tabs={[
                                          {
                                            icon: (
                                              <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                              >
                                                <path
                                                  fill="inherit"
                                                  d="M8 21v-2h2v-2H4q-.825 0-1.412-.587T2 15V5q0-.825.588-1.412T4 3h16q.825 0 1.413.588T22 5v10q0 .825-.587 1.413T20 17h-6v2h2v2zm-4-6h16V5H4zm0 0V5z"
                                                />
                                              </svg>
                                            ),
                                            name: "desktop",
                                            title: "Desktop",
                                          },
                                          {
                                            icon: (
                                              <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                              >
                                                <path
                                                  fill="inherit"
                                                  d="M12 20.5q.425 0 .713-.288T13 19.5t-.288-.712T12 18.5t-.712.288T11 19.5t.288.713t.712.287M5 23q-.825 0-1.412-.587T3 21V3q0-.825.588-1.412T5 1h14q.825 0 1.413.588T21 3v18q0 .825-.587 1.413T19 23zm0-5v3h14v-3zm0-2h14V6H5zM5 4h14V3H5zm0 0V3zm0 14v3z"
                                                />
                                              </svg>
                                            ),
                                            name: "tablet",
                                            title: "Tablet",
                                          },
                                          {
                                            icon: (
                                              <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                              >
                                                <path
                                                  fill="inherit"
                                                  d="M7 23q-.825 0-1.412-.587T5 21V3q0-.825.588-1.412T7 1h10q.825 0 1.413.588T19 3v3.1q.45.175.725.55T20 7.5v2q0 .475-.275.85T19 10.9V21q0 .825-.587 1.413T17 23zm0-2h10V3H7zm0 0V3zm5-1q.425 0 .713-.288T13 19t-.288-.712T12 18t-.712.288T11 19t.288.713T12 20"
                                                />
                                              </svg>
                                            ),
                                            name: "mobile",
                                            title: "Mobile",
                                          },
                                        ]}
                                      >
                                        {(tab) => (
                                          <Flex direction="column" gap={2}>
                                            {tab.name === "desktop" && (
                                              <>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <ToggleControl
                                                      label="Hide on Desktop"
                                                      checked={attributes.hideOnDesktop || false}
                                                      onChange={(hideOnDesktop) =>
                                                        setAttributes({ hideOnDesktop })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                              </>
                                            )}
                                            {tab.name === "tablet" && (
                                              <>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <ToggleControl
                                                      label="Hide on Tablet"
                                                      checked={attributes.hideOnTablet || false}
                                                      onChange={(hideOnTablet) =>
                                                        setAttributes({ hideOnTablet })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                              </>
                                            )}
                                            {tab.name === "mobile" && (
                                              <>
                                                <Flex gap={2}>
                                                  <FlexItem style={{ flex: 1 }}>
                                                    <ToggleControl
                                                      label="Hide on Mobile"
                                                      checked={attributes.hideOnMobile || false}
                                                      onChange={(hideOnMobile) =>
                                                        setAttributes({ hideOnMobile })
                                                      }
                                                    />
                                                  </FlexItem>
                                                </Flex>
                                              </>
                                            )}
                                          </Flex>
                                        )}
                                      </TabPanel>
                                    </PanelRow>
                                  )}
                                </Flex>
                              </Fragment>
                            );

                          case "disable-pointer-events":
                            return (
                              <PanelRow key={customization}>
                                <ToggleControl
                                  label="Disable pointer events"
                                  checked={attributes.disablePointerEvents || false}
                                  onChange={(disablePointerEvents) =>
                                    setAttributes({ disablePointerEvents })
                                  }
                                />
                              </PanelRow>
                            );

                          case "rotate-element":
                            return (
                              <PanelRow key={customization}>
                                <AnglePickerControl
                                  label="Rotate"
                                  help={__("Rotate the element")}
                                  value={attributes.rotateElement || 0}
                                  onChange={(rotateElement) => {
                                    setAttributes({ rotateElement });
                                  }}
                                />
                              </PanelRow>
                            );

                          case "responsive-grid-columns":
                            return (
                              <Fragment key={customization}>
                                <PanelRow>
                                  <ToggleControl
                                    label="Enable responsive grid columns"
                                    checked={attributes.enableResponsiveGridCols || false}
                                    onChange={(enableResponsiveGridCols) =>
                                      setAttributes({ enableResponsiveGridCols })
                                    }
                                  />
                                </PanelRow>
                                {attributes?.enableResponsiveGridCols && (
                                  <Fragment>
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
                                  </Fragment>
                                )}
                              </Fragment>
                            );

                          case "responsive-sizes":
                            return (
                              <Fragment key={customization}>
                                <PanelRow>
                                  <ToggleControl
                                    label="Enable responsive sizes"
                                    help={__("Leave values blank for auto")}
                                    checked={attributes.enableResponsiveSizes || false}
                                    onChange={(enableResponsiveSizes) =>
                                      setAttributes({ enableResponsiveSizes })
                                    }
                                  />
                                </PanelRow>
                                {attributes?.enableResponsiveSizes && (
                                  <PanelRow>
                                    <Flex gap={2}>
                                      <FlexItem style={{ flex: 1 }}>
                                        <UnitControl
                                          label={__("Tablet")}
                                          value={attributes.tabletWidth}
                                          onChange={(tabletWidth) => {
                                            setAttributes({ tabletWidth });
                                          }}
                                        />
                                      </FlexItem>
                                      <FlexItem style={{ flex: 1 }}>
                                        <UnitControl
                                          label={__("Mobile")}
                                          value={attributes.mobileWidth}
                                          onChange={(mobileWidth) => {
                                            setAttributes({ mobileWidth });
                                          }}
                                        />
                                      </FlexItem>
                                    </Flex>
                                  </PanelRow>
                                )}
                              </Fragment>
                            );

                          case "overflow-visible":
                            return (
                              <PanelRow>
                                <ToggleControl
                                  label="Enable visible overflow"
                                  checked={attributes.overflowVisible}
                                  onChange={(overflowVisible) => setAttributes({ overflowVisible })}
                                />
                              </PanelRow>
                            );

                          case "object-fit-contain":
                            return (
                              <PanelRow key={customization}>
                                <ToggleControl
                                  label="Enable object fit: contain"
                                  checked={attributes.objectFitContain}
                                  onChange={(objectFitContain) =>
                                    setAttributes({ objectFitContain })
                                  }
                                />
                              </PanelRow>
                            );

                          case "image-has-transparency":
                            return (
                              <PanelRow>
                                <ToggleControl
                                  label="Use background image as transparent texture"
                                  checked={attributes.enableImageTransparency}
                                  onChange={(enableImageTransparency) =>
                                    setAttributes({ enableImageTransparency })
                                  }
                                />
                              </PanelRow>
                            );

                          case "mobile-padding":
                            return (
                              <Fragment key={customization}>
                                <ToggleControl
                                  label="Add Mobile Padding"
                                  checked={attributes.enableMobilePadding || false}
                                  onChange={(enableMobilePadding) =>
                                    setAttributes({
                                      enableMobilePadding,
                                      ...(enableMobilePadding ? {} : { mobilePadding: {} }), // clear mobilePadding when toggled off
                                    })
                                  }
                                />
                                {attributes.enableMobilePadding && (
                                  <SpacingSizesControl
                                    label={"Mobile Padding"}
                                    onChange={(mobilePadding) => setAttributes({ mobilePadding })}
                                    values={attributes.mobilePadding}
                                  />
                                )}
                              </Fragment>
                            );

                          case "z-index":
                            return (
                              <PanelRow key={customization}>
                                <RangeControl
                                  help={__("Controls the visibility level of this element")}
                                  label="Z Index"
                                  value={attributes.zIndex}
                                  min={-100}
                                  onChange={(zIndex) => {
                                    setAttributes({ zIndex });
                                  }}
                                />
                              </PanelRow>
                            );
                        }
                      })
                    }
                  </PanelBody>
                </Panel>
              </InspectorControls>
            </Fragment>
          )}
        </Fragment>
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
