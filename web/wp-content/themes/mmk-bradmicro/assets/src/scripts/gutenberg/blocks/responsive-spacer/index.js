/*** IMPORTS ****************************************************************/

import { registerBlockType } from "@wordpress/blocks";
import edit from "./edit";
import save from "./save";
import metadata from "./block.json";
import deprecated from "./deprecated"; // Import deprecated versions

// Styles
import "./styles/style.scss";
import "./styles/index.scss";

registerBlockType(metadata, {
	edit,
	save,
	deprecated: deprecated,
});
