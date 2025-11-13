/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import {
	RichText,
	InnerBlocks,
	useBlockProps,
	InspectorControls,
	withColors,
	PanelColorSettings,
} from "@wordpress/block-editor";
import { useState } from "@wordpress/element";

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = [
	"core/paragraph",
	"core/heading",
	"core/image",
	"core/list",
	"core/quote",
	"core/html",
	"core/buttons",
	"core/button",
	"core/columns",
];
const BLOCK_TEMPLATE = [["core/paragraph", {}]];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props
 * @returns {WPElement}
 */
function Edit(props) {
	const {
		titleColor,
		setTitleColor,
		titleBackgroundColor,
		setTitleBackgroundColor,
		isSelected,
		attributes: { title },
		setAttributes,
		open,
		setState,
	} = props;

	const [showContent, setShowContent] = useState(false);

	const block = `wp-block-madden-theme-accordion-section`;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelColorSettings
					title={__("Toggle Colors", "madden-theme")}
					initialOpen={true}
					colorSettings={[
						{
							value: titleColor.color,
							onChange: setTitleColor,
							label: __("Title Color", "madden-theme"),
						},
						{
							value: titleBackgroundColor.color,
							onChange: setTitleBackgroundColor,
							label: __("Background Color", "madden-theme"),
						},
					]}
				/>
			</InspectorControls>
			<div
				className={`${block}__header`}
				style={{
					color: titleColor.color,
					backgroundColor: titleBackgroundColor.color,
				}}
			>
				<span
					className={`${block}__header__icon`}
					aria-hidden="true"
					onClick={() => setShowContent(!showContent)}
					style={showContent ? { transform: "rotate(180deg)" } : null}
				>
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
				<RichText
					placeholder={__("Section Title")}
					tagName="h3"
					value={title}
					onChange={(title) => setAttributes({ title })}
					className={`${block}__title`}
					style={{
						color: titleColor.color,
					}}
				/>
			</div>
			{showContent && (
				<div className={`${block}__body`}>
					<div className={`${block}__body__content`}>
						<InnerBlocks
							allowedBlocks={ALLOWED_BLOCKS}
							template={BLOCK_TEMPLATE}
						/>
					</div>
				</div>
			)}
		</div>
	);
}
export default withColors("titleColor", "titleBackgroundColor")(Edit);
