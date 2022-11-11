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
	"release-es6": {
		options: {
			transform: [
				[ "babelify", { presets: [ "es2015" ] } ],

				// This is here to make a production build of React.
				[ "envify", {
					// This makes sure we also transform the React files.
					global: true,
					NODE_ENV: "production",
				} ],
			],
		},
		files: {
			"js/dist/factCheck-<%= pluginVersionSlug %>.js": [ "js/src/factCheck.js" ],
		},
	},
};
