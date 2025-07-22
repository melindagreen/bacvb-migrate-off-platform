/*** IMPORTS ***************************************************************/

// WordPress Dependencies
const { getBlockVariations, registerBlockVariation } = wp.blocks;
const { __ } = wp.i18n;

import { THEME_PREFIX } from "../../inc/constants";

/**
 * Register the [MM] Featured Stories variation for core/query block.
 */
const mmDefaultPost = () => {
    const variations = getBlockVariations("core/query");
    if (!variations || !variations.some((variation) => "mm-default-post" === variation.name)) {
        registerBlockVariation("core/query", {
            name: "mm-default-post",
            isDefault: false,
            title: __("[MM] Featured Stories", THEME_PREFIX),
            description: __("[MM] Image, Title, Readmore", THEME_PREFIX),
            icon: "welcome-widgets-menus",
            attributes: {
                className: "is-style-featured-stories",
                namespace: "mm-default-post",
                query: {
                    perPage: 3,
                    pages: 0,
                    offset: 0,
                    postType: "post",
                    order: "desc",
                    orderBy: "date"
                }
            },
            innerBlocks: [
                [
                    "core/post-template",
                    {},
                    [
                        [
                            "core/group",
                            { className: "wp-block-post__featured-image" },
                            [
                                ["core/post-featured-image", { sizeSlug: "large" }]
                            ]
                        ],
                        [
                            "core/group",
                            { className: "wp-block-post__content" },
                            [
                                ["core/post-title"],
                                ["core/read-more"]
                            ]
                        ]
                    ]
                ]
            ],
            isActive: ["namespace"]
        });
    }
};

/*** FUNCTIONS **************************************************************/

const addAllVariations = () => {
    mmDefaultPost();
};

/*** EXPORTS ****************************************************************/
export default addAllVariations;
