/**
 * This directory registers formats, allowing you to add inline styles through
 * the Format API.
 *
 * Read about Gutenberg formats on the Madden Wiki!
 * https://wiki.maddenmedia.com/Gutenberg_Formats
 */

/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { registerFormatType } from "@wordpress/rich-text";

// Local Dependencies
import allCaps from "./all-caps";
import justify from "./justify";
import nbsp from "./nbsp";
import { THEME_PREFIX } from "../../inc/constants";
import "./styles/style.scss";
import "./styles/index.scss";

/*** CONSTANTS **************************************************************/
const ALL_FORMATS = [allCaps, justify, nbsp];

/*** FUNCTIONS **************************************************************/

/**
 * Register all inline code formats
 * @returns {null}
 */
const registerAllFormats = () =>
	ALL_FORMATS.forEach((format) =>
		registerFormatType(THEME_PREFIX + "/" + format.name, format.options)
	);

/*** EXPORTS ****************************************************************/
export default registerAllFormats;
