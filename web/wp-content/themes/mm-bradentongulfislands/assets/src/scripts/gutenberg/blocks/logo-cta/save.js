import { RichText, useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { imageUrl, imageAlt, title }, className } = props;
  const blockProps = useBlockProps.save();

  return (
    <section className={className}>
      {imageUrl !== "" && 
      <div className={`wp-block-mm-bradentongulfislands-logo-cta__image`}>
        <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
      </div>
      }
      <div className={`wp-block-mm-bradentongulfislands-logo-cta__contents`}>
        {<RichText.Content { ...blockProps } className="contents-title" tagName="button" value={ title } />}
      </div>
    </section>
  )
}

/*** EXPORTS ****************************************************************/
export default Save;