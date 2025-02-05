import { RichText, useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { imageUrl, imageAlt, title, description, lbTitle, lbDescription }, className } = props;
  const blockProps = useBlockProps.save();
  const lightboxId = `wp-block-mm-bradentongulfislands-lightbox-card-${Date.now()}-${Math.floor(Math.random() * 10000)}wp-block-mm-bradentongulfislands-lightbox-card-${Date.now()}-${Math.floor(Math.random() * 10000)}`;

  return (
    <section className={className}>
      <div data-lightbox-selector={lightboxId} className={`wp-block-mm-bradentongulfislands-lightbox-card__card`}>
          <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
          <div className="card-content">
            {<RichText.Content { ...blockProps } className="contents-title" tagName="h3" value={ title } />}
            {<RichText.Content { ...blockProps } className="contents-description" tagName="p" value={ description } />}
          </div>
      </div>
      <div className={`wp-block-mm-bradentongulfislands-lightbox-card__lightbox wp-block-mm-bradentongulfislands-lightbox-card__lightbox--hide ${lightboxId}`}>
          <div className='lightbox-card-overlay__content'>
          <div class="lightbox-card-overlay__close"><span>x</span></div>
            <img src={`${imageUrl}`} alt={`${imageAlt}`} />
            <div className='lightbox-info'>
              <span>
                <h2 id="lightbox-title">{lbTitle}</h2>
                <p id="lightbox-subtitle">{lbDescription}</p>
              </span>
          </div>
        </div>
      </div>
    </section>
  )
}

/*** EXPORTS ****************************************************************/
export default Save;