/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody, PanelRow } from '@wordpress/components'
import { MediaUpload, MediaUploadCheck, URLInput } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const MediaControls = props => {
    const { attributes, setAttributes } = props;
    const { imageId } = attributes;
    const onSelect = (image) => {
        let smallImage = typeof image?.sizes?.madden_hero_md !== 'undefined'  ? image.sizes.madden_hero_md.url : image.url;
        console.log(image?.sizes?.madden_hero_md);
        setAttributes({
            imageUrl: smallImage,
            imageAlt: image.alt
        });
    };

    return (
        <>
            <MediaUploadCheck>
                <MediaUpload
                    title={__('Choose Image', THEME_PREFIX)}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    onSelect={onSelect}
                    value={imageId}
                    render={({ open }) => (
                        <Button onClick={open} isLarge icon="format-gallery" isSecondary>
                            {__('Choose Image', THEME_PREFIX)}
                        </Button>
                    )}
                />
            </MediaUploadCheck>
        </>
    );
}

const Inspector = props => {

    const { attributes, setAttributes } = props;
    const { buttonUrl } = attributes;

    return (
        <InspectorControls>
            <PanelBody
                title="Card Image"
            >
                <MediaControls {...props} />
            </PanelBody>
            <PanelBody title="Banner Content">
                  
                        <PanelRow>
                            <URLInput
                            label={__('Button Url')}
                            autoFocus={ true }
                            value={buttonUrl}
                            onChange={ ( buttonUrl ) => setAttributes({ buttonUrl })}
                            />
                        </PanelRow>
                    </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
