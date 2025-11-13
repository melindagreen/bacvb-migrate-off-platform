/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import { useEntityRecords } from "@wordpress/core-data";
import { FormTokenField, PanelBody } from "@wordpress/components";
import { THEME_PREFIX } from "../../inc/constants";

/**
 * Internal dependencies
 */
import "./styles/edit.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const { menus } = attributes;

	// Fetch all template parts.
	const { hasResolved, records } = useEntityRecords(
		"postType",
		"wp_template_part",
		{
			per_page: -1,
		}
	);

	let menuOptions = [];

	// Filter the template parts for those in the 'menu' area.
	if (hasResolved) {
		menuOptions = records
			.filter((item) => item.area === "menu")
			.map((item) => ({
				label: item.title.rendered.replace("&#038;", "&"),
				value: item.slug,
			}));
	}

	const tokenValue = () => {
		const tValue =
			hasResolved && menus
				? menus.map((menu) => {
						const menuOption = menuOptions.filter((opt) => opt.value === menu);
						return menuOption[0].label.replace("&#038;", "&");
				  })
				: [];
		return tValue;
	};

	const tokenSuggestions = () => {
		const tSuggestions =
			hasResolved && menuOptions
				? menuOptions.map((menu) => menu.label.replace("&#038;", "&"))
				: [];
		return tSuggestions;
	};

	const tokenChange = (selectedItems) => {
		const updatedItems = selectedItems.reduce((itemsArray, itemName) => {
			let findItem = menuOptions.find(
				(menuOption) =>
					menuOption.label.replace("&#038;", "&") ==
					itemName.replace("&#038;", "&")
			);
			if (findItem) {
				itemsArray.push(findItem.value);
			}
			return itemsArray;
		}, []);

		setAttributes({ menus: updatedItems });
	};

	// Modify block props.
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls group="settings">
				<PanelBody
					className={`${THEME_PREFIX}-mega-menu__settings-panel`}
					title={__("Settings", THEME_PREFIX)}
					initialOpen={true}
				>
					<FormTokenField
						label={__("Menus", THEME_PREFIX)}
						value={tokenValue()}
						suggestions={tokenSuggestions()}
						onChange={(selected) => tokenChange(selected)}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>Mega menus placeholder</div>
		</>
	);
}
