/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
  PanelBody,
  Spinner,
  SelectControl,
  ToggleControl,
  __experimentalNumberControl as NumberControl,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { store as coreDataStore } from "@wordpress/core-data";
import { applyFilters } from "@wordpress/hooks";

//Local dependencies
import ManualPostSelect from "./manual-post-select";
import TaxonomyQuery from "./query-taxonomies";
import { getIgnoredPostTypes } from "../../../filters/helpers";

/*** COMPONENTS **************************************************************/
const QuerySettings = (props) => {
  const { attributes, setAttributes, taxonomies } = props;

  //this will fetch all post types available via rest api
  const postTypes = useSelect((select) => {
    const query = [
      "root",
      "postType",
      {
        per_page: -1,
      },
    ];
    return {
      results: select(coreDataStore).getEntityRecords(...query),
      hasStartedResolution: select(coreDataStore).hasStartedResolution("getEntityRecords", query),
      hasFinishedResolution: select(coreDataStore).hasFinishedResolution("getEntityRecords", query),
      isResolving: select(coreDataStore).isResolving("getEntityRecords", query),
    };
  });

  //remove POST_TYPES_TO_IGNORE options from our post type results
  const ignoredPostTypes = getIgnoredPostTypes();
  const postTypeOptions = applyFilters(
    "kraken-core.searchAndFilterPostTypeOptions",
    postTypes.results
      ?.sort((a, b) => a.slug.localeCompare(b.slug))
      .filter((type) => {
        return !ignoredPostTypes.includes(type.slug);
      })
      .map((type) => ({
        label: type.labels.name,
        value: type.slug,
      })),
  );

  const contentTypes = applyFilters("kraken-core.searchAndFilterContentTypes", [
    { label: "Automatic Recent Posts", value: "automatic" },
    { label: "Specific Pages/Posts", value: "manual" },
    { label: "Related Posts (NLP)", value: "related" },
  ]);

  const orderbyOptions = applyFilters(
    "kraken-core.searchAndFilterOrderbyOptions",
    [
      {
        label:
          attributes.postType && attributes.postType.includes("event")
            ? "Event Date"
            : "Most Recent",
        value: "date",
      },
      { label: "Alphabetical", value: "title" },
      { label: "Random", value: "rand" },
      { label: "Manual Posts", value: "post__in" },
    ],
    attributes,
  );

  return (
    <>
      {postTypes.hasFinishedResolution && postTypes.results && postTypes.results.length ? (
        <>
          <PanelBody title="General Settings">
            <SelectControl
              label={__("Content Type")}
              value={attributes.contentType}
              options={contentTypes}
              onChange={(val) => {
                setAttributes({ contentType: val });
              }}
            />
            <SelectControl
              label={__("Post Type")}
              value={attributes.postType}
              options={postTypeOptions}
              onChange={(val) => {
                //reset the selected taxonomy for the query
                setAttributes({ taxonomyQueryTerms: [] });
                //reset the selected taxonomies for the filter bar
                setAttributes({ taxonomyFilters: [] });
                //update the selected post type
                setAttributes({ postType: val });
                //if switching to event cpt; update the default order & orderby values
                if (val.includes("event")) {
                  setAttributes({ orderBy: "date" });
                  setAttributes({ order: "asc" });
                }
              }}
            />
            {attributes.contentType === "manual" && <ManualPostSelect {...props} />}
            <SelectControl
              label={__(`View Style`)}
              value={attributes.enabledView}
              options={[
                { label: "Grid", value: "grid" },
                { label: "List", value: "grid-list" },
                { label: "Map", value: "map" },
                { label: "Grid & Map (Toggled)", value: "grid-map" },
              ]}
              onChange={(val) => {
                setAttributes({ enabledView: val });
              }}
            />
          </PanelBody>

          <PanelBody title="Query Settings" initialOpen={false}>
            <NumberControl
              label={__("Posts Per Page")}
              value={attributes.perPage}
              min={1}
              onChange={(val) => {
                setAttributes({ perPage: Number(val) });
              }}
            />
            <NumberControl
              label={__("Posts Per Page (Mobile)")}
              value={attributes.perPageMobile}
              min={1}
              onChange={(val) => {
                setAttributes({ perPageMobile: Number(val) });
              }}
            />
            <SelectControl
              label={__("Order By")}
              value={attributes.orderBy}
              options={orderbyOptions}
              onChange={(val) => {
                setAttributes({ orderBy: val });
              }}
            />
            <SelectControl
              label={__("Order ASC/DESC")}
              value={attributes.order}
              options={[
                { label: "Ascending", value: "asc" },
                { label: "Descending", value: "desc" },
              ]}
              onChange={(val) => {
                setAttributes({ order: val });
              }}
            />

            {"rand" === attributes.orderBy && (
              <ToggleControl
                label={__("Cache Random Posts")}
                help={__("Cache random posts for a day per user")}
                checked={attributes.cachePosts}
                onChange={() => {
                  setAttributes({
                    cachePosts: !attributes.cachePosts,
                  });
                }}
              />
            )}

            <ToggleControl
              label={__("Enable Taxonomy Query")}
              help={__("Limit the results by taxonomy terms")}
              checked={attributes.enableTaxonomyQuery}
              onChange={() => {
                setAttributes({
                  enableTaxonomyQuery: !attributes.enableTaxonomyQuery,
                });
              }}
            />

            {attributes.enableTaxonomyQuery && <TaxonomyQuery {...props} />}

            {attributes.postType && attributes.postType.includes("event") && (
              <>
                <ToggleControl
                  label={__("Enable Date Query")}
                  help={__("Limit the results by date")}
                  checked={attributes.enableDateQuery}
                  onChange={() => {
                    setAttributes({
                      enableDateQuery: !attributes.enableDateQuery,
                    });
                  }}
                />
                {attributes.enableDateQuery && (
                  <NumberControl
                    label={__("Days to Display")}
                    help={__(
                      "Set how many days of events to load by default. The start date will always be set to the current date.",
                    )}
                    value={attributes.selectedDateRange}
                    min={1}
                    onChange={(val) => {
                      setAttributes({ selectedDateRange: Number(val) });
                    }}
                  />
                )}
              </>
            )}
          </PanelBody>
        </>
      ) : (
        <>
          <Spinner /> Loading post types...
        </>
      )}
    </>
  );
};

export default QuerySettings;
