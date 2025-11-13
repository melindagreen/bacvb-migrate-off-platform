/**
 * Slider Controls Block
 * 
 * Provides external pagination dots and navigation arrows for any slider on the page
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import './styles/index.scss';

// Import edit and save components
import Edit from './edit';
import Save from './save';

registerBlockType('madden-theme/slider-pagination', {
    edit: Edit,
    save: Save
});