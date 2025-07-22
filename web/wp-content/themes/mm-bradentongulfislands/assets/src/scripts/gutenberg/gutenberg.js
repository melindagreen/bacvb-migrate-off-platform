// WordPress dependencies
import domReady from "@wordpress/dom-ready";

// Local dependencies
import registerAllBlockStyles from "./block-styles";
import addAllFilters from "./filters";
import addAllFormats from "./formats";
import addAllVariations from "./variations";
import addAllDispatches from './dispatches';

// Fire all core funcs
domReady(() => {
  addAllDispatches();
  addAllFilters();
  addAllFormats();
  registerAllBlockStyles();
  addAllVariations();
});