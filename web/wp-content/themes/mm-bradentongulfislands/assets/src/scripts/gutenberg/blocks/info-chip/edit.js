/*** IMPORTS ****************************************************************/

import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import Controls from './controls';

/*** COMPONENTS ************************************************************/

const Editor = (props) => {
  const { attributes: { imageUrl, imageAlt, title }, setAttributes, className } = props;
  const blockProps = useBlockProps();

  return (
    <section {...blockProps} className={`${className} wp-block-custom-section`}>
      {imageUrl && (
        <div className={`${className}__image`}>
          <img 
            src={imageUrl} 
            alt={imageAlt || "Showcase Card Image"} 
            data-load-type="img" 
            data-load-offset="lg" 
            data-load-all={imageUrl} 
          />
        </div>
      )}
      <div className={`${className}__contents`}>
        <RichText
          tagName="h3"
          className="contents-title"
          allowedFormats={["core/bold", "core/italic", "core/link"]}
          value={title}
          onChange={(title) => setAttributes({ title })}
          placeholder={__("Add title...")}
        />
      </div>
    </section>
  );
};

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
