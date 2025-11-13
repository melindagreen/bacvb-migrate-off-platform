/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	ComboboxControl,
	SelectControl,
	Button,
	ResponsiveWrapper,
	TextControl,
	TextareaControl,
	Spinner,
} from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState } from "@wordpress/element";
import { store as coreDataStore } from "@wordpress/core-data";
import {
	useBlockProps,
	MediaUpload,
	MediaUploadCheck,
	__experimentalLinkControl as LinkControl,
} from "@wordpress/block-editor";

import CardContent from "./card-content";

/*** CONSTANTS **************************************************************/
import { CARD_STYLES, POST_TYPES_TO_IGNORE } from "../../../inc/constants";

/*** FUNCTIONS **************************************************************/

const Wizard = (props) => {
	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();

	const { posts, hasResolved } = useSelect(
		(select) => {
			const query = [
				"postType",
				attributes.postType,
				{
					per_page: -1,
					status: "publish",
					order: "desc",
					orderby: "date",
				},
			];
			if (
				attributes.postType !== "custom" &&
				attributes.postType !== "queried_post"
			) {
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
		[attributes.postType]
	);

	const renderPosts = () => {
		let options = [];

		if (posts) {
			posts.forEach((post) => {
				// For Kraken Events plugin or anything that disables the regular post title field
				// Check for an ACF field named post_title
				let title = "";
				if (post.title && post.title.raw) {
					title = post.title.raw;
				} else if (post.acf && post.acf.post_title) {
					title = post.acf.post_title;
				}
				options.push({ value: post.id, label: title });
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
			// For Kraken Events plugin or anything that disables the regular post title field
			// Check for an ACF field named post_title
			let contentTitle = "";
			if (content.title && content.title.raw) {
				contentTitle = content.title.raw;
			} else if (content.acf && content.acf.post_title) {
				contentTitle = content.acf.post_title;
			}
			setAttributes({
				contentId: content.id,
				contentTitle: contentTitle,
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

	//this will fetch all post types available via rest api
	const postTypes = useSelect((select) => {
		const query = [
			"root",
			"postType",
			{
				per_page: -1,
			},
		];
		return {
			results: select(coreDataStore).getEntityRecords(...query),
			hasStartedResolution: select(coreDataStore).hasStartedResolution(
				"getEntityRecords",
				query
			),
			hasFinishedResolution: select(coreDataStore).hasFinishedResolution(
				"getEntityRecords",
				query
			),
			isResolving: select(coreDataStore).isResolving("getEntityRecords", query),
		};
	});

	//remove POST_TYPES_TO_IGNORE options from our post type results
	const postTypeOptions = postTypes.results
		?.filter((type) => {
			return !POST_TYPES_TO_IGNORE.includes(type.slug);
		})
		.map((type) => ({
			label: type.labels.name,
			value: type.slug,
		}));

	if (Array.isArray(postTypeOptions)) {
		postTypeOptions.push({
			label: __("Custom", "madden-theme"),
			value: "custom",
		});
	}

	if (Array.isArray(postTypeOptions)) {
		postTypeOptions.push({
			label: __("Queried Post", "madden-theme"),
			value: "queried_post",
		});
	}

	return (
		<div className={`${blockProps.className}`}>
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
				value={attributes.postType}
				options={postTypeOptions}
				onChange={(val) => {
					setAttributes({ postType: val });
				}}
			/>
			{attributes.postType === "custom" ? (
				<>
					<TextControl
						label={__("Content Title")}
						value={attributes.contentTitle}
						onChange={(val) => {
							setAttributes({ contentTitle: val });
						}}
					/>
					<TextareaControl
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
						key={props.clientId}
						label={__("CTA URL")}
						value={attributes.customCtaUrl}
						onChange={(val) => {
							setAttributes({ customCtaUrl: val });
						}}
					/>
					{attributes.customCtaUrl && (
						<Button onClick={() => setAttributes({ customCtaUrl: undefined })}>
							{__("Clear Link")}
						</Button>
					)}
					<MediaUploadCheck>
						<MediaUpload
							title={__("CTA Image")}
							allowedTypes={["image"]}
							onSelect={onImageSelect}
							value={attributes.customImage}
							render={({ open }) => (
								<div className="image-select">
									<Button onClick={open} isLarge icon="format-gallery">
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
							{attributes.postType !== "queried_post" && (
								<>
									<Spinner /> Loading options...
								</>
							)}
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
