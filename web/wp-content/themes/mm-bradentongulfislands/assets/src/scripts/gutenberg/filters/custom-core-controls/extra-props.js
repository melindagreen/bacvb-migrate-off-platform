/*** IMPORTS ****************************************************************/

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

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
	if (
		typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
		Array.isArray(CUSTOMIZE_BLOCKS[name])
	) {
		// parse through matching customizations and extend props
		CUSTOMIZE_BLOCKS[name].map((customization) => {
			switch (customization) {

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
					
				case "responsive-sizes":
					if (attributes?.enableResponsiveSizes) {
					Object.assign(props, {
						style: {
						...props.style,
						"--tablet-width": `${attributes.tabletWidth}`,
						"--mobile-width": `${attributes.mobileWidth}`,
						}
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
