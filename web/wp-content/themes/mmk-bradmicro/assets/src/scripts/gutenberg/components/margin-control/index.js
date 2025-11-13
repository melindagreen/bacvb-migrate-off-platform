/*** IMPORTS ****************************************************************/

// WordPress dependencies
import { RadioControl, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/*** COMPONENTS **************************************************************/

const MarginControl = ({
	styleType,
	editType,
	styleValues,
	updateEditType,
	updateValues,
}) => {
	return (
		<div>
			<div>
				<RadioControl
					label={'Set ' + styleType + 's'}
					options={[
						{
							label: 'Single value',
							value: 'single',
						},
						{
							label: 'Set X and Y values seperately',
							value: 'xy',
						},
						{
							label: 'Set all values seperately',
							value: 'trbl',
						},
					]}
					selected={editType}
					onChange={updateEditType}
				/>
			</div>
			<div>
				{(() => {
					switch (editType) {
						case 'xy':
							return (
								<div>
									<RangeControl
										label={'X  ' + styleType}
										value={styleValues.right}
										onChange={updateValues(
											['right', 'left'],
											styleValues
										)}
										min={0}
										max={10}
									/>
									<RangeControl
										label={'Y ' + styleType}
										value={styleValues.top}
										onChange={updateValues(
											['top', 'bottom'],
											styleValues
										)}
										min={0}
										max={10}
									/>
								</div>
							);
						case 'trbl':
							return (
								<div>
									<RangeControl
										label={'Top ' + styleType}
										value={styleValues.top}
										onChange={updateValues(
											['top'],
											styleValues
										)}
										min={0}
										max={10}
									/>
									<RangeControl
										label={'Right ' + styleType}
										value={styleValues.right}
										onChange={updateValues(
											['right'],
											styleValues
										)}
										min={0}
										max={10}
									/>
									<RangeControl
										label={'Bottom ' + styleType}
										value={styleValues.bottom}
										onChange={updateValues(
											['bottom'],
											styleValues
										)}
										min={0}
										max={10}
									/>
									<RangeControl
										label={'Left ' + styleType}
										value={styleValues.left}
										onChange={updateValues(
											['left'],
											styleValues
										)}
										min={0}
										max={10}
									/>
								</div>
							);
						case 'single':
						default:
							return (
								<div>
									<RangeControl
										label={'All ' + styleType + 's'}
										value={styleValues.top}
										onChange={updateValues(
											['top', 'right', 'bottom', 'left'],
											styleValues
										)}
										min={0}
										max={10}
									/>
								</div>
							);
					}
				})()}
			</div>
		</div>
	);
};

/*** EXPORTS ****************************************************************/

export default MarginControl;
