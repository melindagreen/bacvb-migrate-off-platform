/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

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

					case "z-index":
						settings.attributes = {
							...settings.attributes,
							zIndex: {
								type: "number",
								default: 0,
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

					case "hide-on-mobile":
						settings.attributes = {
							...settings.attributes,
							hideOnMobile: {
								type: "boolean",
								default: false,
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
							}
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
								"type": "object",
								"default": {}
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
