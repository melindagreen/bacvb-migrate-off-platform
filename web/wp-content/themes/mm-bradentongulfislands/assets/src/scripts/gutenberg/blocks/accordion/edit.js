/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { RichText, InnerBlocks } from '@wordpress/block-editor';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = [`${THEME_PREFIX}/accordion-section`];
const BLOCK_TEMPLATE = [
  [`${THEME_PREFIX}/accordion-section`, {}],
];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes: { }, setAttributes, className } = props;

  return (
    <section className={className}>
      <div className='contents'>
        <InnerBlocks
          allowedBlocks={ALLOWED_BLOCKS}
          template={BLOCK_TEMPLATE}
        />
      </div>
    </section>
  )
}

const edit = (props) => {
  return (
    <>
      <Controls {...props} />
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
