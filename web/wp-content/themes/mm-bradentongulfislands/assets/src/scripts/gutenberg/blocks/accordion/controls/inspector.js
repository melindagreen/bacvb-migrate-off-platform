/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody, PanelRow, TextControl } from '@wordpress/components'
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/


const Inspector = props => {

    const { attributes: { title, description }, setAttributes, className } = props;

    return (
        <InspectorControls>
            <PanelBody title="Accordion Settings">
                        <PanelRow>
                            <TextControl
                                label="Title"
                                onChange={ ( title ) => setAttributes( { title } ) }
                                value={ title }
                            />
                        </PanelRow>
                        <PanelRow>
                            <TextControl
                                label="Description"
                                onChange={ ( description ) => setAttributes( { description } ) }
                                value={ description }
                            />
                        </PanelRow>
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector 