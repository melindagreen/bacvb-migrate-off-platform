import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';

// Styles
import "./styles/index.scss";

registerBlockType(metadata.name, {
	edit: Edit,
	save,
});