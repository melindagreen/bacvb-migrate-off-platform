/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const edit = () => {
	const ALLOWED_BLOCKS = [
		"core/cover",
		"core/group",
		"core/columns",
		"core/column",
		"core/heading",
		"core/paragraph",
		"core/list",
		"core/image",
		"core/embed",
		"core/html",
		"core/buttons",
		"madden-theme/content-card",
		"madden-theme/responsive-spacer",
	];

	const SLIDE_TEMPLATE = [["core/paragraph", {}]];

	const blockProps = useBlockProps({
		allowedblocks: ALLOWED_BLOCKS,
		template: SLIDE_TEMPLATE,
	});

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedblocks: ALLOWED_BLOCKS,
		template: SLIDE_TEMPLATE,
	});

	return <div {...innerBlocksProps} />;
};

/*** EXPORTS ****************************************************************/

export default edit;
