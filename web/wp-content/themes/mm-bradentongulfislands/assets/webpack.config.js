const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require('path');

module.exports = {
    ...defaultConfig,
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.js/,
                loader: 'import-glob'
            }
        ]
    },
    resolve: {
        ...defaultConfig.resolve,
        alias: {
            ...defaultConfig.alias,
            scripts: path.resolve(__dirname, 'src/scripts/'),
            styles: path.resolve(__dirname, 'src/styles/'),
            fonts: path.resolve(__dirname, 'fonts/'),
            images: path.resolve(__dirname, 'images/'),
        },
    },
    output: {
        ...defaultConfig.output,
        publicPath: '../build/'
    },
    watchOptions: {
        poll: true,
        ignored: /node_modules/
     }     
}