// WordPress dependencies
import {Fragment} from 'react';
import {BlockControls, MediaReplaceFlow} from '@wordpress/block-editor';

// Local Dependencies
// Inspector - used for controls in inspector
import Inspector from './inspector'

import Toolbar from './toolbar'

/*** CONSTANTS **************************************************************/



const Controls = (props) => {
  return (
  <Fragment>
     <BlockControls>
      <Toolbar {...props}/>
    </BlockControls>
    <Inspector {...props}/>
  </Fragment>)
}

/*** EXPORTS ****************************************************************/

export default Controls;
