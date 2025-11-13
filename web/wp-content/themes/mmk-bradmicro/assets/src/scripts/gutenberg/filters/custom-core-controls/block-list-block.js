/*
This file adds:
- custom classes
- custom inline styles
*/

/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { createHigherOrderComponent } from "@wordpress/compose";
import { getCSSRules } from "@wordpress/style-engine";

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** HELPERS ****************************************************************/

// Ensure CSS length has a unit; add "px" to plain numbers, allow common units, keep "auto" as-is.
const withUnit = (val) => {
	if (val === undefined || val === null || val === "") return "auto";
	const s = String(val).trim();
	if (s === "auto") return "auto";
	// already has a unit?
	if (
		/^-?\d+(\.\d+)?(px|%|em|rem|vh|vw|vmin|vmax|ch|ex|cm|mm|in|pt|pc)$/i.test(s)
	) {
		return s;
	}
	// plain number?
	if (/^-?\d+(\.\d+)?$/.test(s)) {
		return `${s}px`;
	}
	// allow calc() or other expressions unchanged
	return s;
};

/*** FUNCTIONS ****************************************************************/

const withCustomStyles = createHigherOrderComponent((BlockListBlock) => {
	return (props) => {
		const { name, attributes } = props;

		// Constructs CSS objects for WP Style Engine
		function cssObject(outerKey, innerKey, attValue) {
			this.cssObj = {
				[outerKey]: {
					[innerKey]: attValue,
				},
			};
		}

		if (
			typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
			Array.isArray(CUSTOMIZE_BLOCKS[name])
		) {
			let customClassName = "";
			let customBlockStyle = {};

			if (!!attributes.enableMaxWidth && !attributes.defaultMaxWidth) {
				customBlockStyle.maxWidth = attributes.customMaxWidth + "rem";
			}

			// z-index (relative positioning unless absolute positioning is enabled later)
			if (attributes.zIndex && attributes.zIndex !== 0) {
				customBlockStyle.position = "relative";
				customBlockStyle.zIndex = attributes.zIndex;
			}

			// Rotation
			if (attributes.rotateElement && attributes.rotateElement !== 0) {
				customClassName += " has-rotate-element";
				customBlockStyle["--rotate-element"] = `${attributes.rotateElement}deg`;
			}

			// Utility classes
			if (!!attributes.centerOnMobile) {
				customClassName += " center-on-mobile";
			}
			if (!!attributes.hideOnMobile) {
				customClassName += " hide-on-mobile";
			}
			if (!!attributes.reverseOrder) {
				customClassName += " reversed-on-mobile";
			}

			if (!!attributes.enableResponsiveGridCols) {
				customClassName += " responsive-grid-columns";
				customBlockStyle["--tablet-grid-cols"] = attributes.tabletgridCols;
				customBlockStyle["--mobile-grid-cols"] = attributes.mobilegridCols;
			}

			// Responsive widths
			if (!!attributes.enableResponsiveSizes) {
				customClassName += " responsive-sizes";
				customBlockStyle["--tablet-width"] = attributes.tabletWidth;
				customBlockStyle["--mobile-width"] = attributes.mobileWidth;
			}

			// Mobile padding (CSS vars)
			if (!!attributes.enableMobilePadding) {
				if (
					attributes?.mobilePadding &&
					Object.keys(attributes.mobilePadding).length
				) {
					// Start with existing or empty class string
					let classNames = customClassName || "";

					// Add base class if not already present
					if (!classNames.includes("has-mobile-padding")) {
						classNames += " has-mobile-padding";
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
							if (!classNames.includes(classNamePart)) {
								classNames += ` ${classNamePart}`;
							}

							return acc;
						},
						{}
					);

					customClassName = classNames.trim();
					customBlockStyle = {
						...customBlockStyle,
						...paddingObj,
					};
				}
			}

			// Mobile typography (CSS vars)
			if (attributes.mobileFontSize || attributes.mobileLineHeight) {
				let classNames = customClassName || "";
				if (attributes.mobileFontSize) {
					customBlockStyle["--mobile-font-size"] = attributes.mobileFontSize;
					if (!classNames.includes("has-mobile-font-size")) {
						classNames += " has-mobile-font-size";
					}
				}
				if (attributes.mobileLineHeight) {
					customBlockStyle["--mobile-line-height"] =
						attributes.mobileLineHeight;
					if (!classNames.includes("has-mobile-line-height")) {
						classNames += " has-mobile-line-height";
					}
				}
				customClassName = classNames.trim();
			}

			// Absolute positioning (editor-only wrapper styles)
			if (!!attributes.enableAbsolutePosition) {
				const pos = attributes?.absolutePositions || {};
				const positionCss = {
					position: "absolute", // overrides earlier "relative" if set
					left: withUnit(pos.left),
					right: withUnit(pos.right),
					top: withUnit(pos.top),
					bottom: withUnit(pos.bottom),
				};
				customBlockStyle = {
					...customBlockStyle,
					...positionCss,
				};
			}

			// Merge into wrapper props without clobbering existing props
			const existingWrapperStyle = props.wrapperProps?.style || {};
			const mergedWrapperStyle = {
				...existingWrapperStyle,
				...customBlockStyle,
			};

			// Merge classes safely
			const incomingClass = props.attributes?.className || "";
			const mergedClassName = `${(
				customClassName || ""
			).trim()} ${incomingClass}`.trim();

			return (
				<BlockListBlock
					{...props}
					className={mergedClassName}
					wrapperProps={{
						...(props.wrapperProps || {}),
						style: mergedWrapperStyle,
					}}
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
