import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const save = () => {
  const blockProps = useBlockProps.save();
  return (    
    <div {...blockProps} className={`${blockProps.className} swiper-slide`}>
      <InnerBlocks.Content />
    </div>
  )
}

/*** EXPORTS ****************************************************************/
export default save;