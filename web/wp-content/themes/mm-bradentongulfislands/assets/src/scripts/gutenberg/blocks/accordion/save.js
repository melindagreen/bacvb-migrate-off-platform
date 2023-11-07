import { InnerBlocks } from '@wordpress/block-editor';

const Save = (props) => {
  const { className } = props;

  return <section className={className}>
    <InnerBlocks.Content />
  </section>
}

/*** EXPORTS ****************************************************************/
export default Save;