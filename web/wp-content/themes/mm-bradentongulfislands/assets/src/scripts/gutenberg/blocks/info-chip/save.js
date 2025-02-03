import { RichText, useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { imageUrl, imageAlt, title, info }, className } = props;
  const blockProps = useBlockProps.save();

  return (
    <section className={className}>
      <div className={`wp-block-mm-bradentongulfislands-info-chip__content`}>
        <div className={`wp-block-mm-bradentongulfislands-info-chip__front`}>
          <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
          {<RichText.Content { ...blockProps } className="contents-title" tagName="h3" value={ title } />}
        </div>
        <div className={`wp-block-mm-bradentongulfislands-info-chip__back`}>
          <p>{info}</p>
        </div>  
      </div>
    </section>
  )
}

/*** EXPORTS ****************************************************************/
export default Save;