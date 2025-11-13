/*** EXPORTS ***************************************************************/

// Controls available customizations
export const CUSTOMIZE_BLOCKS = {
	"core/columns": [
		"hide-on-mobile",
		"reverse-order",
		"mobile-padding",
		"z-index",
	],
	"core/column": ["mobile-padding"],
	"core/group": ["responsive-grid-columns", "z-index", "wraparound-link"],
	"core/heading": ["center-on-mobile", "mobile-font-settings"],
	"core/list": ["flex-display"],
	"core/paragraph": ["center-on-mobile"],
	"core/image": [
		"center-on-mobile",
		"responsive-sizes",
		"absolute-position",
		"rotate-element",
	],
	"core/buttons": ["center-on-mobile"],
	"core/button": ["mobile-font-settings"],
	"core/separator": ["hide-on-mobile"],
};

export const CUSTOMIZE_SUPPORTS = {
	// "core/list": {
	// 	layout: {
	// 		allowEditing: true,
	// 		allowOrientation: true,
	// 		default: {
	// 			type: "flex",
	// 			orientation: "vertical",
	// 		},
	// 	},
	// 	spacing: {
	// 		blockGap: ["horizontal", "vertical"],
	// 	},
	// 	__experimentalLayout: {
	// 		allowSwitching: false,
	// 		default: {
	// 			type: "default",
	// 			justifyContent: "left",
	// 		},
	// 	},
	// },
};
