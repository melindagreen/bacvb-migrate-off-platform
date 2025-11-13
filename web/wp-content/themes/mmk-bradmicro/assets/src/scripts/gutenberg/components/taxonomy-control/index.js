/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { FormTokenField, SelectControl, Spinner } from "@wordpress/components";
import { withSelect } from "@wordpress/data";
import "./styles/index.scss";

/*** COMPONENTS **************************************************************/

/**
 * Component that renders a form control for selecting taxonomy terms
 *
 * @example
 * <TaxonomyControl
 *      controlType='token'
 *      taxonomySlug='category'
 *      label={__('Filter by categories:')}
 *      value={filterCategories}
 *      onChange={filterCategories => setAttributes({filterCategories})}
 * />
 */
const TaxonomyControl = ({ controlType, label, value, onChange, terms }) => {
	if (!terms || !terms.length) return <Spinner />;

	return (
		<div className="tax-control">
			{(() => {
				// Return appropriate control type
				switch (controlType) {
					case "token":
						return (
							<FormTokenField
								className="tax-control__input tax-control__input--token"
								label={label}
								suggestions={terms.map((term) =>
									term.name.replace("&amp;", "&")
								)}
								// Map slug values to pretty names
								value={value.map(
									(valueTerm) =>
										terms
											.filter((term) => term.slug === valueTerm)
											.map((term) => term.name.replace("&amp;", "&"))[0]
								)}
								// Map pretty names to slug values
								onChange={(selected) =>
									onChange(
										selected.map(
											(selectedTerm) =>
												terms
													.filter(
														(term) =>
															term.name.replace("&amp;", "&") === selectedTerm
													)
													.map((term) => term.slug)[0]
										)
									)
								}
							/>
						);
					case "select":
					default:
						return (
							<SelectControl
								className="tax-control__input tax-control__input--select"
								label={label}
								options={[
									{
										value: "none",
										label: "None selected",
									},
									// Map terms to option objects
									...terms.map((term) => {
										return {
											value: term.slug,
											label: term.name.replace("&amp;", "&"),
										};
									}),
								]}
								value={value[0]}
								onChange={(selected) =>
									onChange(selected === "none" ? [] : [selected])
								}
							/>
						);
				}
			})()}
		</div>
	);
};

/*** EXPORTS ****************************************************************/
export default withSelect((select, ownProps) => {
	const data = {};

	// Select all terms for given taxonomy
	if (ownProps.taxonomySlug) {
		data.terms = select("core").getEntityRecords(
			"taxonomy",
			ownProps.taxonomySlug,
			{ per_page: -1 }
		);
	}

	return data;
})(TaxonomyControl);
