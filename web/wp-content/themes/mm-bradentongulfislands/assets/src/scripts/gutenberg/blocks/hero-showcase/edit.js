/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { Button, Flex, PanelBody, PanelRow, SelectControl, TextControl, TextareaControl, } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { __experimentalLinkControl as LinkControl, MediaUpload, MediaUploadCheck, } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect, } from '@wordpress/data';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls';
import { Repeater } from '../../components';
import { updateObjArrAttr } from '../../inc/utils';
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/
const DEFAULT_SEGMENT = {
  customTitle: '',
  customExcerpt: '',
  postObj: {}
};
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props 
 * @returns {WPElement}
 */
const Wizard = props => {
  const { attributes: { posts, queryMode, }, setAttributes } = props;
  const updateSegment = updateObjArrAttr('posts', setAttributes, posts);


  if (queryMode === 'manual') return (
    <Repeater
      label={__('Grid Items')}
      minLength={3}
      maxLength={3}
      segments={posts}
      segmentsContent={posts.map(post => <>
        <LinkControl
          value={post.postObj}
          onChange={(nextValue) => {
            console.log(nextValue);
            updateSegment('postObj', post)(nextValue)
          }}
          settings={[]}
        />

        <PanelBody title={__('Customize grid item')} initialOpen={false}>
          <p className='instructions'>{__('By default, the grid will pull in the selected post\'s title, thumbnail, and excerpt. To override these features, manually enter them here.')}</p>

          <PanelRow>
            <TextControl
              label={__('Custom Title')}
              value={post.customTitle}
              onChange={updateSegment('customTitle', post)}
            />

            <TextareaControl
              label={__('Custom Excerpt')}
              value={post.customExcerpt}
              onChange={updateSegment('customExcerpt', post)}
            />

            <TextControl
              label={__('Custom CTA Text')}
              value={post.customCTAText}
              onChange={updateSegment('customCTAText', post)}
            />

            <MediaUploadCheck>
              <MediaUpload
                title={__('Custom Thumbnail', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={updateSegment('customThumb', post)}
                value={post.customThumb}
                render={({ open }) => (<Flex
                  className='custom-thumb'
                  align='center'
                  justify='center'
                  direction='column'
                >
                  <p>{__('Custom Thumbnail')}</p>
                  {post?.customThumb?.sizes?.thumbnail?.url &&
                    <img className='preview-thumb' src={post.customThumb.sizes.thumbnail.url} />
                  }
                  <Button onClick={open} isLarge icon="format-image" isSecondary>
                    {post?.customThumb?.sizes?.thumbnail?.url
                      ? __('Replace Custom Thumbnail', THEME_PREFIX)
                      : __('Chose Custom Thumbnail', THEME_PREFIX)}
                  </Button>
                </Flex>
                )}
              />
            </MediaUploadCheck>
          </PanelRow>
        </PanelBody>
        <PanelBody title={__('Factoid')} initialOpen={false}>
          <PanelRow>
            <TextControl
              label={__('Fact Title')}
              value={post.factTitle}
              onChange={updateSegment('factTitle', post)}
            />

            <TextareaControl
              label={__('Fact Description')}
              value={post.factDescription}
              onChange={updateSegment('factDescription', post)}
            />

            <MediaUploadCheck>
              <MediaUpload
                title={__('Fact Thumbnail', THEME_PREFIX)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                onSelect={updateSegment('factThumb', post)}
                value={post.factThumbnail}
                render={({ open }) => (<Flex
                  className='fact-thumb'
                  align='center'
                  justify='center'
                  direction='column'
                >
                  <p>{__('Fact Thumbnail')}</p>
                  {post?.factThumb?.sizes?.thumbnail?.url &&
                    <img className='preview-thumb' src={post.factThumb.sizes.thumbnail.url} />
                  }
                  <Button onClick={open} isLarge icon="format-image" isSecondary>
                    {post?.factThumb?.sizes?.thumbnail?.url
                      ? __('Replace Fact Thumbnail', THEME_PREFIX)
                      : __('Chose Fact Thumbnail', THEME_PREFIX)}
                  </Button>
                </Flex>
                )}
              />
            </MediaUploadCheck>
          </PanelRow>

        </PanelBody>
      </>)}
      newSegment={DEFAULT_SEGMENT}
      placeholderText={__('Add a post')}
      onChange={posts => setAttributes({ posts })}
    />
  )
}

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes: { mode, }, className } = props;

  return (
    <section className={className} >
      {mode === 'edit'
        ? <Wizard {...props} />
        : <ServerSideRender block={props.name} {...props} />}
    </section>
  )
}

const edit = (props) => {
  return (
    <>
      <Controls {...props} />
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
