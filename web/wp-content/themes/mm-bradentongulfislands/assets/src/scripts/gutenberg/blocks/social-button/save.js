import { RichText, useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
	const {
		attributes: { imageUrl, imageAlt, title },
		className,
	} = props;
	const blockProps = useBlockProps.save();

	return (
		<section className="wp-block-mm-bradentongulfislands-social-button">
			{imageUrl !== "" && (
				<div
					className={`wp-block-mm-bradentongulfislands-social-button__image`}
				>
					<img
						src={imageUrl}
						data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"}
						data-load-type="img"
						data-load-offset="lg"
						data-load-all={imageUrl}
					/>
				</div>
			)}

			<div
				className={`wp-block-mm-bradentongulfislands-social-button__contents`}
			>
				{
					<RichText.Content
						{...blockProps}
						className="contents-title"
						tagName="h3"
						value={title}
					/>
				}
				<InnerBlocks.Content />
			</div>
		</section>
	);
};

/*** EXPORTS ****************************************************************/
export default Save;
