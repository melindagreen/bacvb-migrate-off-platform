/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { PanelBody } from "@wordpress/components";
import { PanelColorSettings, withColors } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

// A centralized configuration for all color settings.
// Makes adding, removing, or changing colors much simpler.
const colorConfigurations = {
  general: [
    { attribute: "tabBackgroundColor", label: "Tab Background" },
    { attribute: "tabTextColor", label: "Tab Text" },
    { attribute: "tabBackgroundHoverColor", label: "Tab Background Hover" },
    { attribute: "tabTextHoverColor", label: "Tab Text Hover" },
    { attribute: "tabBackgroundActiveColor", label: "Tab Background Active" },
    { attribute: "tabTextActiveColor", label: "Tab Text Active" },
  ],
};

/*** FUNCTIONS **************************************************************/

/**
 * Converts a camelCase string to a kebab-case string.
 * e.g., "backgroundColor" -> "background-color"
 * @param {string} string The string to convert.
 * @returns {string} The kebab-cased string.
 */
const toKebabCase = (string) => {
  return string.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, "$1-$2").toLowerCase();
};

/**
 * A reusable component to render a panel with color settings.
 * @param {object} props The component props.
 * @returns {JSX.Element} The rendered component.
 */
const ColorPanel = ({ title, colors, props }) => {
  const colorPanelSettings = colors.map(({ attribute, label }) => {
    const setterName = `set${attribute.charAt(0).toUpperCase() + attribute.slice(1)}`;
    return {
      value: props[attribute].color,
      onChange: props[setterName],
      label: __(label, "madden-theme"),
    };
  });

  return (
    <PanelColorSettings
      title={__(title, "madden-theme")}
      colorSettings={colorPanelSettings}
      className={`kraken-core-color-panel`}
    />
  );
};

const ColorControls = (props) => {
  return (
    <>
      <ColorPanel title="Tab Colors" colors={colorConfigurations.general} props={props} />
    </>
  );
};

/*** EXPORTS ****************************************************************/

// Dynamically create the color mapping for the withColors HOC.
const colorMap = Object.values(colorConfigurations)
  .flat()
  .reduce((acc, { attribute }) => {
    acc[attribute] = toKebabCase(attribute);
    return acc;
  }, {});

export default withColors(colorMap)(ColorControls);
