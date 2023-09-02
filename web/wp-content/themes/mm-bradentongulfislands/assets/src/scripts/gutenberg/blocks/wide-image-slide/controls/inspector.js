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
        const { mediaId, mediaUrl, title, info, buttonUrl} = attributes;
     
        const removeMedia = () => {
            setAttributes({
                mediaId: 0,
                mediaUrl: [],
                mediaAlt: ''
            });
        }
     
         const onSelectMedia = (media) => {
            let mediaLG = typeof media?.sizes?.full?.url !== undefined ? media.sizes.full.url : media.url;
            let mediaMD = mediaLG;
            let mediaSM = typeof media?.sizes?.madden_hero_md?.url !== undefined ? media.sizes.madden_hero_md.url : mediaMD;
            console.log(media);
            setAttributes({
                mediaId: media.id,
                mediaUrl: [mediaLG, mediaMD, mediaSM],
                mediaAlt: media.alt
            });
         }

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody
                    title={__('Select block background image')}
                    initialOpen={ true }
                >
                    <div className="editor-post-featured-image">
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={onSelectMedia}
                                value={mediaId}
                                allowedTypes={ ['image'] }
                                render={({open}) => (
                                    <Button
                                                className={ ! mediaId ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview' }
                                                onClick={ open }>
                                                { ! mediaId && ( __( 'Set background image', 'image-selector-example' ) ) }
                                                { !! mediaId && ! mediaUrl[0] && <Spinner /> }
                                                { !! mediaId && mediaUrl[0] &&
                                                    <ResponsiveWrapper>
                                                        <img className="components-responsive-wrapper__content--imgsize" src={ mediaUrl[0] } alt={ __( 'Background image', 'image-selector-example' ) } />
                                                    </ResponsiveWrapper>
                                                }
                                            </Button>
                                )}
                            />
                        </MediaUploadCheck>
                        <div className="component-buttons">
                            {mediaId != 0 && 
                                <MediaUploadCheck>
                                    <MediaUpload
                                        title={__('Replace image')}
                                        value={mediaId}
                                        onSelect={onSelectMedia}
                                        allowedTypes={['image']}
                                        render={({open}) => (
                                            <Button onClick={open}>{__('Replace image')}</Button>
                                        )}
                                    />
                                </MediaUploadCheck>
                            }
                            {mediaId != 0 && 
                                <MediaUploadCheck>
                                    <Button onClick={removeMedia} isDestructive>{__('Remove image')}</Button>
                                </MediaUploadCheck>
                            }
                        </div>
                    </div>
                </PanelBody>
                <PanelBody title="Banner Content">
                        <PanelRow>
                            <TextControl
                                label="Title"
                                onChange={ ( title ) => setAttributes( { title } ) }
                                value={ title }
                            />
                        </PanelRow>
                        <PanelRow>
                            <TextareaControl
                                label="Info"
                                onChange={ ( info ) => setAttributes( { info } ) }
                                value={ info }
                            />
                        </PanelRow>
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
        </Fragment>
    )
}

export default Inspector;
