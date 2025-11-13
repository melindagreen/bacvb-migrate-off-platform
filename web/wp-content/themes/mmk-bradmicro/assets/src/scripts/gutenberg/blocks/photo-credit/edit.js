import { __ } from "@wordpress/i18n";
import ServerSideRender from "@wordpress/server-side-render";
import {
	AlignmentToolbar,
	BlockControls,
	useBlockProps,
	InspectorControls,
} from "@wordpress/block-editor";
import { PanelBody, TextControl } from "@wordpress/components";

export default function Edit(props) {
	const { attributes, setAttributes, name } = props;
	const { caption, textAlign } = attributes;

	const blockProps = useBlockProps({ className: null });

	return (
		<>
			<BlockControls>
				<AlignmentToolbar
					value={textAlign}
					onChange={(value) => setAttributes({ textAlign: value })}
				/>
			</BlockControls>
			{/* Inspector Controls */}
			<InspectorControls>
				<PanelBody title={__("Credit Settings", "madden-theme")}>
					<TextControl
						label={__("Photo Caption", "madden-theme")}
						value={caption}
						onChange={(value) => setAttributes({ caption: value })}
						placeholder={__("Enter photo credit text", "madden-theme")}
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
