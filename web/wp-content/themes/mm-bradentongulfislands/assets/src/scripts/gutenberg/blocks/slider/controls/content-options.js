/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from "@wordpress/i18n";
import {
	Spinner,
	Button,
	PanelBody,
	ResponsiveWrapper,
	SelectControl,
	TextControl,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
	__experimentalUnitControl as UnitControl,
} from "@wordpress/components";
import {
	MediaUpload,
	MediaUploadCheck,
	__experimentalLinkControl as LinkControl,
} from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { store as coreDataStore } from "@wordpress/core-data";

//Local dependencies
import ManualPostSelect from "./manual-post-select";
import TaxonomyFilter from "./taxonomy-filter";
import CardContent from "../../content-card/controls/card-content";

/*** CONSTANTS **************************************************************/
import { CARD_STYLES } from "../../../inc/constants";
console.log("CARD STYLES: ", CARD_STYLES);

/*** COMPONENTS **************************************************************/
const ContentOptions = (props) => {
	const { attributes, setAttributes } = props;

	//sets CTA slide image for automatic/post sliders
	const onImageSelect = (images) => {
		setAttributes({
			ctaSlideImage: images.id,
		});
		setAttributes({
			ctaSlideImageUrl: images.url,
		});
	};

	//sets gallery images for image gallery sliders
	const onGallerySelect = (images) => {
		setAttributes({
			galleryImages: images.map((image) => image),
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

	//remove these options from our post type results
	const excludePostTypes = [
		"attachment",
		"nav_menu_item",
		"wp_block",
		"wp_template",
		"wp_template_part",
		"wp_navigation",
		"rm_content_editor",
		"rank_math_schema",
	];

	const postTypeOptions = postTypes.results
		?.filter((type) => {
			return !excludePostTypes.includes(type.slug);
		})
		.map((type) => ({
			label: type.labels.name,
			value: type.slug,
		}));

	return (
		<div className={props.className}>
			<PanelBody title="Slider Type">
				<SelectControl
					label={__("Content Type")}
					value={attributes.contentType}
					options={[
						{ label: "Automatic Recent Posts", value: "automatic" },
						{ label: "Specific Pages/Posts", value: "manual" },
						{ label: "Image Gallery", value: "gallery" },
						{ label: "Custom", value: "custom" },
					]}
					onChange={(val) => {
						setAttributes({ contentType: val });
					}}
				/>
			</PanelBody>
			{attributes.contentType === "gallery" && (
				<PanelBody title="Image Gallery">
					<MediaUploadCheck>
						<MediaUpload
							title={__("Choose Images")}
							allowedTypes={["image"]}
							gallery
							multiple="add"
							onSelect={onGallerySelect}
							value={attributes.galleryImages.map((image) => image.id)}
							render={({ open }) => (
								<Button onClick={open} icon="format-gallery">
									{__("Choose Images")}
								</Button>
							)}
						/>
					</MediaUploadCheck>
					<br></br>
					<UnitControl
						label={__("Image Height")}
						help={__("This will set all images to the same height")}
						value={attributes.galleryMaxHeight}
						min={1}
						onChange={(val) => {
							setAttributes({ galleryMaxHeight: val });
						}}
					/>
				</PanelBody>
			)}
			{(attributes.contentType === "automatic" ||
				attributes.contentType === "manual") && (
				<>
					<PanelBody title="Content Options">
						{postTypes.hasFinishedResolution &&
						postTypes.results &&
						postTypes.results.length ? (
							<>
								<SelectControl
									label={__("Post Type")}
									value={attributes.postType}
									options={postTypeOptions}
									onChange={(val) => {
										setAttributes({ postType: val });
									}}
								/>
								<ToggleControl
									label={__("Filter by Taxonomy")}
									checked={attributes.enableTaxFilter}
									onChange={() => {
										setAttributes({
											enableTaxFilter: !attributes.enableTaxFilter,
										});
									}}
								/>
								{attributes.enableTaxFilter && <TaxonomyFilter {...props} />}
								{attributes.contentType === "automatic" && (
									<NumberControl
										label={__("Number of Posts")}
										value={attributes.numberOfPosts}
										min={1}
										onChange={(val) => {
											setAttributes({ numberOfPosts: Number(val) });
										}}
									/>
								)}
							</>
						) : (
							<>
								<Spinner /> Loading post types...
							</>
						)}
						<hr />
						<SelectControl
							label={__("Card Style")}
							value={attributes.cardStyle}
							options={CARD_STYLES}
							onChange={(val) => {
								setAttributes({ cardStyle: val });
							}}
						/>
						<CardContent {...props} />
					</PanelBody>
					{attributes.contentType === "manual" && (
						<PanelBody title="Pages/Posts to Display">
							<ManualPostSelect {...props} />
						</PanelBody>
					)}
					<PanelBody title="CTA Slide Options">
						<ToggleControl
							label={__("Custom CTA Slide")}
							checked={attributes.enableCtaSlide}
							onChange={() => {
								setAttributes({ enableCtaSlide: !attributes.enableCtaSlide });
							}}
						/>
						{attributes.enableCtaSlide && (
							<>
								<TextControl
									label={__("Slide Title")}
									value={attributes.ctaSlideTitle}
									onChange={(val) => {
										setAttributes({ ctaSlideTitle: val });
									}}
								/>
								<TextControl
									label={__("CTA Excerpt")}
									value={attributes.ctaSlideExcerpt}
									onChange={(val) => {
										setAttributes({ ctaSlideExcerpt: val });
									}}
								/>
								<TextControl
									label={__("CTA Text")}
									value={attributes.ctaSlideBtnText}
									onChange={(val) => {
										setAttributes({ ctaSlideBtnText: val });
									}}
								/>
								<LinkControl
									label={__("CTA URL")}
									value={attributes.ctaSlideBtnUrl}
									onChange={(val) => {
										setAttributes({ ctaSlideBtnUrl: val });
									}}
								/>
								<MediaUploadCheck>
									<MediaUpload
										title={__("CTA Image")}
										allowedTypes={["image"]}
										onSelect={onImageSelect}
										value={attributes.ctaSlideImage}
										render={({ open }) => (
											<div className="image-select">
												<Button onClick={open} icon="format-gallery">
													{__("Select Image")}
												</Button>
												{attributes.ctaSlideImageUrl != "" && (
													<ResponsiveWrapper>
														<img src={attributes.ctaSlideImageUrl} />
													</ResponsiveWrapper>
												)}
											</div>
										)}
									/>
								</MediaUploadCheck>
							</>
						)}
					</PanelBody>
				</>
			)}
		</div>
	);
};

export default ContentOptions;
