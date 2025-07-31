/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import {
    PanelBody,
    PanelRow,
    TextControl,
    TextareaControl,
    Button,
    Flex
} from '@wordpress/components';
import {
    __experimentalLinkControl as LinkControl,
    MediaUpload,
    MediaUploadCheck
} from '@wordpress/block-editor'; 
import { updateObjArrAttr } from '../../../inc/utils';
import { useSelect, } from '@wordpress/data';


// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/


const Inspector = props => {
    const { attributes: { posts = [], queryMode }, setAttributes } = props;
    const updateSegment = updateObjArrAttr("posts", setAttributes, posts);

    return (
        <InspectorControls>
            {queryMode === 'manual' && posts.map((post, idx) => (
                <PanelBody title={__('Grid Item ') + (idx + 1)} initialOpen={false} key={post.id || idx}>
                    <LinkControl
                        value={post.postObj}
                        onChange={updateSegment("postObj", post)}
                        settings={[]}
                    />
                    <PanelRow>
                        <TextControl
                            label={__('Custom Title')}
                            value={post.customTitle || ''}
                            onChange={updateSegment("customTitle", post)}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextareaControl
                            label={__('Custom Excerpt')}
                            value={post.customExcerpt || ''}
                            onChange={updateSegment("customExcerpt", post)}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={__('Custom CTA Text')}
                            value={post.customCTAText || ''}
                            onChange={updateSegment("customCTAText", post)}
                        />
                    </PanelRow>
                    <PanelRow>
                        <MediaUploadCheck>
                            <MediaUpload
                                title={__('Custom Thumbnail', THEME_PREFIX)}
                                allowedTypes={ALLOWED_MEDIA_TYPES}
                                onSelect={updateSegment("customThumb", post)}
                                value={post.customThumb}
                                render={({ open }) => (
                                    <Flex
                                        className="custom-thumb"
                                        align="center"
                                        justify="center"
                                        direction="column"
                                    >
                                        <p>{__('Custom Thumbnail')}</p>
                                        {post?.customThumb?.sizes?.thumbnail?.url && (
                                            <img
                                                className="preview-thumb"
                                                src={post.customThumb.sizes.thumbnail.url}
                                            />
                                        )}
                                        <Button onClick={open} icon="format-image" isSecondary>
                                            {post?.customThumb?.sizes?.thumbnail?.url
                                                ? __('Replace Custom Thumbnail', THEME_PREFIX)
                                                : __('Choose Custom Thumbnail', THEME_PREFIX)}
                                        </Button>
                                    </Flex>
                                )}
                            />
                        </MediaUploadCheck>
                    </PanelRow>
                </PanelBody>
            ))}
        </InspectorControls>
    )
}

export default Inspector;
