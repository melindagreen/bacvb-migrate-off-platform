/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	__experimentalNumberControl as NumberControl,
	TextControl,
	ToggleControl,
} from "@wordpress/components";

/*** CONSTANTS **************************************************************/

/*** FUNCTIONS **************************************************************/

const CardContent = (props) => {
	const { attributes, setAttributes } = props;

	return (
		<>
			<ToggleControl
				label={__("Display Additional Content?")}
				checked={attributes.displayAdditionalContent}
				onChange={() => {
					setAttributes({
						displayAdditionalContent: !attributes.displayAdditionalContent,
					});
				}}
			/>
			{attributes.displayAdditionalContent && (
				<>
					<ToggleControl
						label={__("Excerpt")}
						checked={attributes.displayExcerpt}
						onChange={() => {
							setAttributes({ displayExcerpt: !attributes.displayExcerpt });
						}}
					/>
					{attributes.displayExcerpt && (
						<NumberControl
							label={__("Excerpt Word Count")}
							value={attributes.excerptLength}
							min={0}
							onChange={(val) => {
								setAttributes({ excerptLength: Number(val) });
							}}
						/>
					)}
					<hr />
					<ToggleControl
						label={__("Category")}
						checked={attributes.displayCategory}
						onChange={() => {
							setAttributes({ displayCategory: !attributes.displayCategory });
						}}
					/>
					<hr />
					<ToggleControl
						label={__("Read More Text")}
						checked={attributes.displayReadMore}
						onChange={() => {
							setAttributes({ displayReadMore: !attributes.displayReadMore });
						}}
					/>
					{attributes.displayReadMore && (
						<TextControl
							label={__("Customize Read More Text")}
							value={attributes.readMoreText}
							onChange={(val) => {
								setAttributes({ readMoreText: val });
							}}
						/>
					)}
				</>
			)}
		</>
	);
};

/*** EXPORTS ****************************************************************/

export default CardContent;
