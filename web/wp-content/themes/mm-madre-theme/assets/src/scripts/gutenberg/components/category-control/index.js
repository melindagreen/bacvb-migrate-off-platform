/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { CheckboxControl, Spinner } from '@wordpress/components';

// Local dependencies
import { THEME_PREFIX } from '../../inc/constants';
import './styles/index.scss';

/*** COMPONENTS **************************************************************/

const CategoryCheck = ({
	category,
	allCategories,
	activeCategories,
	onUpdateCategories,
}) => {
	const childCategories = allCategories.filter(
		(childCategory) => childCategory.parent === category.id
	);
	return (
		<>
			<CheckboxControl
				label={category.name}
				checked={activeCategories.includes(category.id)}
				onChange={(checked) => {
					if (checked) {
						activeCategories.push(category.id);
					} else {
						activeCategories = activeCategories.filter(
							(activeCategory) => activeCategory !== category.id
						);
					}
					onUpdateCategories(activeCategories);
				}}
			/>
			{childCategories.length > 0 && (
				<div className="childCategories">
					{childCategories.map((childCategory) => (
						<CategoryCheck
							category={childCategory}
							allCategories={allCategories}
							activeCategories={activeCategories}
							onUpdateCategories={onUpdateCategories}
						/>
					))}
				</div>
			)}
		</>
	);
};

const CategoryControl = ({
	title,
	allCategories,
	activeCategories,
	onUpdateCategories,
}) => {
	const categoryChecks =
		allCategories && allCategories.length > 0 ? (
			allCategories
				.filter((category) => category.parent === 0)
				.sort((a, b) => a.id - b.id)
				.map((category) => (
					<CategoryCheck
						category={category}
						allCategories={allCategories}
						activeCategories={activeCategories}
						onUpdateCategories={onUpdateCategories}
					/>
				))
		) : (
			<Spinner />
		);

	return (
		<fieldset className={THEME_PREFIX + '-category-control'}>
			{title && <legend>{title}</legend>}
			<div className="category_control_checks">{categoryChecks}</div>
		</fieldset>
	);
};

/*** EXPORTS ****************************************************************/

export default CategoryControl;
