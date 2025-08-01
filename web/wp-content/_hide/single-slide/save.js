import { InnerBlocks } from '@wordpress/block-editor';

const save = () => {

  return (    
    <div className="swiper-slide">
      <InnerBlocks.Content />
    </div>
  )
}

/*** EXPORTS ****************************************************************/
export default save;