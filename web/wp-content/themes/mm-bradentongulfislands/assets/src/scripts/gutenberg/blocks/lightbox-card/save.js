import { RichText, useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { imageUrl, imageAlt, title, description }, className } = props;
  const blockProps = useBlockProps.save();

  return (
    <section className={className}>
      <div className={`wp-block-mm-bradentongulfislands-lightbox-card__card`}>
          <img src={imageUrl} data-load-alt={imageAlt !== "" ? imageAlt : "Showcase Card Image"} data-load-type="img" data-load-offset="lg" data-load-all={imageUrl} />
          <div className="card-content">
            {<RichText.Content { ...blockProps } className="contents-title" tagName="h3" value={ title } />}
            {<RichText.Content { ...blockProps } className="contents-description" tagName="p" value={ description } />}
          </div>
      </div>
      {/* <div className='wp-block-mm-bradentongulfislands-lightbox-card__lightbox wp-block-mm-bradentongulfislands-lightbox-card__lightbox--hide'>
        <div class="lightbox-card-overlay__close">x</div>
          <div className='lightbox-card-overlay__content'>
            <div className='lightbox-info'>
              <span>
                <h3 id="lightbox-title"></h3>
                <h4 id="lightbox-subtitle"></h4>
              </span>
              <a id="lightbox-buttontext" aria-label="more info button" href="#" target="__blank"></a>
          </div>
        </div>
      </div> */}
    </section>
  )
}

/*** EXPORTS ****************************************************************/
export default Save;