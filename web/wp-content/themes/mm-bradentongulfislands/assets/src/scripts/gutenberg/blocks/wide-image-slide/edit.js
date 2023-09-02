/*** IMPORTS ****************************************************************/
import { Placeholder } from '@wordpress/components';

// WordPress dependencies
import { __ } from '@wordpress/i18n';

// Local Dependencies
// Controls - add block/inspector controls here 
import Controls from './controls'


/*** COMPONTANTS ************************************************************/

/**
 * The editor for the block
 * @param {*} props 
 * @returns {WPElement}
 */

const Editor = (props) => {

  const { attributes } = props;
  const { mediaUrl } = attributes;

  const blockStyle = {
    backgroundImage: mediaUrl[0] != '' ? 'url("' + mediaUrl[0] + '")' : 'none'
  };

  return (
    <div className="block-slider-img" style={blockStyle}>{mediaUrl[0] != '' ? null : <Placeholder className="components-placeholder--large" withIllustration={true} />}</div>
  );
}

const edit = (props) => {
  return (
    <>
      <Editor {...props} />
      <Controls {...props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

export default edit;
