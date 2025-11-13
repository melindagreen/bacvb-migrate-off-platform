// deprecated.js
import { useBlockProps } from "@wordpress/block-editor";

const v1 = {
	attributes: {
		heightDesktop: {
			type: "string",
			default: "4rem",
		},
		heightMobile: {
			type: "string",
			default: "3rem",
		},
		spacerId: {
			type: "string",
			default: "",
		},
	},
	save: ({ attributes }) => {
		const { heightDesktop, heightMobile, spacerId } = attributes;

		const blockProps = useBlockProps.save({
			style: {
				height: heightDesktop,
			},
		});

		return (
			<div
				{...blockProps}
				className={`${blockProps.className} ${spacerId}`}
			>
				<style>
					{`@media (max-width: 781px) {
				.${spacerId} {
					height: ${heightMobile} !important;
				}
			}`}
				</style>
			</div>
		);
	},
};

const v2 = {
	attributes: {
		heightDesktop: {
			type: "string",
			default: "4rem",
		},
		heightTablet: {
			type: "string",
			default: "",
		},
		heightMobile: {
			type: "string",
			default: "3rem",
		},
	},
	save: ({ attributes }) => {
		const { heightDesktop, heightTablet, heightMobile } = attributes;

		const block = "wp-block-madden-theme-responsive-spacer";
		const desktopHeight = heightDesktop;
		const tabletHeight = heightTablet ? heightTablet : desktopHeight;
		const mobileHeight = heightMobile ? heightMobile : tabletHeight;

		const blockProps = useBlockProps.save();

		return (
			<div {...blockProps}>
				<div
					className={`${block}--desktop`}
					style={{
						height: desktopHeight,
					}}
				></div>
				<div
					className={`${block}--tablet`}
					style={{
						height: tabletHeight,
					}}
				></div>
				<div
					className={`${block}--mobile`}
					style={{
						height: mobileHeight,
					}}
				></div>
			</div>
		);
	},
};

export default [v2, v1];
