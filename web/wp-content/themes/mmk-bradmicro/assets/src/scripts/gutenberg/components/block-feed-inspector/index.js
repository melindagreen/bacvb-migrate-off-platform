/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import {
	PanelBody,
	SelectControl,
	FormTokenField,
	__experimentalNumberControl as NumberControl,
} from "@wordpress/components";
import Repeater from "../repeater";
import PostControlDynamic from "../post-control-dynamic";
import TaxonomyControlDynamic from "../taxonomy-control-dynamic";

/*** COMPONENTS **************************************************************/
const BlockFeedInspector = (props) => {
	const { attributes, setAttributes } = props;
	const {
		sourceType,
		relatedPostTypes,
		orderby,
		order,
		numPosts,
		offset,
		selectedPosts,
		selectedTerms,
	} = attributes;

	let taxonomy = false;
	switch (sourceType) {
		case "post":
			taxonomy = "category";
			break;
		case "listing":
			taxonomy = "idss_listing_categories";
			break;
		default:
			taxonomy = false;
			break;
	}

	const orderbyOptions = () => {
		const options = [
			{ value: "none", label: __("None", "madden-theme") },
			{ value: "ID", label: __("ID", "madden-theme") },
			{ value: "author", label: __("Author", "madden-theme") },
			{ value: "title", label: __("Title", "madden-theme") },
			{ value: "name", label: __("Name", "madden-theme") },
			{ value: "type", label: __("Post Type", "madden-theme") },
			{ value: "date", label: __("Date", "madden-theme") },
			{ value: "modified", label: __("Modified", "madden-theme") },
			{ value: "parent", label: __("Parent", "madden-theme") },
			{ value: "rand", label: __("Random", "madden-theme") },
			{
				value: "menu_order",
				label: __("Menu Order", "madden-theme"),
			},
		];
		if (
			"related" !== sourceType &&
			"children" !== sourceType &&
			"siblings" !== sourceType
		) {
			options.push({
				value: "custom_order",
				label: __("Custom Order", "madden-theme"),
			});
		}
		return options;
	};

	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={__("Select a source", "madden-theme")}
					value={sourceType}
					onChange={(updatedSourceType) => {
						setAttributes({ sourceType: updatedSourceType });
					}}
					options={[
						{ value: "page", label: __("Pages", "madden-theme") },
						{ value: "post", label: __("Posts", "madden-theme") },
						{ value: "listing", label: __("Listings", "madden-theme") },
						{ value: "story", label: __("Stories", "madden-theme") },
						{ value: "children", label: __("Children", "madden-theme") },
						{ value: "siblings", label: __("Siblings", "madden-theme") },
						{ value: "related", label: __("Related Posts", "madden-theme") },
					]}
				/>
				{"related" === sourceType && (
					<FormTokenField
						label={__("Limit posts to these post types", "madden-theme")}
						value={relatedPostTypes}
						suggestions={["post", "page", "press-releases", "listing"]}
						onChange={(selected) => {
							setAttributes({ relatedPostTypes: selected });
						}}
						__nextHasNoMarginBottom
					/>
				)}
			</PanelBody>
			<PanelBody title={__("Query args", "madden-theme")}>
				{"related" !== sourceType && (
					<>
						<SelectControl
							label={__("Orderby", "madden-theme")}
							value={orderby}
							onChange={(updatedOrderby) => {
								setAttributes({ orderby: updatedOrderby });
							}}
							options={orderbyOptions()}
							__nextHasNoMarginBottom
						/>
						<SelectControl
							label={__("Order", "madden-theme")}
							value={order}
							onChange={(updatedOrder) => {
								setAttributes({ order: updatedOrder });
							}}
							options={[
								{ value: "ASC", label: __("ASC", "madden-theme") },
								{ value: "DESC", label: __("DESC", "madden-theme") },
							]}
							__nextHasNoMarginBottom
						/>
					</>
				)}
				<NumberControl
					label={__("Number of Posts", "madden-theme")}
					isShiftStepEnabled={true}
					onChange={(value) => {
						setAttributes({ numPosts: parseInt(value) });
					}}
					shiftStep={10}
					value={numPosts}
				/>
				{"related" !== sourceType && (
					<NumberControl
						label={__("Offset", "madden-theme")}
						isShiftStepEnabled={true}
						onChange={(value) => {
							setAttributes({ offset: parseInt(value) });
						}}
						shiftStep={10}
						value={offset}
					/>
				)}
			</PanelBody>
			{"related" !== sourceType &&
				"children" !== sourceType &&
				"siblings" !== sourceType && (
					<PanelBody title={__("Posts", "madden-theme")}>
						<PostControlDynamic
							controlType="token"
							postType={sourceType}
							label={__("Select Posts", "madden-theme")}
							value={selectedPosts}
							onChange={(newPosts) =>
								setAttributes({ selectedPosts: newPosts })
							}
						/>
						<Repeater
							segments={selectedPosts}
							onChange={(val) => setAttributes({ selectedPosts: val })}
							placeholderText={__("Add post")}
							segmentsContent={selectedPosts.map((post, index) => (
								<div>{post.title}</div>
							))}
						/>
					</PanelBody>
				)}

			{taxonomy && (
				<PanelBody title={__("Terms", "madden-theme")}>
					<TaxonomyControlDynamic
						controlType="token"
						taxonomy={taxonomy}
						label={__("Select Categories", "madden-theme")}
						value={selectedTerms}
						onChange={(newTerms) => setAttributes({ selectedTerms: newTerms })}
					/>
				</PanelBody>
			)}
		</InspectorControls>
	);
};

export default BlockFeedInspector;
