/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	ComboboxControl,
	SelectControl,
	Button,
	ResponsiveWrapper,
	TextControl,
	Spinner,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";
import {
	MediaUpload,
	MediaUploadCheck,
	__experimentalLinkControl as LinkControl,
} from "@wordpress/block-editor";

import CardContent from "./card-content";

/*** CONSTANTS **************************************************************/
import { CARD_STYLES } from "../../../inc/constants";

/*** FUNCTIONS **************************************************************/

const Wizard = (props) => {
	const { attributes, setAttributes } = props;

	const { posts, hasResolved } = useSelect(
		(select) => {
			const query = [
				"postType",
				attributes.contentType,
				{
					per_page: -1,
					status: "publish",
					order: "desc",
					orderby: "date",
				},
			];
			if (attributes.contentType !== "custom") {
				return {
					posts: select(coreDataStore).getEntityRecords(...query),
					hasResolved: select(coreDataStore).hasFinishedResolution(
						"getEntityRecords",
						query
					),
				};
			} else {
				return { posts: null };
			}
		},
		[attributes.contentType]
	);

	const renderPosts = () => {
		let options = [];

		if (posts) {
			posts.forEach((post) => {
				options.push({ value: post.id, label: post.title.raw });
			});
		} else {
			options.push({
				label: __(attributes.contentTitle),
				value: attributes.contentId,
			});
			options.push({ value: 0, label: __("Loading...") });
		}

		return options;
	};
	const [filteredOptions, setFilteredOptions] = useState(renderPosts());

	const selectPost = (id) => {
		if (id && id !== 0) {
			const content = posts.find((post) => post.id == id);
			setAttributes({
				contentId: content.id,
				contentTitle: content.title.raw,
				mode: "preview",
			});
		}
	};

	//sets the custom image for custom content cards
	const onImageSelect = (images) => {
		setAttributes({
			customImage: images.id,
		});
		setAttributes({
			customImageUrl: images.url,
		});
	};

	return (
		<div
			className={`content-selector wp-block-mm-bradentongulfislands-content-card`}
		>
			<SelectControl
				label={__("Card Style")}
				value={attributes.cardStyle}
				options={CARD_STYLES}
				onChange={(val) => {
					setAttributes({ cardStyle: val });
				}}
			/>
			<SelectControl
				label={__("Content Type")}
				value={attributes.contentType}
				options={[
					{ label: "Blog Posts", value: "post" },
					{ label: "Pages", value: "page" },
					{ label: "Custom", value: "custom" },
				]}
				onChange={(val) => {
					setAttributes({ contentType: val });
				}}
			/>
			{attributes.contentType === "custom" ? (
				<>
					<TextControl
						label={__("Content Title")}
						value={attributes.contentTitle}
						onChange={(val) => {
							setAttributes({ contentTitle: val });
						}}
					/>
					<TextControl
						label={__("Content Excerpt")}
						value={attributes.contentExcerpt}
						onChange={(val) => {
							setAttributes({ contentExcerpt: val });
						}}
					/>
					<TextControl
						label={__("CTA Text")}
						value={attributes.customCtaText}
						onChange={(val) => {
							setAttributes({ customCtaText: val });
						}}
					/>
					<LinkControl
						label={__("CTA URL")}
						value={attributes.customCtaUrl}
						onChange={(val) => {
							setAttributes({ customCtaUrl: val });
						}}
					/>
					<MediaUploadCheck>
						<MediaUpload
							title={__("CTA Image")}
							allowedTypes={["image", "audio", "video"]}
							onSelect={onImageSelect}
							value={attributes.customImage}
							render={({ open }) => (
								<div className="image-select">
									<Button onClick={open} icon="format-gallery">
										{__("Select Image")}
									</Button>
									{attributes.customImageUrl != "" && (
										<ResponsiveWrapper>
											<img src={attributes.customImageUrl} />
										</ResponsiveWrapper>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</>
			) : (
				<>
					{hasResolved && posts && posts.length ? (
						<ComboboxControl
							label={__("Select content")}
							options={filteredOptions}
							value={attributes.contentId}
							onChange={(val) => selectPost(val)}
							onFilterValueChange={(inputValue) =>
								setFilteredOptions(
									renderPosts().filter((option) =>
										option.label
											.toLowerCase()
											.includes(inputValue.toLowerCase())
									)
								)
							}
						/>
					) : (
						<>
							<Spinner /> Loading options...
						</>
					)}

					<hr />
					<CardContent {...props} />
				</>
			)}
		</div>
	);
};

/*** EXPORTS ****************************************************************/

export default Wizard;
