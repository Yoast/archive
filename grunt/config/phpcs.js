module.exports = {
	options: {
		ignoreExitCode: true,
	},
	plugin: {
		options: {
			bin: "vendor/bin/phpcs",
			standard: "phpcs.xml",
			extensions: "php",
		},
		dir: [
			"<%= files.php %>",
		],
	},
};
