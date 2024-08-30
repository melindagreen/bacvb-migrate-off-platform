/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { RichText, InnerBlocks, useBlockProps } from '@wordpress/block-editor';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = ['core/social-links'];
const BLOCK_TEMPLATE = [
  ['core/social-links', {}],
];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */

const Editor = props => {
  const { attributes: { imageUrl, imageAlt, title }, setAttributes, className } = props;
  const blockProps = useBlockProps();

  return (
    <section className={className}>
      {imageUrl !== "" && 
      <div className={`${className}__image`}>
        <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
      </div>
      }
      <div className={`${className}__contents`}>
      <RichText
        { ...blockProps }
        tagName="h2" 
        className="contents-title"
        allowedFormats={ [ "core/bold", "core/italic" ] } 
        value={ title }
        onChange={ ( title ) => {
          setAttributes( { title } )
        } } 
        placeholder={ __( "Add title..." ) } 
      />
        <InnerBlocks
          allowedBlocks={ALLOWED_BLOCKS}
          template={BLOCK_TEMPLATE}
        />
      </div>
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
