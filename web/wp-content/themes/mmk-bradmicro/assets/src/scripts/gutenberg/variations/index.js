/*** IMPORTS ***************************************************************/

// WordPress Dependencies
const { getBlockVariations, registerBlockVariation } = wp.blocks;
const { __ } = wp.i18n;

import { THEME_PREFIX } from "../../inc/constants";

/**
 * This is a core feature now; leaving as an example.
const groupGrid = () => {
	const variations = getBlockVariations("core/group");
	if (
		!variations ||
		!variations.some((variation) => "group-grid" === variation.name)
	) {
		registerBlockVariation("core/group", {
			name: "group-grid",
			title: __("Grid", THEME_PREFIX),
			icon: "grid-view",
			description: __("Arrange blocks in a grid.", THEME_PREFIX),
			attributes: {
				layout: {
					type: "grid",
				},
			},
			scope: ["block", "inserter", "transform"],
			isActive: (blockAttributes) => blockAttributes.layout?.type === "grid",
		});
	}
};
 */

/*** FUNCTIONS **************************************************************/

const addAllVariations = () => {
	//groupGrid();
};

/*** EXPORTS ****************************************************************/
export default addAllVariations;
