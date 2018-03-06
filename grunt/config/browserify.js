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
			"js/dist/factCheck-<%= pluginVersionSlug %>.js": [ "js/src/factCheck.js" ],
		},
	},
};
