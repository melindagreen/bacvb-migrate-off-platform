/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { Button, PanelBody } from "@wordpress/components";
import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";

// Local dependencies
import { THEME_PREFIX } from "scripts/inc/constants";

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ["image"];

/*** COMPONENTS **************************************************************/

const MediaControls = (props) => {
	const { attributes, setAttributes } = props;
	const { imageId } = attributes;
	const onSelect = (image) => {
		let smallImage =
			typeof image?.sizes?.madden_hero_sm !== "undefined"
				? image.sizes.madden_hero_sm.url
				: image.url;

		setAttributes({
			imageUrl: smallImage,
			imageAlt: image.alt,
		});
	};

	return (
		<>
			<MediaUploadCheck>
				<MediaUpload
					title={__("Choose Image", THEME_PREFIX)}
					allowedTypes={ALLOWED_MEDIA_TYPES}
					onSelect={onSelect}
					value={imageId}
					render={({ open }) => (
						<Button onClick={open} icon="format-gallery" isSecondary>
							{__("Choose Image", THEME_PREFIX)}
						</Button>
					)}
				/>
			</MediaUploadCheck>
		</>
	);
};

const Inspector = (props) => {
	const { attributes, setAttributes } = props;

	return (
		<InspectorControls>
			<PanelBody title="Social Button Image">
				<MediaControls {...props} />
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
