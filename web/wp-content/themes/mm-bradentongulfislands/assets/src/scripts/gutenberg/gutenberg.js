// WordPress dependencies
import domReady from '@wordpress/dom-ready';

// Local dependencies
import registerAllBlocks from './blocks';
import registerAllBlockStyles from './block-styles';
import addAllFilters from './filters';
import addAllFormats from './formats';

// Fire all core funcs
domReady( () => {
	registerAllBlockStyles();
	addAllFilters();
	registerAllBlocks();
	addAllFormats();
} );
