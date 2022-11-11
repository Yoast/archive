// See https://github.com/gruntjs/grunt-contrib-copy
module.exports = {
	artifact: {
		files: [
			{
				expand: true,
				cwd: ".",
				src: [
					"js/dist/**/*.min.js",
					"readme.txt",
					"fact-check-for-yoastseo.php"
				],
				dest: "artifact",
			},
		],
	},
};
