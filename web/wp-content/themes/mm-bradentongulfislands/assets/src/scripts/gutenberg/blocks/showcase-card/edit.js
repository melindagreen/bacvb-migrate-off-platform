/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { RichText, useBlockProps } from "@wordpress/block-editor";

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from "./controls"

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = ["core/image", "core/paragraph", "core/heading"];
const BLOCK_TEMPLATE = [];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes: { imageUrl, imageAlt, bodyText, title, buttonText }, setAttributes, className } = props;
  const blockProps = useBlockProps();

  return (
    <section {...blockProps}>
      {imageUrl !== "" && 
      <div className={`wp-block-mm-bradentongulfislands-showcase-card__image`}>
        <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
      </div>
      }
      <div className={`${className}__contents`}>
      <RichText
        tagName="h2" 
        className="contents-title"
        allowedFormats={ [ "core/bold", "core/italic" ] } 
        value={ title }
        onChange={ ( title ) => {
          setAttributes( { title } )
        } } 
        placeholder={ __( "Add title..." ) } 
      />
      <RichText
        tagName="p" 
        className="contents-body"
        value={ bodyText } 
        allowedFormats={ [ "core/bold", "core/italic" ] } 
        onChange={ ( bodyText ) => {
          setAttributes( { bodyText } ) 
        }} 
        placeholder={ __( "Add body text..." ) } 
      />
       <RichText
        tagName="h3" 
        className="contents-button"
        value={ buttonText } 
        allowedFormats={ [ "core/bold", "core/italic" ] } 
        onChange={ ( buttonText ) => {
          setAttributes( { buttonText } ) 
        }} 
        placeholder={ __( "Add Button Text..." ) } 
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
