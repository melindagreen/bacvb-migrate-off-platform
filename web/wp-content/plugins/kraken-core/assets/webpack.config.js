const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

module.exports = {
  ...defaultConfig,
  resolve: {
    ...defaultConfig.resolve,
    alias: {
      ...defaultConfig.alias,
      scripts: path.resolve(__dirname, "src/scripts/"),
      styles: path.resolve(__dirname, "src/styles/"),
      fonts: path.resolve(__dirname, "fonts/"),
      images: path.resolve(__dirname, "images/"),
    },
  },
  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
      {
        test: /\.js/,
        loader: "import-glob",
      },
      {
        test: /\.(png|jpe?g|gif|svg)$/i,
        type: "asset/resource",
        generator: {
          filename: "images/[name][ext]",
        },
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          {
            loader: "sass-loader",
            options: {
              api: "modern-compiler",
              additionalData: `@use "styles/utils" as *;`,
              implementation: require("sass-embedded"),
              sassOptions: {
                includePaths: [path.resolve(__dirname, "src"), path.resolve(__dirname, "images")],
              },
            },
          },
        ],
      },
    ],
  },
  entry: {
    ...defaultConfig.entry(),
    admin: "./src/scripts/admin.js",
    app: "./src/scripts/app.js",
    filters: "./src/filters/index.js",
    customFields: "./src/scripts/custom-fields/index.js",
  },
};
