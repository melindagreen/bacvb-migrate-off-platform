/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';
import { cloneElement } from '@wordpress/element';

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from './constants';

/*** FUNCTIONS ****************************************************************/

/**
 * Apply any needed customizations to the resultant saved element & its attributes
 * @param {E} el 
 * @param {*} block 
 * @param {*} attributes 
 * @returns 
 */
const applyCustomAttrs = (el, block, attributes) => {
    const { name } = block;

    // if customizations exist...
    if (
        typeof CUSTOMIZE_BLOCKS[name] !== 'undefined' &&
        Array.isArray(CUSTOMIZE_BLOCKS[name])
    ) {
        let newProps = { ...el.props };

        // default wrapper prp, has no impact on output
        // overwrite to wrap output in new element

        // NOTE 20220125 this solution is not ideal as only the
        // last overwrite will apply, but directly modding was causing
        // issues, refactor later? also why isn't this using children? -ashw
        let ElWrap = ({ content }) => <>{content}</>;

        // parse through matching customizations
        CUSTOMIZE_BLOCKS[name].forEach((customization) => {
            switch (customization) {
                case 'wraparound-link':
                    if (typeof attributes.wraparoundLink.url !== 'undefined') {
                        // overwrite wrap func with anchor tag
                        ElWrap = ({content}) => <a
                            href={attributes.wraparoundLink.url}
                            target={attributes?.wraparoundLink?.opensInNewTab ? '_blank' : '_self'}
                            className='wp-block-cover-link'
                            rel="noopener"
                        >
                          {content}
                        </a>;
                    }
                break;
                case 'overlap':
                    if(attributes.overlap !== 0) {
                        
                        let margin = '-' + Math.abs(attributes.overlap) + 'rem';
                        if(attributes.overlap > 0) {
                        newProps.style = { ...newProps.style, marginTop: margin };
                        }
                        else {
                        newProps.style = { ...newProps.style, marginBottom: margin };
                        }
                    }
                break;
                case 'layer':
                    if(attributes.layer !== 0) {
                        const layerIndex = {
                            top: 1,
                            middle: 0,
                            bottom: -1
                        }
                        newProps.style = { ...newProps.style, zIndex: layerIndex[attributes.layer], position: 'relative' };
                    }
                break;
            }
        });

        // return modified element
        return <ElWrap content={cloneElement(el, newProps)} />;
    }

    return el;
}

/*** EXPORTS ****************************************************************/

export default {
    name: 'personalization-attributes',
    hook: 'blocks.getSaveElement',
    action: applyCustomAttrs,
};
