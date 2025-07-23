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
  const { attributes: { description, ctaText, title }, setAttributes, className } = props;
  const blockProps = useBlockProps();

  return (
    <section {...blockProps}>
      <RichText
       tagName="h2" 
        className="contents-title"
        allowedFormats={ [ "core/bold", "core/italic", "core/link" ] } 
        value={ title }
        onChange={ ( title ) => {
          setAttributes( { title } )
        } } 
        placeholder={ __( "Add title..." ) } 
      />
      <RichText
        tagName="p" 
        className="contents-description"
        allowedFormats={ [ "core/bold", "core/italic", "core/link" ] } 
        value={ description }
        onChange={ ( description ) => {
          setAttributes( { description } )
        } } 
        placeholder={ __( "Add Description..." ) } 
      />
      <RichText
        tagName="button" 
        className="contents-ctaText"
        allowedFormats={ [ "core/bold", "core/italic", "core/link" ] } 
        value={ ctaText }
        onChange={ ( ctaText ) => {
          setAttributes( { ctaText } )
        } } 
        placeholder={ __( "Add CTA..." ) } 
      />
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
