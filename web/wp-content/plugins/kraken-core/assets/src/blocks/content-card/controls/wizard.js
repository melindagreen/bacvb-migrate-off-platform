/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
  ComboboxControl,
  SelectControl,
  Button,
  ResponsiveWrapper,
  TextControl,
  Spinner,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState, useEffect, useCallback } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";
import apiFetch from "@wordpress/api-fetch";
import {
  useBlockProps,
  MediaUpload,
  MediaUploadCheck,
  __experimentalLinkControl as LinkControl,
} from "@wordpress/block-editor";

import { getCardStyles, getIgnoredPostTypes } from "../../../filters/helpers";
import CardContent from "./card-content";

/*** UTILITIES **************************************************************/

// Debounce utility function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Decode HTML entities in text
function decodeHtmlEntities(text) {
  if (!text) return text;

  const textarea = document.createElement("textarea");
  textarea.innerHTML = text;
  return textarea.value;
}

// Extract title from post object with proper HTML entity decoding
function extractPostTitle(post) {
  let title = "";

  // Check different possible title structures
  if (post.title && post.title.raw) {
    title = post.title.raw;
  } else if (post.title && typeof post.title === "string") {
    title = post.title;
  } else if (post.title && post.title.rendered) {
    title = post.title.rendered;
  } else if (post.acf && post.acf.post_title) {
    title = post.acf.post_title;
  } else {
    // Fallback to post title field
    title = post.post_title || post.name || `Post ${post.id}`;
  }

  // Decode HTML entities
  return decodeHtmlEntities(title);
}

/*** FUNCTIONS **************************************************************/

const Wizard = (props) => {
  const { attributes, setAttributes } = props;
  const blockProps = useBlockProps();

  const { posts, hasResolved } = useSelect(
    (select) => {
      const query = [
        "postType",
        attributes.postType,
        {
          per_page: 50, // Load only first 50 posts initially
          status: "publish",
          order: "desc",
          orderby: "date",
        },
      ];
      if (attributes.postType !== "custom" && attributes.postType !== "queried_post") {
        return {
          posts: select(coreDataStore).getEntityRecords(...query),
          hasResolved: select(coreDataStore).hasFinishedResolution("getEntityRecords", query),
        };
      } else {
        return { posts: null };
      }
    },
    [attributes.postType],
  );

  // State for search functionality
  const [searchResults, setSearchResults] = useState([]);
  const [isSearching, setIsSearching] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");

  // Debounced search function
  const debouncedSearch = useCallback(
    debounce(async (query, postType) => {
      if (!query || query.length < 2) {
        setSearchResults([]);
        setIsSearching(false);
        return;
      }

      setIsSearching(true);
      try {
        // Search for both the original query and HTML entity encoded version
        const encodedQuery = query.replace(/&/g, "&amp;");

        //Built in post types need to be plural
        let endpoint = postType;
        if (postType === "page") {
          endpoint = "pages";
        } else if (postType === "post") {
          endpoint = "posts";
        }

        const searchPath = `/wp/v2/${endpoint}?search=${encodeURIComponent(
          query,
        )}&per_page=50&status=publish`;

        // If the query contains &, also search for the encoded version
        let response;
        if (query.includes("&") && query !== encodedQuery) {
          // Search for both versions and combine results
          const [response1, response2] = await Promise.all([
            apiFetch({ path: searchPath }),
            apiFetch({
              path: `/wp/v2/${endpoint}?search=${encodeURIComponent(
                encodedQuery,
              )}&per_page=50&status=publish`,
            }),
          ]);

          // Combine and deduplicate results
          const combined = [...response1, ...response2];
          const uniqueIds = new Set();
          response = combined.filter((post) => {
            if (uniqueIds.has(post.id)) return false;
            uniqueIds.add(post.id);
            return true;
          });
        } else {
          response = await apiFetch({ path: searchPath });
        }

        const searchOptions = response.map((post) => {
          const title = extractPostTitle(post);
          return { value: post.id, label: title };
        });
        setSearchResults(searchOptions);
      } catch (error) {
        console.error("Search error:", error);
        setSearchResults([]);
      } finally {
        setIsSearching(false);
      }
    }, 300),
    [],
  );

  // Calculate options based on current state
  const getOptions = () => {
    // If user is searching, show search results
    if (searchQuery && searchQuery.length >= 2) {
      return searchResults;
    }

    let options = [];

    // Build the initial list from the fetched posts.
    if (posts) {
      options = posts.map((post) => ({
        value: post.id,
        label: extractPostTitle(post),
      }));
    }

    // If a post is selected (contentId exists) but it's not in our current
    // list of `options`, we manually add it to the start of the array.
    // This ensures the ComboboxControl can find a match and display its title.
    if (
      attributes.contentId &&
      attributes.contentTitle &&
      !options.some((option) => option.value === attributes.contentId)
    ) {
      options.unshift({
        value: attributes.contentId,
        label: attributes.contentTitle,
      });
    }

    // Handle the initial loading state when `posts` is still null.
    if (!posts && attributes.postType !== "custom" && attributes.postType !== "queried_post") {
      if (attributes.contentId) {
        // If loading but we have a saved post, show that.
        return [
          {
            label: __(attributes.contentTitle),
            value: attributes.contentId,
          },
        ];
      }
      // Otherwise, show a generic loading message.
      return [{ value: 0, label: __("Loading...") }];
    }

    return options;
  };

  const [filteredOptions, setFilteredOptions] = useState(getOptions());

  // Update filtered options when posts or search results change
  useEffect(() => {
    setFilteredOptions(getOptions());
  }, [posts, searchResults, searchQuery]);

  const selectPost = (id) => {
    if (id && id !== 0) {
      // Try to find the post in either posts or searchResults
      let content = posts?.find((post) => post.id == id);

      // If not found in posts, try search results
      if (!content && searchResults.length > 0) {
        //Built in post types need to be plural
        let endpoint = attributes.postType;
        if (attributes.postType === "page") {
          endpoint = "pages";
        } else if (attributes.postType === "post") {
          endpoint = "posts";
        }

        // We need to fetch the full post data for search results
        apiFetch({
          path: `/wp/v2/${endpoint}/${id}`,
        })
          .then((post) => {
            const contentTitle = extractPostTitle(post);
            setAttributes({
              contentId: post.id,
              contentTitle: contentTitle,
              mode: "preview",
            });
          })
          .catch((error) => {
            console.error("Error fetching post:", error);
          });
        return;
      }

      if (content) {
        const contentTitle = extractPostTitle(content);
        setAttributes({
          contentId: content.id,
          contentTitle: contentTitle,
          mode: "preview",
        });
      }
    }
  };

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

  //remove ignoredPostTypes from our post type results
  const ignoredPostTypes = getIgnoredPostTypes();
  const postTypeOptions = postTypes.results
    ?.filter((type) => {
      return !ignoredPostTypes.includes(type.slug);
    })
    .map((type) => ({
      label: type.labels.name,
      value: type.slug,
    }));

  if (Array.isArray(postTypeOptions)) {
    postTypeOptions.push({
      label: __("Custom", "kraken-core"),
      value: "custom",
    });
  }

  if (Array.isArray(postTypeOptions)) {
    postTypeOptions.push({
      label: __("Queried Post", "kraken-core"),
      value: "queried_post",
    });
  }

  return (
    <div className={`${blockProps.className}`}>
      <SelectControl
        label={__("Card Style")}
        value={attributes.cardStyle}
        options={getCardStyles()}
        onChange={(val) => {
          setAttributes({ cardStyle: val });
        }}
      />
      <SelectControl
        label={__("Content Type")}
        value={attributes.postType}
        options={postTypeOptions}
        onChange={(val) => {
          setAttributes({ postType: val });
        }}
      />
      {attributes.postType === "custom" ? (
        <>
          <TextControl
            label={__("Content Title")}
            value={attributes.contentTitle}
            onChange={(val) => {
              setAttributes({ contentTitle: val });
            }}
          />
          <TextControl
            label={__("CTA Text")}
            value={attributes.customCtaText}
            onChange={(val) => {
              setAttributes({ customCtaText: val });
            }}
          />
          <LinkControl
            key={props.clientId}
            label={__("CTA URL")}
            value={attributes.customCtaUrl}
            onChange={(val) => {
              setAttributes({ customCtaUrl: val });
            }}
          />
          <MediaUploadCheck>
            <MediaUpload
              title={__("Upload Image")}
              allowedTypes={["image"]}
              onSelect={(images) => {
                setAttributes({
                  customImage: {
                    id: images.id,
                    url: images.url,
                  },
                });
              }}
              value={attributes.customImage.id}
              render={({ open }) => (
                <div className="image-select">
                  <Button onClick={open} isLarge icon="format-gallery">
                    {__("Select Image")}
                  </Button>
                  {attributes.customImage.url != "" && (
                    <ResponsiveWrapper>
                      <img src={attributes.customImage.url} />
                    </ResponsiveWrapper>
                  )}
                </div>
              )}
            />
          </MediaUploadCheck>
        </>
      ) : (
        <>
          {hasResolved && posts && posts.length ? (
            <ComboboxControl
              label={__("Select content")}
              options={filteredOptions}
              value={attributes.contentId}
              onChange={(val) => selectPost(val)}
              onFilterValueChange={(inputValue) => {
                setSearchQuery(inputValue);
                debouncedSearch(inputValue, attributes.postType);
              }}
            />
          ) : (
            <>
              {attributes.postType !== "queried_post" && (
                <>
                  <Spinner /> Loading options...
                </>
              )}
            </>
          )}

          <hr />
          <CardContent {...props} />
        </>
      )}
    </div>
  );
};

/*** EXPORTS ****************************************************************/

export default Wizard;
