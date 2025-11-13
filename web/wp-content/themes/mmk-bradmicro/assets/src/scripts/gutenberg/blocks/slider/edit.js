/*** IMPORTS ****************************************************************/
// WordPress dependencies
import { __ } from "@wordpress/i18n";
import ServerSideRender from "@wordpress/server-side-render";
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { useEffect } from "@wordpress/element";
import { useRefEffect } from "@wordpress/compose";

// Local dependencies
import { initSwiperSliders } from "./assets/slider";

// Controls - add block/inspector controls here
import Controls from "./controls";

/*** CONSTANTS **************************************************************/
import { THEME_PREFIX } from "scripts/inc/constants";

/*** FUNCTIONS **************************************************************/

const Editor = (props) => {
	const { attributes, setAttributes } = props;

	const sliderRef = useRefEffect((element) => {
		const options = {
			adminSlider: true,
		};

		if (attributes.contentType === "custom") {
			options.slideClass = "wp-block-" + THEME_PREFIX + "-single-slide";
			options.wrapperClass = "swiper-wrapper";
		}

		let slider = initSwiperSliders(element, options);

		return () => {
			//destroy will reset the slider and active slide on ever re-render
			//slider.destroy();
		};
	});

	const ALLOWED_BLOCKS = [THEME_PREFIX + "/single-slide"];
	const SLIDE_TEMPLATE = [[THEME_PREFIX + "/single-slide", {}]];

	/*
	Block props for section wrapper
	*/
	let wrapperProps = useBlockProps();

	const innerBlocksProps = useInnerBlocksProps(
		{ className: "swiper-wrapper" },
		{
			allowedBlocks: ALLOWED_BLOCKS,
			template: SLIDE_TEMPLATE,
			orientation: "horizontal",
		}
	);

	/*
	Displays most recent posts or specified posts if selected as content type
	*/
	const { displayedPosts } = useSelect(
		(select) => {
			if (attributes.contentType === "automatic") {
				const query = {
					per_page: attributes.numberOfPosts,
					status: "publish",
					orderby:
						attributes.postOrder === "rand" ? "date" : attributes.postOrder,
					order: attributes.postOrder === "title" ? "asc" : "desc",
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
			attributes.postOrder,
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
			sliderDataset["data-" + key.toLowerCase()] = value;
		}
	});

	useEffect(() => {
		setAttributes({ sliderId: props.clientId });
	}, []);

	return (
		<section
			{...wrapperProps}
			id={`swiper-slider-${props.clientId}`}
			data-uid={props.clientId}
			className={`
				${wrapperProps.className}
				slider-type-${attributes.contentType} 
				${
					attributes.enableArrowNavigation && attributes.arrowsBelowSlider
						? "slider-arrows-below"
						: ""
				}
			`}
		>
			<div className={`swiper`} {...sliderDataset} ref={sliderRef}>
				{attributes.contentType !== "custom" && (
					<div className="swiper-wrapper">
						{(attributes.contentType === "automatic" ||
							attributes.contentType === "manual") && (
							<>
								{displayedPosts && displayedPosts.length ? (
									displayedPosts.map((post, index) => (
										<div className="swiper-slide">
											<ServerSideRender
												block={THEME_PREFIX + "/content-card"}
												attributes={{
													contentId: post.id,
													...attributes,
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
							</>
						)}

						{attributes.contentType === "gallery" &&
							attributes.galleryImages &&
							attributes.galleryImages.map((image, index) => (
								<div className="swiper-slide">
									<figure
										className="wp-block-image"
										style={{
											height: attributes.galleryMaxHeight
												? attributes.galleryMaxHeight
												: "100%",
										}}
									>
										<img src={image.url} alt={image.alt} />
										{image.caption && <figcaption>{image.caption}</figcaption>}
									</figure>
								</div>
							))}
					</div>
				)}

				{attributes.contentType === "custom" && (
					<div {...innerBlocksProps}>{innerBlocksProps.children}</div>
				)}
			</div>

			{(attributes.enableScrollbar ||
				attributes.enablePagination ||
				attributes.enableArrowNavigation) && (
				<div className="swiper-navigation-wrapper">
					{attributes.enableScrollbar && (
						<div
							className="swiper-scrollbar"
							data-color={attributes.scrollbarColor}
						></div>
					)}
					{attributes.enablePagination && (
						<div
							className="swiper-pagination"
							data-color={attributes.dotColor}
							data-color-active={attributes.dotColorActive}
						></div>
					)}
					{attributes.enableArrowNavigation && (
						<>
							<div
								className="swiper-button-prev"
								data-color={attributes.arrowColor}
								data-color-background={attributes.arrowBackgroundColor}
							></div>
							<div
								className="swiper-button-next"
								data-color={attributes.arrowColor}
								data-color-background={attributes.arrowBackgroundColor}
							></div>
						</>
					)}
				</div>
			)}

			{attributes.enableSlidesPerView && attributes.enableSlidesPerViewAuto && (
				<style>
					{`#swiper-slider-${props.clientId} .swiper-wrapper > div {
					width: ${attributes.slidesPerViewWidth};
				}`}
				</style>
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
