/* global require */
const webpackConfigProd = require( "../../webpack/webpack.config.prod" );
const webpackConfigDev = require( "../../webpack/webpack.config.dev" );

module.exports = {
	buildProd: webpackConfigProd,
	buildDev: webpackConfigDev,
};
