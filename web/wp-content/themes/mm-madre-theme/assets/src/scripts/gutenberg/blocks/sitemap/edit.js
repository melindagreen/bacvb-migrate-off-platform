/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {
	ServerSideRender,
	CheckboxControl,
	TextControl,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
const { useSelect } = wp.data;

// Local Dependencies
// Controls - add block/inspector controls here
import Controls from "./controls";
import { POST_TYPES_TO_IGNORE } from "../../inc/constants";

/*** CONSTANTS **************************************************************/

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props
 * @returns {WPElement}
 */
const Wizard = (props) => {
	const {
		attributes: { sitemapTypes },
		setAttributes,
	} = props;

	const postTypes = useSelect(
		(select) => select("core").getPostTypes({ per_page: -1 }),
		[]
	);

	return (
		<>
			<div className="sitemap-types">
				<h3>Post types to display</h3>
				{postTypes &&
					postTypes.length &&
					postTypes
						.filter((type) => !POST_TYPES_TO_IGNORE.includes(type.slug))
						.map((type) => (
							<fieldset>
								<CheckboxControl
									label={type.name}
									checked={sitemapTypes[type.slug]}
									onChange={(checked) => {
										const newTypes = { ...sitemapTypes };
										newTypes[type.slug] = checked ? type.name : false;
										setAttributes({ sitemapTypes: newTypes });
									}}
								/>

								{sitemapTypes[type.slug] && (
									<TextControl
										label={`Label for ${type.name}`}
										value={sitemapTypes[type.slug]}
										onChange={(newLabel) => {
											const newTypes = { ...sitemapTypes };
											newTypes[type.slug] = newLabel;
											setAttributes({ sitemapTypes: newTypes });
										}}
									/>
								)}
							</fieldset>
						))}
			</div>
		</>
	);
};

/**
 * The editor for the block
 * @param {*} props
 * @returns {WPElement}
 */
const Editor = (props) => {
	const {
		attributes: { mode },
		className,
	} = props;

	return (
		<section className={className}>
			{mode === "edit" ? (
				<Wizard {...props} />
			) : (
				<ServerSideRender block={props.name} {...props} />
			)}
		</section>
	);
};

const edit = (props) => {
	return (
		<>
			<Controls {...props} />
			<Editor {...props} />
		</>
	);
};

/*** EXPORTS ****************************************************************/

export default edit;
