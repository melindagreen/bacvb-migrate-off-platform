import { InnerBlocks } from '@wordpress/block-editor';

const save = (props) => {
  if (props.attributes.contentType !== 'custom') {
    return null;
  } else {
    return (
      <>
        <InnerBlocks.Content />
      </>
    )
  }
}

/*** EXPORTS ****************************************************************/
export default save;