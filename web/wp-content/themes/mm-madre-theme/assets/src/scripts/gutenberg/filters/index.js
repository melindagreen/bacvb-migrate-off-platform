// WordPress Dependencies
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

// Local Dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import Anchors from './anchors';

const filters = [
	Anchors,
];

export default () => {
	filters.forEach((filter) => {
		addFilter(filter.hook, THEME_PREFIX + '/' + filter.name, filter.action);
	});
};
