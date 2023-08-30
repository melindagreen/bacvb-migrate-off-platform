/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor'
import { PanelBody, PanelRow, SelectControl } from '@wordpress/components'
import { } from '@wordpress/block-editor'
import { useSelect, } from '@wordpress/data';


// Local dependencies
import { THEME_PREFIX } from 'scripts/inc/constants';
import { } from '../../../components';

/*** CONSTANTS **************************************************************/
const ALLOWED_MEDIA_TYPES = ['image'];

/*** COMPONENTS **************************************************************/


const Inspector = props => {
    const { attributes: { queryMode, persona, }, setAttributes } = props;

    const personae = useSelect((select) => select(
        'maddenontology/data-store'
    ).getAllPersonae());

    return (
        <InspectorControls>
            <PanelBody title={__('Grid Settings')}>
                <PanelRow>
                    <SelectControl
                        label={__('Query mode')}
                        value={queryMode}
                        options={[
                            {
                                label: 'Manual',
                                value: 'manual'
                            },
                            {
                                label: 'Persona',
                                value: 'persona'
                            }
                        ]}
                        onChange={queryMode => setAttributes({ queryMode })}
                    />
                </PanelRow>
                {queryMode === 'persona' && <PanelRow>
                    <SelectControl
                        label={__('Persona')}
                        value={persona}
                        options={personae.map(personaOpt => {
                            return {
                                label: personaOpt.name,
                                value: personaOpt.id,
                            }
                        })}
                        onChange={persona => setAttributes({ persona })}
                    />
                </PanelRow>}
            </PanelBody>
        </InspectorControls>
    )
}

export default Inspector;
