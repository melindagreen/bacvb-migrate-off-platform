/*
Documentation: https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#editor-blocklistblock
- This file is for outputting the custom classes and/or styles in the editor. This is not strictly required, but it is recommended to maintain a good editor experience and consistency with the front-end.
- console.log can be used anywhere in the return function and will print anytime the block attributes are updating.
- If you add console.log(attributes); you can see all of the currently saved attributes for each block
*/

/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { createHigherOrderComponent } from "@wordpress/compose";
import { getCSSRules } from "@wordpress/style-engine";

// Get the preset values
const CUSTOMIZE_BLOCKS = KrakenThemeSettings.blockFilterPresets || {};

/*** FUNCTIONS ****************************************************************/

const withCustomStyles = createHigherOrderComponent((BlockListBlock) => {
  return (props) => {
    const { name, attributes, setAttributes } = props;

    // Constructs CSS objects for WP Style Engine
    function cssObject(outerKey, innerKey, attValue) {
      this.cssObj = {
        [outerKey]: {
          [innerKey]: attValue,
        },
      };
    }

    if (typeof CUSTOMIZE_BLOCKS[name] !== "undefined" && Array.isArray(CUSTOMIZE_BLOCKS[name])) {
      let customClassName = "";
      let customBlockStyle = {};

      if (!!attributes.enableMaxWidth && !attributes.defaultMaxWidth) {
        customBlockStyle.maxWidth = attributes.customMaxWidth + "rem";
      }

      if (attributes.zIndex && attributes.zIndex !== 0) {
        customClassName += " has-z-index";
        customBlockStyle["--z-index"] = `${attributes.zIndex}`;
      }

      if (!!attributes.alignfullOnMobile) {
        customClassName += " alignfull-on-mobile";
      }

      if (!!attributes.centerOnMobile) {
        customClassName += " center-on-mobile";
      }

      if (!!attributes.responsiveDisplay) {
        customClassName += " responsive-display";
      }

      if (!!attributes.reverseOrder) {
        customClassName += " reversed-on-mobile";
      }

      if (!!attributes.stackOnTablet) {
        customClassName += " stack-on-tablet";
      }

      if (!!attributes.enableMaxWidth) {
        customClassName += " main-column-auto";
      }

      if (!!attributes.positionAbsolute) {
        customClassName += " has-position-absolute";

        /// Defaults/Desktop
        customBlockStyle["--position-top"] = !!attributes.positionAbsolute
          ? attributes.positionTop
          : "auto";
        customBlockStyle["--position-bottom"] = !!attributes.positionAbsolute
          ? attributes.positionBottom
          : "auto";
        customBlockStyle["--position-left"] = !!attributes.positionAbsolute
          ? attributes.positionLeft
          : "auto";
        customBlockStyle["--position-right"] = !!attributes.positionAbsolute
          ? attributes.positionRight
          : "auto";
        customBlockStyle["--transform-x"] = !!attributes.positionAbsolute
          ? attributes.transformX
          : "0";
        customBlockStyle["--transform-y"] = !!attributes.positionAbsolute
          ? attributes.transformY
          : "0";

        //// Tablet
        if (attributes.positionTopTablet) {
          customBlockStyle["--position-top-tablet"] = attributes.positionTopTablet;
        }
        if (attributes.positionBottomTablet) {
          customBlockStyle["--position-bottom-tablet"] = attributes.positionBottomTablet;
        }
        if (attributes.positionLeftTablet) {
          customBlockStyle["--position-left-tablet"] = attributes.positionLeftTablet;
        }
        if (attributes.positionRightTablet) {
          customBlockStyle["--position-right-tablet"] = attributes.positionRightTablet;
        }
        if (attributes.transformXTablet) {
          customBlockStyle["--transform-x-tablet"] = attributes.transformXTablet;
        }
        if (attributes.transformYTablet) {
          customBlockStyle["--transform-y-tablet"] = attributes.transformYTablet;
        }

        /// Mobile
        if (attributes.positionTopMobile) {
          customBlockStyle["--position-top-mobile"] = attributes.positionTopMobile;
        }
        if (attributes.positionBottomMobile) {
          customBlockStyle["--position-bottom-mobile"] = attributes.positionBottomMobile;
        }
        if (attributes.positionLeftMobile) {
          customBlockStyle["--position-left-mobile"] = attributes.positionLeftMobile;
        }
        if (attributes.positionRightMobile) {
          customBlockStyle["--position-right-mobile"] = attributes.positionRightMobile;
        }
        if (attributes.transformXMobile) {
          customBlockStyle["--transform-x-mobile"] = attributes.transformXMobile;
        }
        if (attributes.transformYMobile) {
          customBlockStyle["--transform-y-mobile"] = attributes.transformYMobile;
        }
      }

      if (attributes.rotateElement && attributes.rotateElement !== 0) {
        customClassName += " has-rotate-element";
        customBlockStyle["--rotate-element"] = `${attributes.rotateElement}deg`;
      }

      if (!!attributes.enableResponsiveGridCols) {
        customClassName += " responsive-grid-columns";
        customBlockStyle["--tablet-grid-cols"] = attributes.tabletgridCols;
        customBlockStyle["--mobile-grid-cols"] = attributes.mobilegridCols;
      }

      if (!!attributes.enableResponsiveSizes) {
        customClassName += " responsive-sizes";
        customBlockStyle["--tablet-width"] = attributes.tabletWidth;
        customBlockStyle["--mobile-width"] = attributes.mobileWidth;
      }

      if (!!attributes.overflowVisible) {
        customClassName += " overflow-visible";
      }

      if (!!attributes.objectFitContain) {
        customClassName += " has-object-fit-contain";
      }

      if (!!attributes.enableImageTransparency) {
        customClassName += " image-has-transparency";
      }

      if (!!attributes.enableMobilePadding) {
        if (attributes?.mobilePadding && Object.keys(attributes.mobilePadding).length) {
          // Start with existing or empty class string
          let classNames = customClassName || "";

          // Add base class if not already present
          if (!classNames.includes("has-mobile-padding")) {
            classNames += " has-mobile-padding";
          }

          const prefix = "--mobile";
          const padding = new cssObject("spacing", "padding", attributes.mobilePadding);
          const mobilePaddingCssRules = getCSSRules(padding.cssObj); // array of { key, value }

          const paddingObj = mobilePaddingCssRules.reduce((acc, { key, value }) => {
            const varName = `${prefix}-${key.replace(/([A-Z])/g, "-$1").toLowerCase()}`;
            acc[varName] = value;

            // Add class like has-mobile-padding-top
            const classNamePart = `has-mobile-${key.replace(/([A-Z])/g, "-$1").toLowerCase()}`;
            if (!classNames.includes(classNamePart)) {
              classNames += ` ${classNamePart}`;
            }

            return acc;
          }, {});

          customClassName = classNames.trim();
          customBlockStyle = {
            ...customBlockStyle,
            ...paddingObj,
          };
        }
      }

      return (
        <BlockListBlock
          {...props}
          className={`${customClassName} ${props.attributes.className}`}
          wrapperProps={{ style: { ...customBlockStyle } }}
        />
      );
    }

    return <BlockListBlock {...props} />;
  };
}, "withClientIdClassName");

/*** EXPORTS ***************************************************************/

export default {
  name: "customBlockList",
  hook: "editor.BlockListBlock",
  action: withCustomStyles,
};
