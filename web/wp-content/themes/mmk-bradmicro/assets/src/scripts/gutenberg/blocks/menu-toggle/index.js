import { registerBlockType } from "@wordpress/blocks";
import edit from "./edit";
import save from "./save";
import metadata from "./block.json";
import "./styles/style.scss";

registerBlockType(metadata, {
	edit,
	save,
});
