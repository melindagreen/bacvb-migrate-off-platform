import {
	InnerBlocks,
	getColorClassName,
	RichText,
} from "@wordpress/block-editor";

const Save = (props) => {
	const {
		attributes: { title, textColor },
		className,
	} = props;

	let titleClass = "accordion-section__title ";
	if (typeof textColor !== undefined)
		titleClass += getColorClassName("color", textColor);

	return (
		<section className="wp-block-mm-bradentongulfislands-accordion-section">
			<header className="accordion__header">
				<RichText.Content tagName="h3" className={titleClass} value={title} />
				<span className="fusion-toggle-icon-wrapper" aria-hidden="true">
					<div className="arrow"></div>
				</span>
			</header>
			<div className="accordion__body">
				<InnerBlocks.Content />
			</div>
		</section>
	);
};

/*** EXPORTS ****************************************************************/
export default Save;
