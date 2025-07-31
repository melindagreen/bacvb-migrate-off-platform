/*
This file adds:
- custom classes
- custom inline styles
*/

/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { createHigherOrderComponent } from "@wordpress/compose";
import { getCSSRules } from '@wordpress/style-engine';

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** FUNCTIONS ****************************************************************/

const withCustomStyles = createHigherOrderComponent((BlockListBlock) => {
	return (props) => {
		const { name, attributes, setAttributes } = props;
		
		// Constructs CSS objects for WP Style Engine
		function cssObject(outerKey, innerKey, attValue) {
			this.cssObj = {
				[outerKey]: {
					[innerKey]: attValue
				}
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

			if (attributes.zIndex && attributes.zIndex !== 0) {
				customBlockStyle.position = "relative";
				customBlockStyle.zIndex = attributes.zIndex;
			}

			if (!!attributes.centerOnMobile) {
				customClassName += " center-on-mobile";
			}

			if (!!attributes.hideOnMobile) {
				customClassName += " hide-on-mobile";
			}

			if (!!attributes.reverseOrder) {
				customClassName += " reversed-on-mobile";
			}

			if (!!attributes.enableMaxWidth) {
				customClassName += " main-column-auto";
			}
				
			if (!!attributes.enableResponsiveSizes) {
				customClassName += " responsive-sizes";
				customBlockStyle["--tablet-width"] = attributes.tabletWidth;
				customBlockStyle["--mobile-width"] = attributes.mobileWidth;
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
						...paddingObj
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
