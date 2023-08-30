/*** IMPORTS ****************************************************************/

// Local dependencies
import block from './block.json';
import edit from './edit';
import save from './save';

// Styles 
import './styles/style.scss';
import './styles/index.scss'; 

// Add edit and save to settings
block.settings.edit = edit;
block.settings.save = save;

/*** EXPORTS ****************************************************************/
export default block;
