const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const glob = require("glob");

/* Pulls in all block scripts inside of the assets folders & creates a separate entry point for each block
* Using [name] these scripts will be ouput to /build/blocks/ and assets.php will pick it up based on the block name
*/
const blockEntries = glob
 .sync("./src/scripts/gutenberg/blocks/*/assets/*.js")
 .reduce((files, path) => {
   const name =
     "blocks/" +
     path
       .split("/")
       .pop()
       .replace(/\.[^]+$/, "");
   return { ...files, [name]: path };
 }, {});

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
    entry: {
        ...defaultConfig.entry(),
		admin: "./src/scripts/admin.js",
		app: "./src/scripts/app.js",
        ...blockEntries
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