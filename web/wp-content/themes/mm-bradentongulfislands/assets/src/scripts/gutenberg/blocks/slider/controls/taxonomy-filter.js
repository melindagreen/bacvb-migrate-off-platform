/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { Spinner, ComboboxControl, SelectControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";
import Repeater from '../../../components/repeater';

/*** COMPONENTS **************************************************************/
const TaxonomyFilter = (props) => {
	const { attributes, setAttributes } = props;
	const [defaultOption, setDefaultOption] = useState(null);

	const taxonomies = useSelect((select) => {
      const query = [
        "root", "taxonomy",
        {
          type: attributes.postType,
					per_page: -1
        }      
      ];
      return {
        results: select(coreDataStore).getEntityRecords(...query),
        hasStartedResolution: select(coreDataStore).hasStartedResolution('getEntityRecords', query),
        hasFinishedResolution: select(coreDataStore).hasFinishedResolution('getEntityRecords', query),
        isResolving: select(coreDataStore).isResolving('getEntityRecords', query)
      }
	  },	
    [attributes.enableTaxFilter, attributes.postType]
  );

  const taxonomyOptions = taxonomies.results?.map(tax => ({
    label: tax.name,
    value: tax.slug
  }));
  
  const termResults = useSelect((select) => {
      const query = [
        "taxonomy", attributes.taxonomyFilter,
        {
          per_page: -1,
        }      
      ];
      return {
        terms: select(coreDataStore).getEntityRecords(...query),
        hasStartedResolution: select(coreDataStore).hasStartedResolution('getEntityRecords', query),
        hasFinishedResolution: select(coreDataStore).hasFinishedResolution('getEntityRecords', query),
        isResolving: select(coreDataStore).isResolving('getEntityRecords', query)
      }
    },	
    [attributes.taxonomyFilter, attributes.postType]
	);

  const renderTerms = () => {
		let options = [];

		if (termResults.terms) {
			options.push({
				value: 0,
				label: __("Select terms..."),
			});
			termResults.terms.forEach((term) => {
				///only return options that are not already selected.
				let skip = attributes.taxonomyTerms.some((x) => {
					return x.id === term.id;
				});

				if (!skip) {
					options.push({ value: term.id, label: term.name });
				}
			});
		} else {
			options.push({ value: 0, label: __("Loading...") });
		}

		return options;
  }
	const [filteredTerms, setFilteredTerms] = useState(renderTerms());
  
  //updates selected terms
	const selectTerm = (id) => {
		if (id && id !== 0) {
			let content = termResults.terms.find((term) => term.id == id);
			let newTerms = [...attributes.taxonomyTerms];

			newTerms.push({
				id: content.id,
				title: content.name
			});

			setAttributes({ taxonomyTerms: newTerms });

			//reset and unfocus the combobox
			setDefaultOption(null);
			document.activeElement.blur();
		}
	};

	return (
		<>
      {taxonomies.hasFinishedResolution && taxonomies.results && taxonomies.results.length ? (
        <SelectControl
          label={__("Taxonomy Filter")}
          value={attributes.taxonomyFilter}
          options={taxonomyOptions}
          onChange={(val) => {
            setAttributes({ taxonomyTerms: [] });
            setAttributes({ taxonomyFilter: val });
          }}
        />
      ) : (
        <>
          <Spinner /> Loading taxonomies...
        </>
      )}
      {termResults.hasFinishedResolution && termResults.terms && termResults.terms.length ? (
				<ComboboxControl
					label={__("Select terms")}
          options={filteredTerms}
					value={defaultOption}
					onChange={(val) => selectTerm(val)}
					onFilterValueChange={(inputValue) =>
						setFilteredTerms(
							renderTerms().filter((option) =>
								option.label.toLowerCase().includes(inputValue.toLowerCase())
							)
						)
					}
				/>
      ) : (
        <>
          <Spinner /> Loading terms...
        </>
      )}

			<Repeater
				segments={attributes.taxonomyTerms}
				onChange={(val) => setAttributes({taxonomyTerms: val})}
				placeholderText={__('Add terms above')}
				segmentsContent={attributes.taxonomyTerms.map((term, index) => (
					<div>{term.title}</div>
				))}
			/>
		</>
	);
};

export default TaxonomyFilter;
