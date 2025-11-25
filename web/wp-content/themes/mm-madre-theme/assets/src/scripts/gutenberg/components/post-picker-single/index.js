/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { __experimentalLinkControl } from '@wordpress/block-editor';

// Local dependencies
import { THEME_PREFIX, POST_TYPES_TO_IGNORE } from '../../inc/constants';
import { CategoryControl, ElipsisLoader } from '..';

/*** COMPONENTS **************************************************************/

const PostPickerSingle = ({ segment, updateSegment, label, className }) => {
	return (
        <div className="components-base-control">
            <label className="components-base-control__label">
                Page
            </label>
			<__experimentalLinkControl
				value={segment.post}
				onChange={updateSegment('post', segment)}
			/>
		</div>
	);
};

/*** EXPORTS ****************************************************************/

export default PostPickerSingle;
