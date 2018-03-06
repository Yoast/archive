module.exports = {
	plugin: {
		src: [ "<%= files.js %>", "!js/src/tests/**" ],
		options: {
			maxWarnings: 0,
		},
	},
};
