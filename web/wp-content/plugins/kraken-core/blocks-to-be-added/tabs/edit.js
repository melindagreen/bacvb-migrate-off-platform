import { __ } from "@wordpress/i18n";
import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";
import { useState, useEffect, useRef } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import Inspector from "./controls/inspector";

const TEMPLATE = [["madden-theme/single-tab"]];

// Helper function to convert camelCase attribute names to kebab-case CSS properties
const toKebabCase = (string) => {
	return string.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, "$1-$2").toLowerCase();
};

/**
 * Creates a style object from the block's color attributes.
 * @param {Object} attributes The block attributes.
 * @returns {Object} A style object with CSS custom properties.
 */
const getColorStyles = (attributes) => {
	const styles = {};
	const colorAttributes = [
		"tabBackgroundColor",
		"tabTextColor",
		"tabBackgroundHoverColor",
		"tabTextHoverColor",
		"tabBackgroundActiveColor",
		"tabTextActiveColor",
	];

	for (const attr of colorAttributes) {
		const value = attributes[attr];
		if (value) {
			const cssVar = `--${toKebabCase(attr)}`;
			// Handle theme color slugs vs. custom hex values
			if (value.startsWith("#")) {
				styles[cssVar] = value;
			} else {
				styles[cssVar] = `var(--wp--preset--color--${value})`;
			}
		}
	}
	return styles;
};

export default function Edit(props) {
	const { attributes, clientId } = props;
	const [activeTab, setActiveTab] = useState(0);

	const { innerBlocks } = useSelect((select) => ({
		innerBlocks: select("core/block-editor").getBlocks(clientId),
	}));

	const prevInnerBlocksLength = useRef(innerBlocks.length);

	// When tabs are added or removed, adjust the active tab index
	useEffect(() => {
		const currentLength = innerBlocks.length;
		const previousLength = prevInnerBlocksLength.current;

		if (currentLength > previousLength) {
			setActiveTab(currentLength - 1);
		} else if (activeTab >= currentLength && currentLength > 0) {
			setActiveTab(currentLength - 1);
		}

		prevInnerBlocksLength.current = currentLength;
	}, [innerBlocks]);

	// Get styles from the parent block's attributes
	const parentStyles = getColorStyles(attributes);
	const blockProps = useBlockProps({ style: parentStyles });

	// Show/hide inactive tabs in the editor
	useEffect(() => {
		innerBlocks.forEach((block, index) => {
			const blockElement = document.getElementById(`block-${block.clientId}`);
			if (blockElement) {
				blockElement.style.display = index === activeTab ? "block" : "none";
			}
		});
	}, [activeTab, innerBlocks]);

	return (
		<>
			<Inspector {...props} />
			<div {...blockProps}>
				<div className="tabs-nav">
					{innerBlocks.map((block, index) => {
						// Get override styles from the child tab's attributes
						const childStyles = getColorStyles(block.attributes);

						return (
							<button
								key={block.clientId}
								className={index === activeTab ? "active" : ""}
								onClick={() => setActiveTab(index)}
								style={childStyles}
							>
								{block.attributes.title || `Tab ${index + 1}`}
							</button>
						);
					})}
				</div>
				<div className="tabs-content">
					<InnerBlocks
						allowedBlocks={["madden-theme/single-tab"]}
						template={TEMPLATE}
						renderAppender={InnerBlocks.DefaultAppender}
					/>
				</div>
			</div>
		</>
	);
}
