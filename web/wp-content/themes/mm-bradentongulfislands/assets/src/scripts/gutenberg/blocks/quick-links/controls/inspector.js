/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { PanelBody, PanelRow, TextControl } from '@wordpress/components'
import { } from '@wordpress/block-editor'
import { useSelect, } from '@wordpress/data';


// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/


const Inspector = props => {
    const { attributes: { blockTitle }, setAttributes } = props;

    return (
        <InspectorControls>
            <PanelBody title={__('Quick Links Settings')}>
                <PanelRow>
                    <TextControl
                        label={__('Title')}
                        value={blockTitle}
                        onChange={blockTitle => setAttributes({ blockTitle })}
                    />
                </PanelRow>
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
