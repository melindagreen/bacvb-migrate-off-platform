import domReady from "@wordpress/dom-ready";
import { addFilter } from "@wordpress/hooks";
import { InspectorControls, PanelColorSettings } from "@wordpress/block-editor"; // PanelColorSettings added here
import { useSelect } from "@wordpress/data";
import {
  PanelBody,
  TextControl,
  ToggleControl,
  RangeControl,
  SelectControl
  // ColorPalette is no longer needed here, PanelColorSettings handles the rendering
  // ColorPalette
} from "@wordpress/components";
import { createHigherOrderComponent } from "@wordpress/compose";
// 1. React and useState are no longer needed as PanelColorSettings handles the component state
// import React, { useState } from 'react';

domReady(() => {
  let attributesConfig = KrakenThemeSettings.blockFilters || [];

  // 2. Add attributes to existing blocks
  addFilter("blocks.registerBlockType", "kraken-core/extend-block-attributes", (settings, name) => {
    if (!attributesConfig[name]) return settings;

    settings.attributes = {
      ...settings.attributes,
      ...attributesConfig[name],
    };

    return settings;
  });

  // 3. Add dynamic inspector controls
  const withDynamicInspectorControls = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      const { name, attributes, setAttributes } = props;
      const config = attributesConfig[name];

      // Fetch the theme color palette dynamically using useSelect hook
      const themeColors = useSelect(
        (select) => {
          const settings = select('core/block-editor')?.getSettings();
          return settings?.colors || [];
        },
        [] // Empty dependency array ensures this runs only once
      );

      if (!config) return <BlockEdit {...props} />;

      // Prepare two separate arrays: one for colors and one for other controls
      const colorSettings = [];
      const nonColorControls = [];

      // Loop through all dynamic attributes to build the control lists
      Object.entries(config).forEach(([attrKey, def]) => {
        const { type, label, options, control = null } = def;
        const value = attributes[attrKey];
        const currentLabel = label || attrKey;
        const currentOptions = options;

        // --- Logic to separate Color attributes for PanelColorSettings ---
        if ((control || type) === "color") {
          colorSettings.push({
            label: currentLabel,
            value: value,
            onChange: (val) => setAttributes({ [attrKey]: val }),
            // PanelColorSettings automatically handles the swatches and picker
          });
          return; // Skip rendering this in the normal loop
        }
        // -----------------------------------------------------------------

        // --- Logic for non-Color attributes ---
        switch (control || type) {
          case "string":
            nonColorControls.push(
              <TextControl
                key={attrKey}
                label={currentLabel}
                value={value || ""}
                onChange={(val) => setAttributes({ [attrKey]: val })}
              />
            );
            break;
          case "select":
            if ( currentOptions ) {
              nonColorControls.push(
                <SelectControl
                  label={currentLabel}
                  value={value || ""}
                  options={currentOptions}
                  onChange={(val) => setAttributes({ [attrKey]: val })}
                />
              );
            }
            break;
          case "boolean":
            nonColorControls.push(
              <ToggleControl
                key={attrKey}
                label={currentLabel}
                checked={!!value}
                onChange={(val) => setAttributes({ [attrKey]: val })}
              />
            );
            break;
          case "number":
            nonColorControls.push(
              <RangeControl
                key={attrKey}
                label={currentLabel}
                value={value || 0}
                onChange={(val) => setAttributes({ [attrKey]: val })}
                min={0}
                max={1000}
              />
            );
            break;
          default:
            nonColorControls.push(null);
        }
      });

      return (
        <>
          <BlockEdit {...props} />
          <InspectorControls>
            {/* Render PanelColorSettings first if there are color attributes */}
            {colorSettings.length > 0 && (
              <PanelColorSettings
                title="Dynamic Color Settings"
                initialOpen={true}
                colorSettings={colorSettings} // The array of color control objects
                colors={themeColors}           // The theme colors fetched via useSelect
                disableCustomColors={true}     // Ensures only theme colors are available
              />
            )}

            {/* Render other attributes in a separate PanelBody */}
            {nonColorControls.length > 0 && (
              <PanelBody title="Other Dynamic Attributes" initialOpen={false}>
                {nonColorControls}
              </PanelBody>
            )}
          </InspectorControls>
        </>
      );
    };
  }, "withDynamicInspectorControls");

  addFilter(
    "editor.BlockEdit",
    "kraken-core/with-dynamic-inspector-controls",
    withDynamicInspectorControls,
  );

  // 4. Save attributes to the block's HTML output
  addFilter(
    "blocks.getSaveContent.extraProps",
    "kraken-core/save-custom-attributes",
    (extraProps, blockType, attributes) => {
      const config = attributesConfig[blockType.name];
      if (!config) return extraProps;

      Object.keys(config).forEach((key) => {
        if (attributes[key] !== undefined) {
          extraProps[`data-${key}`] = attributes[key];
        }
      });

      return extraProps;
    },
  );
});
