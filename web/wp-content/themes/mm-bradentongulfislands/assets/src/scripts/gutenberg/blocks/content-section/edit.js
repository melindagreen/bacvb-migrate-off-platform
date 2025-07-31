/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __experimentalScrollable as Scrollable } from '@wordpress/components'
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

// Local Dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';

/*** CONSTANTS **************************************************************/
const BLOCK_TEMPLATE = [
  ['core/columns',{},[
    ['core/column',{}, [
      ['core/group',{}, [
        ['core/image', {overlap: -2}],
        ['core/heading', {}],
        ['core/paragraph', {}]
      ]]
    ]],
    ['core/column',{verticalAlignment: 'bottom'}, [
      ['core/image', {}]
    ]]
  ]]
];

/*** COMPONTANTS ************************************************************/
import Controls from './controls';

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = (props) => {

  const blockProps = useBlockProps();
  return (
    <section {...blockProps}>
        <InnerBlocks className="item"
          template={BLOCK_TEMPLATE}
          templateInsertUpdatesSelection={true}
          orientation="horizontal"
          templateLock={false}
        />
    </section>
  )
}

const edit = (props) => {
  return ( <>
  <Controls {...props} />
  <Editor {...props} /> 
  </>);
};

/*** EXPORTS ****************************************************************/

export default edit;
