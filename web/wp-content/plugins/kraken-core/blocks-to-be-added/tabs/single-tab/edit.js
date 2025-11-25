import { __ } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks, RichText, InspectorControls } from '@wordpress/block-editor';
import ColorControls from '../controls/color-controls';

const Inspector = (props) => {
	return (
		<InspectorControls group="styles">
			<ColorControls {...props} />
		</InspectorControls>
	);
};

export default function Edit(props) {
	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();

	return (
		<>
			<Inspector {...props} />
			<div {...blockProps}>
				<RichText
					tagName="p"
					className="tab-title-editor"
					placeholder={__('Tab Title', 'madden-theme')}
					value={attributes.title}
					onChange={(title) => setAttributes({ title })}
					withoutInteractiveFormatting
				/>
				<InnerBlocks templateLock={false} />
			</div>
		</>
	);
}