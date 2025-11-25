/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
  Button,
  __experimentalNumberControl as NumberControl,
  ResponsiveWrapper,
  SelectControl,
  TextControl,
  TextareaControl,
  ToggleControl,
} from "@wordpress/components";
import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";

/*** FUNCTIONS **************************************************************/

/**
 * Get dynamic attribute settings from registered filters
 * @param {string} filterName - The filter name suffix (e.g., "content-card.customAdditionalContent", "search-and-filter.customSettings")
 * @returns {Object} Object containing custom settings configurations
 */
export const getDynamicAttributeSettings = (filterName) => {
  // Apply the filter to get all custom settings from registered filters
  const settings = wp.hooks.applyFilters(`kraken-core.${filterName}`, {});
  return settings;
};

/**
 * Clean up old/unused values from dynamic attributes
 * @param {Object} currentSettings - The current available settings
 * @param {Object} currentAttributes - The current attribute values
 * @param {string} attributeName - The name of the attribute to clean
 * @returns {Object} Cleaned attributes object
 */
export const cleanupDynamicAttributes = (currentSettings, currentAttributes, attributeName) => {
  const currentAttributeValues = currentAttributes[attributeName] || {};
  const validKeys = Object.keys(currentSettings);

  // Only keep attributes that have corresponding settings
  const cleanedAttributes = {};
  validKeys.forEach((key) => {
    if (currentAttributeValues.hasOwnProperty(key)) {
      cleanedAttributes[key] = currentAttributeValues[key];
    }
  });

  return cleanedAttributes;
};

/**
 * Render dynamic controls based on custom settings
 * @param {Object} settings - The custom settings configuration object
 * @param {Object} attributes - Block attributes
 * @param {Function} setAttributes - Function to update block attributes
 * @param {string} attributeName - The name of the attribute to store the values (e.g., "customAdditionalContent", "customSettings")
 * @returns {Array} Array of React components for the controls
 */
export const renderDynamicControls = (
  settings,
  attributes,
  setAttributes,
  attributeName = "customAdditionalContent",
) => {
  const controls = [];

  Object.entries(settings).forEach(([key, config]) => {
    const currentValue = attributes[attributeName]?.[key] ?? config.default;

    switch (config.type) {
      case "boolean":
        controls.push(
          <ToggleControl
            key={key}
            label={config.label || key}
            checked={currentValue}
            onChange={(value) => {
              setAttributes({
                [attributeName]: {
                  ...attributes[attributeName],
                  [key]: value,
                },
              });
            }}
          />,
        );
        break;

      case "string":
        controls.push(
          <TextControl
            key={key}
            label={config.label || key}
            value={currentValue}
            onChange={(value) => {
              setAttributes({
                [attributeName]: {
                  ...attributes[attributeName],
                  [key]: value,
                },
              });
            }}
          />,
        );
        break;

      case "media":
        controls.push(
          <MediaUploadCheck>
            <MediaUpload
              label={config.label || key}
              allowedTypes={["image"]}
              onSelect={(images) => {
                setAttributes({
                  [attributeName]: {
                    [key]: {
                      id: images.id,
                      url: images.url,
                    },
                  },
                });
              }}
              value={currentValue.id}
              render={({ open }) => (
                <div className="image-select">
                  <Button onClick={open} isLarge icon="format-gallery">
                    {config.label || key}
                  </Button>
                  {currentValue.url != "" && (
                    <ResponsiveWrapper>
                      <img src={currentValue.url} />
                    </ResponsiveWrapper>
                  )}
                </div>
              )}
            />
          </MediaUploadCheck>,
        );
        break;

      case "number":
        controls.push(
          <NumberControl
            key={key}
            label={config.label || key}
            value={currentValue}
            min={config.min || 0}
            max={config.max || 100}
            onChange={(value) => {
              setAttributes({
                [attributeName]: {
                  ...attributes[attributeName],
                  [key]: Number(value),
                },
              });
            }}
          />,
        );
        break;

      case "select":
        if (config.options && Array.isArray(config.options)) {
          controls.push(
            <SelectControl
              key={key}
              label={config.label || key}
              value={currentValue}
              options={config.options}
              onChange={(value) => {
                setAttributes({
                  [attributeName]: {
                    ...attributes[attributeName],
                    [key]: value,
                  },
                });
              }}
            />,
          );
        }
        break;

      case "textarea":
        controls.push(
          <TextareaControl
            key={key}
            label={config.label || key}
            value={currentValue}
            onChange={(value) => {
              setAttributes({
                [attributeName]: {
                  ...attributes[attributeName],
                  [key]: value,
                },
              });
            }}
          />,
        );
        break;

      default:
        console.warn(`Unknown control type: ${config.type} for key: ${key}`);
    }
  });

  return controls;
};
