module.exports = {
	"fact-check-for-yoastseo": {
		options: {
			output: {
				comments: /^!|@preserve|@license|@cc_on/,
			},
			report: "gzip",
		},
		files: [ {
			expand: true,
			cwd: "js/dist",
			src: [
				"*.js",
				"!*.min.js",
			],
			dest: "js/dist",
			ext: ".min.js",
			extDot: "first",
			isFile: true,
		} ],
	},
};
