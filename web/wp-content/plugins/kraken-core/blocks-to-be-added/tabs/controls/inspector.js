import { InspectorControls } from '@wordpress/block-editor';
import ColorControls from './color-controls';

const Inspector = (props) => {
	return (
		<InspectorControls group="styles">
			<ColorControls {...props} />
		</InspectorControls>
	);
};

export default Inspector;