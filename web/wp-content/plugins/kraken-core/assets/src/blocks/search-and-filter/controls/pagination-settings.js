/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { SelectControl, ToggleControl } from "@wordpress/components";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/
const PaginationSettings = (props) => {
	const { attributes, setAttributes } = props;

	return (
		<>
			<SelectControl
				label={__("Pagination Style")}
				value={attributes.paginationStyle}
				options={[
					{ label: "Load more button", value: "load-more" },
					{ label: "Page numbers", value: "page-numbers" },
					{ label: "None", value: "none" },
				]}
				onChange={(val) => {
					setAttributes({ paginationStyle: val });
				}}
			/>
			<ToggleControl
				label={__("Display Results Count")}
				checked={attributes.displayResultsCount}
				onChange={() => {
					setAttributes({
						displayResultsCount: !attributes.displayResultsCount,
					});
				}}
			/>
		</>
	);
};

export default PaginationSettings;
