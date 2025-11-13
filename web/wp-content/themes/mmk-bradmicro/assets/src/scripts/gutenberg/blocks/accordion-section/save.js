import { InnerBlocks, RichText, useBlockProps } from "@wordpress/block-editor";

const Save = (props) => {
	const {
		attributes: {
			title,
			titleColor,
			customTitleColor,
			titleBackgroundColor,
			customTitleBackgroundColor,
		},
	} = props;

	const block = `wp-block-madden-theme-accordion-section`;

	let headerClasses = `${block}__header`;
	if (titleColor) {
		headerClasses += ` has-${titleColor}-color`;
	}
	if (titleBackgroundColor) {
		headerClasses += ` has-${titleBackgroundColor}-background-color`;
	}

	let headerStyles = {};
	if (customTitleColor) {
		headerStyles.color = customTitleColor;
	}
	if (customTitleBackgroundColor) {
		headerStyles.backgroundColor = customTitleBackgroundColor;
	}

	const blockProps = useBlockProps.save();

	return (
		<section {...blockProps}>
			<header className={headerClasses} style={headerStyles}>
				<span class={`${block}__header__icon`} aria-hidden="true">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="58.979"
						height="31.99"
						viewBox="0 0 58.979 31.99"
					>
						<path
							id="Path_95882"
							data-name="Path 95882"
							d="M-4594.966-2121.5l25.955,25.955,25.955-25.955"
							transform="translate(4598.501 2125.036)"
							fill="none"
							stroke="#2b7b7c"
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="5"
						/>
					</svg>
				</span>
				<RichText.Content
					tagName="h3"
					className={`${block}__header__title`}
					value={title}
				/>
			</header>
			<main className={`${block}__body`} style={{ height: 0 }}>
				<div className={`${block}__body__content`}>
					<InnerBlocks.Content />
				</div>
			</main>
		</section>
	);
};

/*** EXPORTS ****************************************************************/
export default Save;
