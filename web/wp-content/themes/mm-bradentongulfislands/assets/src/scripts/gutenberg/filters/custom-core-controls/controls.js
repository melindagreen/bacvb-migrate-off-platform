/*** IMPORTS ***************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';
import { createHigherOrderComponent, } from '@wordpress/compose';
import { InspectorControls, BlockControls, JustifyContentControl, __experimentalLinkControl as LinkControl } from '@wordpress/block-editor';
import { PanelBody, PanelRow, ToolbarButton, SelectControl, Popover, __experimentalNumberControl as NumberControl, ToggleControl } from '@wordpress/components';
import { useState } from '@wordpress/element';

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from './constants';

/*** FUNCTIONS ****************************************************************/

/**
 * Add custom controls to editor
 */
const withCustomControls = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        const {
            name,
            attributes,
            setAttributes,
        } = props;

        const [popoverOpen, setPopoverOpen] = useState(false);

        // check for matching customizations
        if (
            typeof CUSTOMIZE_BLOCKS[name] !== 'undefined' &&
            Array.isArray(CUSTOMIZE_BLOCKS[name])
        ) {

            // add wrapper to style within editor, otherwise styles aren't visible in editor
            return (
                <div className={`custom-style-wrapper justify-inner-${attributes.justifyContent}`}>
                    <BlockEdit {...props} />

                    <BlockControls group="block">
                        { // parse through matching customizations and add new toolbar controls
                            CUSTOMIZE_BLOCKS[name].map(
                                (customization) => {
                                    switch (customization) {
                                        case 'justify-content':
                                            return <JustifyContentControl
                                                value={attributes.justifyContent}
                                                onChange={justifyContent => setAttributes({ justifyContent })}
                                            />

                                        case 'wraparound-link':
                                            return <ToolbarButton
                                                icon='admin-links'
                                                label='Wraparound Link'
                                                onClick={() => setPopoverOpen(!popoverOpen)}
                                            />
                                    }
                                }
                            )}

                        {popoverOpen && <Popover>
                            {CUSTOMIZE_BLOCKS[name].map(
                                (customization) => {
                                    switch (customization) {
                                        case 'wraparound-link':
                                            return <LinkControl
                                                value={attributes.wraparoundLink}
                                                onChange={wraparoundLink => {
                                                    setAttributes({ wraparoundLink })
                                                }}
                                            />
                                    }
                                }
                            )}
                        </Popover>}
                    </BlockControls>

                    <InspectorControls>
                        { // parse through matching customizations and add new inspector controls
                            CUSTOMIZE_BLOCKS[name].map(
                                (customization) => {
                                    switch (customization) {
                                        case 'reverse-mobile':
                                           return <PanelBody><ToggleControl
                                                label="Reverse on Mobile"
                                                help={
                                                    attributes.reverseMobile
                                                        ? 'Columns are reversed on mobile'
                                                        : 'Columns are not reversed on mobile'
                                                }
                                                checked={ attributes.reverseMobile }
                                                onChange={ reverseMobile => {
                                                    setAttributes({ reverseMobile })
                                                } }
                                            /></PanelBody>
                                        case 'overlap':
                                            return <PanelBody><NumberControl
                                            onChange={ overlap => {
                                                overlap = parseInt(overlap);
                                                setAttributes({ overlap })
                                            } }
                                            isDragEnabled
                                            isShiftStepEnabled
                                            label={'Overlap'}
                                            max={100}
                                            min={-100}
                                            shiftStep={ 1 }
                                            step={1}
                                            value={ attributes.overlap }
                                            /></PanelBody>
                                        case 'layer': 
                                            return <PanelBody><SelectControl
                                            label="Layer"
                                            value={ attributes.layer }
                                            options={ [
                                                { label: 'Middle', value: 'middle' },
                                                { label: 'Top', value: 'top' },
                                                { label: 'Bottom', value: 'bottom' }
                                            ] }
                                            onChange={ layer => {
                                                setAttributes({ layer })
                                            } }
                                        /></PanelBody>
                                    }
                                }
                            )}
                    </InspectorControls>
                </div>
            );
        }

        return <BlockEdit {...props} />;
    };
});

/*** EXPORTS ***************************************************************/

export default {
    name: 'custom-controls',
    hook: 'editor.BlockEdit',
    action: withCustomControls,
};
