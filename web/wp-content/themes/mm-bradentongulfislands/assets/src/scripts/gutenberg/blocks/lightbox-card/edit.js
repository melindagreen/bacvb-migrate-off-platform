/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
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
  const { attributes: { imageUrl, imageAlt, title, description, lightboxId }, setAttributes, className } = props;
  const blockProps = useBlockProps();

  useEffect(() => {
    if (lightboxId === "") {
      console.log('Loading');
      setAttributes({ lightboxId: `wp-block-mm-bradentongulfislands-lightbox-card-${Date.now()}-${Math.floor(Math.random() * 10000)}` });
    }
  }, []);

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
        tagName="h3" 
        className="contents-title"
        allowedFormats={ [ "core/bold", "core/italic", "core/link" ] } 
        value={ title }
        onChange={ ( title ) => {
          setAttributes( { title } )
        } } 
        placeholder={ __( "Add title..." ) } 
      />
      <RichText
        { ...blockProps }
        tagName="p" 
        className="contents-title"
        allowedFormats={ [ "core/bold", "core/italic", "core/link" ] } 
        value={ description }
        onChange={ ( description ) => {
          setAttributes( { description } )
        } } 
        placeholder={ __( "Add description..." ) } 
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
