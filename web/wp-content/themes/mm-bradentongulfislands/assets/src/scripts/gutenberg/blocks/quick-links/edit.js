/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { Button, Flex, PanelBody, PanelRow, SelectControl, TextControl, TextareaControl, } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { __experimentalLinkControl as LinkControl, MediaUpload, MediaUploadCheck, URLInput } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';
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
  linkObj: {}
};
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props 
 * @returns {WPElement}
 */
const Wizard = props => {
  const { attributes: { quickLinks, queryMode, }, setAttributes } = props;
  const updateSegment = updateObjArrAttr('quickLinks', setAttributes, quickLinks);


   return(<> <Repeater
      label={__('Quick Links')}
      segments={quickLinks}
      segmentsContent={quickLinks.map(link => <>
        <LinkControl
          value={link.linkObj}
          onChange={updateSegment('linkObj', link)}
          settings={[]}
        />
        {/* <URLInput
        className='quickLinksURLInput'
        value={link.linkObj}
        onChange={updateSegment('linkObj', link)}
      /> */}

        <PanelBody title={__('Customize link item')} initialOpen={false}>
          <p className='instructions'>{__('By default, the link will pull in the selected link\'s title, thumbnail, and excerpt. To override these features, manually enter them here.')}</p>

          <PanelRow>
            <TextControl
              label={__('Custom Title')}
              value={link.customTitle}
              onChange={updateSegment('customTitle', link)}
            />
          </PanelRow>
        </PanelBody>
      </>)}
      newSegment={DEFAULT_SEGMENT}
      placeholderText={__('Add a link')}
      onChange={quickLinks => setAttributes({ quickLinks })}
    /> </>);
  
}

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {

	const blockProps = useBlockProps();
  const { attributes: { mode, }, className } = props;

  return (
    <section {...blockProps}>
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
