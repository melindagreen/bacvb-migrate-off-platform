/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import ServerSideRender from '@wordpress/server-side-render';

// Local Dependencies
// Controls - add block/inspector controls here
import Controls from "./controls";
import Wizard from "./controls/wizard";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const Editor = (props) => {
	const {
		attributes: { mode },
	} = props;

	return mode === "edit" ? (
		<Wizard {...props} />
	) : (
		<ServerSideRender block={props.name} {...props} />
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