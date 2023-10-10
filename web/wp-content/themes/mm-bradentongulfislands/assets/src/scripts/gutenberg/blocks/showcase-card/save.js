import { RichText, useBlockProps } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { imageUrl, imageAlt, bodyText, title, buttonText, buttonUrl }, className } = props;
  const blockProps = useBlockProps.save();

  return (
    <section className={className}>
      {imageUrl !== "" && 
      <div className={`wp-block-mm-bradentongulfislands-showcase-card__image`}>
        <a href={buttonUrl}><img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} /></a>
        <div className="showcase-card-caption">{title}</div>
      </div>
      }
    
      <div className={`wp-block-mm-bradentongulfislands-showcase-card__contents is-style-collage-square`}>
        {<RichText.Content { ...blockProps } className="contents-title" tagName="h3" value={ title } />}
        <hr></hr>
        {<RichText.Content { ...blockProps } className="contents-body" tagName="p" value={ bodyText } />}
        <a href={buttonUrl} className="contents-button">{buttonText}</a>
      </div>
    </section>
  )
}

/*** EXPORTS ****************************************************************/
export default Save;