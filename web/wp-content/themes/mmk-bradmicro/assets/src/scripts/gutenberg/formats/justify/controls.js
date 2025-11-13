/**
 * External dependencies
 */
import { get } from "lodash";

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { compose, ifCondition } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { RichTextToolbarButton } = wp.blockEditor;

class JustifyControl extends Component {
	render() {
		const { blockId, isBlockJustified, updateBlockAttributes } = this.props;

		const onToggle = () => {
			updateBlockAttributes(blockId, {
				align: isBlockJustified ? null : "justify",
			});
		};
		return (
			<RichTextToolbarButton
				icon="editor-justify"
				title={__("Justify", "madden-theme")}
				onClick={onToggle}
				isActive={isBlockJustified}
			/>
		);
	}
}

export default compose(
	withSelect((select) => {
		const isDisabled = false;
		const selectedBlock = select("core/block-editor").getSelectedBlock();
		if (isDisabled || !selectedBlock) {
			return {
				isDisabled,
			};
		}
		return {
			isDisabled,
			blockId: selectedBlock.clientId,
			blockName: selectedBlock.name,
			isBlockJustified: "justify" === get(selectedBlock, "attributes.align"),
			formatTypes: select("core/rich-text").getFormatTypes(),
		};
	}),
	withDispatch((dispatch) => ({
		updateBlockAttributes: dispatch("core/block-editor").updateBlockAttributes,
	})),
	ifCondition((props) => {
		if (props.isDisabled || !props.blockId) {
			return false;
		}
		return "core/paragraph" === props.blockName;
	})
)(JustifyControl);
