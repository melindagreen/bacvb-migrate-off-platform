// Local dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const save = (props) => {

  const blockProps = useBlockProps.save();
  const { attributes } = props;
  const { sectionTitle } = attributes;

  return (
    <div data-title={sectionTitle} className={`wp-block-mm-bradentongulfislands-content-section` }>
        <InnerBlocks.Content/> 
    </div>);
}

/*** EXPORTS ****************************************************************/
export default save;