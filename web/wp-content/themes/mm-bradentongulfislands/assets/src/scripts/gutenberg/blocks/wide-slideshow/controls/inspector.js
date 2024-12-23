// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TextControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/

const Inspector = props => {

    const { attributes, setAttributes } = props;
    const { bannerTitle, bannerDescription } = attributes;

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody title="Banner Settings" initialOpen={ false }>
                    <PanelRow>
                        <TextControl
                            label="Banner Title"
                            onChange={ ( bannerTitle ) => setAttributes( { bannerTitle } ) }
                            value={ bannerTitle }
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label="Banner Description"
                            onChange={ ( bannerDescription ) => setAttributes( { bannerDescription } ) }
                            value={ bannerDescription }
                        />
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
        </Fragment>
    )
}

export default Inspector;