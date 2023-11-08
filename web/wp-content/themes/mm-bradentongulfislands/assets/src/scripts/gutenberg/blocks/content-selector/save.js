// Local dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const save = (props) => {

  const blockProps = useBlockProps.save();

  return (
    <div className={`wp-block-mm-bradentongulfislands-content-selector` }>
        <select id="content-dropdown">
        </select>
        <InnerBlocks.Content/> 
    </div>);
}

/*** EXPORTS ****************************************************************/
export default save;