// Local dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const save = (props) => {

  const blockProps = useBlockProps.save();

  return (
    <div className={`wp-block-mm-bradentongulfislands-wideslideshow` }>
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
    </div>);
}

/*** EXPORTS ****************************************************************/
export default save;