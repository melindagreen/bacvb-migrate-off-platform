/**
 * 2nd-level higher-order function that generates funcs for onChange
 * that updates one or more properties of an object-type attribute
 *
 * ... OK, this is confusing. TODO try and think of a better way to explain?
 *
 * @param {String} 		attribute 			The attribute to update
 * @param {Function}    attrSetter          The function that sets attribute values -- should be setAttributes
 * @returns {Function}						1st-level higher-order function for onChange of the given attribute
 */
export const updateObjAttr = (attribute, attrSetter) => (
	properties,
	currentValues
) => (newValue) => {
	const newValues = Object.assign({}, currentValues);
	properties.forEach((property) => (newValues[property] = newValue));
	attrSetter({ [attribute]: newValues });
};

/**
 * Create an updater for a property of a single object in an attribute who's value is an array of objects
 * @param {String}		attribute 			The attribute to update
 * @param {Function}    attrSetter          The function that sets attribute values -- should be setAttributes
 * @param {Object[]} 	currentArr 			The current array of objects
 * @returns {Function}						1st-level higher-order function for onChange of the given attribute
 */
export const updateObjArrAttr = (attribute, attrSetter, currentArr) => (
	property,
	currentObj,
	transform = false
) => (newValue) => {
	if (!currentObj.hasOwnProperty('id'))
		throw new Error('The object to update must have the property `id`.');
	if (transform) newValue = transform(newValue);
	const updatedObj = Object.assign({}, currentObj, {
		[property]: newValue,
	});
	const updatedArr = [
		...currentArr.filter((obj) => {
			if (!obj.hasOwnProperty('id'))
				throw new Error(
					'All objects in array must have the property `id`.'
				);
			return obj.id !== updatedObj.id;
		}),
		updatedObj,
	].sort((a, b) => a.id - b.id);
	attrSetter({ [attribute]: updatedArr });
};

/**
 * Transforms an object with properties r, g, b, and optionally a into a valid CSS rgb(a) value
 * @param {Object}      rgbObj          The RGB(A) object
 * @returns {String}                    A valid rgb(a) string
 */
export const rgbObjToStr = (rgbObj) => {
	const { r, g, b, a } = rgbObj;
	return a ? `rgba(${r}, ${g}, ${b}, ${a})` : `rgb(${r}, ${g}, ${b})`;
};

/**
 * Quick and dirty timestamp checker.
 * @param {String} 		timestamp 			The timestamp to check
 * @returns {Boolean}						Is the timestamp a valid date?
 */
export const isValidDate = (timestamp) => isNaN(Date.parse(timestamp));
