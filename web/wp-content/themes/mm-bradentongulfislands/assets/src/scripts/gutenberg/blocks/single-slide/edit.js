/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const edit = () => {
	const ALLOWED_BLOCKS = ["mm-bradentongulfislands/content-card"];

	const SLIDE_TEMPLATE = [["mm-bradentongulfislands/content-card", {}]];

	// Only pass valid DOM attributes here
	const blockProps = useBlockProps();

	// allowedBlocks/template should go here
	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: SLIDE_TEMPLATE,
	});

	return <div {...innerBlocksProps} />;
};

/*** EXPORTS ****************************************************************/

export default edit;
