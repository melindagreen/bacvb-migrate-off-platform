/*
Documentation: https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#blocks-registerblocktype
- This file defines the new settings available for the filter - name, type, and default values. The same options used in block.json files can be used here.
*/

/*** IMPORTS ****************************************************************/
// WordPress Dependencies
import { __ } from "@wordpress/i18n";
// Get the preset values
const CUSTOMIZE_BLOCKS = KrakenThemeSettings.blockFilterPresets || {};

/*** FUNCTIONS ****************************************************************/

/**
 * Add new attributes to customized block
 * @param {*} settings
 * @returns
 */
const addCustomAttrs = (settings) => {
  if (typeof settings.attributes !== "undefined") {
    if (
      typeof CUSTOMIZE_BLOCKS[settings.name] !== "undefined" &&
      Array.isArray(CUSTOMIZE_BLOCKS[settings.name])
    ) {
      // parse through matching customizations and add new attrs
      CUSTOMIZE_BLOCKS[settings.name].forEach((customization) => {
        switch (customization) {
          case "content-width-settings":
            settings.attributes = {
              ...settings.attributes,
              enableMaxWidth: {
                type: "boolean",
                default: false,
              },
              defaultMaxWidth: {
                type: "boolean",
                default: true,
              },
              customMaxWidth: {
                type: "number",
                default: 80,
              },
            };
            break;

          case "reverse-order":
            settings.attributes = {
              ...settings.attributes,
              reverseOrder: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "stack-on-tablet":
            settings.attributes = {
              ...settings.attributes,
              stackOnTablet: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "z-index":
            settings.attributes = {
              ...settings.attributes,
              zIndex: {
                type: "number",
                default: 0,
              },
            };
            break;

          case "alignfull-on-mobile":
            settings.attributes = {
              ...settings.attributes,
              alignfullOnMobile: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "center-on-mobile":
            settings.attributes = {
              ...settings.attributes,
              centerOnMobile: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "position-absolute":
            settings.attributes = {
              ...settings.attributes,
              positionAbsolute: {
                type: "boolean",
                default: false,
              },
              positionTop: {
                type: "string",
                default: "auto",
              },
              positionBottom: {
                type: "string",
                default: "auto",
              },
              positionLeft: {
                type: "string",
                default: "auto",
              },
              positionRight: {
                type: "string",
                default: "auto",
              },
              positionTopTablet: {
                type: "string",
                default: "",
              },
              positionBottomTablet: {
                type: "string",
                default: "",
              },
              positionLeftTablet: {
                type: "string",
                default: "",
              },
              positionRightTablet: {
                type: "string",
                default: "",
              },
              positionTopMobile: {
                type: "string",
                default: "",
              },
              positionBottomMobile: {
                type: "string",
                default: "",
              },
              positionLeftMobile: {
                type: "string",
                default: "",
              },
              positionRightMobile: {
                type: "string",
                default: "",
              },
              transformX: {
                type: "string",
                default: "auto",
              },
              transformY: {
                type: "string",
                default: "auto",
              },
              transformXTablet: {
                type: "string",
                default: "",
              },
              transformYTablet: {
                type: "string",
                default: "",
              },
              transformXMobile: {
                type: "string",
                default: "",
              },
              transformYMobile: {
                type: "string",
                default: "",
              },
            };
            break;

          case "responsive-display":
            settings.attributes = {
              ...settings.attributes,
              responsiveDisplay: {
                type: "boolean",
                default: false,
              },
              hideOnMobile: {
                type: "boolean",
                default: false,
              },
              hideOnTablet: {
                type: "boolean",
                default: false,
              },
              hideOnDesktop: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "disable-pointer-events":
            settings.attributes = {
              ...settings.attributes,
              disablePointerEvents: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "responsive-grid-columns":
            settings.attributes = {
              ...settings.attributes,
              enableResponsiveGridCols: {
                type: "boolean",
                default: false,
              },
              tabletGridCols: {
                type: "number",
                default: 1,
              },
              mobileGridCols: {
                type: "number",
                default: 1,
              },
            };
            break;

          case "responsive-sizes":
            settings.attributes = {
              ...settings.attributes,
              enableResponsiveSizes: {
                type: "boolean",
                default: false,
              },
              tabletWidth: {
                type: "string",
                default: "auto",
              },
              mobileWidth: {
                type: "string",
                default: "auto",
              },
            };
            break;

          case "overflow-visible":
            settings.attributes = {
              ...settings.attributes,
              overflowVisible: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "object-fit-contain":
            settings.attributes = {
              ...settings.attributes,
              objectFitContain: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "image-has-transparency":
            settings.attributes = {
              ...settings.attributes,
              enableImageTransparency: {
                type: "boolean",
                default: false,
              },
            };
            break;

          case "mobile-padding":
            settings.attributes = {
              ...settings.attributes,
              enableMobilePadding: {
                type: "boolean",
                default: false,
              },
              mobilePadding: {
                type: "object",
                default: {},
              },
            };
            break;

          case "rotate-element":
            settings.attributes = {
              ...settings.attributes,
              rotateElement: {
                type: "number",
                default: 0,
              },
            };
            break;
        }
      });
    }
  }

  return settings;
};

/*** EXPORTS ****************************************************************/

export default {
  name: "personalization-attributes",
  hook: "blocks.registerBlockType",
  action: addCustomAttrs,
};
