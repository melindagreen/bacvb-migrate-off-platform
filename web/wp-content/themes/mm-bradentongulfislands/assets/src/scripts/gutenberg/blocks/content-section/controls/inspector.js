/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls, MediaUpload, MediaUploadCheck, URLInput } from '@wordpress/block-editor';
import { PanelBody, PanelRow, Button, ResponsiveWrapper, Spinner , TextControl, TextareaControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const Inspector = props => {

        const { attributes, setAttributes } = props;
        const { sectionTitle } = attributes;
     

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody title="Section Info">
                        <PanelRow>
                            <TextControl
                                label="Title"
                                onChange={ ( sectionTitle ) => setAttributes( { sectionTitle } ) }
                                value={ sectionTitle }
                            />
                        </PanelRow>
                </PanelBody>
            </InspectorControls>
        </Fragment>
    )
}

export default Inspector;
