/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { Button, PanelBody, TextControl, TextareaControl, PanelRow, Spinner } from '@wordpress/components'
import { MediaUpload, MediaUploadCheck, URLInput } from '@wordpress/block-editor'

// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { TaxonomyControl } from '../../../components';
import Media  from '../components/Media';
import FerryStop  from '../components/FerryStopInfo';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const Inspector = props => {
    const { attributes, setAttributes } = props;

    return (
        <InspectorControls>
            <FerryStop 
                attributes={attributes}
                setAttributes={setAttributes}
            />
        </InspectorControls>
    )
}

export default Inspector;
