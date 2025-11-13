import { registerBlockType } from "@wordpress/blocks";
import Edit from "./edit";
import Save from "./save";
import metadata from "./block.json";
//import { preformatted as icon } from "@wordpress/icons";
import "./styles/index.scss";
import "./styles/style.scss";

registerBlockType(metadata, {
	edit: Edit,
	save: Save,
});
