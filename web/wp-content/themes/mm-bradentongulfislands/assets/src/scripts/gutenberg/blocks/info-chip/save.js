import { RichText, useBlockProps } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { imageUrl, imageAlt, title, info }, className } = props;
  const blockProps = useBlockProps.save();

  return (
    <section {...blockProps} className={className}>
      <div className={`wp-block-mm-bradentongulfislands-info-chip__content`}>
        <div className={`wp-block-mm-bradentongulfislands-info-chip__front`}>
          <img 
            src={imageUrl} 
            alt={imageAlt || "Showcase Card Image"} 
            data-load-type="img" 
            data-load-offset="lg" 
            data-load-all={imageUrl} 
          />
          <RichText.Content className="contents-title" tagName="h3" value={title} />
        </div>
        <div className={`wp-block-mm-bradentongulfislands-info-chip__back`}>
          <RichText.Content tagName="p" value={info} />
        </div>  
      </div>
    </section>
  );
};

export default Save;
