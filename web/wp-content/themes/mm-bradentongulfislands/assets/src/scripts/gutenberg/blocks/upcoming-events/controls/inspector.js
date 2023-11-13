/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody, TextControl } from '@wordpress/components'
import { MediaUpload, MediaUploadCheck, URLInput } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { TaxonomyControl } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const MediaControls = props => {
    const { attributes, setAttributes } = props;
    const { defaultThumb } = attributes;
    const onSelect = (images) => {
        setAttributes({
            defaultThumb: images.map(image => image.id),
        });
    };

    return (
        <>
            <MediaUploadCheck>
                <MediaUpload
                    title={__('Choose Images', THEME_PREFIX)}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    onSelect={selected => setAttributes({
                        defaultThumb: selected.url,
                    })}
                    value={defaultThumb}
                    render={({ open }) => (
                        <>
                            {defaultThumb && <img
                                className='listings-grid__default-thumb'
                                src={defaultThumb}
                            />}
                            <Button onClick={open} isLarge icon="format-image" isSecondary>
                                {__('Choose Thumbnail', THEME_PREFIX)}
                            </Button>
                        </>
                    )}
                />
            </MediaUploadCheck>
        </>
    );
}

const Inspector = props => {
    const { attributes: { title, ctaText, ctaURL }, setAttributes } = props;

    return (
        <InspectorControls>
            <PanelBody title='Upcoming Events Info'>
                <TextControl
                    label="Title"
                    onChange={ ( title ) => setAttributes( { title } ) }
                    value={ title }
                />
                <MediaControls {...props} />
            </PanelBody>
            <PanelBody title='CTA'>
                <TextControl
                    label="CTA Text"
                    onChange={ ( ctaText ) => setAttributes( { ctaText } ) }
                    value={ ctaText }
                />
                <URLInput
                    label="URL"
                    value={ ctaURL }
                    onChange={ ( ctaURL ) => setAttributes({ ctaURL })}
                />
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
