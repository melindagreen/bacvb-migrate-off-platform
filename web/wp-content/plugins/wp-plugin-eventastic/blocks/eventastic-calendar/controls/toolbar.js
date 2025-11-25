/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { __ } from '@wordpress/i18n';
import { Button, Toolbar } from '@wordpress/components';

// Local dependencies
const THEME_PREFIX = "eventastic_calendar";

/*** COMPONENTS **************************************************************/

const TogglePreview = (props) => {
    const { attributes, setAttributes } = props;

    const isEditing = attributes.mode === 'edit';

    const onClick = () => {
        setAttributes({ mode: isEditing ? 'preview' : 'edit' });
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

    return isEditing
        ? PreviewButton
        : EditButton
};

const Tools = props => {
    return (
        <Toolbar>
            <TogglePreview {...props} />
        </Toolbar>
    )
}

/*** EXPORTS ****************************************************************/

export default Tools;
