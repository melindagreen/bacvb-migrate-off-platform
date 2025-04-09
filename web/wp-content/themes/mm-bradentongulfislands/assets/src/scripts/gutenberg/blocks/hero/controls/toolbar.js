/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';
import {
  MediaUpload,
} from "@wordpress/block-editor";
import { Button, Toolbar, IconButton } from '@wordpress/components';

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { usePreview } from '.././../../inc/hooks.js';

/*** COMPONENTS **************************************************************/

const TogglePreview = (props) => {
	const { attributes, setAttributes } = props;
	const [isPreview, togglePreview] = usePreview(attributes, setAttributes);

	const onClick = () => {
		togglePreview();
	};

	const PreviewButton = (
		<Button
		label={__('Preview', THEME_PREFIX)}
		icon="visibility"
		onClick={onClick}
		/>
	);

	const EditButton = (
		<Button
		label={__('Edit', THEME_PREFIX)}
		icon="edit"
		onClick={onClick}
		/>
	);

	return isPreview
	? PreviewButton
	: EditButton
};

//<TogglePreview {...props} />
const Tools = props => {
	return (
		<Toolbar>
			<TogglePreview {...props} />
		</Toolbar>
	)
}

/*** EXPORTS ****************************************************************/

export default Tools;
