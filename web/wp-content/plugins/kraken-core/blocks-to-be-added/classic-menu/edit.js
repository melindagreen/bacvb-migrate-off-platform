import { __ } from "@wordpress/i18n";
import ServerSideRender from "@wordpress/server-side-render";
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
	PanelColorSettings,
	__experimentalSpacingSizesControl as SpacingSizesControl,
} from "@wordpress/block-editor";
import {
	PanelBody,
	SelectControl,
	Spinner,
	ToggleControl
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";

export default function Edit(props) {
	const { attributes, setAttributes, name } = props;
	const {
		menu,
		toggles,
		textAlign,
		linkPadding,
		linkEqualHeight,
		subNavDisplay,
		subNavTextColor,
		subNavBackgroundColor,
		subNavTextAlign,
		subNavPadding,
		subNavLinkPadding,
	} = attributes;

	const blockProps = useBlockProps({ className: null });

	// Fetch classic menus using core API
	const { menus, isLoading } = useSelect((select) => {
		const menuStore = select("core");
		return {
			menus: menuStore.getMenus(),
			isLoading: !menuStore.hasFinishedResolution("getMenus"),
		};
	}, []);

	const menuOptions =
		menus?.map(({ id, name }) => ({
			label: name,
			value: id.toString(),
		})) || [];

	return (
		<>
			{/* Block Controls */}
			<BlockControls>
				<AlignmentToolbar
					value={textAlign}
					onChange={(value) => setAttributes({ textAlign: value })}
				/>
			</BlockControls>

			{/* Inspector Controls */}
			<InspectorControls>
				<PanelBody title={__("Menu Settings", "madden-theme")}>
					{isLoading ? (
						<Spinner />
					) : (
						<SelectControl
							label={__("Select Menu", "madden-theme")}
							value={menu}
							options={[
								{ label: __("Select a menu", "madden-theme"), value: "" },
								...menuOptions,
							]}
							onChange={(value) => setAttributes({ menu: value })}
						/>
					)}
					<ToggleControl
						label={__("Show Sub Menu Toggle", "madden-theme")}
						checked={toggles}
						onChange={(value) => setAttributes({ toggles: value })}
					/>
					<SelectControl
						label={__("Select Menu Display", "madden-theme")}
						value={subNavDisplay}
						options={[
							{ label: __("Always Visible", "madden-theme"), value: "visible" },
							{ label: __("On Hover", "madden-theme"), value: "hover" },
							{ label: __("On Click", "madden-theme"), value: "click" },
							{
								label: __("On Toggle Click", "madden-theme"),
								value: "toggle-click",
							},
						]}
						onChange={(value) => setAttributes({ subNavDisplay: value })}
					/>
					<SelectControl
						label={__("Mobile Breakpoint", "madden-theme")}
						value={attributes.mobileBreakpoint}
						options={[
							{ label: __("Small", "madden-theme"), value: "sm" },
							{ label: __("Medium", "madden-theme"), value: "md" },
							{ label: __("Large", "madden-theme"), value: "lg" },
							{ label: __("X-Large", "madden-theme"), value: "xl" },
							{ label: __("Disabled", "madden-theme"), value: "disabled" },
						]}
						onChange={(value) => setAttributes({ mobileBreakpoint: value })}
					/>
				</PanelBody>
				<PanelBody title={__("Link Settings", "madden-theme")}>
					<ToggleControl
						label={__("Equal Height Links", "madden-theme")}
						checked={linkEqualHeight}
						onChange={(value) => setAttributes({ linkEqualHeight: value })}
					/>
					<SpacingSizesControl
						label={__("Link Padding", "madden-theme")}
						values={linkPadding}
						onChange={(value) => {
							console.log("value", value);
							if (typeof value === "object") {
								setAttributes({ linkPadding: value });
							}
						}}
					/>
				</PanelBody>
				<PanelBody
					title={__("Sub Menu Settings", "madden-theme")}
					initialOpen={false}
				>
					<AlignmentToolbar
						value={subNavTextAlign}
						onChange={(value) => setAttributes({ subNavTextAlign: value })}
					/>
					<PanelColorSettings
						title={__("Sub Menu Colors", "madden-theme")}
						colorSettings={[
							{
								label: __("Text Color", "madden-theme"),
								value: subNavTextColor,
								onChange: (value) => setAttributes({ subNavTextColor: value }),
							},
							{
								label: __("Background Color", "madden-theme"),
								value: subNavBackgroundColor,
								onChange: (value) =>
									setAttributes({ subNavBackgroundColor: value }),
							},
						]}
					/>
					<SpacingSizesControl
						label={__("Sub Menu Padding", "madden-theme")}
						values={subNavPadding}
						onChange={(value) => setAttributes({ subNavPadding: value })}
						units={["px", "em", "rem"]}
					/>
					<SpacingSizesControl
						label={__("Sub Menu Link Padding", "madden-theme")}
						values={subNavLinkPadding}
						onChange={(value) => setAttributes({ subNavLinkPadding: value })}
						units={["px", "em", "rem"]}
					/>
				</PanelBody>
			</InspectorControls>

			{/* Block Rendering */}
			<div {...blockProps}>
				<ServerSideRender block={name} attributes={attributes} />
			</div>
		</>
	);
}
