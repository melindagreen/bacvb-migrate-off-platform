/*
Documentation: https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#blocks-getsavecontent-extraprops
- This file is where we define the extra properties (props) to be saved to the block’s root element on both the frontend and backend.
- Any html attributes can be added here - id, class, style, data attributes, etc.
- console.log can be used anywhere in the customizeExtraProps function and will print any time the block is updated or saved.
*/

/*** IMPORTS ****************************************************************/
// WordPress Dependencies
import { getCSSRules } from "@wordpress/style-engine";

// Get the preset values
const CUSTOMIZE_BLOCKS = KrakenThemeSettings.blockFilterPresets || {};

/*** FUNCTIONS ****************************************************************/

/**
 * Customize extra props of modified elements for use w/ customizations
 * @param {*} props
 * @param {*} blockType
 * @param {*} attributes
 * @returns
 */
const customizeExtraProps = (props, blockType, attributes) => {
  const { name } = blockType;

  // check for matching customizations
  if (typeof CUSTOMIZE_BLOCKS[name] !== "undefined" && Array.isArray(CUSTOMIZE_BLOCKS[name])) {
    // parse through matching customizations and extend props
    CUSTOMIZE_BLOCKS[name].map((customization) => {
      switch (customization) {
        case "content-width-settings":
          if (attributes?.enableMaxWidth) {
            const currentClassName = props.className || "";
            if (!currentClassName.includes("main-column-auto")) {
              props.className = currentClassName
                ? `${currentClassName} main-column-auto`
                : "main-column-auto";
            }

            if (!attributes.defaultMaxWidth) {
              Object.assign(props, {
                style: {
                  ...props.style,
                  maxWidth: `${attributes.customMaxWidth}rem`,
                },
              });
            }
          }
          break;

        case "z-index":
          if (attributes.zIndex && attributes.zIndex !== 0) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("has-z-index")) {
              props.className = currentClassName
                ? `${currentClassName} has-z-index`
                : "has-z-index";
            }

            Object.assign(props, {
              style: {
                ...props.style,
                "--z-index": `${attributes.zIndex}`,
              },
            });
          }
          break;

        case "stack-on-tablet":
          if (attributes?.stackOnTablet) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("stack-on-tablet")) {
              props.className = currentClassName
                ? `${currentClassName} stack-on-tablet`
                : "stack-on-tablet";
            }
          }
          break;

        case "position-absolute":
          if (attributes?.positionAbsolute) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("has-position-absolute")) {
              props.className = currentClassName
                ? `${currentClassName} has-position-absolute`
                : "has-position-absolute";
            }

            /// Defaults/Desktop
            const newStyles = {
              ...props.style,
              "--position-top": attributes.positionTop !== "" ? attributes.positionTop : "auto",
              "--position-bottom":
                attributes.positionBottom !== "" ? attributes.positionBottom : "auto",
              "--position-left": attributes.positionLeft !== "" ? attributes.positionLeft : "auto",
              "--position-right":
                attributes.positionRight !== "" ? attributes.positionRight : "auto",
              "--transform-x": attributes.transformX !== "" ? attributes.transformX : "0",
              "--transform-y": attributes.transformY !== "" ? attributes.transformY : "0",
            };

            //// Tablet
            if (attributes.positionTopTablet) {
              newStyles["--position-top-tablet"] = attributes.positionTopTablet;
            }
            if (attributes.positionBottomTablet) {
              newStyles["--position-bottom-tablet"] = attributes.positionBottomTablet;
            }
            if (attributes.positionLeftTablet) {
              newStyles["--position-left-tablet"] = attributes.positionLeftTablet;
            }
            if (attributes.positionRightTablet) {
              newStyles["--position-right-tablet"] = attributes.positionRightTablet;
            }
            if (attributes.transformXTablet) {
              newStyles["--transform-x-tablet"] = attributes.transformXTablet;
            }
            if (attributes.transformYTablet) {
              newStyles["--transform-y-tablet"] = attributes.transformYTablet;
            }

            /// Mobile
            if (attributes.positionTopMobile) {
              newStyles["--position-top-mobile"] = attributes.positionTopMobile;
            }
            if (attributes.positionBottomMobile) {
              newStyles["--position-bottom-mobile"] = attributes.positionBottomMobile;
            }
            if (attributes.positionLeftMobile) {
              newStyles["--position-left-mobile"] = attributes.positionLeftMobile;
            }
            if (attributes.positionRightMobile) {
              newStyles["--position-right-mobile"] = attributes.positionRightMobile;
            }
            if (attributes.transformXMobile) {
              newStyles["--transform-x-mobile"] = attributes.transformXMobile;
            }
            if (attributes.transformYMobile) {
              newStyles["--transform-y-mobile"] = attributes.transformYMobile;
            }

            Object.assign(props, {
              style: newStyles,
            });
          }
          break;

        case "responsive-display":
          if (attributes?.responsiveDisplay) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("responsive-display")) {
              props.className = currentClassName
                ? `${currentClassName} responsive-display`
                : "responsive-display";
            }

            // Add styles
            const newStyles = {
              ...props.style,
            };

            if (attributes.hideOnDesktop == true) {
              newStyles["--display-desktop"] = "none";
            }

            if (attributes.hideOnTablet == true) {
              newStyles["--display-tablet"] = "none";
            }

            if (attributes.hideOnMobile == true) {
              newStyles["--display-mobile"] = "none";
            }

            Object.assign(props, {
              style: newStyles,
            });
          }
          break;

        case "rotate-element":
          if (attributes.rotateElement && attributes.rotateElement !== 0) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("has-rotate-element")) {
              props.className = currentClassName
                ? `${currentClassName} has-rotate-element`
                : "has-rotate-element";
            }

            Object.assign(props, {
              style: {
                ...props.style,
                "--rotate-element": `${attributes.rotateElement}deg`,
              },
            });
          }
          break;

        case "responsive-grid-columns":
          if (attributes?.enableResponsiveGridCols) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("responsive-grid-columns")) {
              props.className = currentClassName
                ? `${currentClassName} responsive-grid-columns`
                : "responsive-grid-columns";
            }

            Object.assign(props, {
              style: {
                ...props.style,
                "--tablet-grid-cols": `${attributes.tabletGridCols}`,
                "--mobile-grid-cols": `${attributes.mobileGridCols}`,
              },
            });
          }
          break;

        case "responsive-sizes":
          if (attributes?.enableResponsiveSizes) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("responsive-sizes")) {
              props.className = currentClassName
                ? `${currentClassName} responsive-sizes`
                : "responsive-sizes";
            }

            Object.assign(props, {
              style: {
                ...props.style,
                "--tablet-width": `${attributes.tabletWidth}`,
                "--mobile-width": `${attributes.mobileWidth}`,
              },
            });
          }
          break;

        case "reverse-order":
          if (attributes?.reverseOrder) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("reversed-on-mobile")) {
              props.className = currentClassName
                ? `${currentClassName} reversed-on-mobile`
                : "reversed-on-mobile";
            }
          }
          break;

        case "alignfull-on-mobile":
          if (attributes?.alignfullOnMobile) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("alignfull-on-mobile")) {
              props.className = currentClassName
                ? `${currentClassName} alignfull-on-mobile`
                : "alignfull-on-mobile";
            }
          }
          break;

        case "center-on-mobile":
          if (attributes?.centerOnMobile) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("center-on-mobile")) {
              props.className = currentClassName
                ? `${currentClassName} center-on-mobile`
                : "center-on-mobile";
            }
          }
          break;

        case "disable-pointer-events":
          if (attributes?.disablePointerEvents) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("disable-pointer-events")) {
              props.className = currentClassName
                ? `${currentClassName} disable-pointer-events`
                : "disable-pointer-events";
            }
          }
          break;

        case "overflow-visible":
          if (attributes?.overflowVisible) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("overflow-visible")) {
              props.className = currentClassName
                ? `${currentClassName} overflow-visible`
                : "overflow-visible";
            }
          }
          break;

        case "object-fit-contain":
          if (attributes?.objectFitContain) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("has-object-fit-contain")) {
              props.className = currentClassName
                ? `${currentClassName} has-object-fit-contain`
                : "has-object-fit-contain";
            }
          }
          break;

        case "image-has-transparency":
          if (attributes?.enableImageTransparency) {
            // Add class
            const currentClassName = props.className || "";
            if (!currentClassName.includes("image-has-transparency")) {
              props.className = currentClassName
                ? `${currentClassName} image-has-transparency`
                : "image-has-transparency";
            }
          }
          break;

        case "mobile-padding":
          if (attributes?.enableMobilePadding) {
            if (attributes?.mobilePadding && Object.keys(attributes.mobilePadding).length) {
              let newClassName = props.className || "";

              if (!newClassName.includes("has-mobile-padding")) {
                newClassName += " has-mobile-padding";
              }

              const prefix = "--mobile";
              const cssObj = {
                spacing: {
                  padding: attributes.mobilePadding,
                },
              };
              const mobilePaddingCssRules = getCSSRules(cssObj);

              const paddingObj = mobilePaddingCssRules.reduce((acc, { key, value }) => {
                const varName = `${prefix}-${key.replace(/([A-Z])/g, "-$1").toLowerCase()}`;
                acc[varName] = value;

                // Add class like has-mobile-padding-top
                const classNamePart = `has-mobile-${key.replace(/([A-Z])/g, "-$1").toLowerCase()}`;
                if (!newClassName.includes(classNamePart)) {
                  newClassName += ` ${classNamePart}`;
                }

                return acc;
              }, {});

              // Assign new className and styles
              props.className = newClassName.trim();
              Object.assign(props, {
                style: {
                  ...props.style,
                  ...paddingObj,
                },
              });
            }
          }
          break;
      }
    });
  }

  return props;
};

/*** EXPORTS ****************************************************************/

export default {
  name: "extra-props",
  hook: "blocks.getSaveContent.extraProps",
  action: customizeExtraProps,
};
