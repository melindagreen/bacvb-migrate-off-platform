/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __experimentalScrollable as Scrollable } from '@wordpress/components'
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

// Local Dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = ['mm-bradentongulfislands/content-section'];
const BLOCK_TEMPLATE = [
  ['mm-bradentongulfislands/content-section', {}]
];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = (props) => {

  const blockProps = useBlockProps();
  return (
    <section {...blockProps}>
      <Scrollable  scrollDirection="x" style={ { maxWidth: "100vw", marginBottom: "5px"} }>
      <div className="contents" {...blockProps }>
        <InnerBlocks className="item"
          allowedBlocks={ALLOWED_BLOCKS}
          template={BLOCK_TEMPLATE}
          templateInsertUpdatesSelection={true}
          orientation="horizontal"
          templateLock={false}
        />
      </div>
        </Scrollable>
    </section>
  )
}

const edit = (props) => {
  return ( <Editor {...props} /> );
};

/*** EXPORTS ****************************************************************/

export default edit;
