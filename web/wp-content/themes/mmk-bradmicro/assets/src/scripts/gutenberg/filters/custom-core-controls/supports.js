import { CUSTOMIZE_SUPPORTS } from "./constants";

const addCustomSupports = (settings) => {
	const supports = { ...settings.supports };

	if (
		typeof CUSTOMIZE_SUPPORTS[settings.name] !== "undefined" &&
		typeof CUSTOMIZE_SUPPORTS[settings.name] === "object"
	) {
		const customSupports = CUSTOMIZE_SUPPORTS[settings.name];
		for (const key in customSupports) {
			if (customSupports.hasOwnProperty(key)) {
				const updatedSupport = customSupports[key];
				supports[key] = {
					...supports[key],
					...updatedSupport,
				};
			}
		}
		console.log("supports", supports);
	}

	return {
		...settings,
		supports: supports,
	};
};

export default {
	name: "madden-theme/custom-supports",
	hook: "blocks.registerBlockType",
	action: addCustomSupports,
};
