module.exports = {
	build: {
		options: {
			transform: [
				[ "babelify", { presets: [ "es2015" ] } ],
			],
			browserifyOptions: {
				debug: true,
			},
		},
		files: {
			"js/dist/test-<%= pluginVersionSlug %>.js": [ "js/src/test.js" ],
		},
	},
};
