/*** IMPORTS ****************************************************************/

import { registerBlockType } from "@wordpress/blocks";
import edit from "./edit";
import save from "./save";
import metadata from "./block.json";

// Styles
import "./styles/style.scss";
import "./styles/index.scss";

registerBlockType(metadata, {
	edit,
	save,
});
