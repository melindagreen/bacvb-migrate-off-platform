/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { PanelBody, PanelRow, SelectControl } from '@wordpress/components'
import { } from '@wordpress/block-editor'
import { useSelect, } from '@wordpress/data';


// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/


const Inspector = props => {
    const { attributes: { queryMode }, setAttributes } = props;

    return (
        <InspectorControls>
            {/* <PanelBody title={__('Grid Settings')}>
                <PanelRow>
                    <SelectControl
                        label={__('Query mode')}
                        value={queryMode}
                        options={[
                            {
                                label: 'Manual',
                                value: 'manual'
                            }
                        ]}
                        onChange={queryMode => setAttributes({ queryMode })}
                    />
                </PanelRow>
            </PanelBody> */}
        </InspectorControls>
    )
}

export default Inspector;
