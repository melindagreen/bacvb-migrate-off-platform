import { useBlockProps } from "@wordpress/block-editor";
import { TextControl, Notice } from "@wordpress/components";
import { useMemo } from "@wordpress/element";

function extractCrowdriffId(input) {
	if (!input) return "";
	const scriptMatch = input.match(/id=["'](cr-init__[a-zA-Z0-9]+)["']/);
	if (scriptMatch) return scriptMatch[1];
	const idMatch = input.match(/cr-init__[a-zA-Z0-9]+/);
	if (idMatch) return idMatch[0];
	return "";
}

export default function Edit({ attributes, setAttributes }) {
	const { crowdriffInput = "" } = attributes;
	const extractedId = useMemo(() => extractCrowdriffId(crowdriffInput), [crowdriffInput]);

	return (
		<div {...useBlockProps()}>
			<TextControl
				label="Crowdriff Script or ID"
				value={crowdriffInput}
				onChange={(val) => setAttributes({ crowdriffInput: val })}
				help="Paste the full Crowdriff script or just the embed ID (cr-init__...)."
			/>
			{crowdriffInput && !extractedId && (
				<Notice status="warning" isDismissible={false}>
					Could not extract a valid Crowdriff ID from your input.
				</Notice>
			)}
			{extractedId && (
				//show the element
                <div dangerouslySetInnerHTML={{ __html: crowdriffInput }} />
			)}
		</div>
	);
} 