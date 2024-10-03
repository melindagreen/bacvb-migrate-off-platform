// Local dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const save = (props) => {

  const blockProps = useBlockProps.save();
  const {attributes} = props;
  const {bannerTitle,bannerDescription} = attributes;


  return (
    <div className={`wp-block-mm-bradentongulfislands-wideslideshow` }>
    {bannerTitle !== "" || bannerDescription !== "" && <div class="wp-block-mm-bradentongulfislands-wideslideshow__banner-title"><h2>{bannerTitle}</h2><p>{bannerDescription}</p></div>}
    <div className='wideslideshow-ligthbox-overlay wideslideshow-ligthbox-overlay--hide'>
      <div class="wideslideshow-ligthbox-overlay__close">x</div>
      <div className='wideslideshow-ligthbox-overlay__content'>
        <iframe frameborder="0" id="lightbox-iframe" src=""></iframe>
        <div className='lightbox-info'>
          <span>
            <h3 id="lightbox-title"></h3>
            <h4 id="lightbox-subtitle"></h4>
          </span>
          <a id="lightbox-buttontext" aria-label="more info button" href="#" target="__blank"></a>
        </div>
      </div>
    </div>
    <div className={`swiper-wideslideshow swiper` }>
      <div className={`swiper-wrapper bc-wrapper`}>
        <InnerBlocks.Content/> 
      </div>
    </div>
    <div className="hc-wrapper">
      <div className="bc-wrapper__items">
            <div className="bc-infoblock is-style-collage-square">
              <div className="bc-infoblock__content">
                <h2 id="infoblock-title" className="infoblock__item infoblock__item--hide infoblock__item--green">Slide Title</h2>
                <h3 id="infoblock-info" className="infoblock__item infoblock__item--hide">Slide Info</h3>
                <div id="infoblock-buttonurl" className="infoblock__item infoblock__item--hide">
                  <a id="infoblock-buttontext" class="infoblock__link" aria-label="more info button" href="#">CTA</a>
                </div>
                <div className="swiper-pagination"></div>
              </div>
              <div className="bc-infoblock__navigation">
                <div className="swiper-button-next"></div>
                <div className="swiper-button-prev"></div>
              </div>
            </div>
      </div>
    </div>
    <div className={`swiper-thumbnail-preview-slider--thumbnails swiper` }>
      <div className={`swiper-wrapper bc-wrapper`}>
      </div>
    </div>
    </div>
    );
}

/*** EXPORTS ****************************************************************/
export default save;