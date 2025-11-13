/*** IMPORTS ****************************************************************/

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** FUNCTIONS ****************************************************************/

const withUnit = (val) => {
	if (val === undefined || val === null || val === "") return "auto";
	// match common CSS length units
	if (
		/^-?\d+(\.\d+)?(px|%|em|rem|vh|vw|vmin|vmax|ch|ex|cm|mm|in|pt|pc)$/.test(
			val
		)
	) {
		return val;
	}
	// if it's just a number, add px
	if (!isNaN(val)) {
		return `${val}px`;
	}
	// fallback
	return val;
};

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
	if (
		typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
		Array.isArray(CUSTOMIZE_BLOCKS[name])
	) {
		// parse through matching customizations and extend props
		CUSTOMIZE_BLOCKS[name].map((customization) => {
			switch (customization) {
				case "wraparound-link":
					if (attributes.wraparoundLink) {
						props.wrapperProps = {
							...props.wrapperProps,
							link: attributes.wraparoundLink,
						};
					}
					break;

				case "content-width-settings":
					if (attributes?.enableMaxWidth) {
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
						Object.assign(props, {
							style: {
								...props.style,
								position: `relative`,
								zIndex: `${attributes.zIndex}`,
							},
						});
					}
					break;

				case "responsive-grid-columns":
					if (attributes?.enableResponsiveGridCols) {
						Object.assign(props, {
							style: {
								...props.style,
								"--tablet-grid-cols": `${attributes.tabletGridCols}`,
								"--mobile-grid-cols": `${attributes.mobileGridCols}`,
							},
						});
					}
					break;

				case "rotate-element":
					if (attributes?.rotateElement && attributes.rotateElement !== 0) {
						Object.assign(props, {
							style: {
								...props.style,
								"--rotate-element": `${attributes.rotateElement}deg`,
							},
							className: `${
								props.className ? props.className + " " : ""
							}has-rotate-element`,
						});
					}
					break;

				case "absolute-position":
					if (attributes.enableAbsolutePosition) {
						const positionCss = {
							position: "absolute",
							left: withUnit(attributes.absolutePositions.left),
							right: withUnit(attributes.absolutePositions.right),
							top: withUnit(attributes.absolutePositions.top),
							bottom: withUnit(attributes.absolutePositions.bottom),
						};
						Object.assign(props, {
							style: {
								...props.style,
								...positionCss,
							},
						});
					}
					break;

				case "responsive-sizes":
					if (attributes?.enableResponsiveSizes) {
						Object.assign(props, {
							style: {
								...props.style,
								"--tablet-width": `${attributes.tabletWidth}`,
								"--mobile-width": `${attributes.mobileWidth}`,
							},
						});
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
