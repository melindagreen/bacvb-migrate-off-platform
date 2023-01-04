/**
 * This directory registers block styles (special classes with an improved GUI)
 * through a JSON array and contains the stylesheets that corespond to those classes.
 * 
 * @link https://wiki.maddenmedia.com/Adding_a_Gutenberg_Block_Style
 */

/*** IMPORTS ****************************************************************/
import { registerBlockStyle, unregisterBlockStyle } from '@wordpress/blocks';

// Local dependencies
import coreStyles from './block-styles.json';
import './styles/style.scss';
import './styles/index.scss';

/*** FUNCTIONS **************************************************************/

/**
 * Take imported JSON and register all new core styles then unregister unwanted
 * default styles.
 * @returns {null}
 */
const registerAllBlockStyles = () => {
	// wp.domReady(function () {
	for ( const block in coreStyles ) {
		// Register new core styles
		if ( coreStyles[ block ].add && coreStyles[ block ].add.length > 0 )
			coreStyles[ block ].add.forEach( ( style ) =>
				registerBlockStyle( block, style )
			);

		// Unregister unwanted core styles
		// TODO 20220125 deregistering doesn't actually work right now :/ -ashw
		if (
			coreStyles[ block ].remove &&
			coreStyles[ block ].remove.length > 0
		)
			coreStyles[ block ].remove.forEach( ( style ) => {
				unregisterBlockStyle( block, style );
			} );
	}
	// });
};

/*** EXPORTS ****************************************************************/

export default registerAllBlockStyles;
