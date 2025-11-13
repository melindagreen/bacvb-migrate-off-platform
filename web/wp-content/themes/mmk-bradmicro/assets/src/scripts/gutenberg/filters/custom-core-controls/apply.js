/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { cloneElement } from "@wordpress/element";
import { getCSSRules } from "@wordpress/style-engine";

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** FUNCTIONS ****************************************************************/

/**
 * Apply any needed customizations to the resultant saved element & its attributes
 * @param {E} el
 * @param {*} block
 * @param {*} attributes
 * @returns
 */
const applyCustomAttrs = (el, block, attributes) => {
	const { name } = block;

	// if customizations exist...
	if (
		typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
		Array.isArray(CUSTOMIZE_BLOCKS[name])
	) {
		let newProps = { ...el.props };

		// default wrapper prp, has no impact on output
		// overwrite to wrap output in new element

		// NOTE 20220125 this solution is not ideal as only the
		// last overwrite will apply, but directly modding was causing
		// issues, refactor later? also why isn't this using children? -ashw
		let ElWrap = ({ content }) => <>{content}</>;

		// Constructs CSS objects for WP Style Engine
		function cssObject(outerKey, innerKey, attValue) {
			this.cssObj = {
				[outerKey]: {
					[innerKey]: attValue,
				},
			};
		}

		// parse through matching customizations
		CUSTOMIZE_BLOCKS[name].forEach((customization) => {
			switch (customization) {
				case "z-index":
					if (attributes.zIndex && attributes.zIndex !== 0) {
						newProps.style = {
							...newProps.style,
							position: `relative`,
							zIndex: `${attributes.zIndex}`,
						};
					}
					break;

				case "reverse-order":
					if (attributes?.reverseOrder) {
						// Reverse the order of the children
						if (
							!newProps.className ||
							!newProps.className.includes("reversed-on-mobile")
						) {
							newProps.className = `${newProps.className} reversed-on-mobile`;
						}
					}
					break;

				case "center-on-mobile":
					if (attributes?.centerOnMobile) {
						// Center on mobile
						if (
							!newProps.className ||
							!newProps.className.includes("center-on-mobile")
						) {
							newProps.className = `${newProps.className} center-on-mobile`;
						}
					}
					break;

				case "hide-on-mobile":
					if (attributes?.hideOnMobile) {
						// Hide on mobile
						if (
							!newProps.className ||
							!newProps.className.includes("hide-on-mobile")
						) {
							newProps.className = `${newProps.className} hide-on-mobile`;
						}
					}
					break;

				case "disable-pointer-events":
					if (attributes?.disablePointerEvents) {
						newProps.className = `${newProps.className} disable-pointer-events`;
					}
					break;

				case "responsive-grid-columns":
					if (attributes?.enableResponsiveGridCols) {
						newProps.className = `${newProps.className} responsive-grid-columns`;
						newProps.style = {
							...newProps.style,
							"--tablet-grid-cols": `${attributes.tabletGridCols}`,
							"--mobile-grid-cols": `${attributes.mobileGridCols}`,
						};
					}
					break;

				case "responsive-sizes":
					if (attributes?.enableResponsiveSizes) {
						newProps.className = `${newProps.className} responsive-sizes`;
						newProps.style = {
							...newProps.style,
							"--tablet-width": `${attributes.tabletWidth}`,
							"--mobile-width": `${attributes.mobileWidth}`,
						};
					}
					break;

				case "mobile-padding":
					if (attributes?.enableMobilePadding) {
						if (
							attributes?.mobilePadding &&
							Object.keys(attributes.mobilePadding).length
						) {
							let newClassName = newProps.className || "";

							if (!newClassName.includes("has-mobile-padding")) {
								newClassName += " has-mobile-padding";
							}

							const prefix = "--mobile";
							const padding = new cssObject(
								"spacing",
								"padding",
								attributes.mobilePadding
							);
							const mobilePaddingCssRules = getCSSRules(padding.cssObj); // array of { key, value }

							const paddingObj = mobilePaddingCssRules.reduce(
								(acc, { key, value }) => {
									const varName = `${prefix}-${key
										.replace(/([A-Z])/g, "-$1")
										.toLowerCase()}`;
									acc[varName] = value;

									// Add class like has-mobile-padding-top
									const classNamePart = `has-mobile-${key
										.replace(/([A-Z])/g, "-$1")
										.toLowerCase()}`;
									if (!newClassName.includes(classNamePart)) {
										newClassName += ` ${classNamePart}`;
									}

									return acc;
								},
								{}
							);

							// Assign new className and styles
							newProps.className = newClassName.trim();
							newProps.style = {
								...newProps.style,
								...paddingObj,
							};
						}
					}
					break;

				case "mobile-font-settings":
					if (attributes.mobileFontSize) {
						newProps.style = {
							...newProps.style,
							"--mobile-font-size": attributes.mobileFontSize,
						};
						if (
							newProps.className &&
							!newProps.className.includes("has-mobile-font-size")
						) {
							newProps.className =
								`${newProps.className} has-mobile-font-size`.trim();
						}
					}
					if (attributes.mobileLineHeight) {
						newProps.style = {
							...newProps.style,
							"--mobile-line-height": attributes.mobileLineHeight,
						};
						if (
							newProps.className &&
							!newProps.className.includes("has-mobile-line-height")
						) {
							newProps.className =
								`${newProps.className} has-mobile-line-height`.trim();
						}
					}
					if (attributes.mobileLetterSpacing) {
						newProps.style = {
							...newProps.style,
							"--mobile-letter-spacing": attributes.mobileLetterSpacing,
						};
						if (
							newProps.className &&
							!newProps.className.includes("has-mobile-letter-spacing")
						) {
							newProps.className =
								`${newProps.className} has-mobile-letter-spacing`.trim();
						}
					}
					break;
			}
		});

		// return modified element
		return <ElWrap content={cloneElement(el, newProps)} />;
	}

	return el;
};

/*** EXPORTS ****************************************************************/

export default {
	name: "personalization-attributes",
	hook: "blocks.getSaveElement",
	action: applyCustomAttrs,
};
