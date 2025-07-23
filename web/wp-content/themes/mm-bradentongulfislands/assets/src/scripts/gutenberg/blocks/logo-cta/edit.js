/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'

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
    <section {...blockProps}>
      {imageUrl !== "" && 
      <div className={`${className}__image`}>
        <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
      </div>
      }
      <div className={`${className}__contents`}>
      <RichText
        tagName="button" 
        className="contents-title"
        allowedFormats={ [ "core/bold", "core/italic", "core/link" ] } 
        value={ title }
        onChange={ ( title ) => {
          setAttributes( { title } )
        } } 
        placeholder={ __( "Add title..." ) } 
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
