/**
 * Internal dependencies
 */
import JustifyControl from "./controls";

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Fragment } = wp.element;

/**
 * Block constants
 */
const FILTER_NAME = "justify";

const options = {
	title: __("Align text justify", "madden-theme"),
	tagName: "p",
	className: FILTER_NAME,
	attributes: {
		style: "style",
	},
	edit({ isActive, value, onChange, activeAttributes }) {
		return (
			<Fragment>
				<JustifyControl
					name={name}
					isActive={isActive}
					value={value}
					onChange={onChange}
					activeAttributes={activeAttributes}
				/>
			</Fragment>
		);
	},
};

export default {
	name: FILTER_NAME,
	options: options,
};
