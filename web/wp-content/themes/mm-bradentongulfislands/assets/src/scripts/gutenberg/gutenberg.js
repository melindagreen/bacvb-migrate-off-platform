// WordPress dependencies
import domReady from '@wordpress/dom-ready';

// Local dependencies
import registerAllBlocks from './blocks';
import registerAllBlockStyles from './block-styles';
import registerAllBlockVariations from './block-variations';
import addAllFilters from './filters';
import addAllFormats from './formats';

// Fire all core funcs
domReady( () => {
	registerAllBlockStyles();
	registerAllBlockVariations();
	addAllFilters();
	registerAllBlocks();
	addAllFormats();
} );
