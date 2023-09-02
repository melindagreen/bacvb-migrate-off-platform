/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';
import { Button, Toolbar, Popover, TextControl } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

/*** COMPONENTS **************************************************************/


const ImageUpload = (props) => {

    const {attributes, setAttributes} = props;
    const {mediaUrl} = attributes;
 
     const onSelectMedia = (media) => {

        let mediaLG = typeof media?.sizes?.full?.url !== undefined ? media.sizes.full.url : media.url;
        let mediaMD = mediaLG;
        let mediaSM = typeof media?.sizes?.madden_hero_md?.url !== undefined ? media.sizes.madden_hero_md.url : mediaMD;
        setAttributes({
            mediaId: media.id,
            mediaUrl: [mediaLG, mediaMD, mediaSM],
            mediaAlt: media.alt
        });
     }
                   return( <> <MediaUploadCheck>
                        {mediaUrl.length === 0 &&<MediaUpload
                            onSelect={onSelectMedia}
                            value={mediaUrl[0]}
                            allowedTypes={ ['image'] }
                            render={({open}) => (
                                <Button
                                            onClick={ open }>
                                    {__('Add Image')}
                                        </Button>
                            )}
                        />}
                    </MediaUploadCheck>
                        {mediaUrl.length > 0 && 
                            <MediaUploadCheck>
                                <MediaUpload
                                    title={__('Replace image')}
                                    value={mediaUrl[0]}
                                    onSelect={onSelectMedia}
                                    allowedTypes={['image']}
                                    render={({open}) => (
                                        <Button onClick={open}>{__('Replace image')}</Button>
                                    )}
                                />
                            </MediaUploadCheck>
                        }
                        </>);
      
};

const Tools = props => {
    return (<>
        <Toolbar>
            <ImageUpload {...props} />
        </Toolbar>
        <Toolbar>
    </Toolbar>
    </>)
}

/*** EXPORTS ****************************************************************/

export default Tools;
