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

const MediaControls = (props) => {
  const { attributes, setAttributes } = props;
  const { annamariabeachImage, beanpointImage, manateebeachImage, holmesbeachImage, cortezbeachImage, coquinabeachImage, beercanislandImage, whitneybeachImage } = attributes;

  return (
    <>
        <MediaUploadCheck>
            <MediaUpload
                title={__('Choose Anna Maria Beach Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    annamariabeachImage: selected.url,
                })}
                value={annamariabeachImage}
                render={({ open }) => (
                    <>
                        {annamariabeachImage && <img
                            src={annamariabeachImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Anna Maria Beach Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Bean Point Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    beanpointImage: selected.url,
                })}
                value={beanpointImage}
                render={({ open }) => (
                    <>
                        {beanpointImage && <img
                            src={beanpointImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Bean Point Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Manatee Beach Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    manateebeachImage: selected.url,
                })}
                value={manateebeachImage}
                render={({ open }) => (
                    <>
                        {manateebeachImage && <img
                            src={manateebeachImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Manatee Beach Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Holmes Beach Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    holmesbeachImage: selected.url,
                })}
                value={holmesbeachImage}
                render={({ open }) => (
                    <>
                        {holmesbeachImage && <img
                            src={holmesbeachImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Holmes Beach Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Cortez Beach Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    cortezbeachImage: selected.url,
                })}
                value={cortezbeachImage}
                render={({ open }) => (
                    <>
                        {cortezbeachImage && <img
                            src={cortezbeachImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Cortez Beach Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Coquina Beach Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    coquinabeachImage: selected.url,
                })}
                value={coquinabeachImage}
                render={({ open }) => (
                    <>
                        {coquinabeachImage && <img
                            src={coquinabeachImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Coquina Beach Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Beer Can Island Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    beercanislandImage: selected.url,
                })}
                value={beercanislandImage}
                render={({ open }) => (
                    <>
                        {beercanislandImage && <img
                            src={beercanislandImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Beer Can Island Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
            <MediaUpload
                title={__('Choose Whitney Beach Image', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={selected => setAttributes({
                    whitneybeachImage: selected.url,
                })}
                value={whitneybeachImage}
                render={({ open }) => (
                    <>
                        {whitneybeachImage && <img
                            src={whitneybeachImage}
                        />}
                        <Button onClick={open} isLarge icon="format-image" isSecondary>
                            {__('Whitney Beach Image', THEME_PREFIX)}
                        </Button>
                    </>
                )}
            />
        </MediaUploadCheck>
    </>
  );
};


const Inspector = props => {
    const { attributes: { annamariabeach, beanpoint, manateebeach, holmesbeach, cortezbeach, coquinabeach, beercanisland, whitneybeach }, setAttributes } = props;

    return (
        <InspectorControls>
            <PanelBody title='Beach Images'>
                <MediaControls {...props} />
            </PanelBody>
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
