//Imports
import {registerBlockVariation} from "@wordpress/blocks";

// Local dependencies
import coreVariations from './block_variations.json';

/*** FUNCTIONS **************************************************************/

/**
 * Take imported JSON and register all new core variations.
 * @returns {null}
 */
const registerAllBlockVariations = () => {
    
	for ( const block in coreVariations ) {
		// Register new core variations
		if ( coreVariations[ block ].add && coreVariations[ block ].add.length > 0 )
			coreVariations[ block ].add.forEach( ( variation ) =>{
				registerBlockVariation( block, variation ) }
			);
	}
};

/*** EXPORTS ****************************************************************/

export default registerAllBlockVariations;