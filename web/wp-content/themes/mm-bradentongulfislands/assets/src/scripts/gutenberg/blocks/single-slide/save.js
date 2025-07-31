import { InnerBlocks } from "@wordpress/block-editor";

const save = () => {
	return (
		<div className="wp-block-mm-bradentongulfislands-single-slide swiper-slide">
			<InnerBlocks.Content />
		</div>
	);
};

/*** EXPORTS ****************************************************************/
export default save;
