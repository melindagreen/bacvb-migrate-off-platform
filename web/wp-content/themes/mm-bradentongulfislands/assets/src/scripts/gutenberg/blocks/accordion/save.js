import { InnerBlocks } from '@wordpress/block-editor';

const Save = (props) => {
  const { attributes: { title }, className } = props;

  return <section className={className}>
    {title !== '' && 
    <div className='section-title'>
      <h2>{title}</h2>
    </div>
    }
    <InnerBlocks.Content />
  </section>
}

/*** EXPORTS ****************************************************************/
export default Save;