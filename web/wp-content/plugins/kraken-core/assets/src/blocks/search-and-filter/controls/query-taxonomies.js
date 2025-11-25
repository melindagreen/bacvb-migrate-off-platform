/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { Spinner, SelectControl, CheckboxControl, TextControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { store as coreDataStore } from "@wordpress/core-data";
import { useState, useMemo } from "@wordpress/element";

/*** COMPONENTS **************************************************************/
const TaxonomyQuery = (props) => {
  const { attributes, setAttributes, taxonomies } = props;
  const [searchTerm, setSearchTerm] = useState("");

  const taxonomyOptions = taxonomies.results?.map((tax) => ({
    label: tax.name,
    value: tax.slug,
  }));

  const termResults = useSelect(
    (select) => {
      const query = [
        "taxonomy",
        attributes.taxonomyQueryType,
        {
          per_page: -1,
        },
      ];
      return {
        terms: select(coreDataStore).getEntityRecords(...query),
        hasStartedResolution: select(coreDataStore).hasStartedResolution("getEntityRecords", query),
        hasFinishedResolution: select(coreDataStore).hasFinishedResolution(
          "getEntityRecords",
          query,
        ),
        isResolving: select(coreDataStore).isResolving("getEntityRecords", query),
      };
    },
    [attributes.taxonomyQueryType, attributes.postType],
  );

  // Helper function to decode HTML entities
  const decodeHtmlEntities = (text) => {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = text;
    return textarea.value;
  };

  // Build hierarchical term structure
  const buildHierarchicalTerms = useMemo(() => {
    if (!termResults.terms) return [];

    const termsMap = new Map();
    const rootTerms = [];

    // First pass: create map of all terms with decoded names
    termResults.terms.forEach((term) => {
      termsMap.set(term.id, {
        ...term,
        name: decodeHtmlEntities(term.name),
        children: [],
      });
    });

    // Second pass: build hierarchy
    termResults.terms.forEach((term) => {
      if (term.parent === 0) {
        rootTerms.push(termsMap.get(term.id));
      } else {
        const parent = termsMap.get(term.parent);
        if (parent) {
          parent.children.push(termsMap.get(term.id));
        }
      }
    });

    return rootTerms;
  }, [termResults.terms]);

  // Filter terms based on search
  const filteredTerms = useMemo(() => {
    if (!searchTerm.trim()) return buildHierarchicalTerms;

    const searchLower = searchTerm.toLowerCase();
    const filtered = [];
    const addedParents = new Set();

    const searchInHierarchy = (terms) => {
      terms.forEach((term) => {
        const matches = term.name.toLowerCase().includes(searchLower);
        const hasMatchingChildren = term.children.some((child) =>
          child.name.toLowerCase().includes(searchLower),
        );

        if (matches || hasMatchingChildren) {
          // Add parent if not already added
          if (!addedParents.has(term.id)) {
            filtered.push(term);
            addedParents.add(term.id);
          }

          // Add matching children
          term.children.forEach((child) => {
            if (child.name.toLowerCase().includes(searchLower) && !addedParents.has(child.id)) {
              filtered.push(child);
              addedParents.add(child.id);
            }
          });
        }

        // Recursively search in children
        if (term.children.length > 0) {
          searchInHierarchy(term.children);
        }
      });
    };

    searchInHierarchy(buildHierarchicalTerms);
    return filtered;
  }, [buildHierarchicalTerms, searchTerm]);

  // Render hierarchical terms recursively
  const renderHierarchicalTerms = (terms, depth = 0) => {
    return terms.map((term) => (
      <div key={term.id}>
        <CheckboxControl
          label={`${"— ".repeat(depth)}${term.name}`}
          checked={attributes.taxonomyQueryTerms.includes(term.id)}
          onChange={(isChecked) => selectQueryTerms(isChecked, term.id)}
        />
        {term.children &&
          term.children.length > 0 &&
          renderHierarchicalTerms(term.children, depth + 1)}
      </div>
    ));
  };

  //updates selected query terms
  const selectQueryTerms = (isChecked, termId) => {
    let terms = [...attributes.taxonomyQueryTerms];
    if (isChecked) {
      terms.push(termId);
    } else {
      terms = terms.filter((x) => x !== termId);
    }
    setAttributes({ taxonomyQueryTerms: terms });
  };

  return (
    <>
      {taxonomies.hasFinishedResolution ? (
        <>
          {taxonomies.results && taxonomies.results.length ? (
            <>
              <SelectControl
                label={__("Taxonomy Type")}
                value={attributes.taxonomyQueryType}
                options={taxonomyOptions}
                onChange={(val) => {
                  setAttributes({ taxonomyQueryTerms: [] });
                  setAttributes({ taxonomyQueryType: val });
                }}
              />

              {termResults.hasFinishedResolution ? (
                <>
                  {termResults.terms && termResults.terms.length ? (
                    <div className="taxonomy-term-checklist">
                      <div className="taxonomy-term-header">
                        <label className="components-base-control__label">
                          {__(`Select Terms (${attributes.taxonomyQueryTerms.length})`)}
                        </label>
                        {attributes.taxonomyQueryTerms.length > 0 && (
                          <button
                            className="clear-all-button"
                            onClick={() => setAttributes({ taxonomyQueryTerms: [] })}
                          >
                            Clear All
                          </button>
                        )}
                      </div>

                      {/* Selected Term Chips */}
                      {attributes.taxonomyQueryTerms.length > 0 && (
                        <div className="selected-terms-chips">
                          {attributes.taxonomyQueryTerms.map((termId) => {
                            const term = termResults.terms?.find((t) => t.id === termId);
                            return term ? (
                              <button
                                key={termId}
                                onClick={() => selectQueryTerms(false, termId)}
                                className="term-chip"
                              >
                                {decodeHtmlEntities(term.name)}
                                <span className="term-chip-remove">×</span>
                              </button>
                            ) : null;
                          })}
                        </div>
                      )}

                      {/* Search Input */}
                      <TextControl
                        placeholder="Search terms..."
                        value={searchTerm}
                        onChange={(value) => setSearchTerm(value)}
                        className="term-search-input"
                      />

                      <div className="term-checklist-wrapper">
                        {searchTerm.trim()
                          ? renderHierarchicalTerms(filteredTerms)
                          : renderHierarchicalTerms(buildHierarchicalTerms)}
                      </div>
                    </div>
                  ) : (
                    <p style={{ margin: "-12px 0 24px" }}>No terms found for this taxonomy.</p>
                  )}
                </>
              ) : (
                <>
                  <Spinner /> Loading terms...
                </>
              )}
            </>
          ) : (
            <p style={{ margin: "-12px 0 24px" }}>No taxonomies found for this post type.</p>
          )}
        </>
      ) : (
        <>
          <Spinner /> Loading taxonomies...
        </>
      )}
    </>
  );
};

export default TaxonomyQuery;
