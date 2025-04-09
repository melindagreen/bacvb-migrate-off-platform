
/**
 * Custom hook to manage the preview state of a component.
 *
 * @param {Object} attributes - The attributes object containing component state.
 * @param {Function} setAttributes - Function to update the attributes object.
 * @returns {[boolean, Function]} - An array containing the current preview state and a function to toggle the preview state.
 */
export const usePreview = (attributes, setAttributes) => {
    const { isPreview = true } = attributes;

    const togglePreview = () => {
        setAttributes({ isPreview: !isPreview });
    };

    return [isPreview, togglePreview];
};
