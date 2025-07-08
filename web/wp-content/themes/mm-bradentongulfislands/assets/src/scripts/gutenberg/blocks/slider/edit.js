/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import ServerSideRender from "@wordpress/server-side-render";
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useEffect } from "@wordpress/element";

// Local dependencies
import { initSwiperSliders } from "./assets/slider";

// Controls - add block/inspector controls here
import Controls from "./controls";

/*** CONSTANTS **************************************************************/
import { THEME_PREFIX } from "scripts/inc/constants";

/*** FUNCTIONS **************************************************************/

const Editor = (props) => {
	const { attributes } = props;

	const ALLOWED_BLOCKS = [THEME_PREFIX + "/single-slide"];
	const SLIDE_TEMPLATE = [[THEME_PREFIX + "/single-slide", {}]];

	/*
	By combining using blockProps & innerBlocksProps, we can remove extra wrapping <divs> and have access to the direct child blocks with our slides.
	*/
	let blockProps = useBlockProps({
		className: "swiper-wrapper",
		template: SLIDE_TEMPLATE,
	});

	let innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: SLIDE_TEMPLATE,
	});

	/*
	Displays most recent posts or specified posts if selected as content type
	*/
	const { displayedPosts } = useSelect(
		(select) => {
			if (attributes.contentType === "automatic") {
				const query = {
					per_page: attributes.numberOfPosts,
					status: "publish",
					order: "desc",
					orderby: "date",
				};
				if (attributes.enableTaxFilter && attributes.taxonomyTerms.length) {
					const termIds = attributes.taxonomyTerms.map((term) => {
						return term.id;
					});
					query[attributes.taxonomyFilter] = termIds;
				}
				return {
					displayedPosts: select("core").getEntityRecords(
						"postType",
						attributes.postType,
						query
					),
				};
			} else if (attributes.contentType === "manual") {
				const includedIds = attributes.manualPosts.map((post) => {
					return post.id;
				});
				const query = {
					per_page: -1,
					include: includedIds,
					status: "publish",
					order: "desc",
					orderby: "include",
				};
				return {
					displayedPosts: select("core").getEntityRecords(
						"postType",
						attributes.postType,
						query
					),
				};
			} else {
				return {
					displayedPosts: null,
				};
			}
		},
		[
			attributes.contentType,
			attributes.postType,
			attributes.numberOfPosts,
			attributes.manualPosts,
			attributes.enableTaxFilter,
			attributes.taxonomyTerms,
		]
	);

	/*
	This is for our slider.js file which picks up the data attributes for the slider settings
	*/
	const sliderDataset = [];
	Object.entries(attributes).forEach((entry) => {
		const [key, value] = entry;
		if (value !== false) {
			sliderDataset["data-" + key.toLocaleLowerCase()] = value;
		}
	});

	/*
	Watch attribute updates
	*/
	const countInnerBlocks = useSelect((select) =>
		select("core/block-editor").getBlock(props.clientId)
	).innerBlocks;

	useEffect(() => {
		if (attributes.contentType === "custom") {
			//when using inner blocks we have to adjust the swiper classes.
			initSwiperSliders(
				"#swiper-slider-" + props.clientId + " .swiper",
				"swiper-wrapper",
				"block-editor-block-list__block"
			);
		} else {
			initSwiperSliders("#swiper-slider-" + props.clientId + " .swiper");
		}
	}, [attributes, countInnerBlocks.length]);

	return (
		<section
			id={`swiper-slider-${props.clientId}`}
			className={`${props.className} slider-type-${attributes.contentType} ${
				attributes.enableArrowNavigation && attributes.arrowsBelowSlider
					? "slider-arrows-below"
					: ""
			}`}
		>
			<div className={`swiper`} {...sliderDataset}>
				{attributes.contentType !== "custom" && (
					<div className="swiper-wrapper">
						{(attributes.contentType === "automatic" ||
							attributes.contentType === "manual") &&
						displayedPosts &&
						displayedPosts.length ? (
							displayedPosts.map((post, index) => (
								<div className="swiper-slide">
									<ServerSideRender
										block={THEME_PREFIX + "/content-card"}
										attributes={{
											contentId: post.id,
											cardStyle: attributes.cardStyle,
											contentType: attributes.postType,
											displayAdditionalContent:
												attributes.displayAdditionalContent,
											displayExcerpt: attributes.displayExcerpt,
											excerptLength: attributes.excerptLength,
											displayReadMore: attributes.displayReadMore,
											readMoreText: attributes.readMoreText,
											mode: "preview",
										}}
									/>
								</div>
							))
						) : (
							<div className="swiper-slide">
								No results found for {attributes.postType} with selected{" "}
								{attributes.taxonomyFilter}
							</div>
						)}

						{(attributes.contentType === "automatic" ||
							attributes.contentType === "manual") &&
							attributes.enableCtaSlide && (
								<div className="swiper-slide">
									<ServerSideRender
										block={THEME_PREFIX + "/content-card"}
										attributes={{
											cardStyle: attributes.cardStyle,
											contentType: "custom",
											contentTitle: attributes.ctaSlideTitle,
											customImage: attributes.ctaSlideImage,
											customCtaText: attributes.ctaSlideBtnText,
											customCtaUrl: attributes.ctaSlideBtnUrl,
											mode: "preview",
										}}
									/>
								</div>
							)}

						{attributes.contentType === "gallery" &&
							attributes.galleryImages &&
							attributes.galleryImages.map((image, index) => (
								<div className="swiper-slide">
									<figure className="wp-block-image">
										<img src={image.url} alt={image.alt} />
										{image.caption && <figcaption>{image.caption}</figcaption>}
									</figure>
								</div>
							))}
					</div>
				)}

				{attributes.contentType === "custom" && <div {...innerBlocksProps} />}
			</div>

			{(attributes.enableScrollbar ||
				attributes.enablePagination ||
				attributes.enableArrowNavigation) && (
				<div className="swiper-navigation-wrapper">
					{attributes.enableScrollbar && (
						<div
							className="swiper-scrollbar"
							data-color={attributes.scrollbarColor.name}
						></div>
					)}
					{attributes.enablePagination && (
						<div
							className="swiper-pagination"
							data-color={attributes.dotColor.name}
							data-color-active={attributes.dotColorActive.name}
						></div>
					)}
					{attributes.enableArrowNavigation && (
						<>
							<div
								className="swiper-button-prev"
								data-color={attributes.arrowColor.name}
								data-color-background={attributes.arrowBackgroundColor.name}
							></div>
							<div
								className="swiper-button-next"
								data-color={attributes.arrowColor.name}
								data-color-background={attributes.arrowBackgroundColor.name}
							></div>
						</>
					)}
				</div>
			)}
		</section>
	);
};

const edit = (props) => {
	return (
		<>
			<Controls {...props} />
			<Editor {...props} />
		</>
	);
};

/*** EXPORTS ****************************************************************/
export default edit;
