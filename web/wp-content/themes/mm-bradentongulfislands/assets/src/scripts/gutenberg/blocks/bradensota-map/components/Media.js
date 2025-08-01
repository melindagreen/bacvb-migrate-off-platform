import React, { useState, useEffect } from 'react';
import { Button, Spinner } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { ResponsiveWrapper } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';



const Media = ({ mediaId, placeholderText, mediaAttribute, setAttributes }) => {
    const [mediaUrl, setMediaUrl] = useState('');

    const { image } = useSelect((select) => ({
        image: select('core').getMedia(mediaId),
    }), [mediaId]);

    useEffect(() => {
        if (image) {
            setMediaUrl(image.source_url);
        } else {
            setMediaUrl('');
        }
    }, [image]);

    const removeMedia = () => {
        setAttributes({
            [mediaAttribute]: 0
        });
    };

    const onSelectMedia = (media) => {
        setAttributes({
            [mediaAttribute]: media.id
        });
    };

    return (
        <div style={{ margin: '1rem auto' }} className="editor-post-featured-image">
            <MediaUploadCheck>
                <MediaUpload
                    onSelect={onSelectMedia}
                    value={mediaId}
                    allowedTypes={['image']}
                    render={({ open }) => (
                        <Button
                            className={!mediaId ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview'}
                            onClick={open}
                        >
                            {!mediaId && placeholderText && __(placeholderText, 'image-selector-example')}
                            {!mediaId && !placeholderText && __('Set background image', 'image-selector-example')}
                            {!!mediaId && <Spinner />}
                            {!!mediaId && (
                                <img className="components-responsive-wrapper__content--imgsize" src={mediaUrl} alt={__('Background image', 'image-selector-example')} />            
                            )}
                    
                        </Button>
                    )}
                />
            </MediaUploadCheck>
            <div className="component-buttons">
                {mediaId !== 0 &&
                    <MediaUploadCheck>
                        <MediaUpload
                            title={__('Replace image')}
                            value={mediaId}
                            onSelect={onSelectMedia}
                            allowedTypes={['image']}
                            render={({ open }) => (
                                <Button onClick={open}>{__('Replace image')}</Button>
                            )}
                        />
                    </MediaUploadCheck>
                }
                {mediaId !== 0 &&
                    <MediaUploadCheck>
                        <Button onClick={removeMedia} isDestructive>{__('Remove image')}</Button>
                    </MediaUploadCheck>
                }
            </div>
        </div>
    );
};

export default Media;
