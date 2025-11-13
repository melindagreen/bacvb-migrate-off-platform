import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";

const Save = ({ attributes, className }) => {
	const { openFirstItem, autoCloseOthers } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<section
			{...blockProps}
			data-openFirstItem={openFirstItem}
			data-autoCloseOthers={autoCloseOthers}
		>
			<InnerBlocks.Content />
		</section>
	);
};

/*** EXPORTS ****************************************************************/
export default Save;
