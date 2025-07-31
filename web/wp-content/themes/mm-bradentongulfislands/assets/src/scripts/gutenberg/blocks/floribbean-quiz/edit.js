/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import ServerSideRender from "@wordpress/server-side-render";
import { useBlockProps } from "@wordpress/block-editor";
import { TextControl } from "@wordpress/components";
import { useEffect } from "@wordpress/element";

// Local dependencies
import { initFloribbeanQuiz } from "./assets/floribbean-quiz";
// Controls - add block/inspector controls here
import Controls from "./controls";

/*** CONSTANTS **************************************************************/

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props
 * @returns {WPElement}
 */
const Wizard = (props) => {
	const { attributes, setAttributes } = props;

	return <div style={{ maxWidth: "50rem", margin: "auto" }}></div>;
};

/**
 * The editor for the block
 * @param {*} props
 * @returns {WPElement}
 */
const Editor = (props) => {
	const blockProps = useBlockProps();
	const { attributes, className } = props;

	useEffect(() => {
		initFloribbeanQuiz();
	}, [attributes]);

	// Ensure that `attributes` does not contain invalid data
	const safeAttributes = typeof attributes === "object" ? attributes : {};

	return (
		<section {...blockProps}>
			{/* ServerSideRender not working */}
			<ServerSideRender block={props.name} httpMethod={"POST"} {...props} />
		</section>
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
