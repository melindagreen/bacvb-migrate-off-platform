// WordPress dependencies
import {Fragment} from 'react';
import {BlockControls} from '@wordpress/block-editor';

// Local Dependencies
// Inspector - used for controls in inspector
import Inspector from './inspector'

/*** CONSTANTS **************************************************************/



const Controls = (props) => {
  return (
  <Fragment>
    <Inspector {...props}/>
  </Fragment>)
}

/*** EXPORTS ****************************************************************/

export default Controls;
