/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { registerBlockType } from "@wordpress/blocks";

// Local Dependencies
import { THEME_PREFIX, BLOCK_NAME_PREFIX } from "scripts/inc/constants";

import ExampleACF from './example-acf';
import ExampleDynamic from './example-dynamic';
import ExampleStatic from './example-static';
import Hero from './hero';
import PortraitGrid from './portrait-grid';

/*** CONSTANTS **************************************************************/

// Collect blocks
// NOTE: ACF blocks should not be registered here, only imported, or they will
// appear twice in the editor!
const blocks = [ Hero, PortraitGrid ];

/*** EXPORTS ****************************************************************/
export default () => {
  blocks.forEach(block => {
    block.settings.title = BLOCK_NAME_PREFIX + block.settings.title;
    registerBlockType(THEME_PREFIX + "/" + block.name, block.settings);
  });
};
