/*** IMPORTS ****************************************************************/

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from './constants';

/*** FUNCTIONS ****************************************************************/

/**
 * Customize extra props of modified elements for use w/ customizations
 * @param {*} props 
 * @param {*} blockType 
 * @param {*} attributes 
 * @returns 
 */
const customizeExtraProps = (props, blockType, attributes) => {
    const { name } = blockType;

    // check for matching customizations
    if (
        typeof CUSTOMIZE_BLOCKS[name] !== 'undefined' &&
        Array.isArray(CUSTOMIZE_BLOCKS[name])
    ) {
        // parse through matching customizations and extend props
        CUSTOMIZE_BLOCKS[name].map(
            (customization) => {
                switch (customization) {
                    case 'justify-content':
                        Object.assign(props, {style: { ...props.style, justifyContent: attributes.justifyContent }});
                        break;
                }
            });
    }

    return props;
}

/*** EXPORTS ****************************************************************/

export default {
    name: 'extra-props',
    hook: 'blocks.getSaveContent.extraProps',
    action: customizeExtraProps,
}