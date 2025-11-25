/*** IMPORTS ***************************************************************/
import { addFilter } from "@wordpress/hooks";

import customAttributes from "./attributes";
import customBlockList from "./block-list-block";
import customBlockEdit from "./block-edit";
import extraProps from "./extra-props";

/*** FUNCTIONS **************************************************************/

// Register filters immediately (not in domReady) to ensure they're applied consistently
const ALL_FILTERS = [customAttributes, customBlockEdit, customBlockList, extraProps];

ALL_FILTERS.forEach((filter) =>
  addFilter(filter.hook, "kraken-core/" + filter.name, filter.action),
);
