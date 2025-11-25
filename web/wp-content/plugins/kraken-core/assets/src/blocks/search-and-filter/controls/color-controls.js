/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { PanelBody, Button } from "@wordpress/components";
import { PanelColorSettings, withColors } from "@wordpress/block-editor";

/*** CONSTANTS **************************************************************/

// A centralized configuration for all color settings.
// Makes adding, removing, or changing colors much simpler.
const colorConfigurations = {
  general: [
    { attribute: "backgroundColor", label: "Card Background" },
    { attribute: "textColor", label: "Card Text" },
    { attribute: "spinnerColor", label: "Loading Spinner" },
    { attribute: "resultsCountTextColor", label: "Results Count Text" },
    { attribute: "noResultsTextColor", label: "No Results Text" },
  ],
  filters: [
    { attribute: "filterBarBackgroundColor", label: "Bar Background" },
    { attribute: "filterBarTextColor", label: "Bar Text" },
    { attribute: "filterBackgroundColor", label: "Background" },
    { attribute: "filterTextColor", label: "Text" },
    { attribute: "filterBackgroundHoverColor", label: "Background Hover" },
    { attribute: "filterTextHoverColor", label: "Text Hover" },
    { attribute: "activeFilterBackgroundColor", label: "Active Background" },
    { attribute: "activeFilterTextColor", label: "Active Text" },
    {
      attribute: "activeFilterBackgroundHoverColor",
      label: "Active Background Hover",
    },
    { attribute: "activeFilterTextHoverColor", label: "Active Text Hover" },
    { attribute: "resetFilterBackgroundColor", label: "Reset Background" },
    { attribute: "resetFilterTextColor", label: "Reset Text" },
    {
      attribute: "resetFilterBackgroundHoverColor",
      label: "Reset Background Hover",
    },
    { attribute: "resetFilterTextHoverColor", label: "Reset Text Hover" },
  ],
  events: [
    { attribute: "eventDateBackgroundColor", label: "Event Date Background" },
    { attribute: "eventDateTextColor", label: "Event Date Text" },
  ],
  pagination: [
    { attribute: "paginationBackgroundColor", label: "Background" },
    { attribute: "paginationBackgroundHoverColor", label: "Background Hover" },
    {
      attribute: "paginationBackgroundActiveColor",
      label: "Background Active",
    },
    { attribute: "paginationTextColor", label: "Text" },
    { attribute: "paginationTextHoverColor", label: " Text Hover" },
    { attribute: "paginationTextActiveColor", label: "Text Active" },

    { attribute: "paginationArrowBackgroundColor", label: "Arrow Background" },
    {
      attribute: "paginationArrowBackgroundHoverColor",
      label: "Arrow Background Hover",
    },
    { attribute: "paginationArrowColor", label: "Arrow" },
    { attribute: "paginationArrowHoverColor", label: "Arrow Hover" },
  ],
  toggles: [
    { attribute: "viewToggleBackgroundColor", label: "Background" },
    { attribute: "viewToggleBackgroundHoverColor", label: "Background Hover" },
    { attribute: "viewToggleTextColor", label: "Text" },
    { attribute: "viewToggleTextHoverColor", label: "Text Hover" },
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
      label: __(label, "kraken-core"),
    };
  });

  /**
   * Resets all colors in this panel by calling their setters with undefined.
   */
  const handleReset = () => {
    colors.forEach(({ attribute }) => {
      const setterName = `set${attribute.charAt(0).toUpperCase() + attribute.slice(1)}`;
      const setter = props[setterName];
      if (setter) {
        setter(undefined);
      }
    });
  };

  /**
   * Checks if any color in this panel has been set.
   * @returns {boolean} True if at least one color is set.
   */
  const hasColorsSet = colors.some(({ attribute }) => {
    return props[attribute]?.color !== undefined;
  });

  return (
    <PanelBody title={__(title, "kraken-core")} initialOpen={false}>
      <PanelColorSettings
        colorSettings={colorPanelSettings}
        className={`kraken-core-color-panel`}
      />
      {hasColorsSet && (
        <div style={{ marginTop: "0.75rem" }}>
          <Button variant="secondary" isSmall onClick={handleReset}>
            {__("Reset Colors", "kraken-core")}
          </Button>
        </div>
      )}
    </PanelBody>
  );
};

const ColorControls = (props) => {
  return (
    <>
      <ColorPanel title="General Colors" colors={colorConfigurations.general} props={props} />
      <ColorPanel title="Filter Colors" colors={colorConfigurations.filters} props={props} />
      {props.attributes["postType"] === "event" && (
        <ColorPanel title="Event Colors" colors={colorConfigurations.events} props={props} />
      )}
      <ColorPanel title="Pagination Colors" colors={colorConfigurations.pagination} props={props} />
      <ColorPanel title="View Toggle Colors" colors={colorConfigurations.toggles} props={props} />
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
