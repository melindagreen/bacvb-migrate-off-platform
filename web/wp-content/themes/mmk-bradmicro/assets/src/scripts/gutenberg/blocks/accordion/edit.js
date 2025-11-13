/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";

// Local Dependencies
import Controls from "./controls";

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = ["madden-theme/accordion-section"];
const BLOCK_TEMPLATE = [["madden-theme/accordion-section", {}]];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props
 * @returns {WPElement}
 */
const Editor = (props) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InnerBlocks allowedBlocks={ALLOWED_BLOCKS} template={BLOCK_TEMPLATE} />
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
