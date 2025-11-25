// WordPress dependencies
import { __ } from "@wordpress/i18n";

// Local Dependencies
// Inspector - used for controls in inspector
import Inspector from './inspector'

/*** CONSTANTS **************************************************************/

const Controls = props => {
  return (
    <Inspector {...props} />
  )
}

/*** EXPORTS ****************************************************************/

export default Controls;
