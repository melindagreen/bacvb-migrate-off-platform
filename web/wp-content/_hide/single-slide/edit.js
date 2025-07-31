/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/
const edit = () => {
  const ALLOWED_BLOCKS = [
    "mm-bradentongulfislands/content-card"
  ];

	const SLIDE_TEMPLATE = [["mm-bradentongulfislands/content-card", {}]];

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
