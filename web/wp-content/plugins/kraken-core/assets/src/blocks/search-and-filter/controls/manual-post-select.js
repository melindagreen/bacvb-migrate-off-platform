/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { ComboboxControl, Spinner } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";
import Repeater from "../../../scripts/components/repeater";

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/
const manualPostSelect = (props) => {
  const { attributes, setAttributes } = props;
  const [defaultOption, setDefaultOption] = useState(null);

  //retrieves the post/page options if user selects manual post as content type
  const results = useSelect(
    (select) => {
      const query = [
        "postType",
        attributes.postType,
        {
          per_page: -1,
          status: "publish",
          order: "desc",
          orderby: "date",
        },
      ];
      if (attributes.contentType === "manual") {
        return {
          posts: select(coreDataStore).getEntityRecords(...query),
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
      } else {
        return { posts: null };
      }
    },
    [attributes.contentType, attributes.postType]
  );

  //renders the combobox search/select options
  const renderPosts = () => {
    let options = [];

    if (results.posts) {
      options.push({
        value: 0,
        label: __("Select content..."),
      });
      results.posts.forEach((post) => {
        ///only return options that are not already selected.
        let skip = attributes.manualPosts.some((x) => {
          return x.id === post.id;
        });

        if (!skip) {
          options.push({ value: post.id, label: post.title.raw });
        }
      });
    } else {
      options.push({ value: 0, label: __("Loading...") });
    }

    return options;
  };
  const [filteredOptions, setFilteredOptions] = useState(renderPosts());

  //updates selected posts
  const selectPost = (id) => {
    if (id && id !== 0) {
      let content = results.posts.find((post) => post.id == id);
      let newPosts = [...attributes.manualPosts];

      newPosts.push({
        id: content.id,
        title: content.title.raw,
      });

      setAttributes({ manualPosts: newPosts });

      //reset and unfocus the combobox
      setDefaultOption(null);
      document.activeElement.blur();
    }
  };

  return (
    <>
      {results.hasFinishedResolution &&
      results.posts &&
      results.posts.length ? (
        <ComboboxControl
          label={__("Select content")}
          options={filteredOptions}
          value={defaultOption}
          onChange={(val) => selectPost(val)}
          onFilterValueChange={(inputValue) =>
            setFilteredOptions(
              renderPosts().filter((option) =>
                option.label.toLowerCase().includes(inputValue.toLowerCase())
              )
            )
          }
        />
      ) : (
        <>
          <Spinner /> Loading options...
        </>
      )}

      <Repeater
        segments={attributes.manualPosts}
        onChange={(val) => setAttributes({ manualPosts: val })}
        placeholderText={__("Add content above")}
        segmentsContent={attributes.manualPosts.map((post, index) => (
          <div>{post.title}</div>
        ))}
      />
    </>
  );
};

export default manualPostSelect;
