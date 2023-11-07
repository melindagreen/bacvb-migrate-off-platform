/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { } from '@wordpress/components'
import { PanelColorSettings } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';

/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = props => {
    const { textColor, setTextColor } = props;

    return (
        <InspectorControls>
            <PanelColorSettings
                title={__('Color settings')}
                colorSettings={[
                    {
                        value: textColor.color,
                        onChange: setTextColor,
                        label: __('Title color')
                    }
                ]}
            />
        </InspectorControls>
    )
}

export default Inspector 