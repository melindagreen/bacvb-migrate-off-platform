/**
 * EXAMPLE: How to Use the Dynamic Attribute Filter System
 *
 * This file demonstrates how to use the dynamic attribute filter system
 * to add custom controls to any block in the Kraken Core plugin.
 *
 * IMPORTANT: This is currently NOT being used by the content-card block.
 * The content-card block now pulls settings from KrakenThemeSettings.blockData.cardAttributes
 * instead of using the filter system. However, this filter system is available
 * for use in other blocks or future implementations.
 *
 * HOW THE FILTER SYSTEM WORKS:
 * 1. Register a filter using addFilter() with a specific filter name pattern
 * 2. The filter name must follow: "kraken-core.{blockName}.{attributeName}"
 * 3. Return an object with control configurations
 * 4. Use getDynamicAttributeSettings() and renderDynamicControls() in your block
 *
 * FILTER NAME PATTERN:
 * - "kraken-core.content-card.customAdditionalContent" -> attributes.customAdditionalContent
 * - "kraken-core.search-and-filter.customSettings" -> attributes.customSettings
 * - "kraken-core.any-block.customOptions" -> attributes.customOptions
 */

import { addFilter } from "@wordpress/hooks";

// Example: Add custom settings for region display
const customSettings = {
  displayRegion: {
    type: "boolean",
    default: false,
    label: "Display Region",
  },
  displayMilePosts: {
    type: "boolean",
    default: false,
    label: "Display Mile Posts",
  },
  regionText: {
    type: "string",
    default: "",
    label: "Region Text",
  },
  priority: {
    type: "number",
    default: 1,
    min: 1,
    max: 10,
    label: "Priority Level",
  },
  regionType: {
    type: "select",
    default: "state",
    label: "Region Type",
    options: [
      { label: "State", value: "state" },
      { label: "County", value: "county" },
      { label: "City", value: "city" },
      { label: "Country", value: "country" },
    ],
  },
  regionDescription: {
    type: "textarea",
    default: "",
    label: "Region Description",
  },
};

// Apply the filter to add these custom settings
// NOTE: This filter is currently NOT being used by the content-card block
addFilter(
  "kraken-core.content-card.customAdditionalContent",
  "kraken-core/content-card/example",
  () => customSettings,
);

/**
 * HOW TO USE THIS FILTER SYSTEM IN YOUR BLOCKS:
 *
 * 1. IMPORT THE HELPER FUNCTIONS:
 *    import { getDynamicAttributeSettings, renderDynamicControls } from "../helpers";
 *
 * 2. IN YOUR BLOCK COMPONENT:
 *    const customSettings = getDynamicAttributeSettings("your-block.yourAttribute");
 *    if (Object.keys(customSettings).length > 0) {
 *      return (
 *        <>
 *          <hr />
 *          {renderDynamicControls(customSettings, attributes, setAttributes, "yourAttribute")}
 *        </>
 *      );
 *    }
 *
 * 3. REGISTER YOUR FILTER:
 *    addFilter(
 *      "kraken-core.your-block.yourAttribute",
 *      "kraken-core/your-block",
 *      () => yourCustomSettings,
 *    );
 *
 * SUPPORTED CONTROL TYPES:
 * - "boolean" -> ToggleControl
 * - "string" -> TextControl
 * - "number" -> NumberControl (with min/max support)
 * - "select" -> SelectControl (requires options array)
 * - "textarea" -> TextareaControl
 *
 * CONTROL CONFIGURATION OBJECT:
 * {
 *   controlKey: {
 *     type: "boolean|string|number|select|textarea",
 *     default: defaultValue,
 *     label: "Display Label",
 *     min: 0,        // For number controls
 *     max: 100,      // For number controls
 *     options: [     // For select controls
 *       { label: "Option 1", value: "value1" },
 *       { label: "Option 2", value: "value2" }
 *     ]
 *   }
 * }
 *
 * EXAMPLE FOR DIFFERENT BLOCKS:
 *
 * // For search-and-filter block with customSettings attribute:
 * addFilter(
 *   "kraken-core.search-and-filter.customSettings",
 *   "kraken-core/search-and-filter",
 *   () => ({
 *     enableAdvancedSearch: {
 *       type: "boolean",
 *       default: false,
 *       label: "Enable Advanced Search"
 *     }
 *   })
 * );
 *
 * // For any block with customOptions attribute:
 * addFilter(
 *   "kraken-core.my-block.customOptions",
 *   "kraken-core/my-block",
 *   () => ({
 *     customColor: {
 *       type: "select",
 *       default: "blue",
 *       label: "Custom Color",
 *       options: [
 *         { label: "Blue", value: "blue" },
 *         { label: "Red", value: "red" },
 *         { label: "Green", value: "green" }
 *       ]
 *     }
 *   })
 * );
 */
