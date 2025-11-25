/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { RadioControl, SelectControl, ToggleControl } from "@wordpress/components";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/
const FilterBarSettings = (props) => {
  const { attributes, setAttributes, taxonomies } = props;

  const taxonomyOptions = taxonomies.results?.map((tax) => ({
    label: tax.name,
    value: tax.slug,
  }));

  //updates selected taxonomy filters
  const selectFilter = (slug) => {
    let tax = taxonomies.results.find((x) => x.slug == slug);
    let filters = [...attributes.taxonomyFilters];
    if (filters.includes(tax.slug)) {
      filters = filters.filter((x) => x !== tax.slug);
    } else {
      filters.push(tax.slug);
    }
    setAttributes({ taxonomyFilters: filters });
  };

  return (
    <>
      <ToggleControl
        label={__("Enable Filter Bar")}
        help={__("Allows users to filter & sort the results")}
        checked={attributes.enableFilterBar}
        onChange={() => {
          setAttributes({
            enableFilterBar: !attributes.enableFilterBar,
          });
        }}
      />
      {attributes.enableFilterBar && (
        <>
          <ToggleControl
            label={__("Display as Sidebar")}
            checked={attributes.displayFilterSidebar}
            onChange={() => {
              setAttributes({
                displayFilterSidebar: !attributes.displayFilterSidebar,
              });
            }}
          />
          <ToggleControl
            label={__("Enable Search Input")}
            checked={attributes.enableSearchInput}
            onChange={() => {
              setAttributes({
                enableSearchInput: !attributes.enableSearchInput,
              });
            }}
          />
          {attributes.postType && attributes.postType.includes("event") && (
            <>
              <ToggleControl
                label={__("Enable Start Date Filter")}
                checked={attributes.enableStartDateFilter}
                onChange={() => {
                  setAttributes({
                    enableStartDateFilter: !attributes.enableStartDateFilter,
                  });
                }}
              />
              <ToggleControl
                label={__("Enable End Date Filter")}
                checked={attributes.enableEndDateFilter}
                onChange={() => {
                  setAttributes({
                    enableEndDateFilter: !attributes.enableEndDateFilter,
                  });
                }}
              />
            </>
          )}
          <ToggleControl
            label={__("Enable Taxonomy Filters")}
            checked={attributes.enableTaxonomyFilter}
            onChange={() => {
              setAttributes({
                enableTaxonomyFilter: !attributes.enableTaxonomyFilter,
              });
            }}
          />
          {attributes.enableTaxonomyFilter && (
            <>
              <SelectControl
                label={__(`Select Taxonomy Filters (${attributes.taxonomyFilters.length})`)}
                options={taxonomyOptions}
                value={attributes.taxonomyFilters}
                multiple
                onChange={(val) => selectFilter(val)}
              />
              <ToggleControl
                label={__("Require All Terms")}
                checked={attributes.requireAllTerms}
                onChange={() => {
                  setAttributes({
                    requireAllTerms: !attributes.requireAllTerms,
                  });
                }}
              />
            </>
          )}
          <ToggleControl
            label={__("Enable Sorting")}
            checked={attributes.enableSortingFilter}
            onChange={() => {
              setAttributes({
                enableSortingFilter: !attributes.enableSortingFilter,
              });
            }}
          />
          <ToggleControl
            label={__("Enable Active Filter Count")}
            checked={attributes.enableActiveFilterCount}
            onChange={() => {
              setAttributes({
                enableActiveFilterCount: !attributes.enableActiveFilterCount,
              });
            }}
          />
          <ToggleControl
            label={__("Enable Active Filter Display")}
            checked={attributes.enableActiveFilterDisplay}
            onChange={() => {
              setAttributes({
                enableActiveFilterDisplay: !attributes.enableActiveFilterDisplay,
              });
            }}
          />
          <ToggleControl
            label={__("Enable Clear All Button")}
            checked={attributes.enableClearAllButton}
            onChange={() => {
              setAttributes({
                enableClearAllButton: !attributes.enableClearAllButton,
              });
            }}
          />
        </>
      )}
    </>
  );
};

export default FilterBarSettings;
