/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from "@wordpress/block-editor";

// Local Dependencies
// Controls - add block/inspector controls here
import Controls from "./controls";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const Editor = (props) => {

	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<ServerSideRender block={props.name} {...props} />
		</div>
	);
};

const edit = (props) => {
	return (
		<>
			<Controls {...props} />
			<Editor {...props} />
		</>
	);
};

/*** EXPORTS ****************************************************************/

export default edit;