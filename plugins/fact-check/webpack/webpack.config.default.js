const _defaultsDeep = require( "lodash/defaultsDeep" );
const webpack = require( "webpack" );
const paths = require( "./paths" );
const grunt = require( "grunt" );

const readPackageJson = grunt.file.readJSON( "package.json" );
const pluginVersionSlug = paths.flattenVersionForFile( readPackageJson.version );
const outputFilename = "[name]-" + pluginVersionSlug + ".min.js";

const defaultWebpackConfig = {
	devtool: "eval",
	entry: paths.entry,
	context: paths.jsSrc,
	output: {
		path: paths.jsDist,
		filename: outputFilename,
	},
	resolve: {
		extensions: [ ".js", ".jsx" ],
	},
	module: {
		rules: [
			{
				test: /.jsx?$/,
				use: [
					{
						loader: "babel-loader",
					},
				],
			},
		],
	},
	plugins: [
		new webpack.DefinePlugin( {
			"process.env": {
				NODE_ENV: JSON.stringify( "production" ),
			},
		} ),
		new webpack.optimize.UglifyJsPlugin(),
		new webpack.optimize.AggressiveMergingPlugin(),
	],
};

const defaults = ( config ) => {
	return _defaultsDeep( config, defaultWebpackConfig );
};

module.exports = {
	defaults,
};
