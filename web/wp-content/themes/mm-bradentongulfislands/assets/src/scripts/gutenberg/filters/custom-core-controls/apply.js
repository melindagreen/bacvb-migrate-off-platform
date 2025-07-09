/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from "@wordpress/i18n";
import { cloneElement } from "@wordpress/element";

// Local Dependencies
import { CUSTOMIZE_BLOCKS } from "./constants";

/*** FUNCTIONS ****************************************************************/

/**
 * Apply any needed customizations to the resultant saved element & its attributes
 * @param {E} el
 * @param {*} block
 * @param {*} attributes
 * @returns
 */
const applyCustomAttrs = (el, block, attributes) => {
	const { name } = block;

	// if customizations exist...
	if (
		typeof CUSTOMIZE_BLOCKS[name] !== "undefined" &&
		Array.isArray(CUSTOMIZE_BLOCKS[name])
	) {
		let newProps = { ...el.props };

		// default wrapper prp, has no impact on output
		// overwrite to wrap output in new element

		// NOTE 20220125 this solution is not ideal as only the
		// last overwrite will apply, but directly modding was causing
		// issues, refactor later? also why isn't this using children? -ashw
		let ElWrap = ({ content }) => <>{content}</>;

		// parse through matching customizations
		CUSTOMIZE_BLOCKS[name].forEach((customization) => {
			switch (customization) {
				case "wraparound-link":
					if (
						attributes.wraparoundLink &&
						typeof attributes.wraparoundLink.url !== "undefined"
					) {
						// overwrite wrap func with anchor tag
						ElWrap = ({ content }) => (
							<a
								href={attributes.wraparoundLink.url}
								target={
									attributes?.wraparoundLink?.opensInNewTab ? "_blank" : "_self"
								}
								className="wp-block-cover-link"
								rel="noopener"
							>
								{content}
							</a>
						);
					}
					break;
				case "center-on-mobile":
					if (attributes?.centerOnMobile) {
						// Center on mobile
						if (
							!newProps.className ||
							!newProps.className.includes("center-on-mobile")
						) {
							newProps.className = `${newProps.className} center-on-mobile`;
						}
					}
					break;
				case "lightbox-data":
					function getPhotoCredit(url) {
						let name = "photocredit";
						name = name.replace(/[[]/, "\\[").replace(/[\]]/, "\\]");
						const regex = new RegExp("[?&]" + name + "=([^&#]*)");
						const results = regex.exec(url);

						if (results !== null) {
							const photoCredit = decodeURIComponent(
								results[1].replace(/\+/g, " ")
							);
							const photoIcon = (
								<img
									src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/camera-icon.svg"
									alt="Camera Icon"
								/>
							);
							const photoCreditContent = (
								<div class="photocredit" data-photocredit={photoCredit}>
									{photoIcon}
								</div>
							);
							return photoCreditContent;
						} else {
							return "";
						}
					}

					if (typeof attributes.lbTitle !== "undefined") {
						ElWrap = ({ content }) => (
							<>
								{content}

								{attributes.lbImageIds.length > 0 ? (
									<div
										className={`lightbox-imagecarousel swiper-slide-imagecarousel swiper`}
									>
										<div className={`swiper-wrapper`}>
											{attributes.lbImageIds !== undefined
												? attributes.lbImageIds.map((id, index) => {
														return (
															<div className="wp-block-image swiper-slide">
																{getPhotoCredit(attributes.lbImageUrls[index])}
																<img
																	src="/wp-content/themes/mm-bradentongulfislands/assets/images/pixel.png"
																	alt={
																		attributes.lbImageAlts[index] !== ""
																			? attributes.lbImageAlts[index]
																			: "Carousel Image"
																	}
																	data-load-type="img"
																	data-load-all={attributes.lbImageUrls[index]}
																/>
															</div>
														);
												  })
												: null}
										</div>
										<div className="swiper-pagination-imagecarousel"></div>
										<div className="swiper-button-prev-imagecarousel"></div>
										<div className="swiper-button-next-imagecarousel"></div>
									</div>
								) : (
									""
								)}

								<div className="lightbox-data lb-content">
									<h1>{attributes.lbTitle}</h1>
									<p>{attributes.lbDescription}</p>
								</div>
							</>
						);
					}
					break;
				case "overlap":
					if (attributes.overlap !== 0) {
						let margin = "-" + Math.abs(attributes.overlap) + "rem";
						if (attributes.overlap > 0) {
							newProps.style = { ...newProps.style, marginTop: margin };
						} else {
							newProps.style = { ...newProps.style, marginBottom: margin };
						}
					}
					break;
				case "layer":
					if (attributes.layer !== 0) {
						const layerIndex = {
							top: 1,
							middle: 0,
							bottom: -1,
						};
						newProps.style = {
							...newProps.style,
							zIndex: layerIndex[attributes.layer],
							position: "relative",
						};
					}
					break;
				case "reverse-mobile":
					if (attributes.reverseMobile) {
						newProps.style = {
							...newProps.style,
							flexDirection: "column-reverse",
						};
					}
					break;
			}
		});

		// return modified element
		return <ElWrap content={cloneElement(el, newProps)} />;
	}

	return el;
};

/*** EXPORTS ****************************************************************/

export default {
	name: "personalization-attributes",
	hook: "blocks.getSaveElement",
	action: applyCustomAttrs,
};
