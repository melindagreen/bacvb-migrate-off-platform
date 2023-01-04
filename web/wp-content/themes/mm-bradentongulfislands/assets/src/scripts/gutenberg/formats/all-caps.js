/*** IMPORTS ****************************************************************/

// WordPress Dependencies
import { RichTextToolbarButton, } from '@wordpress/block-editor';
import { toggleFormat, } from '@wordpress/rich-text';

// Local Dependencies
import { THEME_PREFIX } from '../../inc/constants';

/*** CONSTANTS **************************************************************/
const FILTER_NAME = 'all-caps';

/*** FUNCTIONS **************************************************************/

/**
 * Editor for all-caps format
 * @param {Object} props                The editor's properties
 * @returns {RichTextToolbarButton}     The editor toolbar
 */
const editAllCaps = ({ isActive, onChange, value }) => {
    return (
        <RichTextToolbarButton
            icon="arrow-up-alt"
            title="All-Caps"
            onClick={() => {
                onChange(
                    toggleFormat(value, {
                        type: THEME_PREFIX + '/' + FILTER_NAME,
                    })
                );
            }}
            isActive={isActive}
        />
    );
}

/*** EXPORTS ****************************************************************/

export default {
    name: FILTER_NAME,
    options: {
        title: 'All-Caps Text',
        tagName: 'span',
        className: FILTER_NAME,
        edit: editAllCaps
    }
}
