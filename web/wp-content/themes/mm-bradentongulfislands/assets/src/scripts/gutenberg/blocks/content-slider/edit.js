/*** IMPORTS ****************************************************************/

import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';

// Local Dependencies
import { BLOCK_PREFIX } from '../../../inc/constants';

/*** CONSTANTS **************************************************************/

const ALLOWED_BLOCKS = ['core/group'];
const BLOCK_TEMPLATE = [
  ['core/group', {}]
];

/*** COMPONENTS *************************************************************/

const edit = ({ clientId }) => {
  const blockProps = useBlockProps();
  const { replaceInnerBlocks } = useDispatch('core/block-editor');
  const thisBlock = useSelect(
    select => select('core/block-editor').getBlock(clientId),
    [clientId]
  );

  const onCreateSection = () => {
    const newBlock = wp.blocks.createBlock('core/group', {});
    const newBlocks = [...(thisBlock?.innerBlocks || []), newBlock];
    replaceInnerBlocks(clientId, newBlocks, false);
  };

  return (
    <section {...blockProps}>
        <InnerBlocks
          allowedBlocks={ALLOWED_BLOCKS}
          template={BLOCK_TEMPLATE}
          templateInsertUpdatesSelection={true}
          orientation="horizontal"
          templateLock={false}
          renderAppender={InnerBlocks.ButtonBlockAppender}
        />
      <Button isPrimary onClick={onCreateSection}>Add Slide</Button>
    </section>
  );
};



/*** EXPORTS ****************************************************************/

export default edit;
