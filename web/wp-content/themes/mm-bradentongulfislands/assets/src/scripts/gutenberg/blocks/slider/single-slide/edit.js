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
    "core/buttons",
    "core/heading",
    "core/paragraph",
    "core/list",
    "core/image",
    "core/embed",
    "core/html",
    "mmnino/responsive-spacer",
    "mmnino/content-card"
  ];

	const SLIDE_TEMPLATE = [["core/paragraph", {}]];

	const blockProps = useBlockProps({
		allowedBlocks: ALLOWED_BLOCKS,
		template: SLIDE_TEMPLATE
	});

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: SLIDE_TEMPLATE
  });

  return (
    <div {...innerBlocksProps} />
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
