import { InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
	const {
		attributes: { title, description },
		className,
	} = props;

	return (
		<section className="wp-block-mm-bradentongulfislands-accordion">
			{title !== "" && (
				<div className="section-title">
					<h2>{title}</h2>
					{description !== "" && <p>{description}</p>}
				</div>
			)}
			<InnerBlocks.Content />
		</section>
	);
};

/*** EXPORTS ****************************************************************/
export default Save;
