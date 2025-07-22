/**
 * Internal dependencies
 */
import Edit from "./components/edit";

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Fragment } = wp.element;

/**
 * Block constants
 */
const FILTER_NAME = "nbsp";

const options = {
	title: __("Nonbreaking Space", "madden-theme"),
	tagName: "span",
	className: "nbsp",
	edit({ isActive, value, onChange, activeAttributes }) {
		return (
			<Fragment>
				<Edit
					name={FILTER_NAME}
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
