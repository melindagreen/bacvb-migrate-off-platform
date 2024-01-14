/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody } from '@wordpress/components'
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { TaxonomyControl } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const MediaControls = props => {
    const { attributes, setAttributes } = props;
    const { exampleImageIds } = attributes;
    const onSelect = (images) => {
        setAttributes({
            exampleImageIds: images.map(image => image.id),
        });
    };

    return (
        <>
            <MediaUploadCheck>
                <MediaUpload
                    title={__('Choose Images', THEME_PREFIX)}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    gallery
                    multiple="add"
                    onSelect={onSelect}
                    value={exampleImageIds}
                    render={({ open }) => (
                        <Button onClick={open} isLarge icon="format-gallery" isSecondary>
                            {__('Choose Images', THEME_PREFIX)}
                        </Button>
                    )}
                />
            </MediaUploadCheck>
        </>
    );
}

const Inspector = props => {
    const { attributes: {  }, setAttributes } = props;

    return (
        <InspectorControls>

        </InspectorControls>
    )
}

export default Inspector;
