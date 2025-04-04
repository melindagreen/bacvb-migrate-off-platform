/*** IMPORTS ****************************************************************/

// Local dependencies
import block from './block';
import edit from './edit';
import save from './save';

// Styles
import "./styles/index.scss";

// Add edit and save to settings
block.settings.parent = block.parent;
block.settings.edit = edit;
block.settings.save = save;

/*** EXPORTS ****************************************************************/
export default block; 
