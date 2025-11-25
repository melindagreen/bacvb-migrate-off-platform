/*** IMPORTS ****************************************************************/

// WordPress dependencies
import {
	TextControl,
	FormTokenField,
	Button,
	SelectControl,
	Spinner,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { withState } from '@wordpress/compose';

// Local dependencies
import { THEME_PREFIX, POST_TYPES_TO_IGNORE } from '../../inc/constants';
import { CategoryControl, ElipsisLoader } from '..';
import './styles/index.scss';

/*** CONSTANTS **************************************************************/

const ORDERBY_OPTS = [
	{
		label: 'random',
		value: 'random',
	},
	{
		label: 'date',
		value: 'date',
	},
	{
		label: 'title',
		value: 'title',
	},
	{
		label: 'persona score',
		value: 'persona',
	},
	{
		label: 'start date',
		value: 'visitor_start_date',
	},
	{
		label: 'end date',
		value: 'visitor_end_date',
	},
];

/*** COMPONENTS **************************************************************/

const TaxonomyEditor = ({
	taxonomy,
	segment,
	updateSegment,
	editingTaxes,
	setState,
}) => {
	let termEditor;

	if (taxonomy.hierarchical) {
		termEditor = taxonomy.terms ? (
			<CategoryControl
				title={__('Filter by category:')}
				allCategories={taxonomy.terms}
				activeCategories={segment[taxonomy.slug]}
				onUpdateCategories={updateSegment(taxonomy.slug, segment)}
			/>
		) : (
			<Spinner />
		);
	} else {
		termEditor = taxonomy.terms ? (
			<FormTokenField
				label={__('Filter by tag:')}
				value={segment[taxonomy.slug]}
				suggestions={taxonomy.terms.map((term) => term.name)}
				onChange={updateSegment(taxonomy.slug, segment)}
			/>
		) : (
			<Spinner />
		);
	}

	return (
		<div className="segment_edit_section">
			<p>
				Filter by the following {taxonomy.name}:&nbsp;
				<span className="display_tax">
					{segment[taxonomy.slug] ? (
						segment[taxonomy.slug]
							.map((currentTerm) => {
								if (taxonomy.hierarchical) {
									const termMatches = taxonomy?.terms
										? taxonomy.terms.filter(
												(term) =>
													term.id === currentTerm
										  )
										: false;
									return termMatches &&
										termMatches.length > 0 &&
										termMatches[0].name
										? termMatches[0].name
										: '...';
								} else {
									return currentTerm;
								}
							})
							.join(', ')
					) : (
						<ElipsisLoader />
					)}
					&nbsp;
				</span>
				{!editingTaxes.includes(taxonomy.slug) ? (
					<Button
						icon="edit"
						onClick={() => {
							let newEditingTaxes = [].concat(editingTaxes);
							if (!newEditingTaxes.includes(taxonomy.slug))
								newEditingTaxes.push(taxonomy.slug);
							setState({ editingTaxes: newEditingTaxes });
						}}
					/>
				) : (
					<ElipsisLoader />
				)}
			</p>
			<div
				className={
					'edit_tax' +
					(editingTaxes.includes(taxonomy.slug) ? ' show' : '')
				}
			>
				<Button
					icon="yes"
					className="confirm_tax_edit"
					onClick={() => {
						let newEditingTaxes = [].concat(taxonomy.slug);
						const index = newEditingTaxes.indexOf(taxonomy.slug);
						if (index > -1) newEditingTaxes.splice(index, 1);
						setState({ editingTaxes: newEditingTaxes });
					}}
				/>
				{termEditor}
			</div>
		</div>
	);
};

const PostPickerQuery = withState({
	editingTaxes: [],
})(
	({
		editingTaxes,
		setState,
		segment,
		updateSegment,
		postTypes,
		taxonomies,
	}) => {
		let postTypePick = <Spinner />;
		if (postTypes) {
			if (Array.isArray(postTypes)) {
				postTypePick = (
					<SelectControl
						label={__('Post type')}
						hideLabelFromVision
						value={segment.posttype}
						options={postTypes
							.filter(
								(postType) =>
									!POST_TYPES_TO_IGNORE.includes(
										postType.slug
									)
							)
							.map((postType) => {
								return {
									label: postType.name,
									value: postType.slug,
								};
							})}
						onChange={updateSegment('posttype', segment)}
					/>
				);
			} else {
				postTypePick = <span>{postTypes.name}</span>;
			}
		}

		const taxEditors = taxonomies ? (
			taxonomies.map((taxonomy) => {
				return (
					<TaxonomyEditor
						taxonomy={taxonomy}
						segment={segment}
						updateSegment={updateSegment}
						editingTaxes={editingTaxes}
						setState={setState}
					/>
				);
			})
		) : (
			<Spinner />
		);

		return (
			<div className={THEME_PREFIX + '_list_segment'}>
				<div className="segment_edit_section inline_edit_section">
					<TextControl
						style={{
							width: segment.numberposts.length + 0.5 + 'em',
						}}
						label={__('Number of posts')}
						hideLabelFromVision={true}
						type="number"
						min="0"
						max="50"
						step="1"
						value={segment.numberposts}
						onChange={updateSegment('numberposts', segment)}
					/>
					{postTypePick}

					<span>, ordered by</span>
					<SelectControl
						label={__('Order posts by:')}
						hideLabelFromVision
						value={segment.orderby}
						options={ORDERBY_OPTS}
						onChange={updateSegment('orderby', segment)}
					/>
					<span>, in</span>
					<SelectControl
						label={__('Order posts:')}
						hideLabelFromVision={true}
						value={segment.order}
						options={[
							{
								label: 'descending',
								value: 'desc',
							},
							{
								label: 'ascending',
								value: 'asc',
							},
						]}
						onChange={updateSegment('order', segment)}
					/>
					<span>order.</span>
				</div>
				{taxEditors}
			</div>
		);
	}
);

/*** EXPORTS ****************************************************************/

export default PostPickerQuery;
