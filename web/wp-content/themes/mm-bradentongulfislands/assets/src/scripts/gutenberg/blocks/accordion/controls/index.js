// WordPress dependencies
import { Fragment } from 'react';
import { BlockControls } from '@wordpress/block-editor';

// Local Dependencies
// Inspector - used for controls in inspector 
import Inspector from './inspector'

// Toolbar - used for controls in toolbar 
import Toolbar from './toolbar'

/*** CONSTANTS **************************************************************/

const Controls = props => {
  return (
    <Fragment>
      {/* <BlockControls>
        <Toolbar {...props} />
      </BlockControls> */}
      <Inspector {...props} />
    </Fragment>
  )
}

/*** EXPORTS ****************************************************************/

export default Controls