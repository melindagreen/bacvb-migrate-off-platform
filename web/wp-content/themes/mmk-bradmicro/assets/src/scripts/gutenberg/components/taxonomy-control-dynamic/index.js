/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	ComboboxControl,
	FormTokenField,
	Spinner,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import "./styles/index.scss";

const TaxonomyControlDynamic = ({
	controlType = "token",
	taxonomy,
	label = __("Select Terms", "madden-theme"),
	value,
	onChange,
}) => {
	const { terms } = useSelect((select) => {
		const { getEntityRecords } = select("core");

		return {
			terms: getEntityRecords("taxonomy", taxonomy, {
				per_page: -1,
				orderby: "name",
				order: "asc",
			}),
		};
	});

	let options = [];
	if (terms) {
		options = terms.map((term) => {
			return { value: term.slug, label: term.name };
		});
	}

	//update selected terms
	const selectTerm = (slug) => {
		if (slug && "" !== slug) {
			let findTerm = terms.find((term) => term.slug == slug);
			let newTerms = Array.isArray(value) ? [...value] : [];
			newTerms.push({
				id: findTerm.id,
				slug: findTerm.slug,
				title: findTerm.name,
			});

			onChange(newTerms);
			document.activeElement.blur();
		}
	};

	const tokenValue = () => {
		const tValue = value
			? value.map((term) => term.title.replace("&amp;", "&"))
			: [];
		return tValue;
	};

	const tokenSuggestions = () => {
		const tSuggestions = terms
			? terms.map((term) => term.name.replace("&amp;", "&"))
			: [];
		return tSuggestions;
	};

	const tokenChange = (selectedTerms) => {
		const updatedTerms = selectedTerms.reduce((termArray, termName) => {
			let findTerm = terms.find(
				(term) =>
					term.name.replace("&amp;", "&") == termName.replace("&amp;", "&")
			);
			if (findTerm) {
				termArray.push({
					id: findTerm.id,
					slug: findTerm.slug,
					title: findTerm.name,
				});
			}
			return termArray;
		}, []);

		onChange(updatedTerms);
	};

	return (
		<>
			{!terms && <Spinner />}
			{!!terms && "token" === controlType && (
				<FormTokenField
					label={label}
					value={tokenValue()}
					suggestions={tokenSuggestions()}
					onChange={(selected) => tokenChange(selected)}
				/>
			)}
			{!!terms && "select" === controlType && (
				<ComboboxControl
					label={label}
					options={options}
					value={value}
					onChange={(selected) => {
						selectTerm(selected);
					}}
				/>
			)}
		</>
	);
};

export default TaxonomyControlDynamic;
