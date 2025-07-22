/**
 * This directory contains filters that hook into core Gutenberg functionality
 * and modify the output
 *
 * Read about Gutenberg hooks on the Madden Wiki!
 * https://wiki.maddenmedia.com/Working_With_Gutenberg_Hooks
 */

/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { addFilter } from "@wordpress/hooks";
import { __ } from "@wordpress/i18n";

// Local Dependencies
import { THEME_PREFIX } from "../../inc/constants";
import {
	applyCustomizations,
	customAttributes,
	customBlockEdit,
	customBlockList,
	extraProps,
} from "./custom-core-controls";

/*** CONSTANTS **************************************************************/

const ALL_FILTERS = [
	applyCustomizations,
	customAttributes,
	customBlockEdit,
	customBlockList,
	extraProps,
];

/*** FUNCTIONS **************************************************************/

const addAllFilters = () =>
	ALL_FILTERS.forEach((filter) => {
		addFilter(filter.hook, THEME_PREFIX + "/" + filter.name, filter.action);
	});

/*** EXPORTS ****************************************************************/
export default addAllFilters;
