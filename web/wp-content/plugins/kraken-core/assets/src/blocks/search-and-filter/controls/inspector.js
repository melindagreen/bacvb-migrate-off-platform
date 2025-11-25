/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { PanelBody } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useEffect } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";

// Local Dependencies
import QuerySettings from "./query-settings";
import CardSettings from "./card-settings";
import FilterBarSettings from "./filter-bar-settings";
import PaginationSettings from "./pagination-settings";
import ColorControls from "./color-controls";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = (props) => {
	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();

	const taxonomies = useSelect(
		(select) => {
			const query = [
				"root",
				"taxonomy",
				{
					type: attributes.postType,
					per_page: -1,
				},
			];
			return {
				results: select(coreDataStore).getEntityRecords(...query),
				hasStartedResolution: select(coreDataStore).hasStartedResolution(
					"getEntityRecords",
					query
				),
				hasFinishedResolution: select(coreDataStore).hasFinishedResolution(
					"getEntityRecords",
					query
				),
				isResolving: select(coreDataStore).isResolving(
					"getEntityRecords",
					query
				),
			};
		},
		[attributes.postType]
	);

	useEffect(() => {
		if (taxonomies.results && taxonomies.results.length) {
			//sort taxonomy results A-Z
			taxonomies.results.sort((a, b) => a.slug.localeCompare(b.slug));

			//check if the currently selected query type still exists & then select the first option if it does not exist.
			let exists = taxonomies.results.some((x) => {
				return x.slug === attributes.taxonomyQueryType;
			});
			if (!exists) {
				setAttributes({ taxonomyQueryType: taxonomies.results[0].slug });
			}
		}
	}, [taxonomies]);

	return (
		<>
			<InspectorControls group="settings">
				<div className={blockProps.className}>
					<QuerySettings {...props} taxonomies={taxonomies} />
					<CardSettings {...props} />
					<PanelBody title="Filter Bar Settings" initialOpen={false}>
						<FilterBarSettings {...props} taxonomies={taxonomies} />
					</PanelBody>
					<PanelBody title="Pagination Settings" initialOpen={false}>
						<PaginationSettings {...props} />
					</PanelBody>
				</div>
			</InspectorControls>
			<InspectorControls group="styles">
				<ColorControls {...props} />
			</InspectorControls>
		</>
	);
};

export default Inspector;
