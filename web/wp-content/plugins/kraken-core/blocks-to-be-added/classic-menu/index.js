/*** IMPORTS ****************************************************************/

import { registerBlockType } from "@wordpress/blocks";
import { menu as icon } from "@wordpress/icons";
import edit from "./edit";
import save from "./save";
import metadata from "./block.json";

// Styles
import "./styles/style.scss";
import "./styles/index.scss";

registerBlockType(metadata, {
	icon,
	edit,
	save,
});
