/* global require, module */
const defaults = require( "./webpack.config.default.js" ).defaults;

const prodConfig = {
	devtool: "cheap-module-source-map",
};

module.exports = defaults( prodConfig );
