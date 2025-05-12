/*** IMPORTS ****************************************************************/

// Local dependencies
import block from './block.json';
import Edit from './edit';
import Save from './save';

// Styles 
import './styles/style.scss';
import './styles/index.scss'; 

// Add edit and save to settings
block.settings.edit = Edit;
block.settings.save = Save;

/*** EXPORTS ****************************************************************/
export default block;
