/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody, TextControl, TextareaControl  } from '@wordpress/components'
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
    const { attributes: { annamariabeach, beanpoint, manateebeach, holmesbeach, cortezbeach, coquinabeach, beercanisland, whitneybeach }, setAttributes } = props;

    return (
        <InspectorControls>
            <PanelBody title='Beach Content'>
                <TextareaControl
                    label="Anna Maria Beach Content"
                    onChange={ ( annamariabeach ) => setAttributes( { annamariabeach } ) }
                    value={ annamariabeach }
                />
                <TextareaControl
                    label="Bean Point Content"
                    onChange={ ( beanpoint ) => setAttributes( { beanpoint } ) }
                    value={ beanpoint }
                />
                <TextareaControl
                    label="Manatee Beach Content"
                    onChange={ ( manateebeach ) => setAttributes( { manateebeach } ) }
                    value={ manateebeach }
                />
                <TextareaControl
                    label="Holmes Beach Content"
                    onChange={ ( holmesbeach ) => setAttributes( { holmesbeach } ) }
                    value={ holmesbeach }
                />
                <TextareaControl
                    label="Cortez Beach Content"
                    onChange={ ( cortezbeach ) => setAttributes( { cortezbeach } ) }
                    value={ cortezbeach }
                />
                <TextareaControl
                    label="Coquina Beach Content"
                    onChange={ ( coquinabeach ) => setAttributes( { coquinabeach } ) }
                    value={ coquinabeach }
                />
                <TextareaControl
                    label="Beer Can Island Content"
                    onChange={ ( beercanisland ) => setAttributes( { beercanisland } ) }
                    value={ beercanisland }
                />
                <TextareaControl
                    label="Whitney Beach Content"
                    onChange={ ( whitneybeach ) => setAttributes( { whitneybeach } ) }
                    value={ whitneybeach }
                />
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
