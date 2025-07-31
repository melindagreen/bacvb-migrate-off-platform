/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { RichText, InnerBlocks, withColors } from '@wordpress/block-editor';
import React, { useState } from 'react';
import { useBlockProps } from '@wordpress/block-editor';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'

/*** CONSTANTS **************************************************************/
// const ALLOWED_BLOCKS = ['core/paragraph', 'core/heading', 'core/image', 'core/list', 'core/quote', 'core/table', 'core/buttons', 'core/table', 'core/tablepress'];
const BLOCK_TEMPLATE = [
  ['core/paragraph', {}],
];

/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {

	const blockProps = useBlockProps();

  const { attributes: { title }, setAttributes, textColor, className } = props;

  const [open, setOpen] = useState(false);

  let titleClass = 'accordion-section__title ';
  const titleStyle = {};

  if (typeof textColor !== 'undefined') {
    if (typeof textColor.class !== 'undefined') titleClass += textColor.class;
    else if (typeof textColor.color !== 'undefined') titleStyle.color = textColor.color;
  }

  return (
    <section {...blockProps}>
      <div className='accordion__header'>
        <span
          class="fusion-toggle-icon-wrapper"
          aria-hidden="true"
          onClick={() => setOpen(!open)}
        >
          <i class="fa-solid fa-plus"></i>
        </span>
        <RichText
          placeholder={__('Section Title')}
          tagName='h3'
          value={title}
          onChange={title => setAttributes({ title })}
          className={titleClass}
          style={titleStyle}
        />
      </div>

      <div className={`accordion__body ${open ? 'open' : ''}`}>
        <InnerBlocks
          template={BLOCK_TEMPLATE}
        />
      </div>
    </section>
  )
}

const edit = (props) => {
  return (
    <>
      {/* <Controls {...props} /> */}
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

// export default withColors({ textColor: 'color' })(edit);
export default edit;
