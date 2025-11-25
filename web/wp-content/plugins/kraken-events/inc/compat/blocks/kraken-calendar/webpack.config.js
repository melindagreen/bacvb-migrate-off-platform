// Import the original config from the @wordpress/scripts package.
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
module.exports = {
	...defaultConfig,
	entry: {
        ...defaultConfig.entry(),
        'style-one': "./src/style-one.js"
	},
};