/*** IMPORTS ****************************************************************/

// Local dependencies
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl } = wp.components;
const { createHigherOrderComponent } = wp.compose;
const { addFilter } = wp.hooks;
import { useEntityRecords } from "@wordpress/core-data";

import { registerBlockType } from "@wordpress/blocks";
import edit from "./edit";
import save from "./save";
import metadata from "./block.json";
import { blockTable } from "@wordpress/icons";

// Styles -- MUST BE IMPORTED IF YOU WANT THEM IN THE BUILD FOLDER.
import "./styles/style.scss";

import { THEME_PREFIX } from "../../inc/constants";

registerBlockType(metadata, {
	edit,
	save,
	icon: blockTable,
});

// Extend the blocks with custom settings
const addMegaMenuInspectorControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		// Only apply to specific blocks (e.g., page link, navigation link, etc.)
		if (props.name === "core/navigation-link") {
			// Fetch all template parts.
			const { hasResolved, records } = useEntityRecords(
				"postType",
				"wp_template_part",
				{
					per_page: -1,
				}
			);

			let menuOptions = [];

			if (hasResolved) {
				menuOptions = records
					.filter((item) => item.area === "menu")
					.map((item) => ({
						label: item.title.rendered.replace("&#038;", "&"),
						value: item.slug,
					}));

				// Add "Select a Menu" as the first item
				menuOptions.unshift({
					label: __("Select a Menu", THEME_PREFIX),
					value: "", // Usually, an empty value indicates no selection
				});
			}

			return (
				<>
					<BlockEdit {...props} />
					<InspectorControls>
						<PanelBody title={__("Additional Settings", "text-domain")}>
							<SelectControl
								label={__("Mega Menu", "text-domain")}
								value={props.attributes.megaMenu || ""}
								options={menuOptions}
								onChange={(value) => {
									props.setAttributes({ megaMenu: value });
								}}
							/>
						</PanelBody>
					</InspectorControls>
				</>
			);
		}

		return <BlockEdit {...props} />;
	};
}, "withInspectorControls");

// Register the block extension for Page Link and other menu item blocks
addFilter(
	"editor.BlockEdit",
	"madden-theme/add-mega-menu-inspector-controls",
	addMegaMenuInspectorControls
);

// Add the attribute to the blocks
const addMegaMenuOptions = (settings, name) => {
	// Only add the attribute to specific blocks
	if (name === "core/navigation-link") {
		settings.attributes = {
			...settings.attributes,
			megaMenu: {
				type: "string",
				default: "",
			},
		};
	}

	return settings;
};

addFilter(
	"blocks.registerBlockType",
	"madden-theme/add-mega-menu-options",
	addMegaMenuOptions
);
