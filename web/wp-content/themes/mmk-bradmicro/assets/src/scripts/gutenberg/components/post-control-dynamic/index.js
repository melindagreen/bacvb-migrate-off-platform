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

/*** COMPONENTS **************************************************************/

const PostControlDynamic = ({
  controlType = "token",
  postType,
  label = __("Select Posts", "madden-theme"),
  value,
  onChange,
}) => {
  const { posts } = useSelect((select) => {
    const { getEntityRecords } = select("core");

    return {
      posts: getEntityRecords("postType", postType, {
        per_page: -1,
        orderby: "title",
        order: "asc",
      }),
    };
  });

  let options = [];
  if (posts) {
    options = posts.map((post) => {
      return { value: post.slug, label: post.title.rendered };
    });
  }

  const selectPost = (slug) => {
    if (slug && "" !== slug) {
      let findPost = posts.find((post) => post.slug === slug);
      let newPosts = Array.isArray(value) ? [...value] : [];
      newPosts.push({
        id: findPost.id,
        slug: findPost.slug,
        title: findPost.title.rendered,
      });
      onChange(newPosts);
      document.activeElement.blur();
    }
  };

  const tokenValue = () => {
    const tValue = value
      ? value.map((post) => post.title.replace("&amp;", "&"))
      : [];
    return tValue;
  };

  const tokenSuggestions = () => {
    const tSuggestions = posts
      ? posts.map((post) => post.title.rendered.replace("&amp;", "&"))
      : [];
    return tSuggestions;
  };

  const tokenChange = (selectedPosts) => {
    const updatedPosts = selectedPosts.reduce((postArray, postName) => {
      let findPost = posts.find(
        (post) =>
          post.title.rendered.replace("&amp;", "&") ==
          postName.replace("&amp;", "&")
      );
      if (findPost) {
        postArray.push({
          id: findPost.id,
          slug: findPost.slug,
          title: findPost.title.rendered,
        });
      }
      return postArray;
    }, []);

    onChange(updatedPosts);
  };

  return (
    <>
      {!posts && <Spinner />}
      {!!posts && "token" === controlType && (
        <FormTokenField
          label={label}
          value={tokenValue()}
          suggestions={tokenSuggestions()}
          onChange={(selected) => tokenChange(selected)}
        />
      )}
      {!!posts && "select" === controlType && (
        <ComboboxControl
          label={label}
          options={options}
          value={value}
          onChange={(selected) => {
            selectPost(selected);
          }}
        />
      )}
    </>
  );
};

export default PostControlDynamic;
