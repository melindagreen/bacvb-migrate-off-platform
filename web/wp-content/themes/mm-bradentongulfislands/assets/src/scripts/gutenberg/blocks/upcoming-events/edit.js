/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'

/*** CONSTANTS **************************************************************/

/*** COMPONTANTS ************************************************************/

/**
 * Fields that modify the attributes of the current block
 * @param {*} props 
 * @returns {WPElement}
 */
 const Wizard = props => {
  const { attributes: { title }, setAttributes } = props;

  return (
    <TextControl
      label={__('An example input')}
      value={title}
      onChange={title => setAttributes({ title })}
    />
  )
}

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */
const Editor = props => {
  const { attributes: { exampleColor, mode }, className } = props;

  const exampleStyle = {
    backgroundColor: exampleColor
  };

  return (
    <section className={className} style={exampleStyle}>
      {mode === 'edit'
        ? <Wizard {...props} />
        : <ServerSideRender block={props.name} {...props} />}
    </section>
  )
}

const edit = ( props ) => {
  return (
    <>
      <Controls {...props} />
      <Editor {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
