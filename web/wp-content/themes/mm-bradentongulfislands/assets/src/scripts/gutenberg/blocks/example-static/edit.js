/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { RichText, InnerBlocks } from '@wordpress/block-editor';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'

/*** CONSTANTS **************************************************************/
const ALLOWED_BLOCKS = ['core/image', 'core/paragraph'];
const BLOCK_TEMPLATE = [
  ['core/image', {}],
];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes: { exampleColor, exampleText }, setAttributes, className } = props;

  const exampleStyle = {
    backgroundColor: exampleColor
  };

  return (
    <section className={className} style={exampleStyle}>
      <RichText
        tagName='h3'
        value={exampleText}
        onChange={exampleText => setAttributes({ exampleText })}
      />
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
