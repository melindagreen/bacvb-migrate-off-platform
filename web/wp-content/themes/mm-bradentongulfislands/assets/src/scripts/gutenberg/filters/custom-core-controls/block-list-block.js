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

		if (
			typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
			Array.isArray(CUSTOMIZE_BLOCKS[name])
		) {
			let customClassName = "";
			let customBlockStyle = {};

			if (attributes.justifyContent) {
				customBlockStyle.justifyContent = attributes.justifyContent;
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
