/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {
    Button,
    ButtonGroup,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// Local dependencies
import { THEME_PREFIX } from '../../../inc/constants';
import './styles/index.scss';

/*** FUNCTIONS ***************************************************************/

const reorderSegment = (segments, index, moveBy) => {
    const copySegment = segments[index];
    segments[index] = segments[index + moveBy];
    segments[index + moveBy] = copySegment;
    return segments;
}

/*** COMPONENTS **************************************************************/

const Repeater = ({
    label,
    segments, 
    segmentsContent,
    newSegment,
    placeholderText,
    onChange,
}) => {
    if (!placeholderText) placeholderText = 'Add repeater segments';

    return <div className='repeater'>
        {label && <h3>{label}</h3>}
        <div className='repeater-segments'>
            {segments && segments.length
                ? segmentsContent.map((segment, index) => <div className='repeater-segment'>
                    <ButtonGroup className='reorder-segments'>
                        <Button
                            disabled={index === 0}
                            className='reorder-button'
                            icon={<svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                role="img"
                                aria-hidden="true"
                                focusable="false"
                            >
                                <path d="M12,8l-6,6l1.41,1.41L12,10.83l4.59,4.58L18,14L12,8z"></path>
                            </svg>}
                            onClick={() => {
                                const updatedSegments = reorderSegment([...segments], index, -1);
                                onChange(updatedSegments);
                            }}
                        >
                        </Button>
                        <Button
                            disabled={index === segments.length - 1}
                            className='reorder-button'
                            icon={<svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                role="img"
                                aria-hidden="true"
                                focusable="false"
                            >
                                <path d="M7.41,8.59L12,13.17l4.59-4.58L18,10l-6,6l-6-6L7.41,8.59z"></path>
                            </svg>}
                            onClick={() => {
                                const updatedSegments = reorderSegment([...segments], index, 1);
                                onChange(updatedSegments);
                            }}
                        >
                        </Button>
                    </ButtonGroup>
                    <div className='segment-content'>{segment}</div>
                    <Button
                        className="delete-repeater-segment"
                        isSecondary
                        isDestructive
                        icon={<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40">
                            <rect width="40" height="3" y="10" />
                        </svg>}
                        onClick={() => {
                            const updatedSegments = [...segments];
                            updatedSegments.splice(index, 1);
                            onChange(updatedSegments);
                        }}
                    />
                </div>)
                : <p className='placeholder-text'>{placeholderText}</p>}
        </div>
        {newSegment &&
            <Button
                className='add-repeater-segment'
                icon='plus'
                label={__('Add segment')}
                onClick={() => {
                    const updatedSegments = [...segments, Object.assign({ id: segments.length }, newSegment)];
                    onChange(updatedSegments);
                }}
            />
        }
    </div>
};

/*** EXPORTS ****************************************************************/

export default Repeater;
