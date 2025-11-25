/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {
	Button,
	ButtonGroup,
	Icon,
	Panel,
	PanelBody,
} from '@wordpress/components';

// Local dependencies
import { THEME_PREFIX } from '../../inc/constants';
import './styles/index.scss';

/*** COMPONENTS **************************************************************/

const ReorderableList = ({
	className,
	header,
	footer,
	segmentsContent,
	onAddSegment,
	onReorderSegment,
	onDeleteSegment,
}) => {
	const segments = segmentsContent.map((segmentContent, index) => (
		<div className={THEME_PREFIX + '-segment-wrap'}>
			<ButtonGroup className={THEME_PREFIX + '-reorder-segment'}>
				<Button
					disabled={index === 0}
					onClick={() => onReorderSegment(1, index)}
				>
					<svg
						width="24"
						height="24"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg"
						role="img"
						aria-hidden="true"
						focusable="false"
					>
						<path d="M12,8l-6,6l1.41,1.41L12,10.83l4.59,4.58L18,14L12,8z"></path>
					</svg>
				</Button>
				<Button
					disabled={index === segmentsContent.length - 1}
					onClick={() => onReorderSegment(-1, index)}
				>
					<svg
						width="24"
						height="24"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg"
						role="img"
						aria-hidden="true"
						focusable="false"
					>
						<path d="M7.41,8.59L12,13.17l4.59-4.58L18,10l-6,6l-6-6L7.41,8.59z"></path>
					</svg>
				</Button>
			</ButtonGroup>
			{segmentContent}
			<Button
				className="delete-segment"
				isSecondary
				isDestructive
				onClick={() => onDeleteSegment(index)}
			>
				-
			</Button>
		</div>
	));

	return (
		<Panel className={className + ' ' + THEME_PREFIX + '-reorderable-list'}>
			<PanelBody>{header}</PanelBody>
			{segments}
			<PanelBody>
				{footer}
				<Button
					isSecondary
					className={THEME_PREFIX + '_add_segment'}
					onClick={onAddSegment}
				>
					<Icon icon="plus" />
					Add segment
				</Button>
			</PanelBody>
		</Panel>
	);
};

/*** EXPORTS ****************************************************************/

export default ReorderableList;
