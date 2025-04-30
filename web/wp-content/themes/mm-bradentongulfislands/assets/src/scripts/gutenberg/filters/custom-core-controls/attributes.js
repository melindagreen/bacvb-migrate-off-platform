/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from './constants';

/*** FUNCTIONS ****************************************************************/

/**
 * Add new attributes to customized block
 * @param {*} settings 
 * @returns 
 */
const addCustomAttrs = (settings) => {
    if (typeof settings.attributes !== 'undefined') {
        if (
            typeof CUSTOMIZE_BLOCKS[settings.name] !== 'undefined' &&
            Array.isArray(CUSTOMIZE_BLOCKS[settings.name])
        ) {
            // parse through matching customizations and add new attrs
            CUSTOMIZE_BLOCKS[settings.name].forEach(
                (customization) => {
                    switch (customization) {
                        case 'justify-content':
                            settings.attributes = {
                                ...settings.attributes,
                                justifyContent: {
                                    type: 'string',
                                    default: 'center',
                                },
                            };

                        case 'reverse-mobile':
                            settings.attributes = {
                            ...settings.attributes,
                            reverseMobile: {
                                type: 'boolean',
                                default: false,
                            },
                        };

                        case 'photo-credit':
                            settings.attributes = {
                            ...settings.attributes,
                            photoCredit: {
                                type: 'boolean',
                                default: false,
                            },
                        };

                        case 'layer':
                            settings.attributes = {
                            ...settings.attributes,
                            layer: {
                            type: 'string',
                            default: 'middle',
                            },
                        };

                        case "center-on-mobile":
						settings.attributes = {
							...settings.attributes,
							centerOnMobile: {
								type: "boolean",
								default: false,
							},
						};
						break;

                        case 'lightbox-data':
                            settings.attributes = {
                            ...settings.attributes,
                            lbTitle: {
                            type: 'string',
                            default: '',
                            },
                            lbDescription: {
                            type: 'string',
                            default: '',
                            },
                            lbImageIds: {
                                type: 'array',
                                default: []
                              },
                            lbImageUrls: {
                                type: 'array',
                                default: []
                              },
                            lbImageAlts: {
                                type: 'array',
                                default: []
                            }

                        };

                        case 'overlap':
                            settings.attributes = {
                            ...settings.attributes,
                            overlap: {
                            type: 'number',
                            default: 0,
                            },
                        };

                        case 'wraparound-link':
                            settings.attributes = {
                                ...settings.attributes,
                                wraparoundLink: {
                                    type: 'object',
                                    default: {},
                                }
                            }
                    }
                }
            );
        }
    }

    return settings;
}

/*** EXPORTS ****************************************************************/

export default {
    name: 'personalization-attributes',
    hook: 'blocks.registerBlockType',
    action: addCustomAttrs,
};
